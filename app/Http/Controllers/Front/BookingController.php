<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\BookedRoom;
use App\Models\Room;
use Auth;
use DB;
use App\Mail\Websitemail;
use Carbon\Carbon;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Transaction;
Use Stripe;

class BookingController extends Controller
{
    
    public function cart_submit(Request $request)
    {
        $request->validate([
            'room_id' => 'required',
            'checkin_checkout' => 'required',
            'adult' => 'required'
        ]);

        // dd($request->all());
        $dates = explode(' - ',$request->checkin_checkout);
        $checkin_date = $dates[0];
        $checkout_date = $dates[1];

        $d1 = explode('/',$checkin_date);
        $d2 = explode('/',$checkout_date);
        $d1_new = $d1[2].'-'.$d1[1].'-'.$d1[0];
        $d2_new = $d2[2].'-'.$d2[1].'-'.$d2[0];
        $date1 = Carbon::parse($d1_new)->format('Y-m-d');
        $date2 = Carbon::parse($d2_new)->format('Y-m-d');

        if($date1 >= $date2) {
            return back();
        }
        $total_already_booked_rooms = BookedRoom::whereDate('booking_date',$date1)
        ->where('room_id',$request->room_id)->count();

        $room = Room::find($request->room_id);

        if($total_already_booked_rooms == $room->total_rooms) {
            return redirect()->back()->with('error', 'This room is already booked');
        }
        $data = [
            'room_id' => $request->room_id,
            'adult' => $request->adult,
            'children' => $request->children,
            'checkin_date' => $date1,
            'checkout_date' => $date2,
        ];
        session()->push('cart_room',$data);
        return redirect()->route('checkout')->with('success', 'Room is added successfully.');
    }

    public function cart_view()
    {
        return view('front.cart');
    }

    public function cart_delete($id)
    {
        $arr_cart_room_id = array();
        $i=0;
        foreach(session()->get('cart_room_id') as $value) {
            $arr_cart_room_id[$i] = $value;
            $i++;
        }

        $arr_cart_checkin_date = array();
        $i=0;
        foreach(session()->get('cart_checkin_date') as $value) {
            $arr_cart_checkin_date[$i] = $value;
            $i++;
        }

        $arr_cart_checkout_date = array();
        $i=0;
        foreach(session()->get('cart_checkout_date') as $value) {
            $arr_cart_checkout_date[$i] = $value;
            $i++;
        }

        $arr_cart_adult = array();
        $i=0;
        foreach(session()->get('cart_adult') as $value) {
            $arr_cart_adult[$i] = $value;
            $i++;
        }

        $arr_cart_children = array();
        $i=0;
        foreach(session()->get('cart_children') as $value) {
            $arr_cart_children[$i] = $value;
            $i++;
        }

        session()->forget('cart_room_id');
        session()->forget('cart_checkin_date');
        session()->forget('cart_checkout_date');
        session()->forget('cart_adult');
        session()->forget('cart_children');

        for($i=0;$i<count($arr_cart_room_id);$i++)
        {
            if($arr_cart_room_id[$i] == $id) 
            {
                continue;    
            }
            else
            {
                session()->push('cart_room_id',$arr_cart_room_id[$i]);
                session()->push('cart_checkin_date',$arr_cart_checkin_date[$i]);
                session()->push('cart_checkout_date',$arr_cart_checkout_date[$i]);
                session()->push('cart_adult',$arr_cart_adult[$i]);
                session()->push('cart_children',$arr_cart_children[$i]);
            }
        }

        return redirect()->back()->with('success', 'Cart item is deleted.');

    }

    public function cancel_booking(){
        session()->forget('cart_room');
        return redirect()->route('room')->with('success', 'Reservation has been cancel.');
    }

    public function checkout()
    {
        if(!Auth::guard('customer')->check()) {
            return redirect()->back()->with('error', 'You must have to login in order to checkout');
        }

        if(!session()->has('cart_room')) {
            return redirect()->back()->with('error', 'There is no room in the cart');
        }
        $session = session()->get('cart_room')[0];
        $room = Room::find($session['room_id']);
        $date1 = Carbon::parse($session['checkin_date']);
        $date2 = Carbon::parse($session['checkout_date']);
        $diff = $date1->diffInDays($date2);
        $total_price = $room->price * $diff;
        return view('front.checkout',compact('room','total_price','diff'));
    }

    public function payment(Request $request)
    {
        if(!Auth::guard('customer')->check()) {
            return redirect()->back()->with('error', 'You must have to login in order to checkout');
        }

        if(!session()->has('cart_room')) {
            return redirect()->back()->with('error', 'There is no item in the cart');
        }

        $request->validate([
            'billing_name' => 'required',
            'billing_email' => 'required|email',
            'billing_phone' => 'required',
            'billing_country' => 'required',
            'billing_address' => 'required',
            'billing_state' => 'required',
            'billing_city' => 'required',
            'billing_zip' => 'required',
        ]);

        $session = session()->get('cart_room')[0];
        $room = Room::find($session['room_id']);
        $date1 = Carbon::parse($session['checkin_date']);
        $date2 = Carbon::parse($session['checkout_date']);
        $diff = $date1->diffInDays($date2);
        $total_price = $room->price * $diff;
        $customer = auth()->guard('customer')->user();
        $customer->update([
            'name' => $request->billing_name,
            'email' => $request->billing_email,
            'phone' =>  $request->billing_phone,
            'country' =>  $request->billing_country,
            'address' =>  $request->billing_address,
            'state' =>  $request->billing_state,
            'city' =>  $request->billing_city,
            'zip' =>  $request->billing_zip,
        ]);

        return view('front.payment',compact('room','total_price','diff'));
    }

