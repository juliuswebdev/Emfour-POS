<html>
    <head>
        <title>Clock-in Clock-Out | {{ $business->name }}</title>
        <link rel="stylesheet" href="{{ asset('css/vendor.css?v='.$asset_v) }}">
        @if( in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) )
            <link rel="stylesheet" href="{{ asset('css/rtl.css?v='.$asset_v) }}">
        @endif

        @yield('css')
        <!-- app css -->
        <link rel="stylesheet" href="{{ asset('css/app.css?v='.$asset_v) }}">
        <style>
            body {
                background: darkturquoise;
            }
            .header {
                margin: 30px 0 10px;
                text-align: center;
            }
            .header img {
                max-width: 300px;
            }
            #clock_in_clock_out_modal .mf-input-wrapper {
                max-width: 400px;
                margin: 30px auto 0;
                padding: 0!important;
            }
            .cl-co-type-choose {
                display: none;
            }
        </style>
    </head>
    <body>
        <div class="container" id="clock_in_clock_out_modal">
                <div>
                    <div class="header">
                        @if($business->logo)
                        <img src="{{ asset('uploads/business_logos') }}/{{ $business->logo }}">
                        @else
                        <img src="{{ asset('img') }}/default.png" alt="Color-Correction">
                        @endif
                    </div>
                    {!! Form::open(['url' => action([\Modules\Essentials\Http\Controllers\AttendanceController::class, 'clockInClockOut']), 'method' => 'post', 'id' => 'clock_in_clock_out_form' ]) !!}
                    <div class="modal-body mf-clockin-clockout">
                        <div class="row">
                            <div class="col-md-12 ">
                                <div class="text-center">
                                    <h2 class="mf-title font-weight-bold">
                                        {{ $business->name }}
                                    </h2>
                                    <span class="font-weight-bold">
                                        @lang( 'lang_v1.label_cico')
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="mf-input-wrapper">
                            <div class="row mf-input-box">
                                <div class="col-md-12">
                                    <div class="text-center">
                
                                        <div class="form-group">
                                            {!! Form::label('pin', __('lang_v1.enter_security_pin') . ':') !!}
                                            <input minlength="4" maxlenght="9" type="password" name="user_pin" id="user_pin" class=" input-pin form-control" placeholder="{{  __('lang_v1.enter_security_pin') }}" required>
                                        </div>
                
                                    </div>
                                </div>
                            </div>
                            <div class="row cl-co-keyboard">
                                <div class="col-md-12">
                                    <div class="grid-container">
                                        @for ($i=1; $i<=9;$i++)
                                        <div class="grid-item cl-key-input" data-value="{{ $i }}">
                                            <div class="circle">
                                                <span>{{ $i }}</span>
                                            </div>
                                        </div>	
                                        @endfor
                                        <div class="grid-item cl-key-input" data-value="0">
                                            <div class="circle">
                                                <span>0</span>
                                            </div>
                                        </div>	
                                        <div class="grid-item cl-key-input" data-value="CANCEL">
                                            <button class="btn btn-danger text-black">CANCEL</button>
                                        </div>
                                        <div class="grid-item cl-key-input" data-value="ENTER">
                                            <button class="btn btn-success text-black">ENTER</button>	
                                        </div>  
                                    </div>
                                </div>
                            </div>


                            <div class="row cl-co-type-choose">
                                <div class="col-md-12 text-center">
                                    <div>
                                        <h2 class="mf-title font-weight-bold ci-co-user"></h2>
                                    </div>
                                    <div>
                                        <span class="font-weight-bold ci-co-user-role"></span>
                                    </div>
                                    <div>
                                        <span class="font-weight-bold ci-co-business"></span>
                                    </div>

                                    <div class="cl-co-action">
                                        <div>
                                            <button class="btn btn-success cl-co-btn-action" data-action="clock_in" type="button">Clock In</button>
                                        </div>
                                        <div>
                                            <button class="btn btn-warning cl-co-btn-action" data-action="clock_out" type="button">Clock Out</button>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <input type="hidden" name="cico_action" id="cico_action" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>

        </div>
    </body>

   
	<script src="{{ asset('js/auto_height.js?v=' . $asset_v) }}"></script>
    <!-- <script src="{{ asset('js/pos.js?v=' . date('yymmddhhiiss')) }}"></script>    -->
    <script src="{{ asset('js/vendor.js?v=' . $asset_v) }}"></script>

    @if(file_exists(public_path('js/lang/' . session()->get('user.language', config('app.locale')) . '.js')))
        <script src="{{ asset('js/lang/' . session()->get('user.language', config('app.locale') ) . '.js?v=' . $asset_v) }}"></script>
    @else
        <script src="{{ asset('js/lang/en.js?v=' . $asset_v) }}"></script>
    @endif

    <script>
        $(document).ready(function(){
          
            $(document).on('click', '.clock_in_btn:not(:disabled), .clock_out_btn:not(:disabled)', function() {
                var type = $(this).data('type');
                if (type == 'clock_in') {
                    $('#clock_in_clock_out_modal').find('#clock_in_text').removeClass('hide');
                    $('#clock_in_clock_out_modal').find('#clock_out_text').addClass('hide');
                    $('#clock_in_clock_out_modal').find('.clock_in_note').removeClass('hide');
                    $('#clock_in_clock_out_modal').find('.clock_out_note').addClass('hide');
                } else if (type == 'clock_out') {
                    $('#clock_in_clock_out_modal').find('#clock_in_text').addClass('hide');
                    $('#clock_in_clock_out_modal').find('#clock_out_text').removeClass('hide');
                    $('#clock_in_clock_out_modal').find('.clock_in_note').addClass('hide');
                    $('#clock_in_clock_out_modal').find('.clock_out_note').removeClass('hide');
                }
                $('#clock_in_clock_out_modal').find('input#type').val(type);

                $('#clock_in_clock_out_modal').modal('show');

            });

            //Clock in Clockout ReWrite by P
            $(document).on('submit', 'form#clock_in_clock_out_form', function(e) {
                e.preventDefault();
                $(this).find('button[type="submit"]').attr('disabled', true);
                var data = $(this).serialize();
                var cico_action = $('#cico_action').val();
                $.ajax({
                    method: $(this).attr('method'),
                    url: $(this).attr('action'),
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        
                        if (result.success == true){
                            if(cico_action == ""){
                                $('.cl-co-keyboard, .mf-input-box').hide();
                                $('.cl-co-type-choose').show();
                                
                                $('.ci-co-user').text(result.data.first_name+' '+result.data.last_name);
                                $('.ci-co-user-role').text(result.data.role);
                                $('.ci-co-business').text(result.data.business.name);
                            }else{
                                $('#clock_in_clock_out_modal').modal('hide');
                                const wrapper = document.createElement('div');
                                wrapper.innerHTML = "<div>"+result.current_shift+"</div>";
                                swal({ 
                                    icon: 'success', 
                                    html:true, 
                                    title:result.msg, 
                                    content: wrapper, 
                                });
                            }
                        }else{
                            if(cico_action == ""){
                                toastr.error(result.msg);
                            }else{
                                swal({ 
                                    icon: 'error', 
                                    html:true, 
                                    title:result.msg, 
                                });
                            }
                        }

                    },
                });
            });

            //CICO btn action
            $('body').on('click', 'button.cl-co-btn-action', function() {
                var action = $(this).data('action');
                $('#cico_action').val(action);
                $('#clock_in_clock_out_form').submit();
            });

            $('body').on('click', '.cl-key-input', function() {
                var input = $(this).data('value');
                var current_val = $('#user_pin').val();
                if(input == "CANCEL"){
                    $('#user_pin').val('');
                }else if(input == "ENTER"){
                    //Submit to server
                    var total_length = current_val.length;
                    if(current_val == ""){
                        toastr.error("Please enter the security pin.");
                    }

                }else{
                    current_val += input
                    $('#user_pin').val(current_val);
                }
            });

        });
    </script>


</html>