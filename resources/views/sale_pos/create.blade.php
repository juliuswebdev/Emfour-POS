@extends('layouts.app')

@section('title', __('sale.pos_sale'))
@section('content')
<section class="content no-print">
	<input type="hidden" id="amount_rounding_method" value="{{$pos_settings['amount_rounding_method'] ?? ''}}">
	@if(!empty($pos_settings['allow_overselling']))
		<input type="hidden" id="is_overselling_allowed">
	@endif
	@if(session('business.enable_rp') == 1)
        <input type="hidden" id="reward_point_enabled">
    @endif
    @php
		$is_discount_enabled = $pos_settings['disable_discount'] != 1 ? true : false;
		$is_rp_enabled = session('business.enable_rp') == 1 ? true : false;
	@endphp
	{!! Form::open(['url' => action([\App\Http\Controllers\SellPosController::class, 'store']), 'method' => 'post', 'id' => 'add_pos_sell_form' ]) !!}
	<input type="hidden" id="card_charge_percent_hidden" value="{{ $business_details->card_charge }}">
	<div class="row mb-12">
		<div class="col-md-12">
			<div class="row">
				<div class="@if(empty($pos_settings['hide_product_suggestion'])) col-md-7 @else col-md-10 col-md-offset-1 @endif no-padding pr-12">
					<div class="box box-solid mb-12 @if(!isMobile()) mb-40 @endif">
						<div class="box-body pb-0">
							{!! Form::hidden('location_id', $default_location->id ?? null , ['id' => 'location_id', 'data-receipt_printer_type' => !empty($default_location->receipt_printer_type) ? $default_location->receipt_printer_type : 'browser', 'data-default_payment_accounts' => $default_location->default_payment_accounts ?? '']); !!}
							<!-- sub_type -->
							{!! Form::hidden('sub_type', isset($sub_type) ? $sub_type : null) !!}
							<input type="hidden" id="item_addition_method" value="{{$business_details->item_addition_method}}">
							
							
								@include('sale_pos.partials.pos_form')

								@include('sale_pos.partials.pos_form_totals')

								@include('sale_pos.partials.payment_modal')

								@include('payment_device.list_modal')

								@if(empty($pos_settings['disable_suspend']))
									@include('sale_pos.partials.suspend_note_modal')
								@endif

								@if(empty($pos_settings['disable_recurring_invoice']))
									@include('sale_pos.partials.recurring_invoice_modal')
								@endif
							</div>
						</div>
					</div>
				@if(empty($pos_settings['hide_product_suggestion']) && !isMobile())
				<div class="col-md-5 no-padding">
					@include('sale_pos.partials.pos_sidebar')
					@if($business_details->wpc_reservation_site_link)
					<div class="modal fade" id="restaurant_booking_table_modal" tabindex="-1" role="dialog"></div>
					@endif
				</div>
				@endif
			</div>
		</div>
	</div>
	@include('sale_pos.partials.pos_form_actions')

	{!! Form::close() !!}
</section>

<!-- This will be printed -->
<section class="invoice print_section" id="receipt_section">
</section>
<div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
	@include('contact.create', ['quick_add' => true])
</div>
@if(empty($pos_settings['hide_product_suggestion']) && isMobile())
	@include('sale_pos.partials.mobile_product_suggestions')
@endif
<!-- /.content -->
<div class="modal fade register_details_modal" tabindex="-1" role="dialog" 
	aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade close_register_modal" tabindex="-1" role="dialog" 
	aria-labelledby="gridSystemModalLabel">
</div>
<!-- quick product modal -->
<div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>

<div class="modal fade" id="expense_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade" id="weighing_sale_2" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title">Weighing Scale</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="form-group col-sm-12">
					<label for="actual_name">Weight:*</label>
						<input class="form-control" placeholder="Weight" name="weight" type="number" id="weight" autoComplete="off">
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-primary weight_save">Save</button>
			</div>
		</div><!-- /.modal-content -->
	</div>
</div>


@include('sale_pos.partials.configure_search_modal')

@include('sale_pos.partials.recent_transactions_modal')

@include('sale_pos.partials.weighing_scale_modal')

@include('restaurant.orders.checkout-modal')

