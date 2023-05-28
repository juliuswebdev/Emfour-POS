@extends('layouts.app')
@section('title', __('payment_device.payment_devices'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('payment_device.payment_devices')
        <small>@lang('payment_device.manage_your_payment_devices')</small>
    </h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __('payment_device.all_your_payment_device')])
        @slot('tool')
            <div class="box-tools">
                <a class="btn btn-block btn-primary" href="{{action([\App\Http\Controllers\PaymentDevicesController::class, 'create'])}}">
                <i class="fa fa-plus"></i> @lang('payment_device.add_payment_device')</a>
            </div>
        @endslot
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="payment_device_table">
                <thead>
                    <tr>
                        <th style="width: 100px">@lang('payment_device.name')</th>
                        <th style="width: 150px">@lang('payment_device.location')</th>
                        <th style="width: 200px">@lang('payment_device.device_model')</th>
                        <th>@lang('payment_device.settings')</th>
                        <th>@lang('messages.action')</th>
                    </tr>
                </thead>
            </table>
        </div>
    @endcomponent
</section>
<!-- /.content -->
@stop
@section('javascript')
<script type="text/javascript">
    $(document).ready( function(){
        var payment_device_table = $('#payment_device_table').DataTable({
            processing: true,
            serverSide: true,
            buttons:[],
            ajax: '/modules/payment-devices',
            columnDefs: [
                {
                    targets: 0,
                // orderable: false,
                    searchable: false,
                },
            ],
            aaSorting: [[1, 'desc']],
            columns: [
                { data: 'payment_device_name', name: 'payment_device_name' },
                { data: 'business_location_name', name: 'business_location_name' },
                { data: 'payment_device_model_name', name: 'payment_device_model_name' },
                { data: 'payment_device_settings', name: 'payment_device_settings' },
                { data: 'action', name: 'action' },
            ]
        });
        $(document).on('click', 'button.delete_printer_button', function(){
            swal({
              title: LANG.sure,
              text: LANG.confirm_delete_printer,
              icon: "warning",
              buttons: true,
              dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    var href = $(this).data('href');
                    var data = $(this).serialize();

                    $.ajax({
                        method: "DELETE",
                        url: href,
                        dataType: "json",
                        data: data,
                        success: function(result){
                            if(result.success === true){
                                toastr.success(result.msg);
                                payment_device_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                }
            });
        });
    });
</script>
@endsection