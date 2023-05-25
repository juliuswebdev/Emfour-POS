<div class="row" id="featured_products_box" style="display: none;">
@if(!empty($featured_products))
	@include('sale_pos.partials.featured_products')
@endif
</div>
<div class="row">

	@if(count($brands) > 1)
		<div class="col-sm-4" id="product_brand_div">
			{!! Form::select('size', $brands, null, ['id' => 'product_brand', 'class' => 'select2', 'name' => null, 'style' => 'width:100% !important']) !!}
		</div>
	@endif

	@if(!empty($categories))
		<div class="col-md-12" id="product_category_div">
			<h4>@lang('lang_v1.categories')</h4>
			<div class="row">
				<div class="col-md-3">
					<input type="radio" name="category_id" class="" value="all" id="cat-parent-0">
					<label for="cat-parent-0">@lang('lang_v1.all_category')</label>
				</div>
				@foreach($categories as $category)
					<div class="col-md-2">
						<input type="radio" name="category_id" class="" value="{{$category['id']}}" id="cat-parent-{{$category['id']}}">
						<label for="cat-parent-{{$category['id']}}">{{$category['name']}}</label>
					</div>
				@endforeach
				@foreach($categories as $category)
					@if(!empty($category['sub_categories']))
							@foreach($category['sub_categories'] as $sc)
								<div class="col-md-2">
									<label for="cat-child-{{$sc['id']}}">{{$sc['name']}}</label>
									<input type="radio" name="category_id" class="" value="{{$sc['id']}}" id="cat-child-{{$sc['id']}}">
								</div>
							@endforeach
					@endif
				@endforeach
			</div>
		</div>
	@endif

	<!-- used in repair : filter for service/product -->
	<div class="col-md-6 hide" id="product_service_div">
		{!! Form::select('is_enabled_stock', ['' => __('messages.all'), 'product' => __('sale.product'), 'service' => __('lang_v1.service')], null, ['id' => 'is_enabled_stock', 'class' => 'select2', 'name' => null, 'style' => 'width:100% !important']) !!}
	</div>

	<div class="col-sm-4 @if(empty($featured_products)) hide @endif" id="feature_product_div">
		<button type="button" class="btn btn-primary btn-flat" id="show_featured_products">@lang('lang_v1.featured_products')</button>
	</div>
</div>
<br>
<div class="row">
	<input type="hidden" id="suggestion_page" value="1">
	<div class="col-md-12">
		<div class="eq-height-row" id="product_list_body"></div>
	</div>
	<div class="col-md-12 text-center" id="suggestion_page_loader" style="display: none;">
		<i class="fa fa-spinner fa-spin fa-2x"></i>
	</div>
</div>