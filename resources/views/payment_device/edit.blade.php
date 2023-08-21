@extends('layouts.app')
@section('title',  __('payment_device.edit_payment_device'))

@section('content')
<style type="text/css">



</style>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('payment_device.edit_payment_device')</h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">
{!! Form::open(['url' => action([\App\Http\Controllers\PaymentDevicesController::class, 'update'], [$payment_device->id]), 'method' => 'put', 'id' => 'edit_payment_device_form' ]) !!}
	<div class="box box-solid">
    <div class="box-body">
      <div class="row">
        <div class="col-sm-12">
          <div class="form-group">
            {!! Form::label('name', __('payment_device.name') . ':*') !!}
              {!! Form::text('name', $payment_device->name, ['class' => 'form-control', 'required',
              'placeholder' => __('payment_device.name')]); !!}
          </div>
        </div>
        <div class="col-sm-12">
          <div class="form-group">
            {!! Form::label('business_locations',__('payment_device.business_locations') . ':*') !!}
            <select name="business_locations" class="form-control select2" required>
                <option value="">@lang('payment_device.select_business_locations')</option>
                @foreach($business_locations as $item)
                
                <option value="{{ $item->id }}" @if($payment_device->location_id == $item->id) selected @endif>{{ $item->name }}</option>
                @endforeach
            </select>
          </div>
        </div>
        <div class="col-sm-12">
          <div class="form-group">
            {!! Form::label('payment_device_model',__('payment_device.payment_device_model') . ':*') !!}
            <select name="payment_device_model" class="form-control select2" required>
                <option value="">@lang('payment_device.select_payment_device_model')</option>
                @foreach($payment_device_model as $item)
                <option value="{{ $item->id }}" @if($payment_device->device_model_id == $item->id) selected @endif>{{ $item->name }}</option>
                @endforeach
            </select>
          </div>
        </div>
        <div id="payment_device_model_settings">
            <div id="payment_device_model_settings_1" class="col-sm-12" style="display: @if($payment_device->device_model_id == 1)  block @else none @endif">
                <h4></h4>
                @php
                    $settings = json_decode($payment_device->settings);
                    $protocol = $settings->protocol ?? '';
                    $terminal_ip = $settings->terminal_ip ?? '';
                    $cgi_port = $settings->cgi_port ?? '';
                    $auth_key = $settings->auth_key ?? '';
                    $register_id = $settings->register_id ?? '';
                    $allow_to_print_receipt = $settings->allow_to_print_receipt ?? '';
                    $allow_to_customer_tip = $settings->allow_to_customer_tip ?? '';
                    if($allow_to_customer_tip == "Yes"){
                      $tip_option_first = $settings->tip_option_first ?? '';
                      $tip_option_second = $settings->tip_option_second ?? '';
                      $tip_option_third = $settings->tip_option_third ?? '';
                    }else{
                      $tip_option_first = $tip_option_second = $tip_option_third = "";
                    }
                    

                @endphp
                <div class="form-group">
                    {!! Form::label('payment_device_model',__('payment_device.protocol') . ':*') !!}
                    <select name="settings1[protocol]" class="form-control select2" required>
                        <option value="">@lang('payment_device.select_protocol')</option>
                        <option value="http" @if($protocol == 'http') selected @endif>HTTP</option>
                        <option value="https" @if($protocol == 'https') selected @endif>HTTPS</option>
                    </select>
                </div>
                <div class="form-group">
                    {!! Form::label('terminal_ip', __('payment_device.terminal_ip') . ':*') !!}
                    {!! Form::text('settings1[terminal_ip]', $terminal_ip, ['class' => 'form-control', 'required',
                    'placeholder' => __('payment_device.terminal_ip')]); !!}
                </div>
                <div class="form-group">
                    {!! Form::label('cgi_port', __('payment_device.cgi_port') . ':*') !!}
                    {!! Form::text('settings1[cgi_port]', $cgi_port, ['class' => 'form-control', 'required',
                    'placeholder' => __('payment_device.cgi_port')]); !!}
                </div>
                <div class="form-group">
                    {!! Form::label('auth_key', __('payment_device.auth_key') . ':*') !!}
                    {!! Form::text('settings1[auth_key]', $auth_key, ['class' => 'form-control', 'required',
                    'placeholder' => __('payment_device.auth_key')]); !!}
                </div>
                <div class="form-group">
                    {!! Form::label('register_id', __('payment_device.register_id') . ':*') !!}
                    {!! Form::text('settings1[register_id]', $register_id, ['class' => 'form-control', 'required',
                    'placeholder' => __('payment_device.register_id')]); !!}
                </div>

                <div class="form-group">
                  {!! Form::label('payment_device_model',__('payment_device.allow_to_print_receipt') . ':*') !!}
                  <select name="settings1[allow_to_print_receipt]" class="form-control select2" required>
                      <option value="">@lang('payment_device.allow_to_print_receipt')</option>
                      
                      <option value="Yes" @if($allow_to_print_receipt == 'Yes') selected @endif>Yes</option>
                      <option value="No" @if($allow_to_print_receipt == 'No') selected @endif>No</option>

                  </select>
                </div>

                <div class="form-group">
                  {!! Form::label('payment_device_model',__('payment_device.allow_to_customer_tip') . ':*') !!}
                  <select name="settings1[allow_to_customer_tip]" class="form-control select2" id="allow_to_customer_tip" required>
                      <option value="">@lang('payment_device.allow_to_customer_tip')</option>
                      <option value="Yes" @if($allow_to_customer_tip == 'Yes') selected @endif>Yes</option>
                      <option value="No" @if($allow_to_customer_tip == 'No') selected @endif>No</option>
                  </select>
                </div>

                <div class="customer-tip-wrapper" style="display: none;">
                  <div class="row">
                    <div class="col-md-4 col-sm-4">
                      <div class="form-group">
                          {!! Form::label('tip_option_first', __('payment_device.tip_option_first')) !!}
                          {!! Form::text('settings1[tip_option_first]', $tip_option_first, ['class' => 'form-control allow-decimal-only',
                          'placeholder' => __('payment_device.tip_option_first_placeholder')]); !!}
                      </div>
                    </div>

                    <div class="col-md-4 col-sm-4">
                      <div class="form-group">
                        {!! Form::label('tip_option_second', __('payment_device.tip_option_second')) !!}
                        {!! Form::text('settings1[tip_option_second]', $tip_option_second, ['class' => 'form-control allow-decimal-only',
                        'placeholder' => __('payment_device.tip_option_second_placeholder')]); !!}
                      </div>
                    </div>

                    <div class="col-md-4 col-sm-4">
                      <div class="form-group">
                        {!! Form::label('tip_option_third', __('payment_device.tip_option_third')) !!}
                        {!! Form::text('settings1[tip_option_third]', $tip_option_third, ['class' => 'form-control allow-decimal-only',
                        'placeholder' => __('payment_device.tip_option_third_placeholder')]); !!}
                      </div>
                    </div>
                  </div>
                </div>




            </div>
        </div>
        <div class="col-sm-12">
          <button type="submit" class="btn btn-primary pull-right">@lang('messages.update')</button>
        </div>
      </div>
    </div>
  </div>
  {!! Form::close() !!}
