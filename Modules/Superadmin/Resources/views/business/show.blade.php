@extends('layouts.app')
@section('title', __('superadmin::lang.superadmin') . ' | Business')

@section('content')
@include('superadmin::layouts.nav')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'superadmin::lang.view_business' )
        <small> {{$business->name}}</small>
    </h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">
	<div class="box box-solid">
        <div class="box-header">
                <h3 class="box-title">
                        <strong><i class="fa fa-briefcase margin-r-5"></i> 
                        {{ $business->name }}</strong>
                </h3>
        </div>

        <div class="box-body">
            <div class="row">
                    <div class="col-sm-3">
                        <div class="well well-sm">
                            <strong><i class="fa fa-briefcase margin-r-5"></i> 
                            @lang('business.business_name')</strong>
                            <p class="text-muted">
                                {{ $business->name }}
                            </p>

                            <strong><i class="fa fa-globe margin-r-5"></i> 
                                @lang('business.business_type')</strong>
                                <p class="text-muted">
                                    {{ ($business->business_type == null) ? '' : $business->business_type->title }}
                                </p>

                            <strong><i class="fa fa-money margin-r-5"></i> 
                            @lang('business.currency')</strong>
                            <p class="text-muted">
                                {{ $business->currency->currency }}
                            </p>

                            <strong><i class="fa fa-file-text-o margin-r-5"></i> 
                            @lang('business.tax_number1')</strong>
                            <p class="text-muted">
                                @if(!empty($business->tax_number_1))
                                    {{ $business->tax_label_1 }}: {{ $business->tax_number_1 }}
                                @endif
                            </p>

                            <strong><i class="fa fa-file-text-o margin-r-5"></i> 
                            @lang('business.tax_number2')</strong>
                            <p class="text-muted">
                                @if(!empty($business->tax_number_2))
                                {{ $business->tax_label_2 }}: {{ $business->tax_number_2 }}
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="well well-sm">
                            <strong><i class="fa fa-location-arrow margin-r-5"></i> 
                            @lang('business.time_zone')</strong>
                            <p class="text-muted">
                            {{ $business->time_zone }}
                            </p>

                            <strong><i class="fa fa-toggle-on margin-r-5"></i> 
                            @lang('business.is_active')</strong>
                            @if($business->is_active == 0)
                                <p class="text-muted">
                                    Inactive
                                </p>
                            @else
                                <p class="text-muted">
                                    Active
                                </p>
                            @endif

                            <strong><i class="fa fa-user-circle-o margin-r-5"></i> 
                            @lang('business.created_by')</strong>
                            @if(!empty($created_by))
                            <p class="text-muted">
                            {{$created_by->surname}} {{$created_by->first_name}} {{$created_by->last_name}}
                            </p>
                            @endif
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="well well-sm">
                            <strong><i class="fa fa-user-circle-o margin-r-5"></i> 
                            @lang('business.owner')</strong>
                            @if(!empty($business->owner))
                            <p class="text-muted">
                            {{$business->owner->surname}} {{$business->owner->first_name}} {{$business->owner->last_name}}
                            </p>
                            @endif

                            <strong><i class="fa fa-envelope margin-r-5"></i> 
                            @lang('business.email')</strong>
                            <p class="text-muted">
                            {{$business->owner->email}}
                            </p>

                            <strong><i class="fa fa-address-book-o margin-r-5"></i> 
                            @lang('business.mobile')</strong>
                            <p class="text-muted">
                            {{$business->owner->contact_no}}
                            </p>

                            <strong><i class="fa fa-map-marker margin-r-5"></i> 
                            @lang('business.address')</strong>
                            <p class="text-muted">
                            {{$business->owner->address}}
                            </p>
                            <strong><i class="fa fa-percent margin-r-5"></i> 
                            @lang('lang_v1.card_charge')</strong> {{$business->card_charge}}%
                          
                        </div>
                    </div>

                    <div class="col-sm-3">
                            <div>
                                @if(!empty($business->logo))
                                    <img class="img-responsive" src="{{ url( 'uploads/business_logos/' . $business->logo ) }}" alt="Business Logo">
                                @endif
                            </div>
                    </div> 
                </div> 
        </div>
    </div>

    <div class="box box-solid">
        <div class="box-header">
            <h3 class="box-title">@lang('business.business_settings')</h3>
        </div>
        <div class="box-body">
            {!! Form::open(['url' => action('\Modules\Superadmin\Http\Controllers\BusinessController@updateSettings', $business->id), 'method' => 'put' ]) !!}
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                    
                            {!! Form::label('card_charge', __('lang_v1.card_charge_percent') . ':') !!}
                            @if(auth()->user()->can('superadmin'))
                            {!! Form::text('card_charge', $business->card_charge, ['class' => 'form-control']); !!}
                            @else
                            {!! Form::text('card_charge', $business->card_charge, ['class' => 'form-control', 'readonly' => 'true']); !!}
                            @endif
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">
                    @lang('messages.update')
                </button>
                
            {!! Form::close() !!}
        </div>
    </div>

    <div class="box">
        <div class="box-header">
                <h3 class="box-title">
                    <strong><i class="fa fa-map-marker margin-r-5"></i> 
                    @lang( 'superadmin::lang.business_location' )</strong>
                </h3>
        </div>
        <div class="box-body">
                <div class="row">
                    <div class ="col-xs-12">
                    <!-- location table-->
                            <table class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Location ID</th>
                                    <th>Landmark</th>
                                    <th>city</th>
                                    <th>Zip Code</th>
                                    <th>State</th>
                                    <th>Country</th>
                                </tr>
                                </thead>
                                
                                <tbody>
                                    @foreach($business->locations as $location)
                                    <tr>
                                    <td>{{ $location->name }}</td>
                                    <td>{{ $location->location_id }}</td>
                                    <td>{{ $location->landmark }}</td>
                                    <td>{{ $location->city }}</td>
                                    <td>{{ $location->zip_code }}</td>
                                    <td>{{ $location->state }}</td>
                                    <td>{{ $location->country }}</td>
                                    </tr>
                                    @endforeach
                               
                                </tbody>
                            </table>
                    </div>
                </div>
        </div>
    </div>

    <div class="box">
        <div class="box-header">
                <h3 class="box-title">
                    <strong><i class="fa fa-refresh margin-r-5"></i> 
                    @lang( 'superadmin::lang.package_subscription' )</strong>
                </h3>
        </div>
        <div class="box-body">
                <div class="row">
                    <div class ="col-xs-12">
                    <!-- location table-->
                            <table class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>Package Name</th>
                                    <th>Start Date</th>
                                    <th>Trail End Date</th>
                                    <th>End Date</th>
                                    <th>Paid Via</th>
                                    <th>Payment Transaction ID</th>
                                    <th>Created At</th>
                                    <th>Created By</th>
                                </tr>
                                </thead>
                                
                                <tbody>
                                    @foreach($business->subscriptions as $subscription)
                                    <tr>
                                    <td>{{ $subscription->package_details['name'] }}</td>
                                    <td>@if(!empty($subscription->start_date)){{@format_date($subscription->start_date)}}@endif</td>
                                    <td>@if(!empty($subscription->trial_end_date)){{@format_date($subscription->trial_end_date)}}@endif</td>
                                    <td>@if(!empty($subscription->end_date)){{@format_date($subscription->end_date)}}@endif</td>
                                    <td>{{ $subscription->paid_via }}</td>
                                    <td>{{ $subscription->payment_transaction_id }}</td>
                                    <td>{{ $subscription->created_at }}</td>
                                    <td>@if(!empty($subscription->created_user)) {{$subscription->created_user->user_full_name}} @endif</td>
                                    </tr>
                                    @endforeach
                               
                                </tbody>
                            </table>
                    </div>
                </div>
        </div>
    </div>

    @component('components.widget', ['class' => 'box-default', 'title' => __( 'user.all_users' )])
        @can('user.view')
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="users_table">
                    <thead>
                        <tr>
                            <th>@lang( 'business.username' )</th>
                            <th>@lang( 'user.name' )</th>
                            <th>@lang( 'user.role' )</th>
                            <th>@lang( 'business.email' )</th>
                            <th>@lang( 'messages.action' )</th>
                        </tr>
                    </thead>
                </table>
            </div>
        @endcan
    @endcomponent

    <div class="box">
        <div class="box-header">
            <h3 class="box-title">
            @lang('lang_v1.enable_disable_modules')
            </h3>
        </div>
        <div class="box-body">
            
                {!! Form::open(['url' => action('\Modules\Superadmin\Http\Controllers\BusinessController@updateModules', $business->id), 'method' => 'put' ]) !!}
                    @php
                        $enabled_modules = $business->enabled_modules ?? [];
                    @endphp
                    @if(!empty($modules))
                        <div class="row">
                            @foreach($modules as $k => $v)
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <div class="checkbox" style="margin-top: 0">
                                        <label>
                                            {!! Form::checkbox('enabled_modules[]', $k,  in_array($k, $enabled_modules) , 
                                            ['class' => 'input-icheck']); !!} {{$v['name']}}
                                        </label>
                                        @if(!empty($v['tooltip'])) @show_tooltip($v['tooltip']) @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    <div class="row">
                    <div class="col-md-12"><hr></div>
                    </div>

                    @if(!empty($permissions))
                        <div class="row">
                            @foreach($permissions as $module => $module_permissions)
                                @foreach($module_permissions as $permission)
                                @php
                                    $custom_permissions = json_decode($subscription->custom_permissions_super_admin, true);  
                                    $value = isset($custom_permissions[$permission['name']]) ? $custom_permissions[$permission['name']] : false;
                                @endphp
                                <div class="col-sm-3">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox("custom_permissions[$permission[name]]", 1, $value, ['class' => 'input-icheck']); !!}
                                            {{$permission['label']}}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            @endforeach
                        </div>
                    @endif

                    @if(!empty($modules) || !empty($permissions))
                        <div class="col-sm-12">
                            <button type="submit" class="btn btn-primary" style="margin: 20px 5px 0 0 ">
                                @lang('messages.save')
                            </button>
                        </div>
                    @endif

                {!! Form::close() !!}

        </div>
    </div>

