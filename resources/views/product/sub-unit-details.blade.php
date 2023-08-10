@php
	$common_settings = session()->get('business.common_settings');
@endphp
<div class="row">
	<div class="col-md-12">
		<h4>{{$stock_details['variation']}}</h4>
	</div>
    <div class="col-md-8 col-xs-8">
        <table class="table table-condensed mt-5">
            <tr>
                <th>@lang('report.current_stock')</th>
                <td>
                    <span class="display_currency" data-is_quantity="true">{{$stock_details['current_stock']}}</span> {{$stock_details['unit']}}
                </td>
            </tr>
        </table>
    </div>
</div>
<div class="row">
	<div class="col-md-4 col-xs-4">
		<strong>@lang('lang_v1.quantities_in')</strong>
		<table class="table table-condensed">
			<tr>
				<th>@lang('report.total_purchase')</th>
				<td>
					<span class="display_currency" data-is_quantity="true">{{$stock_details['total_purchase']}}</span> {{$stock_details['unit']}}
				</td>
			</tr>
			<tr>
				<th>@lang('lang_v1.opening_stock')</th>
				<td>
					<span class="display_currency" data-is_quantity="true">{{$stock_details['total_opening_stock']}}</span> {{$stock_details['unit']}}
				</td>
			</tr>
			<tr>
				<th>@lang('lang_v1.total_sell_return')</th>
				<td>
					<span class="display_currency" data-is_quantity="true">{{$stock_details['total_sell_return']}}</span> {{$stock_details['unit']}}
				</td>
			</tr>
			<tr>
				<th>@lang('lang_v1.stock_transfers') (@lang('lang_v1.in'))</th>
				<td>
					<span class="display_currency" data-is_quantity="true">{{$stock_details['total_purchase_transfer']}}</span> {{$stock_details['unit']}}
				</td>
			</tr>
		</table>
	</div>
	<div class="col-md-4 col-xs-4">
		<strong>@lang('lang_v1.quantities_out')</strong>
		<table class="table table-condensed">
			<tr>
				<th>@lang('lang_v1.total_sold')</th>
				<td>
					<span class="display_currency" data-is_quantity="true">{{$stock_details['total_sold']}}</span> {{$stock_details['unit']}}
				</td>
			</tr>
			<tr>
				<th>@lang('report.total_stock_adjustment')</th>
				<td>
					<span class="display_currency" data-is_quantity="true">{{$stock_details['total_adjusted']}}</span> {{$stock_details['unit']}}
				</td>
			</tr>
			<tr>
				<th>@lang('lang_v1.total_purchase_return')</th>
				<td>
					<span class="display_currency" data-is_quantity="true">{{$stock_details['total_purchase_return']}}</span> {{$stock_details['unit']}}
				</td>
			</tr>
			
			<tr>
				<th>@lang('lang_v1.stock_transfers') (@lang('lang_v1.out'))</th>
				<td>
					<span class="display_currency" data-is_quantity="true">{{$stock_details['total_sell_transfer']}}</span> {{$stock_details['unit']}}
				</td>
			</tr>
		</table>
	</div>
	@if(count($units) > 0)
    <div class="col-md-8 col-xs-8">
		<strong>@lang('lang_v1.totals')</strong>
		<table class="table table-bordered">
            <tr>
                <th>@lang('lang_v1.sub_unit_inventory')</th>
                <th>@lang('report.current_stock')</th>
            </tr>
            @foreach($units as $unit)
				@if($unit->base_unit_multiplier != 0)
					@php
						$stock_temp = intval($stock_details['current_stock'] / $unit->base_unit_multiplier);
						$stock = floor($stock_temp*100)/100;
					@endphp
					<tr>
						<td>{{ $unit->actual_name }}</td>
						<td>
							<span>{{$stock}}</span> {{ $unit->short_name }}
						</td>
					</tr>
				@endif
			@endforeach

		</table>
	</div>
	@endif
</div>