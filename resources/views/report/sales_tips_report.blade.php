@extends('layouts.app')
@section('title', __('restaurant.sales_tips_report'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('restaurant.sales_tips_report')}}</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('ssr_location_id',  __('purchase.business_location') . ':') !!}
                        {!! Form::select('ssr_location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('service_staff_id',  __('restaurant.service_staff') . ':') !!}
                        {!! Form::select('service_staff_id', $waiters, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('ssr_date_range', __('report.date_range') . ':') !!}
                        {!! Form::text('date_range', @format_date('first day of this month') . ' ~ ' . @format_date('last day of this month'), ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'ssr_date_range', 'readonly']); !!}
                    </div>
                </div>
            @endcomponent
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#ss_tips_tab" data-toggle="tab" aria-expanded="true">@lang('restaurant.sales_tips_report')</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="ss_tips_tab">
                        @include('report.partials.sales_tips_orders_table')
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->

@endsection

@section('javascript')
    
    <script type="text/javascript">
        $(document).ready(function(){
            if($('#ssr_date_range').length == 1){
                $('#ssr_date_range').daterangepicker({
                    ranges: ranges,
                    autoUpdateInput: false,
                    startDate: moment().startOf('month'),
                    endDate: moment().endOf('month'),
                    locale: {
                        format: moment_date_format
                    }
                });
                $('#ssr_date_range').on('apply.daterangepicker', function(ev, picker) {
                    $(this).val(picker.startDate.format(moment_date_format) + ' ~ ' + picker.endDate.format(moment_date_format));
                    sales_tips_report.ajax.reload();
                });

                $('#ssr_date_range').on('cancel.daterangepicker', function(ev, picker) {
                    $(this).val('');
                    sales_tips_report.ajax.reload();
                });
            }

        sales_tips_report = $('table#sales_tips_report').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            "ajax": {
                "url": "/sells",
                "data": function ( d ) {
                    var start = $('input#ssr_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var end = $('input#ssr_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');

                    d.list_for = 'sales_tips_report';
                    d.location_id = $('select#ssr_location_id').val();
                    d.start_date = start;
                    d.end_date = end;
                    d.res_waiter_id = $('select#service_staff_id').val();
                }
            },
            columns: [
                { data: 'transaction_date', name: 'transaction_date'  },
                { data: 'invoice_no', name: 'invoice_no'},
                { data: 'waiter', name: 'ss.first_name'},
                { data: 'business_location', name: 'bl.name'},
                { data: 'total_before_tax', name: 'transactions.total_before_tax'},
                { data: 'discount_amount', name: 'transactions.discount_amount'},
                { data: 'tax_amount', name: 'transactions.tax_amount'},
                { data: 'final_total', name: 'final_total'},
                { data: 'tips_amount', name: 'tips_amount'}
            ],
            columnDefs: [
                    {
                        'searchable'    : false, 
                        'targets'       : [4, 5, 6] 
                    },
                ],
            "fnDrawCallback": function (oSettings) {
                $('#footer_total_amount').text(sum_table_col($('#sales_tips_report'), 'final-total'));
                $('#footer_subtotal').text(sum_table_col($('#sales_tips_report'), 'total_before_tax'));
                $('#footer_total_tax').text(sum_table_col($('#sales_tips_report'), 'total-tax'));
                $('#footer_total_discount').text(sum_table_col($('#sales_tips_report'), 'total-discount'));
                $('#footer_tip_amount').text(sum_table_col($('#sales_tips_report'), 'tip-amount'));
                
                __currency_convert_recursively($('#sales_tips_report'));
            }
        });


            
        //Customer Group report filter
        $('select#ssr_location_id, #ssr_date_range, #service_staff_id').change( function(){
            sales_tips_report.ajax.reload();
        });
    })
    </script>
@endsection