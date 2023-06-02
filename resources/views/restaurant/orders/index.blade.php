@extends('layouts.restaurant')
@section('title', __( 'restaurant.orders' ))

@section('content')

<!-- Main content -->
<section class="content min-height-90hv no-print">
    <div class="row">
        <div class="col-md-12 text-center">
            <h3>@lang( 'restaurant.orders' ) @show_tooltip(__('lang_v1.tooltip_serviceorder'))</h3>
        </div>
        <div class="col-sm-12">
            <button type="button" class="btn btn-sm btn-primary pull-right" id="refresh_orders"><i class="fas fa-sync"></i> @lang( 'restaurant.refresh' )</button>
        </div>
    </div>
    <br>
    <div class="row">
    @if(!$is_service_staff)
        @component('components.widget')
            <div class="col-sm-6">
                {!! Form::open(['url' => action([\App\Http\Controllers\Restaurant\OrderController::class, 'index']), 'method' => 'get', 'id' => 'select_service_staff_form' ]) !!}
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user-secret"></i>
                        </span>
                        {!! Form::select('service_staff', $service_staff, request()->service_staff, ['class' => 'form-control select2', 'placeholder' => __('restaurant.select_service_staff'), 'id' => 'service_staff_id']); !!}
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        @endcomponent
    @endif
    @component('components.widget', ['title' => __( 'lang_v1.line_orders' )])
        <input type="hidden" id="orders_for" value="waiter">
        <div class="row" id="line_orders_div">
         @include('restaurant.partials.line_orders', array('orders_for' => 'waiter'))   
        </div>
        <div class="overlay hide">
          <i class="fas fa-sync fa-spin"></i>
        </div>
    @endcomponent

    @component('components.widget', ['title' => __( 'restaurant.all_your_orders' )])
        <input type="hidden" id="orders_for" value="waiter">
        <div class="row" id="orders_div">
         @include('restaurant.partials.show_orders', array('orders_for' => 'waiter'))   
        </div>
        <div class="overlay hide">
          <i class="fas fa-sync fa-spin"></i>
        </div>
    @endcomponent
    </div>
</section>
<!-- /.content -->
<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" id="pin_server_modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title">@lang('repair::lang.security')</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    {!! Form::open(['action' => '\App\Http\Controllers\Restaurant\OrderController@userCheckPin', 'id' => 'check_user_pin', 'method' => 'post']) !!}
                        <input id="user_id" name="user_id" type="hidden">
                        {!! Form::label('pin', __('business.digits_pin') . ':') !!}
                        <input type="password" name="pin" id="pin" class="form-control" placeholder="{{  __('business.digits_pin') }}">
                        <br>
                        <button type="submit" class="btn btn-primary">@lang( 'messages.submit' )</button>
                    {!! Form::close() !!}
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

@endsection

@section('javascript')
    <script type="text/javascript">
        $('select#service_staff_id').change( function(){
            $('form#select_service_staff_form').submit();
        });
        $(document).ready(function(){

            $(document).on('click', 'a.mark_as_served_btn', function(e){
                e.preventDefault();
                var _this = $(this);
                var href = _this.data('href');
                $('#check_user_pin').attr('mark-as-serve-url', href);
                const urlParams = new URL(window.location.href).searchParams;
                const service_staff = urlParams.get('service_staff');
                $('#check_user_pin #user_id').val(service_staff);
                $.ajax({
                    context: this,
                    method: "POST",
                    url: '/user/check-has-pin',
                    data: { user_id : service_staff },
                    dataType: "json",
                    success: function(result) {
                        if(result.success == true) {
                            $('#pin_server_modal').modal('show');
                        } else {
                            toastr.error(result.msg);
                        }
                    }
                });
            });

            $('#check_user_pin').submit(function(e) {
                e.preventDefault();
                var data = $(this).serialize();
                $.ajax({
                    context: this,
                    method: "POST",
                    url: $(this).attr("action"),
                    data: data,
                    dataType: "json",
                    success: function(result) {
                        if(result.success == true) {
                            toastr.success(result.msg);
                            var href = $(this).attr('mark-as-serve-url');
                            $.ajax({
                                method: "GET",
                                url: href,
                                dataType: "json",
                                success: function(result){
                                    if(result.success == true){
                                        refresh_orders();
                                        toastr.success(result.msg);
                                        $('#pin_server_modal').modal('hide');
                                    } else {
                                        toastr.error(result.msg);
                                    }
                                }
                            });
                        } else {
                            toastr.error(result.msg);
                        }
                        $('#check_user_pin #pin').val('');
                        $('#check_user_pin button').removeAttr('disabled');
                    }
                });
            });

            //Function used for update the is_served column
            $(document).on('click', 'a.btn-served', function(e){
                e.preventDefault();
                var href = $(this).data('href');
                $.ajax({
                    method: "GET",
                    url: href,
                    dataType: "json",
                    success: function(result){
                        if(result.success == true){
                            toastr.success(result.msg);
                            $('#refresh_orders').click();
                        } else {
                            toastr.error(result.msg);
                        }
                    }
                });
            });

            $(document).on('click', 'a.mark_line_order_as_served', function(e){
                e.preventDefault();
                swal({
                  title: LANG.sure,
                  icon: "info",
                  buttons: true,
                }).then((sure) => {
                    if (sure) {
                        var _this = $(this);
                        var href = _this.attr('href');
                        $.ajax({
                            method: "GET",
                            url: href,
                            dataType: "json",
                            success: function(result){
                                if(result.success == true){
                                    refresh_orders();
                                    toastr.success(result.msg);
                                } else {
                                    toastr.error(result.msg);
                                }
                            }
                        });
                    }
                });
            });

            $('.print_line_order').click( function(){
                let data = {
                    'line_id' : $(this).data('id'),
                    'service_staff_id' : $("#service_staff_id").val()
                };
                $.ajax({
                    method: "GET",
                    url: '/modules/print-line-order',
                    dataType: "json",
                    data: data,
                    success: function(result){
                        if (result.success == 1 && result.html_content != '') {
                            $('#receipt_section').html(result.html_content);
                            __print_receipt('receipt_section');
                        } else {
                            toastr.error(result.msg);
                        }
                    }
                });
            });
        });

       
        setInterval(function(){
            $('.blink-allow').fadeIn(1000).fadeOut(1000);
        },0)

    </script>
@endsection