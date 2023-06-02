@forelse($orders as $order)
	<div class="col-md-4 col-xs-6 order_div">
		<div class="small-box bg-gray">
            <div class="inner">
				<div class="d-flex">
					<span><b>#{{$order->invoice_no}}</b></span>
					<span>{{$order->table_name}}</span>
					<span>{{@format_date($order->created_at)}} {{ @format_time($order->created_at)}}</span>
				</div>
				<br>
				<table class="table no-margin no-border table-slim">
					<tr>
						<th>@lang('lang_v1.service_staff')</th><td>
							@if(isset($order->service_staff))
							{{$order->service_staff->surname}} {{$order->service_staff->first_name}} {{$order->service_staff->last_name}}
							@else
							--
							@endif
						</td>
					</tr>
            		<tr>
						<th>@lang('contact.customer')</th><td>{{$order->customer_name}}</td>
					</tr>
            		@if($business_details->business_type_id == 1)
					<tr>
						<th>{{ __('lang_v1.form_label_send_to_kitchen') }}</th>
						<td>
						  @if($order->additional_notes)
							{!! nl2br($order->additional_notes) !!}
						  @else
							--
						  @endif
						</td>
					</tr>
					@endif
            	</table>
				<br>
				<div class="kitchen-order-table-wrapper">
					<table class="table no-margin no-border table-slim">
						<tr>
							<th>{{ __('lang_v1.product') }}</th>
							<th>{{ __('lang_v1.qty') }}</th>
							<th>{{ __('lang_v1.state') }}</th>
						</tr>
						@php
							$mark_disabled = true;	
							$served_btn_bg = "bg-red"; 
							$served_clickable = true;
						@endphp
						
						@foreach ($order->sell_lines as $k => $row)

						@if($row->cook_start == NULL || $row->cook_end == NULL)
							@php
								$mark_disabled = false;
							@endphp
						@endif
						
						@if( ($row->served_at == NULL && $row->res_line_order_status == null) || ($row->res_line_order_status == "served" && $row->served_at == NULL) || ($row->res_line_order_status == "cooked" && $row->served_at == NULL) )
							@php
								$served_clickable = false;
							@endphp
						@endif

					

						@if($row->res_line_order_status == "served")
							@php
								$served_btn_bg = 'bg-grey';
								$served_clickable = false;
							@endphp
						@endif
						
						<tr>
							<td>
								@php
									$product_desciption = ($row->product->product_description == null) ? '' : ' - '.$row->product->product_description;

								@endphp
								{{ $row->product->name.$product_desciption }}
							</td>
							<td>
								{{ $row->quantity }}
							</td>
							<td>
								<div>
									<div class="status-inline-block">

										@if($row->cook_start == null && $row->cook_end == null)
											@php
												$cooking_btn_bg = "bg-black";
												$cooking_clickable = true;
											@endphp
										@elseif($row->cook_start != null && $row->cook_end == null)
											@php
												$cooking_btn_bg = "bg-green";
												$cooking_clickable = false;
											@endphp
										@else
											@php
												$cooking_btn_bg = "bg-grey";
												$cooking_clickable = false;
											@endphp
										@endif
										
										<a href="javascript:;" data-href="{{ ($cooking_clickable) ? action([\App\Http\Controllers\Restaurant\KitchenController::class, 'updateCookProgress'], ['cook_start', $order->id, $row->product_id]) : '' }}" class="{{ ($cooking_clickable) ? 'btn-cooking-stage' : ''}}">
											<span class="label kit-fix-w-label {{ $cooking_btn_bg }} ">
												{{ __('lang_v1.cooking') }}
											</span>
										</a>
										@if($row->cook_start != null)
											<br><span class="fs-12">&nbsp;{{ $row->display_cook_start_time }}</span>
										@endif
									</div>

									<div class="smt-5px smb-5px status-inline-block">

										@if($row->cook_end == null && $row->cook_start == null)
											@php
												$ready_btn_bg = "bg-black";
												$ready_clickable = ($row->cook_start == null) ? false : true;
											@endphp
										@elseif($row->cook_start != null && $row->cook_end == null)
											@php
												$ready_btn_bg = "bg-black";
												$ready_clickable = true;
											@endphp
										@elseif($row->cook_end != null && $row->served_at == null)
											@php
												$ready_btn_bg = "bg-green";
												$ready_clickable = false;
											@endphp
										@else
											@php
												$ready_btn_bg = "bg-grey";
												$ready_clickable = false;
											@endphp
										@endif

										<a href="javascript:;" data-href="{{ ($ready_clickable) ? action([\App\Http\Controllers\Restaurant\KitchenController::class, 'updateCookProgress'], ['cook_end', $order->id, $row->product_id]) : '' }}" class="{{ ($ready_clickable) ? 'btn-cooking-stage' : ''}}">
											<span class="label kit-fix-w-label {{ $ready_btn_bg }}"
											>{{ __('lang_v1.ready') }} </span>
										</a>
										@if($row->cook_end != null)
											<br><span class="fs-12">&nbsp;{{ $row->display_cook_end_time }}</span>
										@endif
									</div>
									
									@if($orders_for == 'waiter' && $order->res_order_status != 'served')
									<div class="smt-5px smb-5px status-inline-block">

										@if($row->cook_start != null && $row->cook_end != null && $row->served_at == null)
											@php
												$serve_btn_bg = "bg-black";
												$serve_clickable = true;
											@endphp
										@elseif( ($row->cook_start == null && $row->cook_end == null) || ($row->cook_start == null) || ($row->cook_end == null) )
											@php
												$serve_btn_bg = "bg-black";
												$serve_clickable = false;
											@endphp
										@elseif($row->served_at != null && $row->res_line_order_status == "cooked")
											@php
												$serve_btn_bg = "bg-green";
												$serve_clickable = false;
											@endphp
										@else
											@php
												$serve_btn_bg = "bg-grey";
												$serve_clickable = false;
											@endphp
										@endif

										<a href="javascript:;" data-href="{{ ($serve_clickable) ? action([\App\Http\Controllers\Restaurant\OrderController::class, 'updateServed'], ['served_at', $order->id, $row->product_id]) : '' }}" class="{{ ($serve_clickable) ? 'btn-served' : ''}}">
											<span class="label kit-fix-w-label {{ $serve_btn_bg }}">{{ __('lang_v1.served') }} </span>
										</a>

										@if($row->served_at != null)
											<br><span class="fs-12">&nbsp;{{ $row->display_served_time }}</span>
										@endif

									</div>
									@endif

								</div>
								
							</td>
						</tr>
						@endforeach
					</table>
				</div>

            </div>
			
            @if($orders_for == 'kitchen')
				@if(!$mark_disabled)
				<a href="javascript:;" class="btn btn-flat small-box-footer bg-red" data-href=""><i class="fa fa-check-square-o"></i> {{ __('lang_v1.mark_as_completed') }}</a>
				@else
            	<a href="#" class="btn btn-flat small-box-footer bg-red mark_as_cooked_btn" data-href="{{action([\App\Http\Controllers\Restaurant\KitchenController::class, 'markAsCooked'], [$order->id])}}"><i class="fa fa-check-square-o"></i> {{ __('lang_v1.mark_as_completed') }}</a>
				@endif
            @elseif($orders_for == 'waiter' && $order->res_order_status != 'served')
				
				<a href="javascript:;" class="btn btn-flat small-box-footer {{ $served_btn_bg }} {{ ($served_clickable) ? 'mark_as_served_btn' : '' }} " data-href="{{ ($served_clickable) ? action([\App\Http\Controllers\Restaurant\OrderController::class, 'markAsServed'], [$order->id]) : '' }}"><i class="fa fa-check-square-o"></i>  {{ __('lang_v1.mark_as_completed') }} </a>
				
            @else
            	<div class="small-box-footer bg-gray">&nbsp;</div>
            @endif
            	<a href="#" class="d-none btn btn-flat small-box-footer bg-info btn-modal" data-href="{{ action([\App\Http\Controllers\SellController::class, 'show'], [$order->id])}}" data-container=".view_modal">@lang('restaurant.order_details') <i class="fa fa-arrow-circle-right"></i></a>
         </div>
	</div>
	@if($loop->iteration % 3 == 0)
		<div class="hidden-xs">
			<div class="clearfix"></div>
		</div>
	@endif
	@if($loop->iteration % 2 == 0)
		<div class="visible-xs">
			<div class="clearfix"></div>
		</div>
	@endif
@empty
<div class="col-md-12">
	<h4 class="text-center">@lang('restaurant.no_orders_found')</h4>
</div>
@endforelse