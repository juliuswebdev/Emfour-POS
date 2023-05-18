@forelse($orders as $order)
	<div class="col-md-4 col-xs-6 order_div">
		<div class="small-box bg-gray">
            <div class="inner">
				<div class="d-flex">
					<span><b>#{{$order->invoice_no}}</b></span>
					<span>@lang('restaurant.table') : {{$order->table_name}}</span>
					<span>@lang('restaurant.placed_at') : {{@format_date($order->created_at)}} {{ @format_time($order->created_at)}}</span>
				</div>
				<br>
				<table class="table no-margin no-border table-slim">
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
							<th>{{ __('lang_v1.quantity') }}</th>
							<th>{{ __('lang_v1.state') }}</th>
						</tr>
						@php
							$mark_disabled = $served_disabled = true;	
						@endphp
						
						@foreach ($order->sell_lines as $k => $row)

						@if($row->cook_start == NULL || $row->cook_end == NULL)
							@php
								$mark_disabled = false;
							@endphp
						@endif

						@if($row->is_served == 0)
							@php
								$served_disabled = false;
							@endphp
						@endif

						
						<tr>
							<td>
								{{ $row->product->name }}
							</td>
							<td>
								{{ $row->quantity }}
							</td>
							<td>
								<div>
									<div>
										<a href="javascript:;" data-href="{{ ($row->cook_start == null) ? action([\App\Http\Controllers\Restaurant\KitchenController::class, 'updateCookProgress'], ['cook_start', $order->id, $row->product_id]) : '' }}" class="{{ ($row->cook_start == null) ? 'btn-cooking-stage' : ''}}">
											<span class="label {{ ($row->cook_start != null) ? 'bg-black' : 'bg-yellow'}} ">
												{{ __('lang_v1.cooking') }}
											</span>
										</a>
										@if($row->cook_start != null)
										<span class="fs-12">&nbsp;&nbsp;({{ $row->display_cook_start_time }})</span>
										@endif
									</div>

									<div class="smt-5px">
										<a href="javascript:;" data-href="{{ ($row->cook_start != null && $row->cook_end == null) ? action([\App\Http\Controllers\Restaurant\KitchenController::class, 'updateCookProgress'], ['cook_end', $order->id, $row->product_id]) : '' }}" class="{{ ($row->cook_start != null && $row->cook_end == null) ? 'btn-cooking-stage' : ''}}">
											<span 
											class="label bg-green {{ ($row->cook_start != null && $row->cook_end != null) ? 'blink-allow' : '' }}"
											>{{ __('lang_v1.ready') }} </span>
										</a>
										@if($row->cook_end != null)
										<span class="fs-12">&nbsp;&nbsp;({{ $row->display_cook_end_time }})</span>
										@endif
									</div>

									@if($orders_for == 'waiter' && $order->res_order_status != 'served' && $row->cook_start != null && $row->cook_end != null)
									<div class="smt-5px">
										<a href="javascript:;" data-href="{{ ($row->is_served == 0) ? action([\App\Http\Controllers\Restaurant\OrderController::class, 'updateServed'], ['is_served', $order->id, $row->product_id]) : '' }}" class="{{ ($row->is_served == 0) ? 'btn-served' : ''}}">
											<span class="label {{ ($row->is_served == 0) ? 'bg-blue' : 'bg-black'}}">{{ __('lang_v1.served') }} </span>
										</a>
									</div>
									@endif

								</div>
								@if($row->cook_start != null && $row->cook_end != null)
								<div class="smt-5px smb-5px">
									<span class="fs-12">{{ $row->display_cook_time }}</span>
								</div>
								@endif
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
				@if(!$served_disabled)
					<a href="javascript:;" class="btn btn-flat small-box-footer bg-red" data-href=""><i class="fa fa-check-square-o"></i> {{ __('lang_v1.mark_as_completed') }} </a>
				@else
					<a href="#" class="btn btn-flat small-box-footer bg-red mark_as_served_btn" data-href="{{action([\App\Http\Controllers\Restaurant\OrderController::class, 'markAsServed'], [$order->id])}}"><i class="fa fa-check-square-o"></i>  {{ __('lang_v1.mark_as_completed') }} </a>
				@endif
            @else
            	<div class="small-box-footer bg-gray">&nbsp;</div>
            @endif
            	<a href="#" class="btn btn-flat small-box-footer bg-info btn-modal" data-href="{{ action([\App\Http\Controllers\SellController::class, 'show'], [$order->id])}}" data-container=".view_modal">@lang('restaurant.order_details') <i class="fa fa-arrow-circle-right"></i></a>
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