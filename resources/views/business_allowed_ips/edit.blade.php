@extends('layouts.app')
@section('title',  __('business_ip.edit_business_ip'))

@section('content')
<style type="text/css">



</style>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('business_ip.edit_business_ip')</h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">
{!! Form::open(['url' => action([\App\Http\Controllers\BusinessAllowedIPController::class, 'update'], [$business_ip->id]), 'method' => 'put', 'id' => 'edit_payment_device_form' ]) !!}
	<div class="box box-solid">
    <div class="box-body">
      <div class="row">
        <div class="col-sm-12">
          <div class="form-group">
            {!! Form::label('name', __('business_ip.name') . ':*') !!}
              {!! Form::text('name', $business_ip->name, ['class' => 'form-control', 'required',
              'placeholder' => __('business_ip.name')]); !!}
          </div>
        </div>
        <div class="col-sm-12">
          <div class="form-group">
            {!! Form::label('ip_address', __('business_ip.ip_address') . ':*') !!}
              {!! Form::text('ip_address', $business_ip->ip_address, ['class' => 'form-control', 'required',
              'placeholder' => __('business_ip.ip_address')]); !!}
          </div>
        </div>
        <div class="col-sm-12">
          <div class="form-group">
            {!! Form::label('business_locations',__('business_ip.business_locations') . ':*') !!}
            <select name="location_id" class="form-control select2" required>
                <option value="">@lang('business_ip.select_business_locations')</option>
                @foreach($business_locations as $item)
                
                <option value="{{ $item->id }}" @if($business_ip->location_id == $item->id) selected @endif>{{ $item->name }} - {{ $item->location_id }}</option>
                @endforeach
            </select>
          </div>
        </div>
        
        <div class="col-sm-12">
          <div class="form-group">
            {!! Form::label('register_number', __('business_ip.register_number') . ':*') !!}
              {!! Form::text('register_number', $business_ip->register_number, ['class' => 'form-control', 'required',
              'placeholder' => __('business_ip.register_number')]); !!}
          </div>
        </div>

        <div class="col-sm-12">
          <div class="form-group">
            {!! Form::label('description', __('business_ip.description') . ':') !!}
              {!! Form::textarea('description', $business_ip->description, ['class' => 'form-control',
              'placeholder' => __('business_ip.description')]); !!}
          </div>
        </div>
        <div class="col-sm-12">
          <button type="submit" class="btn btn-primary pull-right">@lang('messages.update')</button>
        </div>
      </div>
    </div>
  </div>
  {!! Form::close() !!}
</section>
<!-- /.content -->
@stop
