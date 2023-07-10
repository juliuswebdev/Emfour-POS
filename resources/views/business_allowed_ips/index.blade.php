@extends('layouts.app')
@section('title', __('business_ip.business_ips'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('business_ip.business_ips')
        <small>@lang('business_ip.manage_your_business_ips')</small>
    </h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __('business_ip.all_your_business_ips')])
        @slot('tool')
            <div class="box-tools">
                <a class="btn btn-block btn-primary" href="{{action([\App\Http\Controllers\BusinessAllowedIPController::class, 'create'])}}">
                <i class="fa fa-plus"></i> @lang('business_ip.add_business_ip')</a>
            </div>
        @endslot
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="business_ips_table">
                <thead>
                    <tr>
                        <th style="width: 100px">@lang('business_ip.name')</th>
                        <th style="width: 200px">@lang('business_ip.register_number')</th>
                        <th style="width: 200px">@lang('business_ip.ip_address')</th>
                        <th style="width: 300px">@lang('business_ip.location')</th>
                        <th>@lang('business_ip.description')</th>
                        <th style="width: 100px">@lang('messages.action')</th>
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
        var business_ips_table = $('#business_ips_table').DataTable({
            processing: true,
            serverSide: true,
            buttons:[],
            ajax: '/modules/business-ips',
            columnDefs: [
                {
                    targets: 0,
                // orderable: false,
                    searchable: false,
                },
            ],
            aaSorting: [[1, 'desc']],
            columns: [
                { data: 'name', name: 'name' },
                { data: 'register_number', name: 'register_number' },
                { data: 'ip_address', ip_address: 'name' },
                { data: 'location', name: 'location' },
                { data: 'description', name: 'description' },
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
                                business_ips_table.ajax.reload();
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