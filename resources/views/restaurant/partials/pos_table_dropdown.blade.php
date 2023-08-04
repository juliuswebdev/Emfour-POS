@if($tables_enabled)
<div class="col-sm-4 table-mapping-dropdown">
	<div class="form-group">
		<div class="input-group">
			<span class="input-group-addon table-mapping-btn">
				<i class="fa fa-table"></i>
			</span>
			{!! Form::select('res_table_id', $tables, $view_data['res_table_id'], ['class' => 'form-control', 'placeholder' => __('restaurant.select_table')]); !!}
			<span class="input-group-btn">
					<button type="button" class="btn btn-default bg-white btn-flat add_new_table" data-name=""><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
			</span>
		</div>
	</div>
</div>
@endif
@if($waiters_enabled)
<div class="col-sm-4">
	<div class="form-group">
		<div class="input-group">
			<span class="input-group-addon">
				<i class="fa fa-user-secret"></i>
			</span>
			{!! Form::select('res_waiter_id', $waiters, $view_data['res_waiter_id'], ['class' => 'form-control', 'placeholder' => __('restaurant.select_service_staff'), 'id' => 'res_waiter_id', 'required' => $is_service_staff_required ? true : false]); !!}
			@if(!empty($pos_settings['inline_service_staff']))
			<div class="input-group-btn">
                <button type="button" class="btn btn-default bg-white btn-flat" id="select_all_service_staff" data-toggle="tooltip" title="@lang('lang_v1.select_same_for_all_rows')"><i class="fa fa-check"></i></button>
            </div>
            @endif
		</div>
	</div>
</div>
@endif