@include('superadmin::business.update_password_modal')
</section>
<!-- /.content -->
@stop
@section('javascript')
<script type="text/javascript">
    //Roles table
    $(document).ready( function(){
        var users_table = $('#users_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '/superadmin/users/' + "{{$business->id}}",
            columnDefs: [ {
                "targets": [4],
                "orderable": false,
                "searchable": false
            } ],
            "columns":[
                {"data":"username"},
                {"data":"full_name"},
                {"data":"role"},
                {"data":"email"},
                {"data":"action"}
            ]
        });
        
    });

    $(document).on('click', '.update_user_password', function (e) {
        e.preventDefault();
        $('form#password_update_form, #user_id').val($(this).data('user_id'));
        $('span#user_name').text($(this).data('user_name'));
        $('#update_password_modal').modal('show');
    });

    password_update_form_validator = $('form#password_update_form').validate();

    $('#update_password_modal').on('hidden.bs.modal', function() {
        password_update_form_validator.resetForm();
        $('form#password_update_form')[0].reset();
    });

    $(document).on('submit', 'form#password_update_form', function(e) {
        e.preventDefault();
        $(this)
            .find('button[type="submit"]')
            .attr('disabled', true);
        var data = $(this).serialize();
        $.ajax({
            method: 'post',
            url: $(this).attr('action'),
            dataType: 'json',
            data: data,
            success: function(result) {
                if (result.success == true) {
                    $('#update_password_modal').modal('hide');
                    toastr.success(result.msg);
                } else {
                    toastr.error(result.msg);
                }
                $('form#password_update_form')
                .find('button[type="submit"]')
                .attr('disabled', false);
            },
        });
    });

</script>      
@endsection
