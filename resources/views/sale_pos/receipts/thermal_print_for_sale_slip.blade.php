<!-- business information here -->
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <!-- <link rel="stylesheet" href="style.css"> -->
        <title>Receipt-{{$receipt_details->invoice_no}}</title>
    </head>
    <body>
        <div class="ticket">
			@if(empty($receipt_details->letter_head))
				@if(!empty($receipt_details->logo))
					<div class="text-box centered">
						<img style="max-height: 100px; width: auto;" src="{{$receipt_details->logo}}" alt="Logo">
					</div>
				@endif
				<div class="text-box">
				<!-- Logo -->
				<p class="centered">
					<!-- Header text -->
					@if(!empty($receipt_details->header_text))
						<span class="headings">{!! $receipt_details->header_text !!}</span>
						<br/>
					@endif
					<!-- business information here -->
					@if(!empty($receipt_details->display_name))
						<span class="headings">
							{{$receipt_details->display_name}}
						</span>
						<br/>
					@endif
					
					@if(!empty($receipt_details->address))
						{!! $receipt_details->address !!}
						<br/>
					@endif

					@if(!empty($receipt_details->contact))
						{!! $receipt_details->contact !!}
					@endif
					@if(!empty($receipt_details->contact) && !empty($receipt_details->website))
						, 
					@endif
					@if(!empty($receipt_details->website))
						{{ $receipt_details->website }}
					@endif
					@if(!empty($receipt_details->location_custom_fields))
						<br>{{ $receipt_details->location_custom_fields }}
					@endif

					@if(!empty($receipt_details->sub_heading_line1))
						{{ $receipt_details->sub_heading_line1 }}<br/>
					@endif
					@if(!empty($receipt_details->sub_heading_line2))
						{{ $receipt_details->sub_heading_line2 }}<br/>
					@endif
					@if(!empty($receipt_details->sub_heading_line3))
						{{ $receipt_details->sub_heading_line3 }}<br/>
					@endif
					@if(!empty($receipt_details->sub_heading_line4))
						{{ $receipt_details->sub_heading_line4 }}<br/>
					@endif		
					@if(!empty($receipt_details->sub_heading_line5))
						{{ $receipt_details->sub_heading_line5 }}<br/>
					@endif

					@if(!empty($receipt_details->tax_info1))
						<br><b>{{ $receipt_details->tax_label1 }}</b> {{ $receipt_details->tax_info1 }}
					@endif

					@if(!empty($receipt_details->tax_info2))
						<b>{{ $receipt_details->tax_label2 }}</b> {{ $receipt_details->tax_info2 }}
					@endif

					<!-- Title of receipt -->
					@if(!empty($receipt_details->invoice_heading))
					    <div class="centered">**************************************************</div>
                    @endif
				</p>
				</div>
			@else
				<div class="text-box">
					<img style="width: 100%;margin-bottom: 10px;" src="{{$receipt_details->letter_head}}">
				</div>
			@endif
			
			@isset($receipt_details->register_number)
			<div class="textbox-info">
				<p class="f-left"><strong>@lang('business.register_number')</strong></p>
				<p class="f-right">
					{{$receipt_details->register_number}}
				</p>
			</div>
			@endisset

			<div class="textbox-info">
				<p class="f-left"><strong>{!! $receipt_details->invoice_no_prefix !!}</strong></p>
				<p class="f-right">
					{{$receipt_details->invoice_no}}
				</p>
			</div>
			<div class="textbox-info">
				<p class="f-left"><strong>{!! $receipt_details->date_label !!}</strong></p>
				<p class="f-right">
					{{$receipt_details->invoice_date}}
				</p>
			</div>
			
			@if(!empty($receipt_details->due_date_label))
				<div class="textbox-info">
					<p class="f-left"><strong>{{$receipt_details->due_date_label}}</strong></p>
					<p class="f-right">{{$receipt_details->due_date ?? ''}}</p>
				</div>
			@endif

			@if(!empty($receipt_details->sales_person_label))
				<div class="textbox-info">
					<p class="f-left"><strong>{{$receipt_details->sales_person_label}}</strong></p>
				
					<p class="f-right">{{$receipt_details->sales_person}}</p>
				</div>
			@endif
			@if(!empty($receipt_details->commission_agent_label))
				<div class="textbox-info">
					<p class="f-left"><strong>{{$receipt_details->commission_agent_label}}</strong></p>
				
					<p class="f-right">{{$receipt_details->commission_agent}}</p>
				</div>
			@endif

			@if(!empty($receipt_details->brand_label) || !empty($receipt_details->repair_brand))
				<div class="textbox-info">
					<p class="f-left"><strong>{{$receipt_details->brand_label}}</strong></p>
				
					<p class="f-right">{{$receipt_details->repair_brand}}</p>
				</div>
			@endif

			@if(!empty($receipt_details->device_label) || !empty($receipt_details->repair_device))
				<div class="textbox-info">
					<p class="f-left"><strong>{{$receipt_details->device_label}}</strong></p>
				
					<p class="f-right">{{$receipt_details->repair_device}}</p>
				</div>
			@endif
			
			@if(!empty($receipt_details->model_no_label) || !empty($receipt_details->repair_model_no))
				<div class="textbox-info">
					<p class="f-left"><strong>{{$receipt_details->model_no_label}}</strong></p>
				
					<p class="f-right">{{$receipt_details->repair_model_no}}</p>
				</div>
			@endif
			
			@if(!empty($receipt_details->serial_no_label) || !empty($receipt_details->repair_serial_no))
				<div class="textbox-info">
					<p class="f-left"><strong>{{$receipt_details->serial_no_label}}</strong></p>
				
					<p class="f-right">{{$receipt_details->repair_serial_no}}</p>
				</div>
			@endif

			@if(!empty($receipt_details->repair_status_label) || !empty($receipt_details->repair_status))
				<div class="textbox-info">
					<p class="f-left"><strong>
						{!! $receipt_details->repair_status_label !!}
					</strong></p>
					<p class="f-right">
						{{$receipt_details->repair_status}}
					</p>
				</div>
        	@endif

        	@if(!empty($receipt_details->repair_warranty_label) || !empty($receipt_details->repair_warranty))
	        	<div class="textbox-info">
	        		<p class="f-left"><strong>
	        			{!! $receipt_details->repair_warranty_label !!}
	        		</strong></p>
	        		<p class="f-right">
	        			{{$receipt_details->repair_warranty}}
	        		</p>
	        	</div>
        	@endif

        	<!-- Waiter info -->
			@if(!empty($receipt_details->service_staff_label) || !empty($receipt_details->service_staff))
	        	<div class="textbox-info">
	        		<p class="f-left"><strong>
	        			{!! $receipt_details->service_staff_label !!}
	        		</strong></p>
	        		<p class="f-right">
	        			{{$receipt_details->service_staff}}
					</p>
	        	</div>
	        @endif

	        @if(!empty($receipt_details->table_label) || !empty($receipt_details->table))
	        	<div class="textbox-info">
	        		<p class="f-left"><strong>
	        			@if(!empty($receipt_details->table_label))
							<b>{!! $receipt_details->table_label !!}</b>
						@endif
	        		</strong></p>
	        		<p class="f-right">
	        			{{$receipt_details->table}}
	        		</p>
	        	</div>
	        @endif

	        <!-- customer info -->
	        <div class="textbox-info">
	        	<p style="vertical-align: top;"><strong>
	        		{{$receipt_details->customer_label ?? ''}}
	        	</strong></p>

	        	<p>
	        		@if(!empty($receipt_details->customer_info))
	        			<div class="bw">
						{!! $receipt_details->customer_info !!}
						</div>
					@endif
	        	</p>
	        </div>
			
			@if(!empty($receipt_details->client_id_label))
				<div class="textbox-info">
					<p class="f-left"><strong>
						{{ $receipt_details->client_id_label }}
					</strong></p>
					<p class="f-right">
						{{ $receipt_details->client_id }}
					</p>
				</div>
			@endif
			
			@if(!empty($receipt_details->customer_tax_label))
				<div class="textbox-info">
					<p class="f-left"><strong>
						{{ $receipt_details->customer_tax_label }}
					</strong></p>
					<p class="f-right">
						{{ $receipt_details->customer_tax_number }}
					</p>
				</div>
			@endif

			@if(!empty($receipt_details->customer_custom_fields))
				<div class="textbox-info">
					<p class="centered">
						{!! $receipt_details->customer_custom_fields !!}
					</p>
				</div>
			@endif
			
			@if(!empty($receipt_details->customer_rp_label))
				<div class="textbox-info">
					<p class="f-left"><strong>
						{{ $receipt_details->customer_rp_label }}
					</strong></p>
					<p class="f-right">
						{{ $receipt_details->customer_total_rp }}
					</p>
				</div>
			@endif
			@if(!empty($receipt_details->shipping_custom_field_1_label))
				<div class="textbox-info">
					<p class="f-left"><strong>
						{!!$receipt_details->shipping_custom_field_1_label!!} 
					</strong></p>
					<p class="f-right">
						{!!$receipt_details->shipping_custom_field_1_value ?? ''!!}
					</p>
				</div>
			@endif
			@if(!empty($receipt_details->shipping_custom_field_2_label))
				<div class="textbox-info">
					<p class="f-left"><strong>
						{!!$receipt_details->shipping_custom_field_2_label!!} 
					</strong></p>
					<p class="f-right">
						{!!$receipt_details->shipping_custom_field_2_value ?? ''!!}
					</p>
				</div>
			@endif
			@if(!empty($receipt_details->shipping_custom_field_3_label))
				<div class="textbox-info">
					<p class="f-left"><strong>
						{!!$receipt_details->shipping_custom_field_3_label!!} 
					</strong></p>
					<p class="f-right">
						{!!$receipt_details->shipping_custom_field_3_value ?? ''!!}
					</p>
				</div>
			@endif
			@if(!empty($receipt_details->shipping_custom_field_4_label))
				<div class="textbox-info">
					<p class="f-left"><strong>
						{!!$receipt_details->shipping_custom_field_4_label!!} 
					</strong></p>
					<p class="f-right">
						{!!$receipt_details->shipping_custom_field_4_value ?? ''!!}
					</p>
				</div>
			@endif
			@if(!empty($receipt_details->shipping_custom_field_5_label))
				<div class="textbox-info">
					<p class="f-left"><strong>
						{!!$receipt_details->shipping_custom_field_5_label!!} 
					</strong></p>
					<p class="f-right">
						{!!$receipt_details->shipping_custom_field_5_value ?? ''!!}
					</p>
				</div>
			@endif
			@if(!empty($receipt_details->sale_orders_invoice_no))
				<div class="textbox-info">
					<p class="f-left"><strong>
						@lang('restaurant.order_no')
					</strong></p>
					<p class="f-right">
						{!!$receipt_details->sale_orders_invoice_no ?? ''!!}
					</p>
				</div>
			@endif

			@if(!empty($receipt_details->sale_orders_invoice_date))
				<div class="textbox-info">
					<p class="f-left"><strong>
						@lang('lang_v1.order_dates')
					</strong></p>
					<p class="f-right">
						{!!$receipt_details->sale_orders_invoice_date ?? ''!!}
					</p>
				</div>
			@endif
            <table style="margin-top: 10px !important" class="border-bottom width-100 table-f-12 mb-10">
                <thead class="border-bottom-dotted">
                    <tr>
                        <th class="serial_number">#</th>
                        <th class="description" width="40%">
                        	{{$receipt_details->table_product_label}}<br>
                        </th>
                        @if(empty($receipt_details->hide_price))
                        <th class="unit_price text-right" width="30%">
                        	{{$receipt_details->table_unit_price_label}}
                        </th>
                        @if(!empty($receipt_details->discounted_unit_price_label))
							<th class="text-right" width="30%">
								{{$receipt_details->discounted_unit_price_label}}
							</th>
						@endif
                        @if(!empty($receipt_details->item_discount_label))
							<th class="text-right">{{$receipt_details->item_discount_label}}</th>
						@endif
                        <th class="price text-right"  width="30%">{{$receipt_details->table_subtotal_label}}</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                	@forelse($receipt_details->lines as $line)
	                    <tr>
	                        <td class="serial_number" style="vertical-align: top;">
	                        	{{$loop->iteration}}
	                        </td>
	                        <td class="description">
	                        	{{$line['name']}} {{$line['product_variation']}} {{$line['variation']}} 
	                        	@if(!empty($line['sub_sku'])), {{$line['sub_sku']}} @endif @if(!empty($line['brand'])), {{$line['brand']}} @endif @if(!empty($line['cat_code'])), {{$line['cat_code']}}@endif
	                        	@if(!empty($line['product_custom_fields'])), {{$line['product_custom_fields']}} @endif
	                        	@if(!empty($line['product_description']))
	                            	<div class="f-8">
	                            		{!!$line['product_description']!!}
	                            	</div>
	                            @endif
	                        	@if(!empty($line['sell_line_note']))
	                        	<br>
	                        	<span class="f-8">
	                        	{!!$line['sell_line_note']!!}
	                        	</span>
                                @endif 
	                        	@if(!empty($line['lot_number']))<br> {{$line['lot_number_label']}}:  {{$line['lot_number']}} @endif 
	                        	@if(!empty($line['product_expiry'])), {{$line['product_expiry_label']}}:  {{$line['product_expiry']}} @endif
	                        	@if(!empty($line['warranty_name']))
	                            	<br>
	                            	<small>
	                            		{{$line['warranty_name']}}
	                            	</small>
	                            @endif
	                            @if(!empty($line['warranty_exp_date']))
	                            	<small>
	                            		- {{@format_date($line['warranty_exp_date'])}}
	                            </small>
	                            @endif
	                            @if(!empty($line['warranty_description']))
	                            	<small> {{$line['warranty_description'] ?? ''}}</small>
	                            @endif

	                            @if($receipt_details->show_base_unit_details && $line['quantity'] && $line['base_unit_multiplier'] !== 1)
		                            <br><small>
		                            	1 {{$line['units']}} = {{$line['base_unit_multiplier']}} {{$line['base_unit_name']}} <br>
                            			{{$line['base_unit_price']}} x {{$line['orig_quantity']}} = {{$line['line_total']}}
		                            </small>
		                        @endif
                                <br>
                                <span>
                                    @lang('lang_v1.thermal_receipt_small_qty') :    

                                    {{$line['quantity']}} {{$line['units']}} 
                                    @if($receipt_details->show_base_unit_details && $line['quantity'] && $line  ['base_unit_multiplier'] !== 1)
                                        <br>
                                    <small>
                                        {{$line['quantity']}} x {{$line['base_unit_multiplier']}} = {{$line['orig_quantity']}} {{$line['base_unit_name']}}
                                    </small>
                                    @endif

                                </span>
	                        </td>
                            {{--
	                        <td class="quantity text-right">
                                {{$line['quantity']}} {{$line['units']}} 
                                @if($receipt_details->show_base_unit_details && $line['quantity'] && $line  ['base_unit_multiplier'] !== 1)
                                    <br>
                                <small>
                            	    {{$line['quantity']}} x {{$line['base_unit_multiplier']}} = {{$line['orig_quantity']}} {{$line['base_unit_name']}}
                                </small>
                                @endif
                            </td>
                            --}}
	                        @if(empty($receipt_details->hide_price))
	                        <td class="unit_price text-right">{{$line['unit_price_before_discount']}}</td>

	                        @if(!empty($receipt_details->discounted_unit_price_label))
								<td class="text-right">
									{{$line['unit_price_inc_tax']}} 
								</td>
							@endif

	                        @if(!empty($receipt_details->item_discount_label))
								<td class="text-right">
									{{$line['total_line_discount'] ?? '0.00'}}
									@if(!empty($line['line_discount_percent']))
								 		({{$line['line_discount_percent']}}%)
									@endif
								</td>
							@endif
	                        <td class="price text-right">{{$line['line_total']}}</td>
	                        @endif
	                    </tr>
	                    @if(!empty($line['modifiers']))
							@foreach($line['modifiers'] as $modifier)
								<tr>
									<td>
										&nbsp;
									</td>
									<td>
			                            {{$modifier['name']}} {{$modifier['variation']}} 
			                            @if(!empty($modifier['sub_sku'])), {{$modifier['sub_sku']}} @endif @if(!empty($modifier['cat_code'])), {{$modifier['cat_code']}}@endif
			                            @if(!empty($modifier['sell_line_note']))({!!$modifier['sell_line_note']!!}) @endif 
			                        </td>
									<td class="text-right">{{$modifier['quantity']}} {{$modifier['units']}} </td>
									@if(empty($receipt_details->hide_price))
									<td class="text-right">{{$modifier['unit_price_inc_tax']}}</td>
									@if(!empty($receipt_details->discounted_unit_price_label))
										<td class="text-right">{{$modifier['unit_price_exc_tax']}}</td>
									@endif
									@if(!empty($receipt_details->item_discount_label))
										<td class="text-right">0.00</td>
									@endif
									<td class="text-right">{{$modifier['line_total']}}</td>
									@endif
								</tr>
							@endforeach
						@endif
                    @endforeach
                    <tr>
                    	<td @if(!empty($receipt_details->item_discount_label)) colspan="6" @else colspan="5" @endif>&nbsp;</td>
                    	@if(!empty($receipt_details->discounted_unit_price_label))
    					<td></td>
    					@endif
                    </tr>
                </tbody>
            </table>
			@if(!empty($receipt_details->total_quantity_label))
				<div class="flex-box">
					<p class="left text-right">
						{!! $receipt_details->total_quantity_label !!}
					</p>
					<p class="width-50 text-right">
						{{$receipt_details->total_quantity}}
					</p>
				</div>
			@endif
			@if(!empty($receipt_details->total_items_label))
				<div class="flex-box">
					<p class="left text-right">
						{!! $receipt_details->total_items_label !!}
					</p>
					<p class="width-50 text-right">
						{{$receipt_details->total_items}}
					</p>
				</div>
			@endif
			@if(empty($receipt_details->hide_price))
                <div class="flex-box">
                    <p class="sub-headings">
                    	{!! $receipt_details->subtotal_label !!}
                    </p>
                    <p class="width-50 text-right sub-headings">
                    	{{$receipt_details->subtotal}}
                    </p>
                </div>

				<!-- Added Card Charges in Invoice -->
				@if(!empty($receipt_details->payments))
					@foreach($receipt_details->payments as $payment)
						
						@if($payment['method'] == 'card' || $payment['method'] == 'Card' || $payment['method'] == 'CARD')
							<div class="flex-box">
								<p class="sub-headings">
									@lang('lang_v1.card_charge') ({{$payment['card_charge_percent']}}%)
								</p>
								<p class="width-50 text-right sub-headings">
									{{$payment['card_charge_amount']}}
								</p>
							</div>
						@endif
								
					@endforeach
				@endif
				
				<!-- Gratuity Amount-->
				@if( ($receipt_details->gratuity_unformatted_charges > 0) && ($receipt_details->gratuity_label != "") )
				<div class="flex-box">
					<p class="sub-headings">
						{!! $receipt_details->gratuity_label.' ('.$receipt_details->gratuity_percentage.'%)' !!}
					</p>
					<p class="width-50 text-right sub-headings">
						{{$receipt_details->gratuity_charges}}
					</p>
				</div>
				@endif

				<!-- Tips Amount-->
				@if($receipt_details->tips_unformatted_amount > 0)
				<div class="flex-box">
					<p class="sub-headings">
							@lang('lang_v1.tips')
					</p>
					<p class="width-50 text-right sub-headings">
							{{$receipt_details->tips_amount}}
					</p>
				</div>
				@endif

                <!-- Shipping Charges -->
				@if(!empty($receipt_details->shipping_charges))
					<div class="flex-box">
						<p class="sub-headings">
							{!! $receipt_details->shipping_charges_label !!}
						</p>
						<p class="width-50 text-right">
							{{$receipt_details->shipping_charges}}
						</p>
					</div>
				@endif

				@if(!empty($receipt_details->packing_charge))
					<div class="flex-box">
						<p class="sub-headings">
							{!! $receipt_details->packing_charge_label !!}
						</p>
						<p class="width-50 text-right">
							{{$receipt_details->packing_charge}}
						</p>
					</div>
				@endif

				<!-- Discount -->
				@if( !empty($receipt_details->discount) )
					<div class="flex-box">
						<p class="sub-headings">
							{!! $receipt_details->discount_label !!}
						</p>

						<p class="width-50 text-right">
							(-) {{$receipt_details->discount}}
						</p>
					</div>
				@endif

				@if( !empty($receipt_details->total_line_discount) )
					<div class="flex-box">
						<p class="sub-headings">
							{!! $receipt_details->line_discount_label !!}
						</p>

						<p class="width-50 text-right">
							(-) {{$receipt_details->total_line_discount}}
						</p>
					</div>
				@endif

				{{--
				@if( !empty($receipt_details->dp_discount) )
					@php
					 	$dp_discount = json_decode($receipt_details->dp_discount);
					@endphp
					@if($dp_discount)
					@foreach($dp_discount as $item)
						<div class="flex-box">
							<p class="sub-headings">
								{!! $item->label !!}
							</p>
							<p class="width-50 text-right">
								{{$item->discount}}
							</p>
						</div>
					@endforeach
					@endif
				@endif
				--}}

				@if( !empty($receipt_details->additional_expenses) )
					@foreach($receipt_details->additional_expenses as $key => $val)
						<div class="flex-box">
							<p class="width-50 text-right">
								{{$key}}:
							</p>

							<p class="width-50 text-right">
								(+) {{$val}}
							</p>
						</div>
					@endforeach
				@endif

				@if(!empty($receipt_details->reward_point_label) )
					<div class="flex-box">
						<p class="sub-headings">
							{!! $receipt_details->reward_point_label !!}
						</p>

						<p class="width-50 text-right">
							(-) {{$receipt_details->reward_point_amount}}
						</p>
					</div>
				@endif

				@if( !empty($receipt_details->tax) )
					<div class="flex-box">
						<p class="sub-headings">
							{!! $receipt_details->tax_label !!}
						</p>
						<p class="width-50 text-right">
							(+) {{$receipt_details->tax}}
						</p>
					</div>
				@endif

				@if( $receipt_details->round_off_amount > 0)
					<div class="flex-box">
						<p class="sub-headings">
							{!! $receipt_details->round_off_label !!} 
						</p>
						<p class="width-50 text-right">
							{{$receipt_details->round_off}}
						</p>
					</div>
				@endif

				{{--
                <div class="flex-box">
					<p class="width-50 text-right sub-headings">
						{!! $receipt_details->total_label !!}
					</p>
					<p class="width-50 text-right sub-headings">
						{{$receipt_details->total}}
					</p>
				</div>
                --}}

                <div class="flex-box">
					<p class="sub-headings">
						{!! $receipt_details->total_paid_label !!}
					</p>
					<p class="width-50 text-right sub-headings">
						{{$receipt_details->total}}
					</p>
				</div>

				@if(!empty($receipt_details->total_in_words))
				<p colspan="2" class="text-right mb-0">
					<small>
					({{$receipt_details->total_in_words}})
					</small>
				</p>
				@endif

                {{--
				@if(!empty($receipt_details->payments))
					@foreach($receipt_details->payments as $payment)
						<div class="flex-box">
							<p class="width-50 text-right">{{$payment['method']}} ({{$payment['date']}}) </p>
							<p class="width-50 text-right">
								@if($payment['method'] == 'card' || $payment['method'] == 'Card' || $payment['method'] == 'CARD')
								    {{$payment['original_amount']}}<br>
								@lang('lang_v1.card_charge') {{$payment['card_charge_percent']}}%
								    {{$payment['card_charge_amount']}}
								@else
								    {{$payment['amount']}}
								@endif
							</p>
						</div>
					@endforeach
				@endif
                --}}


				<!-- Total Paid-->

                {{--
				@if(!empty($receipt_details->total_paid))
					<div class="flex-box">
						<p class="width-50 text-right">
							{!! $receipt_details->total_paid_label !!}
						</p>
						<p class="width-50 text-right">
							{{$receipt_details->total_paid}}
						</p>
					</div>
				@endif
                --}}

				<!-- Total Due-->
				@if(!empty($receipt_details->total_due) && !empty($receipt_details->total_due_label))
					<div class="flex-box">
						<p class="width-50 text-right">
							{!! $receipt_details->total_due_label !!}
						</p>
						<p class="width-50 text-right">
							{{$receipt_details->total_due}}
						</p>
					</div>
				@endif

				@if(!empty($receipt_details->all_due))
					<div class="flex-box">
						<p class="width-50 text-right">
							{!! $receipt_details->all_bal_label !!}
						</p>
						<p class="width-50 text-right">
							{{$receipt_details->all_due}}
						</p>
					</div>
				@endif
			@endif
            <div class="width-100" style="margin-top:10px;">
                <p class="centered">
                    @lang('lang_v1.receipt_total_items_sold') = {{ count($receipt_details->lines) }}
                </p>
            </div>
            @if($receipt_details->online_sale_payment != null)
            <div class="centered">**************************************************</div>
            @php
                $online_sale_payment = json_decode($receipt_details->online_sale_payment, true);
                $ext_data = explode(',', $online_sale_payment['response']['ExtData']);
                $ex_amount = $ext_data[0];
                $ex_x_amount = explode('=', $ex_amount);
                $total_amount = end($ex_x_amount);
                
                $ex_account_number = $ext_data[7];
                $ex_x_account_number  = explode('=', $ex_account_number);
                $account_number  = end($ex_x_account_number);

                $ex_cart = $ext_data[2];
                $ex_x_cart = explode('=', $ex_cart);
                $card_issue_by = end($ex_x_cart);
            @endphp

            <div class="textbox-info">
				<p class="f-left"><strong>{{ $card_issue_by }} Card</strong></p>
			</div>

            <div class="textbox-info">
				<p class="f-left"><strong>@lang('lang_v1.receipt_account_number')</strong></p>
				<p class="f-right">
					************{{ $account_number }}
				</p>
			</div>

            <div class="textbox-info">
				<p class="f-left"><strong>@lang('lang_v1.receipt_sequence_number')</strong></p>
				<p class="f-right">
					{{ $online_sale_payment['response']['SN'] }}
				</p>
			</div>

            <div class="textbox-info">
				<p class="f-left"><strong>@lang('lang_v1.receipt_termial_id')</strong></p>
				<p class="f-right">
					{{ $online_sale_payment['response']['RegisterId'] }}
				</p>
			</div>

            <div class="textbox-info">
				<p class="f-left"><strong>@lang('lang_v1.receipt_total_amount')</strong></p>
				<p class="f-right">
				{{ $total_amount }}
				</p>
			</div>

            <div class="textbox-info">
				<p class="f-left"><strong>@lang('lang_v1.receipt_response_code')</strong></p>
				<p class="f-right">
					{!! str_replace('%20',' ',$online_sale_payment['response']['RespMSG']) !!}
				</p>
			</div>
            @endif
            <br>
            <div class="width-100">&nbsp;</div>
            @if(empty($receipt_details->hide_price) && !empty($receipt_details->tax_summary_label) )
	            <!-- tax -->
	            @if(!empty($receipt_details->taxes))
	            	<table class="border-bottom width-100 table-f-12">
	            		<tr>
	            			<th colspan="2" class="text-center">{{$receipt_details->tax_summary_label}}</th>
	            		</tr>
	            		@foreach($receipt_details->taxes as $key => $val)
	            			<tr>
	            				<td class="left">{{$key}}</td>
	            				<td class="right">{{$val}}</td>
	            			</tr>
	            		@endforeach
	            	</table>
	            @endif
            @endif

            @if(!empty($receipt_details->additional_notes))
	            <p class="centered">
	            	<strong>Note:</strong> {!! nl2br($receipt_details->additional_notes) !!}
	            </p>
            @endif

            {{-- Barcode --}}
			@if($receipt_details->show_barcode)
				<br/>
				<img class="center-block" src="data:image/png;base64,{{DNS1D::getBarcodePNG($receipt_details->invoice_no, 'C128', 2,30,array(39, 48, 54), true)}}">
			@endif

			@if($receipt_details->show_qr_code && !empty($receipt_details->qr_code_text))
				<img class="center-block mt-5" src="data:image/png;base64,{{DNS2D::getBarcodePNG($receipt_details->qr_code_text, 'QRCODE')}}">
			@endif
			
			@if(!empty($receipt_details->footer_text))
				<p class="centered">
					{!! $receipt_details->footer_text !!}
				</p>
			@endif
            <div class="centered">**************************************************</div>
            <div>
                <p class="centered">
					@lang('lang_v1.receipt_thank_you_msg') {{$receipt_details->display_name}}
				</p>
            </div>
			<div class="centered">**************************************************</div>
        	
        </div>
        <!-- <button id="btnPrint" class="hidden-print">Print</button>
        <script src="script.js"></script> -->
    </body>
