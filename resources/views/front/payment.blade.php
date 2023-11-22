@extends('front.layout.app')
@php
    $adult = 0;
    $children = 0;
    $checkin_date = null;
    $checkout_date = null;

    foreach(session()->get('cart_room') as $value) {
        $adult =  $value['adult'];
        $children =  $value['children'];
        $checkin_date =  $value['checkin_date'];
        $checkout_date =  $value['checkout_date'];
    }

@endphp
@section('main_content')

<script src="https://www.paypalobjects.com/api/checkout.js"></script>

<div class="page-top">
    <div class="bg"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>{{ $global_page_data->payment_heading }}</h2>
            </div>
        </div>
    </div>
</div>
<div class="page-content">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-4 checkout-left mb_30">
                <h4>Make Payment</h4>
                <select name="payment_method" class="form-control select2" id="paymentMethodChange" autocomplete="off">
                    <option value="">Select Payment Method</option>
                    <!-- <option value="PayPal">PayPal</option> -->
                    <option value="cod">On Cash</option>
                    <option value="Stripe">Stripe</option>
                </select>

                <div class="paypal mt_20">
                    <h4>Pay with PayPal</h4>
                    <div id="paypal-button"></div>
                </div>

                <div class="cod mt_20">
                    <h4>Pay with Cash On Delivery</h4>
                    <form action="{{ route('cod',$total_price)}}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary bg-website mb_30">Submit</button>
                    </form>
                </div>

                <div class="stripe mt_20">
                    <h4>Pay with Stripe</h4>
                    @php
                    $cents = $total_price*100;
                    $customer_email = Auth::guard('customer')->user()->email;
                    $stripe_publishable_key = env('STRIPE_KEY');
                    @endphp
                    <form action="{{ route('stripe',$total_price) }}" method="post">
                        @csrf
                        <script
                            src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                            data-key="{{ $stripe_publishable_key }}"
                            data-amount="{{ $cents }}"
                            data-name="{{ env('APP_NAME') }}"
                            data-description=""
                            data-image="{{ asset('stripe.png') }}"
                            data-currency="usd"
                            data-email="{{ $customer_email }}"
                        >
                        </script>
                    </form>
                </div>

            </div>
            <div class="col-lg-4 col-md-4 checkout-right">
                <div class="inner">
                    <h4 class="mb_10">Billing Details</h4>
                    <div>
                        Name: {{ auth()->guard('customer')->user()->name }}
                    </div>
                    <div>
                        Email: {{ auth()->guard('customer')->user()->email }}
                    </div>
                    <div>
                        Phone: {{ auth()->guard('customer')->user()->phone }}
                    </div>
                    <div>
                        Country: {{ auth()->guard('customer')->user()->country }}
                    </div>
                    <div>
                        Address: {{ auth()->guard('customer')->user()->address }}
                    </div>
                    <div>
                        State: {{ auth()->guard('customer')->user()->state }}
                    </div>
                    <div>
                        City: {{ auth()->guard('customer')->user()->city }}
                    </div>
                    <div>
                        Zip: {{ auth()->guard('customer')->user()->zip }}
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 checkout-right">
                <div class="inner">
                    <h4 class="mb_10">Room Details</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td>
                                        {{ $room->name }}
                                        <br>
                                        [{{ date('M d, Y',strtotime($checkin_date)) }} - {{ date('M d, Y',strtotime($checkout_date)) }}]
                                        <br>
                                        Adult: {{ $adult }}, Children: {{ $children }}
                                        <br>
                                        Stay Days: {{ $diff}} 
                                    </td> 
                                    <td class="p_price">
                                        $ {{ $room->price}}
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>Total:</b></td>
                                    <td class="p_price"><b>${{ $total_price }}</b></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@php
$client = 'ARw2VtkTvo3aT7DILgPWeSUPjMK_AS5RlMKkUmB78O8rFCJcfX6jFSmTDpgdV3bOFLG2WE-s11AcCGTD';
@endphp
<script>
	paypal.Button.render({
		env: 'sandbox',
		client: {
			sandbox: '{{ $client }}',
			production: '{{ $client }}'
		},
		locale: 'en_US',
		style: {
			size: 'medium',
			color: 'blue',
			shape: 'rect',
		},
		// Set up a payment
		payment: function (data, actions) {
			return actions.payment.create({
				redirect_urls:{
					return_url: '{{ url("payment/paypal/$total_price") }}'
				},
				transactions: [{
					amount: {
						total: '{{ $total_price }}',
						currency: 'USD'
					}
				}]
			});
		},
		// Execute the payment
		onAuthorize: function (data, actions) {
			return actions.redirect();
		}
	}, '#paypal-button');
</script>
@endsection