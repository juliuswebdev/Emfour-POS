<html>
    <head>
        <title>Booking | {{ $business->name }}</title>
        <link rel="stylesheet" href="{{ asset('css/vendor.css?v='.$asset_v) }}">
        @if( in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) )
            <link rel="stylesheet" href="{{ asset('css/rtl.css?v='.$asset_v) }}">
        @endif

        @yield('css')
        <!-- app css -->
        <link rel="stylesheet" href="{{ asset('css/app.css?v='.$asset_v) }}">
        <style>
            .header {
                margin: 60px 0 30px;
                text-align: center;
            }
            .header img {
                max-width: 300px;
            }
        </style>
    </head>
    <body>
        <div class="container">
        <div class="header">
            @if($business->logo)
            <img src="{{ asset('uploads/business_logos') }}/{{ $business->logo }}">
            @else
            <img src="{{ asset('img') }}/default.png" alt="Color-Correction">
            @endif
            <h3 class="text-center">Welcome to {{ $business->name }}</h3>
        </div>
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">@lang('restaurant.checkin')</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                {!! Form::open(['url' => action('\App\Http\Controllers\Restaurant\BookingController@getBookings', $slug), 'class'=>'form search-check', 'method' => 'post' ]) !!}
                    <label>Search: </label>
                    <div class="row">
                        <div class="col-md-10">
                            <input type="hidden" name="from" value="booked,waiting">
                            <input type="text" placeholder="Search by Reference No, Phone..." name="search_query" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-search">Search</button>
                        </div>
                    </div>
                {!! Form::close() !!}
                <div id="checkin_result"></div>
            </div>
            <!-- /.box-body -->
        </div>
        </div>
    </body>
    <script src="{{ asset('js/vendor.js?v=' . $asset_v) }}"></script>

    @if(file_exists(public_path('js/lang/' . session()->get('user.language', config('app.locale')) . '.js')))
        <script src="{{ asset('js/lang/' . session()->get('user.language', config('app.locale') ) . '.js?v=' . $asset_v) }}"></script>
    @else
        <script src="{{ asset('js/lang/en.js?v=' . $asset_v) }}"></script>
    @endif
    <script>
        $(document).ready(function(){
            $('.search-check').submit(function(e){
                e.preventDefault();
                var data = $(this).serialize();
                $.ajax({
                    context: this,
                    method: "POST",
                    url: $(this).attr("action"),
                    data: data,
                    success: function(result) {
                        $('#checkin_result').html(result);

                        $('.btn-search').removeAttr('disabled');
                    }
                });
            });


            $(document).on('submit', 'form.check_booking_form', function(e){
                e.preventDefault();
                var data = $(this).serialize();
                $.ajax({
                    context: this,
                    method: "PUT",
                    url: $(this).attr("action"),
                    dataType: "json",
                    data: data,
                    success: function(result){
                        if(result.success == true) {
                            $('#checkin_result').html('');
                            $('#checkout_result').html('');
                            $('input[name="search_query"]').val('');
                            toastr.success('Successfully CheckIn!');
                            $(this).find('button[type="submit"]').attr('disabled', false);
                            var booking_status = $(this).find('input[name="booking_status"]').val();
                            var booking_product_ids = $(this).find('input[name="booking_product_ids"]').val();
                            var booking_ref_no = $(this).find('input[name="booking_ref_no"]').val();
                        } else {
                            toastr.error(result.msg);
                        }
                    }
                });
            });
        })
    </script>
</html>