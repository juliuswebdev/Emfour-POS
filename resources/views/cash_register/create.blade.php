@extends('layouts.app')
@section('title',  __('cash_register.open_cash_register'))

@section('content')
<style type="text/css">



</style>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('cash_register.open_cash_register')</h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">
{!! Form::open(['url' => action([\App\Http\Controllers\CashRegisterController::class, 'store']), 'method' => 'post', 
'id' => 'add_cash_register_form' ]) !!}
  <div class="box box-solid">
    <div class="box-body">
    <br><br><br>
    <input type="hidden" name="sub_type" value="{{$sub_type}}">
      <div class="row">
        @if($business_locations->count() > 0)
        <div class="col-sm-8 col-sm-offset-2">
          <div class="form-group">
            {!! Form::label('amount', __('cash_register.cash_in_hand') . ':*') !!}
            {!! Form::text('amount', null, ['class' => 'form-control input_number',
              'placeholder' => __('cash_register.enter_amount'), 'required']); !!}
          </div>
        </div>
        @if(count($business_locations) > 1)
        <div class="clearfix"></div>
        <div class="col-sm-8 col-sm-offset-2">
          <div class="form-group">
            {!! Form::label('location_id', __('business.business_location') . ':') !!}
              {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2',
              'placeholder' => __('lang_v1.select_location')]); !!}
          </div>
        </div>
        @else
          {!! Form::hidden('location_id', array_key_first($business_locations->toArray()) ); !!}
        @endif

        <div class="col-sm-8 col-sm-offset-2">
          <div class="form-group">
            {!! Form::label('register_number', __('business.register_number') . ':') !!}
            <select name="register_number" id="register_number" class="form-control select2">
              <option value="">Select @lang('business.register_number') </option>
              @foreach ($business_register_numbers as $b_row)
                <option data-location-id="{{ $b_row->location_id }}" value="{{  $b_row->id }}">{{  $b_row->register_number }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="col-sm-8 col-sm-offset-2">
          <button type="submit" class="btn btn-primary pull-right">@lang('cash_register.open_register')</button>
        </div>
        @else
        <div class="col-sm-8 col-sm-offset-2 text-center">
          <h3>@lang('lang_v1.no_location_access_found')</h3>
        </div>
      @endif
      </div>
      <br><br><br>
    </div>
  </div>
  {!! Form::close() !!}
</section>
<!-- /.content -->

@endsection

@section('scripts')
<script>
  $(document).on('change', '#location_id', function(){
    var location_id = $('#location_id').val();

    $('#register_number').val('');
    $('#select2-register_number-container').text('Select Register Number')

    $('#register_number').find('option').prop("disabled",true);
    $('#register_number').find('option[data-location-id="'+location_id+'"]').prop("disabled",false);
    $('#register_number').find('option:eq(0)').prop("disabled",false);
    $('#register_number').select2();

  });

  $(document).on('change', '#register_number', function(){
    var location_id = $('#location_id').val();
    if(location_id == ""){
      $('#register_number').val('');
      $('#select2-register_number-container').text('Select Register Number');
      swal({
        title: "Please select the business location.",
        content: "",
        icon: 'error'
      })
      return false;
    }
  });
</script>
@endsection