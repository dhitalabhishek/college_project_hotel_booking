@extends('customer.layout.app')

@section('heading', 'Add Feedback')

@section('main_content')
<div class="section-body">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('customer_feedback') }}" method="post">
                        @csrf
                        <input type="hidden" name="room_id" value="{{ $room->id}}">
                        <input type="hidden" name="customer_id" value="{{ auth()->id()}}">

                        <div class="row">
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label class="form-label">Rating</label>
                                            <input type="text" class="form-control" name="rating" placeholder="Please Enter Rating Out of 5">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-4">
                                            <label class="form-label">Feedback *</label>
                                            <textarea class="form-control" rows="10" cols="5" name="feedback" required >{{ old('feedback') }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label"></label>
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection