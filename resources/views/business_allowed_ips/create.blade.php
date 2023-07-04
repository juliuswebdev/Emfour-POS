@extends('layouts.app')
@section('title',  __('business_ip.add_business_ip'))

@section('content')
<style type="text/css">



</style>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('business_ip.add_business_ip')</h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">
{!! Form::open(['url' => action([\App\Http\Controllers\BusinessAllowedIPController::class, 'store']), 'method' => 'post', 'id' => 'add_business_ip_form' ]) !!}
	<div class="box box-solid">
    <div class="box-body">
      <div class="row">
        <div class="col-sm-12">
          <div class="form-group">
            {!! Form::label('name', __('business_ip.name') . ':*') !!}
              {!! Form::text('name', null, ['class' => 'form-control', 'required',
              'placeholder' => __('business_ip.name')]); !!}
          </div>
        </div>
        <div class="col-sm-12">
          <div class="form-group">
            {!! Form::label('ip_address', __('business_ip.ip_address') . ':*') !!}
              {!! Form::text('ip_address', null, ['class' => 'form-control', 'required',
              'placeholder' => 'ex. 192.168.1.1']); !!}
          </div>
        </div>
        <div class="col-sm-12">
          <div class="form-group">
            {!! Form::label('locations',__('business_ip.locations') . ':*') !!}
            <select name="location_id" class="form-control select2" required>
                <option value="">@lang('business_ip.select_business_locations')</option>
                @foreach($business_locations as $item)
                <option value="{{ $item->id }}">{{ $item->name }} - {{ $item->location_id }}</option>
                @endforeach
            </select>
          </div>
        </div>
        <div class="col-sm-12">
          <div class="form-group">
            {!! Form::label('description', __('business_ip.description') . ':') !!}
              {!! Form::textarea('description', null, ['class' => 'form-control',
              'placeholder' => __('business_ip.description')]); !!}
          </div>
        </div>

        <div class="col-sm-12">
          <button type="submit" class="btn btn-primary pull-right">@lang('messages.save')</button>
        </div>
      </div>
    </div>
  </div>
  {!! Form::close() !!}
</section>
<!-- /.content -->
@stop
