@if($business_details->business_type_id == 1)		
	<div class="modal fade in" tabindex="-1" role="dialog" id="restaurant-checkout">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
					<h4 class="modal-title">@lang('lang_v1.checkout_details')</h4>
				</div>
				<div class="modal-body">
					<!-- /.box-header -->
					<div class="box-body">
						<form action="{{ action('\App\Http\Controllers\Restaurant\OrderController@searchOrderByStatus', 'served') }}" class="form " method="get" id="restaurant-search-order">
							<label>Search: </label>
							<div class="row">
								<div class="col-md-10">
									<input type="text" placeholder="@lang('lang_v1.search_by_1')" name="search_query" class="form-control">
								</div>
								<div class="col-md-2">
									<button type="submit" class="btn btn-primary btn-search">@lang('lang_v1.search')</button>
								</div>
							</div>
						</form>
						<div id="restaurant-search-order_result"></div>
					</div>
					<!-- /.box-body --> 
				</div>
			</div>
		</div>
	</div>
@endif