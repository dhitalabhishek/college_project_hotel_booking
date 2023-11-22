@extends('front.layout.app')

@section('main_content')
<div class="page-top">
    <div class="bg"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>{{ $global_page_data->checkout_heading }}</h2>
            </div>
        </div>
    </div>
</div>
<div class="page-content">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-md-6 checkout-left">
                
                <form action="{{ route('payment') }}" method="post" class="frm_checkout">
                    @csrf
                    <div class="billing-info">
                        <h4 class="mb_30">Billing Information</h4>
                        @php
                        if(session()->has('billing_name')) {
                            $billing_name = session()->get('billing_name');
                        } else {
                            $billing_name = Auth::guard('customer')->user()->name;
                        }

                        if(session()->has('billing_email')) {
                            $billing_email = session()->get('billing_email');
                        } else {
                            $billing_email = Auth::guard('customer')->user()->email;
                        }

                        if(session()->has('billing_phone')) {
                            $billing_phone = session()->get('billing_phone');
                        } else {
                            $billing_phone = Auth::guard('customer')->user()->phone;
                        }

                        if(session()->has('billing_country')) {
                            $billing_country = session()->get('billing_country');
                        } else {
                            $billing_country = Auth::guard('customer')->user()->country;
                        }

                        if(session()->has('billing_address')) {
                            $billing_address = session()->get('billing_address');
                        } else {
                            $billing_address = Auth::guard('customer')->user()->address;
                        }

                        if(session()->has('billing_state')) {
                            $billing_state = session()->get('billing_state');
                        } else {
                            $billing_state = Auth::guard('customer')->user()->state;
                        }

                        if(session()->has('billing_city')) {
                            $billing_city = session()->get('billing_city');
                        } else {
                            $billing_city = Auth::guard('customer')->user()->city;
                        }

                        if(session()->has('billing_zip')) {
                            $billing_zip = session()->get('billing_zip');
                        } else {
                            $billing_zip = Auth::guard('customer')->user()->zip;
                        }
                        @endphp
                        <div class="row">
                            <div class="col-lg-6">
                                <label for="">Name: *</label>
                                <input type="text" class="form-control mb_15" name="billing_name" value="{{ $billing_name }}">
                                @error('billing_name')
                                <span class="text-danger error-text billing_name">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-lg-6">
                                <label for="">Email Address: *</label>
                                <input type="text" class="form-control mb_15" name="billing_email" value="{{ $billing_email }}">
                                @error('billing_email')
                                <span class="text-danger error-text billing_email">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-lg-6">
                                <label for="">Phone Number: *</label>
                                <input type="number" class="form-control mb_15" name="billing_phone" value="{{ $billing_phone }}">
                                @error('billing_phone')
                                <span class="text-danger error-text billing_phone">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-lg-6">
                                <label for="">Country: *</label>
                                <input type="text" class="form-control mb_15" name="billing_country" value="{{ $billing_country }}">
                                @error('billing_country')
                                <span class="text-danger error-text billing_country">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-lg-6">
                                <label for="">Address: *</label>
                                <input type="text" class="form-control mb_15" name="billing_address" value="{{ $billing_address }}">
                                @error('billing_address')
                                <span class="text-danger error-text billing_address">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-lg-6">
                                <label for="">State: *</label>
                                <input type="text" class="form-control mb_15" name="billing_state" value="{{ $billing_state }}">
                                @error('billing_state')
                                <span class="text-danger error-text billing_state">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-lg-6">
                                <label for="">City: *</label>
                                <input type="text" class="form-control mb_15" name="billing_city" value="{{ $billing_city }}">
                                @error('billing_city')
                                <span class="text-danger error-text billing_city">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-lg-6">
                                <label for="">Zip Code: *</label>
                                <input type="text" class="form-control mb_15" name="billing_zip" value="{{ $billing_zip }}">
                                @error('billing_zip')
                                <span class="text-danger error-text billing_zip">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success mb_30">Continue to payment</button>
                    <a href="{{ route('cancel_booking') }}" class="btn btn-primary bg-website mb_30">Cancel Booking</a>

                </form>
            </div>
            <div class="col-lg-4 col-md-6 checkout-right">
                <div class="inner">
                    <h4 class="mb_10">Room Details</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>

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
@endsection