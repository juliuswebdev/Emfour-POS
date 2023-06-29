<div class="pos-tab-content">
	
		@if(!empty($modules))
			<div class="row">
				<h4>@lang('lang_v1.enable_disable_modules')</h4>
				@foreach($modules as $k => $v)
					<div class="col-sm-4">
						<div class="form-group">
							<div class="checkbox">
							<br>
							<label>
								{!! Form::checkbox('enabled_modules[]', $k,  in_array($k, $enabled_modules) , ['class' => 'input-icheck']); !!}
								{{$v['name']}}
							</label>
							@if(!empty($v['tooltip'])) @show_tooltip($v['tooltip']) @endif
							</div>
						</div>
					</div>
				@endforeach
			</div>
		@endif
		<div class="row">
		<div class="col-md-12"><hr></div>
		</div>


		@if(!empty($permissions))
			@php
				$custom_permissions_super_admin = $package->custom_permissions;
				if($subscription->custom_permissions_super_admin != null) {
					$custom_permissions_super_admin = json_decode($subscription->custom_permissions_super_admin, true);
				}
				$super_admin_permission_arr = (isset($custom_permissions_super_admin)) ? array_keys($custom_permissions_super_admin) : [];
			@endphp
			<div class="row">                    
				@foreach($permissions as $module => $module_permissions)
					@foreach($module_permissions as $permission)
					@php
			
						$custom_permissions_business = $package->custom_permissions;
						if($subscription->package_details) {
							$custom_permissions_business = $subscription->package_details;
						}
						$value_business = isset($custom_permissions_business[$permission['name']]) ? $custom_permissions_business[$permission['name']] : false;
					@endphp
					<div class="col-sm-3">
						<div class="checkbox">
							<label @if( in_array($permission['name'], $super_admin_permission_arr) ) @else style="text-decoration: line-through" @endif>
								<input 
									type="checkbox" 
									name="custom_permissions[{{$permission['name']}}]"
									class="input-icheck" value="{{$value_business}}" 
									@if( in_array($permission['name'], $super_admin_permission_arr) ) @else disabled @endif
									@if($value_business == 1) checked @endif
								>
								{{$permission['label']}}
							</label>
						</div>
					</div>
					@endforeach
				@endforeach
			</div>
		@endif
		
</div>