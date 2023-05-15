
<div class="row">
    <div class="col-sm-6">
        <strong>Reference No:</strong> {{ $booking->booking_details->ref_no }}<br>
        <strong>@lang('contact.customer'):</strong> {{ $booking->customer->name }}<br>
        <strong>@lang('restaurant.service_staff'):</strong> {{ $booking->waiter->user_full_name ?? '--' }}<br>
        <strong>@lang('restaurant.correspondent'):</strong> {{ $booking->correspondent->user_full_name ?? '--' }}<br>
        <strong>@lang('restaurant.services'):</strong> {{ $services }}<br>
        <strong>@lang('restaurant.customer_note'):</strong> {{ $booking->booking_note }}
    </div>
    <div class="col-sm-6">
        <strong>@lang('messages.location'):</strong> {{ $booking->location->name }}<br>
        <strong>@lang('restaurant.table'):</strong> {{ $booking->table->name ?? '--' }}<br>
        <strong>@lang('restaurant.booking_starts'):</strong> {{ $booking->booking_start }}<br>
        <strong>@lang('restaurant.booking_ends'):</strong> {{ $booking->booking_end }}<br>
        <strong>@lang('restaurant.time'):</strong> {{ $booking->booking_details->time }}<br>
        <strong>@lang('restaurant.phone'):</strong> {{ $booking->booking_details->phone }}<br>
        <strong>@lang('restaurant.email'):</strong> {{ $booking->booking_details->email }}
    </div>
</div>
<br>
<div class="row">
    <div class="col-sm-9">
        {!! Form::open(['url' => action([\App\Http\Controllers\Restaurant\BookingController::class, 'update'], [$booking->id]), 'method' => 'PUT', 'id' => 'checkin_booking_form', 'class' => 'check_booking_form' ]) !!}
            <div class="input-group">
                <input type="hidden" name="booking_status" value="checkin">
                <!-- /btn-group -->
                <div class="input-group-btn">
                    <button type="submit" class="btn btn-primary">@lang('restaurant.checkin')</button>
                </div>
            </div>
        {!! Form::close() !!}
    </div>
</div>

		