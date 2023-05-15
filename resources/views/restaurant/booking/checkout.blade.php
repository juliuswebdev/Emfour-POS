<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">@lang('restaurant.checkout')</h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        {!! Form::open(['url' => action('\App\Http\Controllers\Restaurant\BookingController@getBookings', $business->slug), 'class'=>'form search-check', 'method' => 'post' ]) !!}
            <label>Search: </label>
            <div class="row">
                <div class="col-md-10">
                    <input type="hidden" name="from" value="checkin">
                    <input type="text" placeholder="Search by Reference No, Phone..." name="search_query" class="form-control">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-search">Search</button>
                </div>
            </div>
        {!! Form::close() !!}
        <div id="checkout_result"></div>
    </div>
    <!-- /.box-body -->
</div>