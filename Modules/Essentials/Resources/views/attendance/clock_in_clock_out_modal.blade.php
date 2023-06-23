{{--

<div class="modal fade" id="clock_in_clock_out_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    <div class="modal-dialog" role="document">
	  <div class="modal-content">

	    {!! Form::open(['url' => action([\Modules\Essentials\Http\Controllers\AttendanceController::class, 'clockInClockOut']), 'method' => 'post', 'id' => 'clock_in_clock_out_form' ]) !!}
	    <div class="modal-header">
	      	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	      	<h4 class="modal-title"><span id="clock_in_text">@lang( 'essentials::lang.clock_in' )</span>
	      	<span id="clock_out_text">@lang( 'essentials::lang.clock_out' )</span></h4>
	    </div>
	    <div class="modal-body">
	    	<div class="row">
	    		<input type="hidden" name="type" id="type">
		      	<div class="form-group col-md-12">
		      		<strong>@lang( 'essentials::lang.ip_address' ): {{$ip_address}}</strong>
		      	</div>
				<div class="form-group col-md-12">
					{!! Form::label('pin', __('business.digits_pin') . ':') !!}
                    <input type="password" name="user_pin" id="user_pin" class="form-control" placeholder="{{  __('business.digits_pin') }}" required>
				</div>
		      	<div class="form-group col-md-12 clock_in_note @if(!empty($clock_in)) hide @endif">
		        	{!! Form::label('clock_in_note', __( 'essentials::lang.clock_in_note' ) . ':') !!}
		        	{!! Form::textarea('clock_in_note', null, ['class' => 'form-control', 'placeholder' => __( 'essentials::lang.clock_in_note'), 'rows' => 3 ]); !!}
		      	</div>
		      	<div class="form-group col-md-12 clock_out_note @if(empty($clock_in)) hide @endif">
		        	{!! Form::label('clock_out_note', __( 'essentials::lang.clock_out_note' ) . ':') !!}
		        	{!! Form::textarea('clock_out_note', null, ['class' => 'form-control', 'placeholder' => __( 'essentials::lang.clock_out_note'), 'rows' => 3 ]); !!}
		      	</div>
		      	<input type="hidden" name="clock_in_out_location" id="clock_in_out_location" value="">
	    	</div>
	    	@if($is_location_required)
		    	<div class="row">
		    		<div class="col-md-12">
		    			<b>@lang('messages.location'):</b> <button type="button" class="btn btn-primary btn-xs" id="get_current_location"> <i class="fas fa-map-marker-alt"></i> @lang('essentials::lang.get_current_location')</button>
		    			<br><span class="clock_in_out_location"></span>
		    		</div>
		    		<div class="col-md-12 ask_location" style="display: none;">
		    			<span class="location_required error"></span>
		    		</div>
		    	</div>
		    @endif
	    </div>

	    <div class="modal-footer">
	      <button type="submit" class="btn btn-primary">@lang( 'messages.submit' )</button>
	      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
	    </div>

	    {!! Form::close() !!}

	  </div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
        	
</div>

--}}

<div class="modal fade" id="clock_in_clock_out_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    <div class="modal-dialog" role="document">
	  <div class="modal-content">

	    {!! Form::open(['url' => action([\Modules\Essentials\Http\Controllers\AttendanceController::class, 'clockInClockOut']), 'method' => 'post', 'id' => 'clock_in_clock_out_form' ]) !!}
	    <div class="modal-header">
	      	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	    </div>
	    <div class="modal-body mf-clockin-clockout">
			<div class="row">
				<div class="col-md-12 ">
					<div class="text-center">
						<h2 class="mf-title font-weight-bold">
							@lang( 'lang_v1.label_enfour_system')
						</h2>
						<span class="font-weight-bold">
							@lang( 'lang_v1.label_cico')
						</span>
					</div>
				</div>
			</div>

			<div class="mf-input-wrapper">
				<div class="row mf-input-box">
					<div class="col-md-12">
						<div class="text-center">
	
							<div class="form-group">
								{!! Form::label('pin', __('lang_v1.enter_security_pin') . ':') !!}
								<input minlength="4" maxlenght="9" type="password" name="user_pin" id="user_pin" class=" input-pin form-control" placeholder="{{  __('lang_v1.enter_security_pin') }}" required>
							</div>
	
						</div>
					</div>
				</div>
				<div class="row cl-co-keyboard">
					<div class="col-md-12">
						<div class="grid-container">
							@for ($i=1; $i<=9;$i++)
							<div class="grid-item cl-key-input" data-value="{{ $i }}">
								<div class="circle">
									<span>{{ $i }}</span>
								</div>
							</div>	
							@endfor
							<div class="grid-item cl-key-input" data-value="0">
								<div class="circle">
									<span>0</span>
								</div>
							</div>	
							<div class="grid-item cl-key-input" data-value="CANCEL">
								<button class="btn btn-danger text-black">CANCEL</button>
							</div>
							<div class="grid-item cl-key-input" data-value="ENTER">
								<button class="btn btn-success text-black">ENTER</button>	
							</div>  
						</div>
					</div>
				</div>


				<div class="row cl-co-type-choose">
					<div class="col-md-12 text-center">
						<div>
							<h2 class="mf-title font-weight-bold ci-co-user"></h2>
						</div>
						<div>
							<span class="font-weight-bold ci-co-user-role"></span>
						</div>
						<div>
							<span class="font-weight-bold ci-co-business"></span>
						</div>

						<div class="cl-co-action">
							<div>
								<button class="btn btn-success cl-co-btn-action" data-action="clock_in" type="button">Clock In</button>
							</div>
							<div>
								<button class="btn btn-warning cl-co-btn-action" data-action="clock_out" type="button">Clock Out</button>
							</div>
						</div>

					</div>
				</div>

				<div class="row">
					<div class="col-md-12">
						<input type="hidden" name="cico_action" id="cico_action" value="">
					</div>
				</div>
			</div>
	    	
	    </div>

	    <div class="modal-footer">
	     </div>

	    {!! Form::close() !!}

	  </div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
        	
</div>