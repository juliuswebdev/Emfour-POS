@if(count($payment_devices) > 0)
    <div class="payment_device_container">
        <h4>@lang('payment_device.choose_device')</h4>
        <div class="row">
            @foreach($payment_devices as $k => $payment_device)
                <div class="col-md-12">

                    @if(auth()->user()->default_payment_device == 0)
                        @php
                            $checked = ($k == 0) ? 'checked' : '';     
                        @endphp
                    @else
                        @php
                            $checked = ($payment_device->id == auth()->user()->default_payment_device) ? 'checked' : '';
                        @endphp
                    @endif
                    <input type="radio" 
                    id="d_{{ $payment_device->id }}" name="payment_device" value="{{ $payment_device->id }}" {{ $checked }}>&nbsp;&nbsp;&nbsp;
                    <label for="d_{{ $payment_device->id }}">{{ $payment_device->name }}</label>
                </div>
            @endforeach
        </div>
        {{-- <a class="mt-5 btn btn-primary btn-select_device">@lang('messages.update')</a> --}}
    </div>
@endif