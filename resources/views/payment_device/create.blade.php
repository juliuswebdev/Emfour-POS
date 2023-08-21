@extends('layouts.app')
@section('title',  __('payment_device.add_payment_device'))

@section('content')
<style type="text/css">



</style>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('payment_device.add_payment_device')</h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">
{!! Form::open(['url' => action([\App\Http\Controllers\PaymentDevicesController::class, 'store']), 'method' => 'post', 'id' => 'add_payment_device_form' ]) !!}
	<div class="box box-solid">
    <div class="box-body">
      <div class="row">
        <div class="col-sm-12">
          <div class="form-group">
            {!! Form::label('name', __('payment_device.name') . ':*') !!}
              {!! Form::text('name', null, ['class' => 'form-control', 'required',
              'placeholder' => __('payment_device.name')]); !!}
          </div>
        </div>
        <div class="col-sm-12">
          <div class="form-group">
            {!! Form::label('business_locations',__('payment_device.business_locations') . ':*') !!}
            <select name="business_locations" class="form-control select2" required>
                <option value="">@lang('payment_device.select_business_locations')</option>
                @foreach($business_locations as $item)
                <option value="{{ $item->id }}">{{ $item->name }}</option>
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
                <option value="{{ $item->id }}">{{ $item->name }}</option>
                @endforeach
            </select>
          </div>
        </div>
        <div id="payment_device_model_settings">
            <div id="payment_device_model_settings_1" class="col-sm-12" style="display: none">
                <h4></h4>
                <div class="form-group">
                    {!! Form::label('payment_device_model',__('payment_device.protocol') . ':*') !!}
                    <select name="settings1[protocol]" class="form-control select2" required>
                        <option value="">@lang('payment_device.select_protocol')</option>
                        <option value="http">HTTP</option>
                        <option value="https">HTTPS</option>
                    </select>
                </div>
                <div class="form-group">
                    {!! Form::label('terminal_ip', __('payment_device.terminal_ip') . ':*') !!}
                    {!! Form::text('settings1[terminal_ip]', null, ['class' => 'form-control', 'required',
                    'placeholder' => __('payment_device.terminal_ip')]); !!}
                </div>
                <div class="form-group">
                    {!! Form::label('cgi_port', __('payment_device.cgi_port') . ':*') !!}
                    {!! Form::text('settings1[cgi_port]', null, ['class' => 'form-control', 'required',
                    'placeholder' => __('payment_device.cgi_port')]); !!}
                </div>
                <div class="form-group">
                    {!! Form::label('auth_key', __('payment_device.auth_key') . ':*') !!}
                    {!! Form::text('settings1[auth_key]', null, ['class' => 'form-control', 'required',
                    'placeholder' => __('payment_device.auth_key')]); !!}
                </div>
                <div class="form-group">
                    {!! Form::label('register_id', __('payment_device.register_id') . ':*') !!}
                    {!! Form::text('settings1[register_id]', null, ['class' => 'form-control', 'required',
                    'placeholder' => __('payment_device.register_id')]); !!}
                </div>
                <div class="form-group">
                  {!! Form::label('payment_device_model',__('payment_device.allow_to_print_receipt') . ':*') !!}
                  <select name="settings1[allow_to_print_receipt]" class="form-control select2" required>
                      <option value="">@lang('payment_device.allow_to_print_receipt')</option>
                      <option value="Yes">Yes</option>
                      <option value="No">No</option>
                  </select>
                </div>

                <div class="form-group">
                  {!! Form::label('payment_device_model',__('payment_device.allow_to_customer_tip') . ':*') !!}
                  <select name="settings1[allow_to_customer_tip]" class="form-control select2" id="allow_to_customer_tip" required>
                      <option value="">@lang('payment_device.allow_to_customer_tip')</option>
                      <option value="Yes">Yes</option>
                      <option value="No">No</option>
                  </select>
                </div>

                <div class="customer-tip-wrapper" style="display: none;">
                  <div class="row">
                    <div class="col-md-4 col-sm-4">
                      <div class="form-group">
                          {!! Form::label('tip_option_first', __('payment_device.tip_option_first') . ':*') !!}
                          {!! Form::text('settings1[tip_option_first]', null, ['class' => 'form-control allow-decimal-only',
                          'placeholder' => __('payment_device.tip_option_first_placeholder')]); !!}
                      </div>
                    </div>

                    <div class="col-md-4 col-sm-4">
                      <div class="form-group">
                        {!! Form::label('tip_option_second', __('payment_device.tip_option_second') . ':*') !!}
                        {!! Form::text('settings1[tip_option_second]', null, ['class' => 'form-control allow-decimal-only',
                        'placeholder' => __('payment_device.tip_option_second_placeholder')]); !!}
                      </div>
                    </div>

                    <div class="col-md-4 col-sm-4">
                      <div class="form-group">
                        {!! Form::label('tip_option_third', __('payment_device.tip_option_third') . ':*') !!}
                        {!! Form::text('settings1[tip_option_third]', null, ['class' => 'form-control allow-decimal-only',
                        'placeholder' => __('payment_device.tip_option_third_placeholder')]); !!}
                      </div>
                    </div>
                  </div>
                </div>

            </div>
        </div>
        <div class="col-sm-12">
          <button type="submit" class="btn btn-primary pull-right">@lang('messages.save')</button>
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
    </script>
@endsection