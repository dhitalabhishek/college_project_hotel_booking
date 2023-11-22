<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Room;
use Auth;

class CustomerHomeController extends Controller
{
    public function index()
    {
        $total_completed_orders = Order::where('status','Completed')->where('customer_id',Auth::guard('customer')->user()->id)->count();
        $total_pending_orders = Order::where('status','Pending')->where('customer_id',Auth::guard('customer')->user()->id)->count();
        return view('customer.home', compact('total_completed_orders','total_pending_orders'));
    }

    public function feedback($room_id)
    {
        $room = Room::find($room_id);
        return view('customer.feedback',compact('room'));
    }

    public function submitFeedback(Request $request){
        $data  = $request->all();
        Feedback::create($data);
        return redirect()->back()->with('success', 'Feedback saved successfully.');
    }
}