@if($business_details->business_type_id == 2)		
	<div class="modal fade in" tabindex="-1" role="dialog" id="booking-checkout">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
					<h4 class="modal-title">@lang('lang_v1.checkout_details')</h4>
				</div>
				<div class="modal-body">
					<!-- /.box-header -->
					<div class="box-body">
						<form action="{{ action('\App\Http\Controllers\Restaurant\BookingController@getBookings', $business_details->slug) }}" class="form search-check" method="post">
							<label>Search: </label>
							<div class="row">
								<div class="col-md-10">
									<input type="hidden" name="from" value="checkin">
									<input type="text" placeholder="@lang('lang_v1.search_by_2')" name="search_query" class="form-control">
								</div>
								<div class="col-md-2">
									<button type="submit" class="btn btn-primary btn-search">@lang('lang_v1.search')</button>
								</div>
							</div>
						</form>
						<div id="checkout_result"></div>
					</div>
					<!-- /.box-body --> 
				</div>
			</div>
		</div>
	</div>
@endif




@stop
@section('css')
	<!-- include module css -->
    @if(!empty($pos_module_data))
        @foreach($pos_module_data as $key => $value)
            @if(!empty($value['module_css_path']))
                @includeIf($value['module_css_path'])
            @endif
        @endforeach
    @endif
@stop
@section('javascript')
	<script src="{{ asset('js/auto_height.js?v=' . $asset_v) }}"></script>
	<script src="{{ asset('js/pos.js?v=' . date('yymmddhhiiss')) }}"></script>
	<script src="{{ asset('js/printer.js?v=' . $asset_v) }}"></script>
	<script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
	<script src="{{ asset('js/opening_stock.js?v=' . $asset_v) }}"></script>
	@include('sale_pos.partials.keyboard_shortcuts')

	<!-- Call restaurant module if defined -->
    @if(in_array('tables' ,$enabled_modules) || in_array('modifiers' ,$enabled_modules) || in_array('service_staff' ,$enabled_modules))
    	<script src="{{ asset('js/restaurant.js?v=' . $asset_v) }}"></script>
    @endif
    <!-- include module js -->
    @if(!empty($pos_module_data))
	    @foreach($pos_module_data as $key => $value)
            @if(!empty($value['module_js_path']))
                @includeIf($value['module_js_path'], ['view_data' => $value['view_data']])
            @endif
	    @endforeach
	@endif

	@if(auth()->user()->can('access_payment_devices'))
	<script>
		$(document).ready(function(){
			var user_default_payment_device = {{ auth()->user()->default_payment_device }};
			if(user_default_payment_device == 0) {
				showDeviceList($('#select_location_id').val());
			}
			//call when click to select payment device cog icon.
			$('#open_device_change_modal').click(function(){
				showDeviceList($('#select_location_id').val());
			})

			$('#select_location_id').change(function(){
				showDeviceList($(this).val());
			});

			$(document).on('click', '.btn-select_device', function(){
				var payment_device = $('input[name="payment_device"]:checked').val();
				var device_name = $('label[for="d_'+payment_device+'"]').text();
				$.ajax({
					method: 'POST',
					url: '/modules/set-user-payment-device',
					data : { payment_device },
					success: function(result) {
						toastr.success(result.msg);
						$('#device_label').text(device_name);
						$('#payment_device_modal').modal('hide');
					}
				});
			});
			function showDeviceList(location_id) {
				$.ajax({
					method: 'GET',
					url: '/modules/payment-devices-list/' + location_id,
					success: function(html) {
						if(html == ""){
							toastr.error("Please added payment methods.");
						}else{
							$('.payment_device_list').html(html);
							$('#payment_device_modal').modal('show');
						}
					}
				});
			}

			//Handel Transaction Cancel event Through click button
			$(document).on('click', '#card-payment-close', function(){
				$('.modal-backdrop').remove();
                enable_pos_form_actions();
			})

		})
	</script>
	@endif

	@if($business_details->wpc_reservation_site_link)
	<script>
		$.ajax({
			method: 'GET',
			url: '/bookings/get-table-mapping',
			success: function(result){
				$('#restaurant_booking_table_modal').html(result).modal('show');
			}
		});
	</script>
	@endif
@endsection
