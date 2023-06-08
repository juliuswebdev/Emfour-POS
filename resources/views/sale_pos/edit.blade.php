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


	{!! Form::open(['url' => action([\App\Http\Controllers\SellPosController::class, 'update'], [$transaction->id]), 'method' => 'post', 'id' => 'edit_pos_sell_form' ]) !!}
	{{ method_field('PUT') }}


	<div class="row mb-12">
		<div class="col-md-12">
			<div class="row">
				<div class="@if(empty($pos_settings['hide_product_suggestion'])) col-md-7 @else col-md-10 col-md-offset-1 @endif no-padding pr-12">
					<div class="box box-solid mb-12 @if(!isMobile()) mb-40 @endif">
						<div class="box-body pb-0">
							{!! Form::hidden('location_id', $transaction->location_id, ['id' => 'location_id', 'data-receipt_printer_type' => !empty($location_printer_type) ? $location_printer_type : 'browser', 'data-default_payment_accounts' => $transaction->location->default_payment_accounts]); !!}
							<!-- sub_type -->
							{!! Form::hidden('sub_type', isset($sub_type) ? $sub_type : null) !!}
							<input type="hidden" id="item_addition_method" value="{{$business_details->item_addition_method}}">
								@include('sale_pos.partials.pos_form_edit')

								@include('sale_pos.partials.pos_form_totals', ['edit' => true])

								@include('sale_pos.partials.payment_modal')

								@if(empty($pos_settings['disable_suspend']))
									@include('sale_pos.partials.suspend_note_modal')
								@endif

								@if(empty($pos_settings['disable_recurring_invoice']))
									@include('sale_pos.partials.recurring_invoice_modal')
								@endif
							</div>
							@if(!empty($only_payment))
								<div class="overlay"></div>
							@endif
						</div>
					</div>
				@if(empty($pos_settings['hide_product_suggestion'])  && !isMobile() && empty($only_payment))
					<div class="col-md-5 no-padding">
						@include('sale_pos.partials.pos_sidebar')
					</div>
				@endif
			</div>
		</div>
	</div>

	@if($transaction->payment_status == 'paid')
		<div class="overlay2">
			<span>
				@lang('lang_v1.transaction_paid')<br>
				<small><a href="{{ action([\App\Http\Controllers\SellPosController::class, 'create']) }}">@lang('lang_v1.create_transaction')</a></small>
			</span>
		</div>
	@endif

	@include('sale_pos.partials.pos_form_actions', ['edit' => true])

	
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

@include('sale_pos.partials.configure_search_modal')

@include('sale_pos.partials.recent_transactions_modal')

@include('sale_pos.partials.weighing_scale_modal')

@include('restaurant.orders.checkout-modal')

@stop

@section('javascript')
	<script src="{{ asset('js/pos.js?v=' . $asset_v) }}"></script>
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
	
	<!-- Sale Return -->
	@if(request()->get('sale-return') == 1)
	<script>
		@if($transaction->payment_status == 'paid')
		__page_leave_confirmation('#edit_pos_sell_form');
		@endif
		$(document).ready(function(){
			
			//Hide the html component on sale return
			$('#product_brand_div').hide();
			$('#product_category_div').hide();
			$('#product_list_body').hide();
			$('#recent-transactions').hide();
			$('#suggestion_page_loader').hide();
			$('.wrapper-of-add-action').hide();
			$('.wrapper-of-sale-return').show();
			$('#customer_id').attr('disabled', 'disabled');
			$('#search_product').attr('readonly', 'readonly');
			$('select[name="res_table_id"]').attr('disabled', 'disabled');
			$('#res_waiter_id').attr('disabled', 'disabled');
			$('#is_recurring').attr('disabled', 'disabled');
			$('.pos-total').find('span.text').text('Total Return');
			$('.pos_form_totals').hide();
			$('#pos_table thead tr th:eq(1)').text('Return Quantity');

			//confirm button event in sale return
			$('.sale-retun-confirm').click(function(){
				$(this).hide();
				$('.btn-payment-sale-return').show();
			});

			//sale return refund event
			$('.btn-payment-sale-return').click(function(){
				var sale_return_action = "{{ url('sale-return/'.$transaction->id.'/invoice') }}"
				var form = document.getElementById("edit_pos_sell_form");
				var data = new FormData(form)
				var sale_return_method = $(this).attr('data-payment-type');
				var processing_text = "<span class='card-payment-popup'><div>Processing of Return..</div><div><button id='card-payment-close' class='btn-danger'>Close</button></div></span>"
				var bg_black_fade_in = '<div class="modal-backdrop fade in">'+processing_text+'</div>';
				$('.ui-helper-hidden-accessible').after(bg_black_fade_in);

				data.append('sale_return_via', sale_return_method);
				$.ajax({
					url: sale_return_action,
					type: "POST",
					headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
					data: data,
					contentType: false,
					cache: false,
					processData: false,
					success: function (result) {
						$('.modal-backdrop').remove();
						if (result.success == 1) {
                            toastr.success(result.msg);
                            //Check if enabled or not
                            if (result.receipt.is_enabled) {
                                pos_print(result.receipt);
                            }
							reset_pos_form();
                        } else {
							toastr.error(result.msg);
                        }
					},
				});
			});

			//Close the card payment on return
			$(document).on('click', '#card-payment-close', function(){
				$('.modal-backdrop').remove();
			});

			$(document).on('click', '.overlay2 a', function(){
				window.onbeforeunload = null;
				$(window).off('beforeunload');
			});
			
			

			//Input qty validation in sale return screen
			$('.input_quantity').on('input', function(){
				var max_qty = $(this).attr('data-max');
				max_qty = parseFloat(max_qty).toFixed(2);
				var input_qty = $(this).val();
				input_qty = parseFloat(input_qty).toFixed(2);
				if(max_qty < input_qty){
					$(this).val(max_qty);
				}
			});

			//Set max qty in sale return screen
			function set_max_quantity(){
				$('#pos_table > tbody > tr').each(function(index, tr) { 
					var max_qty = $(this).find('.input_quantity').attr('value');
					$(this).find('.input_quantity').attr('data-max', max_qty);
				});
			}
			
			set_max_quantity();

		});
	</script>
	@endif


@endsection

@section('css')
	<style type="text/css">
		/*CSS to print receipts*/
		.print_section{
		    display: none;
		}
		@media print{
		    .print_section{
		        display: block !important;
		    }
		}
		@page {
		    size: 3.1in auto;/* width height */
		    height: auto !important;
		    margin-top: 0mm;
		    margin-bottom: 0mm;
		}
		.overlay {
			background: rgba(255,255,255,0) !important;
			cursor: not-allowed;
		}
		.overlay2 {
			position: fixed;
			background: rgba(0,0,0,.3)!important;
			cursor: not-allowed;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			z-index: 9999;
			width: 100%;
			height: 100%;
		}
		.overlay2 span {
			display: block;
			position: absolute;
			text-align: center;
			top: 50%;
			transform: translateY(-50%);
			color: #fff;
			font-size: 200%;
			width: 100%;
		}
	</style>
	<!-- include module css -->
    @if(!empty($pos_module_data))
        @foreach($pos_module_data as $key => $value)
            @if(!empty($value['module_css_path']))
                @includeIf($value['module_css_path'])
            @endif
        @endforeach
    @endif
@endsection