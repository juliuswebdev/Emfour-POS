<div class="modal-dialog" role="document">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">@lang( 'restaurant.booking_details' )</h4>
			</div>

			<div class="modal-body">
					{!! Form::open(['url' => action([\App\Http\Controllers\Restaurant\BookingController::class, 'store']), 'method' => 'post', 'id' => 'add_booking_form' ]) !!}
						@if(count($business_locations) == 1)
							@php 
								$default_location = current(array_keys($business_locations->toArray())) 
							@endphp
						@else
							@php $default_location = null; @endphp
						@endif
						<div class="row">
							<div class="col-sm-12">
								<div class="form-group">
									<div class="input-group">
										<span class="input-group-addon">
											<i class="fa fa-map-marker"></i>
										</span>
										{!! Form::select('location_id', $business_locations, $default_location, ['class' => 'form-control', 'placeholder' => __('purchase.business_location'), 'required', 'id' => 'booking_location_id']); !!}
									</div>
								</div>
							</div>
							<div class="clearfix"></div>
							<div class="col-sm-6">
								<div class="form-group">
									<div class="input-group">
										<span class="input-group-addon">
											<i class="fa fa-user"></i>
										</span>
										{!! Form::select('contact_id', 
											$customers, null, ['class' => 'form-control', 'id' => 'booking_customer_id', 'placeholder' => __('contact.customer'), 'required']); !!}
										<span class="input-group-btn">
											<button type="button" class="btn btn-default bg-white btn-flat add_new_customer" data-name=""  @if(!auth()->user()->can('customer.create')) disabled @endif><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
										</span>
									</div>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<div class="input-group">
										<span class="input-group-addon">
											<i class="fa fa-user"></i>
										</span>
										{!! Form::select('correspondent', 
											$correspondents, null, ['class' => 'form-control', 'placeholder' => __('restaurant.select_correspondent'), 'id' => 'correspondent']); !!}
									</div>
								</div>
							</div>
							<div class="clearfix"></div>
							<div id="restaurant_module_span"></div>
							<div class="clearfix"></div>
							<div class="col-sm-6">
								<div class="form-group">
								{!! Form::label('status', __('restaurant.start_time') . ':*') !!}
									<div class='input-group date' >
									<span class="input-group-addon">
										<span class="glyphicon glyphicon-calendar"></span>
									</span>
									{!! Form::text('booking_start', null, ['class' => 'form-control','placeholder' => __( 'restaurant.start_time' ), 'required', 'id' => 'start_time', 'readonly']); !!}
									</div>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									{!! Form::label('status', __('restaurant.end_time') . ':*') !!}
									<div class='input-group date' >
									<span class="input-group-addon">
										<span class="glyphicon glyphicon-calendar"></span>
									</span>
									{!! Form::text('booking_end', null, ['class' => 'form-control','placeholder' => __( 'restaurant.end_time' ), 'required', 'id' => 'end_time', 'readonly']); !!}
									</div>
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group">
								{!! Form::label('booking_note', __( 'restaurant.customer_note' ) . ':') !!}
								{!! Form::textarea('booking_note', null, ['class' => 'form-control','placeholder' => __( 'restaurant.customer_note' ), 'rows' => 3 ]); !!}
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group">
									<div class="checkbox">
										{!! Form::checkbox('send_notification', 1, true, ['class' => 'input-icheck hidden', 'id' => '']); !!} @lang('restaurant.send_notification_to_customer')
									</div>
								</div>
							</div>
							<div class="col-sm-12">
								<button type="submit" class="btn btn-primary">@lang( 'messages.update' )</button>
							</div>
						</div>
					{!! Form::close() !!}
				
					<br>
					<hr>
					<div class="row">
						<div class="col-sm-12">
							<button type="button" class="btn btn-info btn-modal pull-right" data-href="{{action([\App\Http\Controllers\NotificationController::class, 'getTemplate'], ['transaction_id' => $booking->id,'template_for' => 'new_booking'])}}" data-container=".view_modal">@lang('restaurant.send_notification_to_customer')</button>
						</div>
					</div>
					<br>
					<div class="row">
						<div class="col-sm-9">
							{!! Form::open(['url' => action([\App\Http\Controllers\Restaurant\BookingController::class, 'update'], [$booking->id]), 'method' => 'PUT', 'id' => 'edit_booking_form' ]) !!}
								<div class="input-group">
									<!-- /btn-group -->
									{!! Form::select('booking_status', $booking_statuses, $booking->booking_status, ['class' => 'form-control', 'placeholder' => __('restaurant.change_booking_status'), 'required']); !!}
									<div class="input-group-btn">
									<button type="submit" class="btn btn-primary">@lang('messages.update')</button>
									</div>
								</div>
							{!! Form::close() !!}
						</div>
						<div class="col-sm-3 text-center">
							<button type="button" class="btn btn-danger" id="delete_booking" data-href="{{action([\App\Http\Controllers\Restaurant\BookingController::class, 'destroy'], [$booking->id])}}">@lang('restaurant.delete_booking')</button>
						</div>
					</div>
				</div>
				<br>
				<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
			</div>
		

	</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->