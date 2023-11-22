@extends('customer.layout.app')

@section('heading', 'My Orders')

@section('main_content')
<div class="section-body">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="example1">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Order No</th>
                                    <th>Payment Method</th>
                                    <th>Booking Date</th>
                                    <th>Paid Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $row)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $row->order_no }}</td>
                                    <td>{{ $row->payment_method }}</td>
                                    <td>{{ $row->booking_date->toFormattedDateString() }}</td>
                                    <td>$ {{ $row->paid_amount }}</td>
                                    <td>{{ $row->status }}</td>
                                    <td class="pt_10 pb_10">
                                        @if($row->status == "Completed")
                                        <a href="{{ route('customer_feedback_form',$row->order_details->room_id) }}" class="btn btn-info">Feedback</a>
                                        @endif
                                        <a href="{{ route('customer_invoice',$row->id) }}" class="btn btn-primary">Detail</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection