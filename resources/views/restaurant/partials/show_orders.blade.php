@php
	function buildTree(array $elements, $parentId = 0) {
		$branch = array();

		foreach ($elements as $element) {
			if ($element['parent_sell_line_id'] == $parentId) {
				$children = buildTree($elements, $element['id']);
				if ($children) {
					$element['children'] = $children;
				}
				$branch[] = $element;
			}
		}
		return $branch;
	}
@endphp
@forelse($orders as $order)
	<div class="col-md-4 col-xs-6 order_div" data-service-staff="{{ $order->res_waiter_id }}">
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
						
							foreach ($order->sell_lines as $key => $value) {
								if (! empty($value->sub_unit_id)) {
									$t = new \App\Utils\TransactionUtil;
									$formated_sell_line = $t->recalculateSellLineTotals($business_details->id, $value);
									$order->sell_lines[$key] = $formated_sell_line;
								}
							}

							$mark_disabled = true;	
							$served_btn_bg = "bg-red"; 
							$served_clickable = true;
							if($orders_for == 'kitchen'){
								$build_tree =  buildTree($order->sell_lines->where('is_available', 1)->toArray());
							}else{
								$build_tree =  buildTree($order->sell_lines->toArray());
							}
							$order_sell_lines = json_decode(json_encode($build_tree), FALSE);
						@endphp
						
						@foreach ($order_sell_lines as $k => $row)

							@if($row->cook_start == NULL || $row->cook_end == NULL)
								@php
									$mark_disabled = false;
								@endphp
							@endif
							
							@if( ($row->served_at == NULL && $row->res_line_order_status == null && $row->is_available == 1) || ($row->res_line_order_status == "served" && $row->served_at == NULL && $row->is_available == 1) || ($row->res_line_order_status == "cooked" && $row->served_at == NULL && $row->is_available == 1) )
								@php
									$served_clickable = false;
								@endphp
							@endif

							@if($row->res_line_order_status == "served" && $row->is_available == 1)
								@php
									$served_btn_bg = 'bg-grey';
									$served_clickable = false;
								@endphp
							@endif
							
							@php
								$product = \App\Product::where('id', $row->product_id)->select('product_custom_field1', 'type', 'name', 'product_description', 'unit_id')->first();
							@endphp
							
							@if($product->product_custom_field1 == 1 || $orders_for == 'waiter')
								<tr>
									<td>
										
										{!! $product->name !!}
										@if(!empty($row->children))
											@foreach($row->children as $children)
											@php
												$product_children = \App\Variation::where('id', $children->variation_id)->select('name')->first();
											@endphp
											<br><small>&nbsp;&nbsp;&nbsp;- {{ $product_children->name }}</small>
											@endforeach
										@endif
									</td>
									<td>
										{{ $row->quantity }}
								
										@if($row->sub_unit_id)
											@php 
												$unit = \App\Unit::find($row->sub_unit_id);
											@endphp
											{{ $unit->short_name }}
										@else
											@php 
												$unit = \App\Unit::find($product->unit_id);
											@endphp
											{{ $unit->short_name }}
										@endif
									</td>

									<td>
										<div>
											
											@if($product->product_custom_field1 == 1)


												@if($row->is_available == 1)
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
								
														<br><span class="fs-12">@if($row->cook_start != null){{ $row->display_cook_start_time }} @else &nbsp; @endif</span>

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
														
															<br><span class="fs-12">@if($row->cook_end != null) {{ $row->display_cook_end_time }} @else &nbsp; @endif</span>
														
													</div>

													@if($orders_for == 'kitchen' && $row->cook_start == null)
														<!-- Not Available Btn HTML -->
														<div class="status-inline-block">
															<a href="javascript:;" data-href="{{ action([\App\Http\Controllers\Restaurant\KitchenController::class, 'removeFromKitchen'], [$order->id, $row->product_id]) }}" class="btn-not-available">
																<span class="label kit-fix-w-label bg-blue">
																	{{ __('lang_v1.not_available') }}
																</span>
															</a>
														</div>
													@endif
												@else
													
												<div class="status-inline-block">
													<a href="javascript:;" class="">
														<span class="label kit-fix-w-label bg-blue">
															{{ __('lang_v1.not_available') }}
														</span>
													</a>
												</div>
														
												@endif
												


											@else
												<div class="status-inline-block" style="width: 60px; height: 41.5px; vertical-align: top;">--</div>
												<div class="smt-5px smb-5px status-inline-block" style="width: 60px; height: 41.5px; vertical-align: top;">--</div>
											@endif
											
											@if($orders_for == 'waiter')
												
												@if($row->is_available == 1)

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
														@elseif($row->served_at != null && ($row->res_line_order_status == "cooked" || $row->res_line_order_status == "ready"))
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
														<br><span class="fs-12">@if($row->served_at != null){{ $row->display_served_time }} @else &nbsp; @endif</span>
													</div>

												@endif

											@endif
										</div>
									</td>
								</tr>
							@endif
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
            @elseif( $orders_for == 'waiter' && ($order->res_order_status != 'served') )

				<a href="javascript:;" class="btn btn-flat small-box-footer {{ $served_btn_bg }} {{ ($served_clickable) ? 'mark_as_served_btn' : '' }} " data-href="{{ ($served_clickable) ? action([\App\Http\Controllers\Restaurant\OrderController::class, 'markAsServed'], [$order->id]) : '' }}"><i class="fa fa-check-square-o"></i>  {{ __('lang_v1.mark_as_completed') }} </a>
            @else
			<a href="javascript:;" class="btn btn-flat small-box-footer bg-grey  " data-href=""><i class="fa fa-check-square-o"></i>{{ __('lang_v1.mark_as_completed') }}</a>
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