<!-- default value -->
@php
    $go_back_url = action([\App\Http\Controllers\SellPosController::class, 'index']);
    $transaction_sub_type = '';
    $view_suspended_sell_url = action([\App\Http\Controllers\SellController::class, 'index']).'?suspended=1';
    $pos_redirect_url = action([\App\Http\Controllers\SellPosController::class, 'create']);
@endphp

@if(!empty($pos_module_data))
    @foreach($pos_module_data as $key => $value)
        @php
            if(!empty($value['go_back_url'])) {
                $go_back_url = $value['go_back_url'];
            }

            if(!empty($value['transaction_sub_type'])) {
                $transaction_sub_type = $value['transaction_sub_type'];
                $view_suspended_sell_url .= '&transaction_sub_type='.$transaction_sub_type;
                $pos_redirect_url .= '?sub_type='.$transaction_sub_type;
            }
        @endphp
    @endforeach
@endif
<input type="hidden" name="transaction_sub_type" id="transaction_sub_type" value="{{$transaction_sub_type}}">
@inject('request', 'Illuminate\Http\Request')
<div class="col-md-12 no-print pos-header">
  <input type="hidden" id="pos_redirect_url" value="{{$pos_redirect_url}}">
  <div class="row">

    <div class="col-md-7">
      <div class="m-6 mt-5 pos-time-location-register-number" style="display: flex;">
        
        <p ><strong>@lang('business.register_number'): {{  $register_number }} &nbsp;&nbsp;</strong> 
        <p ><strong>@lang('sale.location'): &nbsp;</strong> 
          @if(empty($transaction->location_id))
            @if(count($business_locations) > 1)
            <div style="width: 20;margin-bottom: 5px;">
               {!! Form::select('select_location_id', $business_locations, $default_location->id ?? null , ['class' => 'form-control input-sm',
                'id' => 'select_location_id', 
                'required', 'autofocus'], $bl_attributes); !!}
            </div>
            @else
              {{$default_location->name}}
              <input type="hidden" id="select_location_id" value="{{ $default_location->id }}">
            @endif
          @endif

          @if(!empty($transaction->location_id)) <span id="location_id_code">{{$transaction->location->name}} ({{ $transaction->location->location_id }})</span> <input type="hidden" id="select_location_id" value="{{ $transaction->location->id }}"> @endif &nbsp; <span class="curr_datetime">{{ @format_datetime('now') }}</span> <i class="fa fa-keyboard hover-q text-muted" aria-hidden="true" data-container="body" data-toggle="popover" data-placement="bottom" data-content="@include('sale_pos.partials.keyboard_shortcuts_details')" data-html="true" data-trigger="hover" data-original-title="" title=""></i>
        </p>
        @if($__is_essentials_enabled && $is_employee_allowed)

        <div class="clock-inout-btn-wrapper">
          <button class="btn bg-info btn-flat clockinout">@lang( 'lang_v1.btn_label_clockin_clockout' )</button>
        </div>

        {{--
          <button 
            type="button" 
            class="btn bg-green btn-flat 
            pull-left m-8 btn-sm mt-0
            clock_in_btn
            @if(!empty($clock_in))
                disabled
              @endif
            "
            @if(!empty($clock_in))
                disabled
              @endif
              data-type="clock_in"
              data-toggle="tooltip"
              data-placement="bottom"
              title="@lang('essentials::lang.clock_in')" >
            <i class="fas fa-arrow-circle-down"></i>&nbsp;&nbsp;@lang('essentials::lang.clock_in')
          </button>

          <button 
            type="button" 
            class="btn bg-yellow btn-flat pull-left m-8 
            btn-sm mt-0 clock_out_btn
            @if(empty($clock_in))
                disabled
              @endif
            " 
            @if(empty($clock_in))
                disabled
              @endif	
              data-type="clock_out"
              data-toggle="tooltip"
              data-placement="bottom"
              data-html="true"
              title="@lang('essentials::lang.clock_out') @if(!empty($clock_in))
                            <br>
                            <small>
                              <b>@lang('essentials::lang.clocked_in_at'):</b> {{@format_datetime($clock_in->clock_in_time)}}
                            </small>
                            <br>
                            <small><b>@lang('essentials::lang.shift'):</b> {{ucfirst($clock_in->shift_name)}}</small>
                            @if(!empty($clock_in->start_time) && !empty($clock_in->end_time))
                              <br>
                              <small>
                                <b>@lang('restaurant.start_time'):</b> {{@format_time($clock_in->start_time)}}<br>
                                <b>@lang('restaurant.end_time'):</b> {{@format_time($clock_in->end_time)}}
                              </small>
                            @endif
                        @endif" 
              >
              <i class="fas fa-hourglass-half"></i>&nbsp;&nbsp;@lang('essentials::lang.clock_out')
          </button>
        --}}

        @endif
      </div>





    </div>

    <div class="col-md-5">

      {{--
      <a href="{{$go_back_url}}" title="{{ __('lang_v1.go_back') }}" class="btn btn-info btn-flat m-6 btn-xs m-5 pull-right">
        <strong><i class="fa fa-backward fa-lg"></i></strong>
      </a>
      --}}


      @if(!empty($pos_settings['inline_service_staff']))
        <button type="button" id="show_service_staff_availability" title="{{ __('lang_v1.service_staff_availability') }}" class="btn btn-primary btn-flat m-6 btn-xs m-5 pull-right" data-container=".view_modal" 
          data-href="{{ action([\App\Http\Controllers\SellPosController::class, 'showServiceStaffAvailibility'])}}">
            <strong><i class="fa fa-users fa-lg"></i></strong>
        </button>
      @endif
      
      @can('close_cash_register')
      <button type="button" id="close_register" title="{{ __('cash_register.close_register') }}" class="btn btn-danger btn-flat m-6 btn-xs m-5 btn-modal pull-right" data-container=".close_register_modal" 
          data-href="{{ action([\App\Http\Controllers\CashRegisterController::class, 'getCloseRegister'])}}">
            <strong><i class="fa fa-window-close fa-lg"></i></strong>
      </button>
      @endcan
      
      @can('view_cash_register')
      <button type="button" id="register_details" title="{{ __('cash_register.register_details') }}" class="btn btn-success btn-flat m-6 btn-xs m-5 btn-modal pull-right mo-btn-hide" data-container=".register_details_modal" 
          data-href="{{ action([\App\Http\Controllers\CashRegisterController::class, 'getRegisterDetails'])}}">
            <strong><i class="fa fa-briefcase fa-lg" aria-hidden="true"></i></strong>
      </button>
      @endcan

      <button title="@lang('lang_v1.calculator')" id="btnCalculator" type="button" class="btn btn-success btn-flat pull-right m-5 btn-xs mt-10 popover-default mo-btn-hide" data-toggle="popover" data-trigger="click" data-content='@include("layouts.partials.calculator")' data-html="true" data-placement="bottom">
            <strong><i class="fa fa-calculator fa-lg" aria-hidden="true"></i></strong>
      </button>

      {{--
      <button type="button" class="btn btn-danger btn-flat m-6 btn-xs m-5 pull-right popover-default" id="return_sale" title="@lang('lang_v1.sell_return')" data-toggle="popover" data-trigger="click" data-content='<div class="m-8"><input type="text" class="form-control" placeholder="@lang("sale.invoice_no")" id="send_for_sell_return_invoice_no"></div><div class="w-100 text-center"><button type="button" class="btn btn-danger" id="send_for_sell_return">@lang("lang_v1.send")</button></div>' data-html="true" data-placement="bottom">
            <strong><i class="fas fa-undo fa-lg"></i></strong>
      </button>
      --}}

      <button type="button" title="{{ __('lang_v1.full_screen') }}" class="btn btn-primary btn-flat m-6 hidden-xs btn-xs m-5 pull-right" id="full_screen">
            <strong><i class="fa fa-window-maximize fa-lg"></i></strong>
      </button>

      <button type="button" id="view_suspended_sales" title="{{ __('lang_v1.view_suspended_sales') }}" class="btn bg-yellow btn-flat m-6 btn-xs m-5 btn-modal pull-right mo-btn-hide" data-container=".view_modal" 
          data-href="{{$view_suspended_sell_url}}">
            <strong><i class="fa fa-pause-circle fa-lg"></i></strong>
      </button>
      @if(empty($pos_settings['hide_product_suggestion']) && isMobile())
        <button type="button" title="{{ __('lang_v1.view_products') }}"   
          data-placement="bottom" class="btn btn-success btn-flat m-6 btn-xs m-5 btn-modal pull-right mo-btn-hide" data-toggle="modal" data-target="#mobile_product_suggestion_modal">
            <strong><i class="fa fa-cubes fa-lg"></i></strong>
        </button>
      @endif

      @if(Module::has('Repair') && $transaction_sub_type != 'repair')
        @include('repair::layouts.partials.pos_header')
      @endif

        @if(in_array('pos_sale', $enabled_modules) && !empty($transaction_sub_type))
          @can('sell.create')
            <a href="{{action([\App\Http\Controllers\SellPosController::class, 'create'])}}" title="@lang('sale.pos_sale')" class="btn btn-success btn-flat m-6 btn-xs m-5 pull-right">
              <strong><i class="fa fa-th-large"></i> &nbsp; @lang('sale.pos_sale')</strong>
            </a>
          @endcan
        @endif
        @can('expense.add')
        @php
          $is_open_security_pin_on_expance_btn_click = false;
        @endphp
        @isset($pos_settings['enable_pin_protection_on_expense_button'])
            @if($pos_settings['enable_pin_protection_on_expense_button'] == 1)
              @php
                $is_open_security_pin_on_expance_btn_click = true;
              @endphp
            @else
              @php
                $is_open_security_pin_on_expance_btn_click = false;
              @endphp
            @endif
        @endisset
        <button type="button" title="{{ __('expense.add_expense') }}"   
          data-placement="bottom" class="btn bg-purple btn-flat m-6 btn-xs m-5 btn-modal pull-right" id="{{ ($is_open_security_pin_on_expance_btn_click) ? 'open_security_pin_modal': 'add_expense' }}">
            <strong><i class="fa fas fa-minus-circle"></i> @lang('expense.add_expense')</strong>
        </button>
        @endcan

        <button id="open_device_change_modal" type="button" class="btn btn-success btn-flat m-6 btn-xs m-5 btn-modal pull-right">
          <span id="device_label">{{ ($payment_device == null) ? __('lang_v1.select_payment_device') : $payment_device->name }}</span> <strong><i class="fa fa-cog fa-lg"></i></strong>
        </button>

        <a 
          class="btn btn-success btn-flat m-6 btn-xs m-5 btn-modal pull-right mo-btn-hide" 
          href="#" 
          onClick="window.open('{{action([\App\Http\Controllers\SellPosController::class, 'getPosPublic'])}}','winname','directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=1000');">
          <strong><i class="fa fa-tv fa-lg"></i></strong>
        </a>

    </div>
    
  </div>
</div>