</html>

<style type="text/css">
.f-8 {
	font-size: 8px !important;
}
body {
	color: #000000;
}

@media print {
	* {
    	font-size: 12px;
    	font-family: 'Times New Roman';
    	word-break: break-all;
        text-transform: uppercase;
	}
	.f-8 {
		font-size: 8px !important;
	}
.headings{
	font-size: 16px;
	font-weight: 700;
	text-transform: uppercase;
	white-space: nowrap;
}

.sub-headings{
	font-size: 15px !important;
	font-weight: 700 !important;
}

.border-top{
    border-top: 1px solid #242424;
}
.border-bottom{
	border-bottom: 1px solid #242424;
}

.border-bottom-dotted{
	border-bottom: 1px dotted darkgray;
}

td.serial_number, th.serial_number{
	width: 5%;
    max-width: 5%;
}

td.description,
th.description {
    width: 35%;
    max-width: 35%;
}

td.quantity,
th.quantity {
    width: 15%;
    max-width: 15%;
    word-break: break-all;
}
td.unit_price, th.unit_price{
	width: 25%;
    max-width: 25%;
    word-break: break-all;
}

td.price,
th.price {
    width: 20%;
    max-width: 20%;
    word-break: break-all;
}

.centered {
    text-align: center;
    align-content: center;
}

.ticket {
    width: 300px;
    max-width: 300px;
}

img {
    max-width: inherit;
    width: auto;
}

    .hidden-print,
    .hidden-print * {
        display: none !important;
    }
}
.table-info {
	width: 100%;
}
.table-info tr:first-child td, .table-info tr:first-child th {
	padding-top: 8px;
}
.table-info th {
	text-align: left;
}
.table-info td {
	text-align: right;
}
.logo {
	float: left;
	width:35%;
	padding: 10px;
}

.text-with-image {
	float: left;
	width:65%;
}
.text-box {
	width: 100%;
	height: auto;
}

.textbox-info {
	clear: both;
    font-size: 7px;
}
.textbox-info p {
	margin-bottom: 0px
    font-size: 7px;
}
.flex-box {
	display: flex;
	width: 100%;
}
.flex-box p {
	width: 50%;
	margin-bottom: 0px;
	white-space: nowrap;
}

.table-f-12 th, .table-f-12 td {
	font-size: 12px;
	word-break: break-word;
}

.bw {
	word-break: break-word;
}
</style>