</section>
<!-- /.content -->
@stop
@section('javascript')
    <script>
        $('#payment_device_model_settings_'+$('select[name="payment_device_model"]').val()).find('h4').text($('select[name="payment_device_model"]').find('option:selected').text());
        $(document).ready(function(){
            $('select[name="payment_device_model"]').change(function() {
                var id = $(this).val();
                var elem = $('#payment_device_model_settings_'+id);
                elem.show().siblings().hide();
                elem.find('h4').text($(this).find('option:selected').text());
            });
        });

        $(".allow-decimal-only").keydown(function (event) {
            
            if (event.shiftKey == true) {
                event.preventDefault();
            }

            if ((event.keyCode >= 48 && event.keyCode <= 57) || 
                (event.keyCode >= 96 && event.keyCode <= 105) || 
                event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 37 ||
                event.keyCode == 39 || event.keyCode == 46 || event.keyCode == 190) {

            } else {
                event.preventDefault();
            }

            if($(this).val().indexOf('.') !== -1 && event.keyCode == 190)
            event.preventDefault(); 
            //if a decimal has been added, disable the "."-button
        });

        $('#allow_to_customer_tip').change(function(){
          var allow_to_customer_tip = $(this).val();
          show_customer_tips_options(allow_to_customer_tip);
        })

        function show_customer_tips_options(allow_to_customer_tip){
          var wrapper = $('.customer-tip-wrapper');
          if(allow_to_customer_tip == "Yes"){
            wrapper.show()
            wrapper.find('input').removeAttr('disabled');
          }else{
            wrapper.hide();
            wrapper.find('input').attr('disabled', 'disabled');
          }
        }

        var allow_to_customer_tip = $('#allow_to_customer_tip').val();
        show_customer_tips_options(allow_to_customer_tip);

    </script>
@endsection