    public function paypal($final_price)
    {
        $client = env('PAYPAL_CLIENT_ID');
        $secret = env('PAYPAL_CLIENT_SECRET_KEY');

        $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                $client, // ClientID
                $secret // ClientSecret
            )
        );

        $paymentId = request('paymentId');
        $payment = Payment::get($paymentId, $apiContext);

        $execution = new PaymentExecution();
        $execution->setPayerId(request('PayerID'));

        $transaction = new Transaction();
        $amount = new Amount();
        $details = new Details();

        $details->setShipping(0)
            ->setTax(0)
            ->setSubtotal($final_price);

        $amount->setCurrency('USD');
        $amount->setTotal($final_price);
        $amount->setDetails($details);
        $transaction->setAmount($amount);
        $execution->addTransaction($transaction);
        $result = $payment->execute($execution, $apiContext);

        if($result->state == 'approved')
        {
            $paid_amount = $result->transactions[0]->amount->total;
            
            $order_no = time();

            $statement = DB::select("SHOW TABLE STATUS LIKE 'orders'");
            $ai_id = $statement[0]->Auto_increment;

            $obj = new Order();
            $obj->customer_id = Auth::guard('customer')->user()->id;
            $obj->order_no = $order_no;
            $obj->transaction_id = $result->id;
            $obj->payment_method = 'PayPal';
            $obj->paid_amount = $paid_amount;
            $obj->booking_date = date('d/m/Y');
            $obj->status = 'Completed';
            $obj->save();
            
            $arr_cart_room_id = array();
            $i=0;
            foreach(session()->get('cart_room_id') as $value) {
                $arr_cart_room_id[$i] = $value;
                $i++;
            }

            $arr_cart_checkin_date = array();
            $i=0;
            foreach(session()->get('cart_checkin_date') as $value) {
                $arr_cart_checkin_date[$i] = $value;
                $i++;
            }

            $arr_cart_checkout_date = array();
            $i=0;
            foreach(session()->get('cart_checkout_date') as $value) {
                $arr_cart_checkout_date[$i] = $value;
                $i++;
            }

            $arr_cart_adult = array();
            $i=0;
            foreach(session()->get('cart_adult') as $value) {
                $arr_cart_adult[$i] = $value;
                $i++;
            }

            $arr_cart_children = array();
            $i=0;
            foreach(session()->get('cart_children') as $value) {
                $arr_cart_children[$i] = $value;
                $i++;
            }

            for($i=0;$i<count($arr_cart_room_id);$i++)
            {
                $r_info = Room::where('id',$arr_cart_room_id[$i])->first();
                $d1 = explode('/',$arr_cart_checkin_date[$i]);
                $d2 = explode('/',$arr_cart_checkout_date[$i]);
                $d1_new = $d1[2].'-'.$d1[1].'-'.$d1[0];
                $d2_new = $d2[2].'-'.$d2[1].'-'.$d2[0];
                $t1 = strtotime($d1_new);
                $t2 = strtotime($d2_new);
                $diff = ($t2-$t1)/60/60/24;
                $sub = $r_info->price*$diff;

                $obj = new OrderDetail();
                $obj->order_id = $ai_id;
                $obj->room_id = $arr_cart_room_id[$i];
                $obj->order_no = $order_no;
                $obj->checkin_date = $arr_cart_checkin_date[$i];
                $obj->checkout_date = $arr_cart_checkout_date[$i];
                $obj->adult = $arr_cart_adult[$i];
                $obj->children = $arr_cart_children[$i];
                $obj->subtotal = $sub;
                $obj->save();

                while(1) {
                    if($t1>=$t2) {
                        break;
                    }
    
                    $obj = new BookedRoom();
                    $obj->booking_date = date('d/m/Y',$t1);
                    $obj->order_no = $order_no;
                    $obj->room_id = $arr_cart_room_id[$i];
                    $obj->save();
    
                    $t1 = strtotime('+1 day',$t1);
                }

            }

            $subject = 'New Order';
            $message = 'You have made an order for hotel booking. The booking information is given below: <br>';
            $message .= '<br>Order No: '.$order_no;
            $message .= '<br>Transaction Id: '.$result->id;
            $message .= '<br>Payment Method: PayPal';
            $message .= '<br>Paid Amount: '.$paid_amount;
            $message .= '<br>Booking Date: '.date('d/m/Y').'<br>';

            for($i=0;$i<count($arr_cart_room_id);$i++) {

                $r_info = Room::where('id',$arr_cart_room_id[$i])->first();

                $message .= '<br>Room Name: '.$r_info->name;
                $message .= '<br>Price Per Night: $'.$r_info->price;
                $message .= '<br>Checkin Date: '.$arr_cart_checkin_date[$i];
                $message .= '<br>Checkout Date: '.$arr_cart_checkout_date[$i];
                $message .= '<br>Adult: '.$arr_cart_adult[$i];
                $message .= '<br>Children: '.$arr_cart_children[$i].'<br>';
            }            

            $customer_email = Auth::guard('customer')->user()->email;

            \Mail::to($customer_email)->send(new Websitemail($subject,$message));

            session()->forget('cart_room_id');
            session()->forget('cart_checkin_date');
            session()->forget('cart_checkout_date');
            session()->forget('cart_adult');
            session()->forget('cart_children');
            session()->forget('billing_name');
            session()->forget('billing_email');
            session()->forget('billing_phone');
            session()->forget('billing_country');
            session()->forget('billing_address');
            session()->forget('billing_state');
            session()->forget('billing_city');
            session()->forget('billing_zip');

            return redirect()->route('home')->with('success', 'Payment is successful');
        }
        else
        {
            return redirect()->route('home')->with('error', 'Payment is failed');
        }


    }

    public function stripe(Request $request,$final_price)
    {
        $stripe_secret_key = env('STRIPE_SECRET');
        $cents = $final_price*100;
        Stripe\Stripe::setApiKey($stripe_secret_key);
        $response = Stripe\Charge::create ([
            "amount" => $cents,
            "currency" => "usd",
            "source" => $request->stripeToken,
            "description" => "Room Booking"
        ]);

        $responseJson = $response->jsonSerialize();
        $transaction_id = $responseJson['balance_transaction'];
        $last_4 = $responseJson['payment_method_details']['card']['last4'];

        $order = new Order();
        $order->customer_id = auth()->guard('customer')->user()->id;
        $order->order_no = time();
        $order->transaction_id = $transaction_id;
        $order->payment_method = 'Stripe';
        $order->card_last_digit = $last_4;
        $order->paid_amount = $final_price;
        $order->booking_date = today();
        $order->status = 'Completed';
        $order->save();

        $cart_data = session()->get('cart_room')[0];
        $date1 = Carbon::parse($cart_data['checkin_date']);
        $date2 = Carbon::parse($cart_data['checkout_date']);
        $roomPrice = Room::find($cart_data['room_id'])->price;
        $diff = $date1->diffInDays($date2);
        $price = $roomPrice * $diff;

        $order->order_details()->create([
            'order_no' => $order->order_no,
            'room_id' => $cart_data['room_id'],
            'checkin_date' => $cart_data['checkin_date'],
            'checkout_date' => $cart_data['checkout_date'],
            'adult' => $cart_data['adult'],
            'children' => $cart_data['children'],
            'subtotal' => $price ?? 0
         ]);

         $obj = new BookedRoom();
         $obj->booking_date = $cart_data['checkin_date'];
         $obj->order_no = $order->order_no;
         $obj->room_id = $cart_data['room_id'];
         $obj->save();

         $subject = 'New Order';
         $message = 'You have made an order for hotel booking. The booking information is given below: <br>';
         $message .= '<br>Order No: '.$order->order_no;
         $message .= '<br>Transaction Id: '.$order->transaction_id;
         $message .= '<br>Payment Method: Stripe';
         $message .= '<br>Paid Amount: '.$order->paid_amount;
         $message .= '<br>Booking Date: '.$order->booking_date.'<br>';
         $customer_email = Auth::guard('customer')->user()->email;

         \Mail::to($customer_email)->send(new Websitemail($subject,$message));
         
        session()->forget('cart_room');
        return redirect()->route('home')->with('success', 'Payment is successful');
    }

    public function cod(Request $request,$final_price)
    {
        $order = new Order();
        $order->customer_id = auth()->guard('customer')->user()->id;
        $order->order_no = time();
        $order->transaction_id = "null";
        $order->payment_method = 'Cash On Delivery';
        $order->card_last_digit = "null";
        $order->paid_amount = $final_price;
        $order->booking_date = today();
        $order->status = 'Pending';
        $order->save();

        $cart_data = session()->get('cart_room')[0];
        $date1 = Carbon::parse($cart_data['checkin_date']);
        $date2 = Carbon::parse($cart_data['checkout_date']);
        $roomPrice = Room::find($cart_data['room_id'])->price;
        $diff = $date1->diffInDays($date2);
        $price = $roomPrice * $diff;

        $order->order_details()->create([
            'order_no' => $order->order_no,
            'room_id' => $cart_data['room_id'],
            'checkin_date' => $cart_data['checkin_date'],
            'checkout_date' => $cart_data['checkout_date'],
            'adult' => $cart_data['adult'],
            'children' => $cart_data['children'],
            'subtotal' => $price ?? 0
         ]);

         $obj = new BookedRoom();
         $obj->booking_date = $cart_data['checkout_date'];
         $obj->order_no = $order->order_no;
         $obj->room_id = $cart_data['room_id'];
         $obj->save();
        session()->forget('cart_room');
        return redirect()->route('home')->with('success', 'Payment is successful');
    }


}
