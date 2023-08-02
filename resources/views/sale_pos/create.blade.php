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
	<input type="hidden" name="table_chair_selected" id="table_chair_selected" value="">
	<div class="row mb-12">
		<div class="col-md-12">
			<div class="row">
				<div class="@if(empty($pos_settings['hide_product_suggestion'])) col-md-7 @else col-md-10 col-md-offset-1 @endif no-padding pr-12 mo-padding-10">
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

<div class="modal fade in" tabindex="-1" role="dialog" id="sale-return-modal">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title">@lang('lang_v1.title_sale_return')</h4>
			</div>
			<div class="modal-body">
				<!-- /.box-header -->
				<div class="box-body">
					<form action="{{ action('\App\Http\Controllers\SellPosController@saleReturnPinVerify') }}" class="form" method="post" id="frmSalePinVerify">
						<label>@lang('lang_v1.enter_sale_return_9_digit_pin')*</label>
						<div class="row">
							<div class="col-md-10">
								<input type="password" placeholder="@lang('lang_v1.enter_sale_return_9_digit_pin')" required="required" maxlength="9" minlength="4" name="security_pin" class="form-control">
							</div>
							<div class="col-md-2">
								<button type="submit" class="btn btn-primary btn-verify-pin">@lang('lang_v1.verify')</button>
							</div>
						</div>
					</form>
				</div>
				<!-- /.box-body --> 
			</div>
		</div>
	</div>
</div>


<div class="modal fade in" tabindex="-1" role="dialog" id="sale-return-invoice-modal">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title">@lang('lang_v1.title_sale_return_invoice')</h4>
			</div>
			<div class="modal-body">
				<!-- /.box-header -->
				<div class="box-body">
					<form action="{{ action('\App\Http\Controllers\SellPosController@getSaleReturnInvoice') }}" class="form" method="post" id="frmSaleReturnInvoiceVerify">
						<label>@lang('lang_v1.enter_sale_return_invoice_number')*</label>
						<div class="row">
							<div class="col-md-10">
								<input type="text" placeholder="@lang('lang_v1.enter_sale_return_invoice_number')" required="required" name="sale_return_invoice_number" class="form-control">
							</div>
							<div class="col-md-2">
								<button type="submit" class="btn btn-primary btn-sale-return-invoice">@lang('lang_v1.submit')</button>
							</div>
						</div>
					</form>
				</div>
				<!-- /.box-body --> 
			</div>
		</div>
	</div>
</div>



<div class="modal fade in" tabindex="-1" role="dialog" id="security-pin-modal">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title">@lang('lang_v1.title_security_pin')</h4>
			</div>
			<div class="modal-body">
				<!-- /.box-header -->
				<div class="box-body">
					<form action="{{ action('\App\Http\Controllers\SellPosController@securityPinVerify') }}" class="form" method="post" id="frmSecurityPinVerify">
						<label>@lang('lang_v1.enter_security_pin')*</label>
						<div class="row">
							<div class="col-md-10">
								<input type="password" placeholder="@lang('lang_v1.enter_security_pin')" required="required" maxlength="9" minlength="4" name="security_pin" class="form-control">
							</div>
							<div class="col-md-2">
								<button type="submit" class="btn btn-primary btn-verify-security-pin">@lang('lang_v1.verify')</button>
							</div>
						</div>
					</form>
				</div>
				<!-- /.box-body --> 
			</div>
		</div>
	</div>
