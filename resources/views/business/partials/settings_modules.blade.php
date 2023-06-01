<div class="pos-tab-content">
	<div class="row">
	@if(!empty($modules))
		<h4>@lang('lang_v1.enable_disable_modules')</h4>
      <ul>
		  @foreach($modules as $k => $v)
            <li><label>{{$v['name']}}</label>@if(!empty($v['tooltip'])) @show_tooltip($v['tooltip']) @endif</li>
        @endforeach
      </ul>
	@endif

	</div>
</div>
