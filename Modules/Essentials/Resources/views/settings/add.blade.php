@extends('layouts.app')
@section('title', (request()->segment(1) == 'essentials') ? __('lang_v1.essentials_settings') : __('lang_v1.hrm_settings') )

@section('content')

@if(request()->segment(1) == 'essentials' && request()->segment(2) == 'settings')
@include('essentials::layouts.nav_essentials')
@else
@include('essentials::layouts.nav_hrm')
@endif

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ (request()->segment(1) == 'essentials') ? __('lang_v1.essentials_settings') : __('lang_v1.hrm_settings') }}</h1>
</section>

<!-- Main content -->
<section class="content hrm-essentials">
    {!! Form::open(['action' => '\Modules\Essentials\Http\Controllers\EssentialsSettingsController@update', 'method' => 'post', 'id' => 'essentials_settings_form']) !!}
    <div class="row">
        <div class="col-xs-12">
           <!--  <pos-tab-container> -->
            <div class="col-xs-12 pos-tab-container">
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 pos-tab-menu">
                    <div class="list-group">
                        @if(request()->segment(1) == 'essentials')
                            <a href="#" class="list-group-item text-center active">@lang('essentials::lang.essentials')</a>
                        @else
                            <a href="#" class="list-group-item text-center active">@lang('essentials::lang.leave')</a>
                            <a href="#" class="list-group-item text-center">@lang('essentials::lang.payroll')</a>
                            <a href="#" class="list-group-item text-center">@lang('essentials::lang.attendance')</a>
                            <a href="#" class="list-group-item text-center">@lang('essentials::lang.sales_target')</a>
                        @endif    
                    </div>
                </div>
                @if(request()->segment(1) == 'essentials')
                <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 pos-tab">
                    @include('essentials::settings.partials.essentials_settings')
                </div>
                @else
                <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 pos-tab">
                    @include('essentials::settings.partials.leave_settings')
                </div>
                <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 pos-tab">
                    @include('essentials::settings.partials.payroll_settings')
                </div>
                <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 pos-tab">
                    @include('essentials::settings.partials.attendance_settings')
                </div>
                <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 pos-tab">
                    @include('essentials::settings.partials.sales_target_settings')
                </div>
                @endif
            </div>

            <!--  </pos-tab-container> -->
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="form-group pull-right">
            {{Form::submit(__('messages.update'), ['class'=>"btn btn-danger"])}}
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</section>
@stop
@section('javascript')
<script type="text/javascript">
    $(document).ready( function () {
        tinymce.init({
            selector: 'textarea#leave_instructions',
        });

        $('#essentials_settings_form').validate({ 
            ignore: [],
        });
    });
</script>
@endsection