</div>



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

			/*
			$('#select_location_id').change(function(){
				showDeviceList($(this).val());
			});
			*/
			
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

			//Sale Return click event
			$(document).on('click', '.sale-return', function(){
				$('#sale-return-modal').modal('show');
			})

			//Verify Sale Return to Server Side
			$("#frmSalePinVerify").validate({
				errorElement: 'span',
				errorPlacement: function (error, element) {
					error.addClass('invalid-feedback');
					element.closest('.form-group').append(error);
					if (element.hasClass('select2')) {
						error.insertAfter(element.parent().find('span.select2'));
					} else if (element.parent('.input-group').length ||
						element.prop('type') === 'checkbox' || element.prop('type') === 'radio') {
						error.insertAfter(element.parent());
					} else {
						error.insertAfter(element);
					}
				},
				highlight: function (element, errorClass, validClass) {
					$(element).addClass('is-invalid').removeClass('is-valid');
				},
				unhighlight: function (element, errorClass, validClass) {
					$(element).removeClass('is-invalid').addClass('is-valid');
				},
				focusInvalid: true,
				submitHandler: function (form) {
					$.ajax({
						url: $('#frmSalePinVerify').attr('action'),
						type: "POST",
						headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
						data: new FormData(form),
						contentType: false,
						cache: false,
						processData: false,
						success: function (res) {
							if(res.status == true){
								$('#sale-return-modal').modal('hide');
								$('#frmSalePinVerify')[0].reset();
								$('#sale-return-invoice-modal').modal('show');
								toastr.success(res.message);	
							}else{
								toastr.error(res.message);	
							}
						},
					});
				}
			});

			//Check Invoice Number is Exit or Not & Return the Redirect URL
			$("#frmSaleReturnInvoiceVerify").validate({
				errorElement: 'span',
				errorPlacement: function (error, element) {
					error.addClass('invalid-feedback');
					element.closest('.form-group').append(error);
					if (element.hasClass('select2')) {
						error.insertAfter(element.parent().find('span.select2'));
					} else if (element.parent('.input-group').length ||
						element.prop('type') === 'checkbox' || element.prop('type') === 'radio') {
						error.insertAfter(element.parent());
					} else {
						error.insertAfter(element);
					}
				},
				highlight: function (element, errorClass, validClass) {
					$(element).addClass('is-invalid').removeClass('is-valid');
				},
				unhighlight: function (element, errorClass, validClass) {
					$(element).removeClass('is-invalid').addClass('is-valid');
				},
				focusInvalid: true,
				submitHandler: function (form) {
					$.ajax({
						url: $('#frmSaleReturnInvoiceVerify').attr('action'),
						type: "POST",
						headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
						data: new FormData(form),
						contentType: false,
						cache: false,
						processData: false,
						success: function (res) {
							if(res.status == true){
								$('#sale-return-invoice-modal').modal('hide');
								$('#frmSaleReturnInvoiceVerify')[0].reset();
								//console.log(res.data.redirect_url);
								window.location.href = res.data.redirect_url;
							}else{
								toastr.error(res.message);	
							}
						},
					});
				}
			});


			//Validate security pin for add expance
			$("#frmSecurityPinVerify").validate({
				errorElement: 'span',
				errorPlacement: function (error, element) {
					error.addClass('invalid-feedback');
					element.closest('.form-group').append(error);
					if (element.hasClass('select2')) {
						error.insertAfter(element.parent().find('span.select2'));
					} else if (element.parent('.input-group').length ||
						element.prop('type') === 'checkbox' || element.prop('type') === 'radio') {
						error.insertAfter(element.parent());
					} else {
						error.insertAfter(element);
					}
				},
				highlight: function (element, errorClass, validClass) {
					$(element).addClass('is-invalid').removeClass('is-valid');
				},
				unhighlight: function (element, errorClass, validClass) {
					$(element).removeClass('is-invalid').addClass('is-valid');
				},
				focusInvalid: true,
				submitHandler: function (form) {

					$.ajax({
						url: $('#frmSecurityPinVerify').attr('action'),
						type: "POST",
						headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
						data: new FormData(form),
						contentType: false,
						cache: false,
						processData: false,
						success: function (res) {
							if(res.status == true){
								$('#security-pin-modal').modal('hide');
								$('#frmSecurityPinVerify')[0].reset();
								toastr.success(res.message);	
								//Call the ajax for open add expance popup.
								$.ajax({
									url: '/expenses/create',
									data: { 
										location_id: $('#select_location_id').val()
									},
									dataType: 'html',
									success: function(result) {
										$('#expense_modal').html(result);
										$('#expense_modal').modal('show');
									},
								});
							}else{
								toastr.error(res.message);	
							}
						},
					});
				}
			});

			

		})
	</script>
	@endif

	@if($__is_table_mapping_enabled)
		<div class="modal fade" id="restaurant_booking_table_modal" tabindex="-1" role="dialog"></div>
		<style>
			select[name="res_table_id"] option {
				display: none;
			}
			.table-chair-btn {
				cursor: pointer;
			}
			.table-chair-btn.active {
				background-color: green!important;
			}
			#restaurant_booking_table_modal .close {
				position: relative;
				z-index: 99;
				top: -60px;
				color: #000;
				opacity: 1;
				font-size: 50px;
			}
		</style>
		<script>
			$.ajax({
				method: 'GET',
				url: '/bookings/get-table-mapping?location_id=' + $('#select_location_id').val(),
				success: function(result){
					$('#restaurant_booking_table_modal').html(result);
				}
			});
			$(document).on('click', 'select[name="res_table_id"]', function(e) {
				e.preventDefault();
				$.ajax({
				method: 'GET',
				url: '/bookings/get-table-chair-selected?location_id=' + $('#select_location_id').val(),
					success: function(result){
						$('.table-chair-btn').removeClass('active locked');
						$('.table-chair-btn').each(function(){
							var id = $(this).attr('id');
							if(result.includes(id)) {
								$(this).addClass('active locked');
							}
						});
					}
				});

				$('#restaurant_booking_table_modal').modal('show');
			});
			
			var this_click = '';
			var counter = 0;
			$(document).on('click', '.table-chair-btn:not(.locked)', function(e) {
				e.preventDefault();
				
		
				//$(this).toggleClass('active');
			
				if(!$(this).hasClass('chair')) {
					$('.table.active:not(.locked)').removeClass('active');
					if($(this).attr('data-table-chair-id') == this_click && !counter) {
						counter = 1;
						$(this).removeClass('active');
					} else {
						counter = 0;
						$(this).addClass('active');
					}
				} else {
					$(this).toggleClass('active');
				}

				if($(this).attr('data-type') == 'table') {
					this_click = $(this).attr('data-table-chair-id');
				}


				var table_chair_selected = [];
				var table_id = 0;

				$('.table-chair-btn.active:not(.locked)').each(function(){
					var id = $(this).attr('id');
					table_id = $(this).attr('data-table-chair-id');
					table_chair_selected.push(id);
				});

				$('#table_chair_selected').val(JSON.stringify(table_chair_selected));

				var res_table_id = 0;
				$('select[name="res_table_id"] option').each(function(){
					var text = $(this).text();
					if(text == table_id) {
						res_table_id = $(this).attr('value');
					}
				
				});
				if(res_table_id) {
					$('select[name="res_table_id"]').val(res_table_id);
				} else {
					$('select[name="res_table_id"]').val('');
					$('.table.active:not(.locked)').removeClass('active');
				}
			
			});

		</script>
	@endif
@endsection
