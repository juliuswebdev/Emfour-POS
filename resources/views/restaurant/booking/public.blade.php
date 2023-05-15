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
            .body-booking {
                margin-top: 50px;
            }
            * {
                font-size: 16px;
            }
            img {
                width: 100%;
                height: auto;
            }
            ul {
                padding-left: 0;
            }
            ul li {
                
                list-style-type: none;
                margin-bottom: 10px;
            }
            label {
                font-weight: 400;
                margin-bottom: 0;
            }
            .input-group {
                width: 100%;
                position: relative;
                padding-bottom: 10px;
            }
            label.error {
                font-size: 12px;
                position: absolute;
                left: 0;
                bottom: -8px;
                margin-bottom: 0;
            }
            .col-md-12,
            .col-md-6 {
                position: unset;
                margin-bottom: 10px;
            }
            label[id="services[]-error"],
            #location_id-error,
            #staff-error {
                bottom: unset;
                top: -20px;
            }
            #tab2 ul li,
            #tab3 ul li {
                list-style-type: disc;
            }
            .form img,
            #tab3 img {
                width: 35px;
                height: 35px;
                margin-right: 10px;
            }
            .tab-title {
                margin-bottom: 20px;
            }
            .sidenav {
                margin-top: 30px;
            }
            .sidenav li a {
                padding: 14px 10px;
                display: block;
                font-size: 16px;
                line-height: 1;
            }
            .sidenav li a.active {
                color: #fff;
                background-color: #2b80ec;
            }
            .sidenav li a strong {
                width: 30px;
                display: inline-block;
            }
            .card {
                padding: 40px;
                box-shadow: 0 12px 8px 0 rgba(22,45,61,.04), 0 1px 4px 0 rgba(22,45,61,.1);
                border-radius: 6px;
            }
            .card .tab {
                display: none;
            }
            .card .tab.active {
                display: block;
            }
            .form input[type="radio"],
            .form input[type="checkbox"] {
                margin-right: 10px;
            }
            .form > .form-group {
                margin-bottom: 30px;
            }
            .tab-title {
                padding: 10px 20px;
                color: #fff;
                background-color: #2b80ec;
                background-image: linear-gradient(to right,#2b80ec,#1d1f33);
            }
            span.required {
                color: red;
            }
            .ref_no {
                font-size: 20px;
                font-weight: 700;
            }
            @media (min-width: 1200px) {
                .container {
                    width: 1470px;
                }
            }
        </style>
    </head>
    <body class="body-booking">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    @if($business->logo)
                    <img src="{{ asset('uploads/business_logos') }}/{{ $business->logo }}">
                    @else
                    <img src="{{ asset('img') }}/default.png" alt="Color-Correction">
                    @endif
                    <h3 class="text-center">{{ $business->name }}</h3>
                    <ul class="sidenav">
                        <li><a href="#tab1" class="tab-link active"><strong><i class="fa fa-calendar margin-r-5"></i></strong>Booking</a></li>
                        <li><a href="#tab2" class="tab-link"><strong><i class="fa fa-users margin-r-5"></i></strong>Staff</a></li>
                        <li><a href="#tab3" class="tab-link"><strong><i class="fa fa-file margin-r-5"></i></strong>Services</a></li>
                        <li><a href="#tab4" class="tab-link"><strong><i class="fa fa-marker margin-r-5"></i></strong>Business Locations</a></li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="tab active" id="tab1">
                            <p><small>
                                <strong>Our Booking Policy</strong><br>
                                Booking an appointment with us gives you an advantage of having a sure slot and a less to no waiting time once you arrive at the shop.<br><br></small>
                            </p>
                         
                            @if(\Session::has('status'))
                                @if(\Session::get('status') == true)
                                    @php
                                        $booking = \Session::get('booking');
                                        $booking_details = \Session::get('booking_details');
                                        $time_details = \Session::get('time_details');
                                        $user = App\User::find($booking->correspondent_id);
                                        
                                    
                                        $business_location = App\BusinessLocation::find($booking->location_id);
                                        
                                    @endphp

                                    <h2>Thanks for Booking!</h2>
                                    <p><strong>Reference Number: </strong><span class="ref_no">{{ $booking_details->ref_no }}</span></p>
                                    <p><strong>Full Name: </strong>{{ $booking_details->full_name ?? 'N/A' }}</p>
                                    <p><strong>Phone: </strong>{{ $booking_details->phone ?? 'N/A' }}</p>
                                    <p><strong>Email: </strong>{{ $booking_details->email ?? 'N/A' }}</p>
                                    <p><strong>Staff: </strong>{{ $user->first_name ?? '' }} {{ $user->last_name ?? '' }}</p>
                                    <p><strong>Services: </strong>{{ \Session::get('services') }}</p>
                                    <p><strong>Business Location: </strong>{{ $business_location->name ?? '' }} <small>({{ $business_location->city ?? '' }} {{ $business_location->state ?? '' }} {{ $business_location->country ?? '' }})</small></p>
                                    <p><strong>Time: </strong>{{ $time_details }}</p>
                                    <p><strong>Note: </strong>{{ $booking->booking_note ?? '' }}</p>
                                
                                @else 
                                Something wrong! Please contact business location.
                                @endif
                            @else
                            {!! Form::open(['url' => action([\App\Http\Controllers\Restaurant\BookingController::class, 'postPublicBooking']), 'class'=>'form', 'method' => 'post', 'id' => 'add_booking_form' ]) !!}
                              
                                <div class="form-group service-checkbox required">
                                    <h4 class="tab-title">Your Service <span class="required">*</span></h4>
                                    @if(count($services))
                                    <div class="input-group">
                                        <div class="row">
                                            @foreach($services as  $service)
                                                @foreach($service->variations as $variation)
                                                <div class="col-md-12">
                                                        <input type="checkbox" name="services[]" value="{{$variation->id}}">
                                                        <label>
                                                            <img src="{{ $service->image_url }}">
                                                            {{ $service->name }} @if($variation->name != 'DUMMY')[{{ $variation->name }}]@endif
                                                            <small>
                                                                (
                                                                    {{ $business->currency->symbol }} 
                                                                    {{round($variation->default_sell_price, 2) }}
                                                                )
                                                            </small>
                                                        </label>
                                                </div>
                                                @endforeach

                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <h4 class="tab-title">Your Staff <span class="required">*</span></h4>
                                    @if(count($services))
                                    <div class="input-group">
                                        <div class="row">
                                            @foreach($correspondents as $key => $item)
                                            <div class="col-md-6">
                                                    <input type="radio" name="staff" value="{{$key}}">
                                                    <label>{{ $item }}</label>
                                            </div>
                                            @endforeach
                                            <div class="col-md-6">
                                                    <input type="radio" name="staff" value="0">
                                                    <label>Undecided</label>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <h4 class="tab-title">Business Location <span class="required">*</span></h4>
                                    @if(count($business_locations))
                                    <div class="input-group">
                                        <ul>
                                            @foreach($business_locations as $key => $item)  
                                            <li>
                                                <input type="radio" name="location_id" value="{{$item->id}}">
                                                <label>{{ $item->name }} <small>({{ $item->city ?? '' }} {{ $item->state ?? '' }} {{ $item->country ?? '' }})</small></label>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <h4 class="tab-title">Date and Time</h4>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="status">Start time:<span class="required">*</span></label>
                                            <div class='input-group date' >
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                                <input class="form-control" placeholder="Start time"  id="start_time" readonly name="booking_start" type="text">
                                            </div>
                                        </div>
                                        <div class="col-md-4">                                    
                                            <label for="status">End time:<span class="required">*</span></label>
                                            <div class="input-group date">
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                                <input class="form-control" placeholder="End time"  id="end_time" readonly="" name="booking_end" type="text" aria-required="true">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="status">Time:<span class="required">*</span></label>
                                            <div class="input-group date">
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                                <input class="form-control" placeholder="Time"  id="time" readonly="" name="time" type="text" aria-required="true">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <h4 class="tab-title">Your Information</h4>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group input-group">
                                                <label>First Name:<span class="required">*</span></label>
                                                <input type="text" name="first_name" class="form-control" >
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group input-group">
                                                <label>Last Name:<span class="required">*</span></label>
                                                <input type="text" name="last_name" class="form-control" >
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group input-group">
                                                <label>Your Phone:<span class="required">*</span></label>
                                                <input type="text" name="phone" class="form-control" >
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group input-group">
                                                <label>Your Email:<span class="required">*</span></label>
                                                <input type="email" name="email" class="form-control" >
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group input-group">
                                                <label>Note:<span class="required">*</span></label>
                                                <textarea name="booking_note" class="form-control" ></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Book</button>
                            {!! Form::close() !!}
                            @endif
                            
                        </div>
                        <div class="tab" id="tab2">
                            <h4 class="tab-title">Staffs</h4>
                            @if(count($correspondents))
                                <ul style="padding-left: 30px">
                                    @foreach($correspondents as $key => $item)
                                    <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                        <div class="tab" id="tab3">
                            <h4 class="tab-title">Services</h4>
                            @if(count($services))
                                <div class="row">
                                    @foreach($services as $key => $item)
                                    <div class="col-md-6"><img src="{{ $item->image_url }}" alt="{{ $item->name }}">{{ $item->name }}</div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="tab" id="tab4">
                            <h4 class="tab-title">Business Locations</h4>
                            @if(count($business_locations))
                                <div class="row">
                                    @foreach($business_locations as $key => $item)
                                    <div class="col-md-6">
                                        <h4>{{ $item->name }}</h4>
                                        <strong><i class="fa fa-map-marker margin-r-5"></i>Address:</strong>
                                        <p class="text-muted">{{ $item->city ?? '' }} {{ $item->state ?? '' }} {{ $item->country ?? '' }}</p>
                                    </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <h4>Contact Info</h4>
                    <div>
                        <strong><i class="fa fa-map-marker margin-r-5"></i>Address:</strong>
                        <p class="text-muted">{{ $business_info->city ?? '' }} {{ $business_info->state ?? '' }} {{ $business_info->country ?? '' }}</p>
                    </div>
                    <div>
                        <strong><i class="fa fa-envelope margin-r-5"></i>Email</strong>
                        <p class="text-muted"><a href="mailto:{{ $business->owner->email ?? '' }}">{{ $business->owner->email ?? '' }}</a></p>
                    </div>
                    <div>
                        <strong><i class="fa fa-phone margin-r-5"></i>Mobile</strong>
                        <p class="text-muted"><a href="tel:{{ $business_info->mobile ?? '' }}">{{ $business_info->mobile ?? '' }}</a></p>
                    </div>
                    <div>
                        <strong><i class="fa fa-globe margin-r-5"></i>Website</strong>
                        <p class="text-muted"><a href="{{ $business_info->website ?? '' }}" target="_blank">{{ $business_info->website ?? '' }}</a></p>
                    </div>
                    <div>
                        <strong><i class="fa fa-landmark margin-r-5"></i>Landmark</strong>
                        <p class="text-muted">{{ $business_info->landmark ?? '' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </body>
    
    <script src="{{ asset('js/vendor.js?v=' . $asset_v) }}"></script>

    @if(file_exists(public_path('js/lang/' . session()->get('user.language', config('app.locale')) . '.js')))
        <script src="{{ asset('js/lang/' . session()->get('user.language', config('app.locale') ) . '.js?v=' . $asset_v) }}"></script>
    @else
        <script src="{{ asset('js/lang/en.js?v=' . $asset_v) }}"></script>
    @endif
    <script type="text/javascript">
        var datepicker_date_format = "mm/dd/yyyy";
        var moment_date_format = "MM/DD/YYYY";
        var moment_time_format = "HH:mm";
        var moment_12_hour_format = "hh:mm a";
    </script>
    <script type="text/javascript">
        $(document).ready( function(){
            $('.tab-link').click(function(e) {
                e.preventDefault();
                var id = $(this).attr('href');
                $(this).addClass('active').parent().siblings().find('a').removeClass('active');
                $(id).addClass('active').siblings().removeClass('active');
            });

            $('#time').datetimepicker({
                format: moment_12_hour_format,
                minDate: moment(),
                ignoreReadonly: true,
            });
       
            $('#start_time').datetimepicker({
                format: moment_date_format + ' ' +moment_time_format,
                minDate: moment(),
                ignoreReadonly: true
            });
            
            $('#end_time').datetimepicker({
                format: moment_date_format + ' ' +moment_time_format,
                minDate: moment(),
                ignoreReadonly: true,
            });

            $('#add_booking_form').validate({ // initialize the plugin
                rules: {
                    'services[]': {
                        required: true,
                    },
                    'staff' : {
                        required: true
                    },
                    'location_id' : {
                        required: true
                    },
                    'booking_start' : {
                        required: true
                    },
                    'booking_end' : {
                        required: true
                    },
                    'time' : {
                        required: true
                    },
                    'first_name' : {
                        required: true
                    },
                    'last_name' : {
                        required: true
                    },
                    'phone' : {
                        required: true
                    },
                    'email' : {
                        required: true
                    },
                    'booking_note' : {
                        required: true
                    }
                },
                messages: {
                    'services[]': {
                        required: "You must check at least 1 box",
                    }
                }
            });

        });
    </script>

</html>