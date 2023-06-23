@if(!empty($shift_info))
	<div class="text-center shift-label">
		@lang('lang_v1.your_shifts'):
	</div>

	<div class="shift-text-wrapper">
		<h4>
			{{ucfirst($shift_info->name)}}
			<small>
				(
					<code>
						@lang('essentials::lang.'.$shift_info->type)
					</code>
				)
			</small>
		</h4>
	</div>
	@if($shift_info->type == 'fixed_shift')
	<div class="shift-table">
		<table class="table">
		<thead class="thead-light">
		  <tr>
			<th scope="col">@lang('lang_v1.start_date')</th>
			<th scope="col">@lang('lang_v1.end_date')</th>
			<th scope="col">@lang('lang_v1.timing')</th>
		  </tr>
		</thead>
		<tbody>
		  <tr>
			<td>{{@format_date($essential_shift->start_date)}}</td>
			<td>{{@format_date($essential_shift->end_date)}}</td>
			<td>{{ $shift_info->start_time }} - {{ $shift_info->end_time }}</td>
		  </tr>
		</tbody>
	  </table>
	</div>
	@endif
@endif