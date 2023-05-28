@if(count($payment_devices) > 0)
    <div class="payment_device_container">
        <h4>@lang('payment_device.choose_device')</h4>
        <div class="row">
            @php $count = 0; @endphp
            @foreach($payment_devices as $payment_device)
                @php $count++ @endphp
                <div class="col-md-12">
                    <input type="radio" name="payment_device" value="{{ $payment_device->id }}" @if($count == 1) checked @endif>&nbsp;&nbsp;&nbsp;
                    <label>{{ $payment_device->name }}</label>
                </div>
            @endforeach
        </div>
        <a class="mt-5 btn btn-primary btn-select_device">@lang('messages.update')</a>
    </div>
@endif