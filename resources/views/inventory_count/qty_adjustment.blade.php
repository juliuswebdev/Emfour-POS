@extends('layouts.app')
@section('title', __('inventory_count.post_finalized_count'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('inventory_count.post_finalized_count')
    </h1>
</section>
<section class="content">
    <div class="box box-primary">
        <div class="box-header">
            <p class="italic"><small>@lang('inventory_count.required_notes')</small></p>
        </div>
        <div class="box-body">
            <form method="POST" action="{{ action('\App\Http\Controllers\InventoryCountController@post_count', [$count_header->id]) }}" accept-charset="UTF-8" id="adjustment-form" enctype="multipart/form-data">
                {{ csrf_field() }}
                <input type="hidden" name="count_detail_id_deleted" value="">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('inventory_count.location') *</label>
                                    <select required id="location" name="location" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins">
                                        @foreach($business_locations as $item)
                                            <option value="{{ $item->id }}" @if ($count_header->business_location_id == $item->id) selected @endif>{{$item->name}} [{{$item->location_id}}]</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('inventory_count.attach_document')</label>
                                    <input type="file" name="attach_document" class="form-control" >
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label>@lang('inventory_count.select_product')</label>
                                <div class="form-group">
                                    <input type="text" name="product_code_name" id="search" placeholder="@lang('inventory_count.search')" class="form-control" />
                                </div>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-md-12">
                                <h5>@lang('inventory_count.order_table') *</h5>
                                <div class="table-responsive mt-3">
                                    <table id="myTable" class="table table-hover order-list">
                                        <thead>
                                            <tr>
                                                <th>@lang('inventory_count.product_name')</th>
                                                <th>@lang('inventory_count.sku')</th>
                                                <th>@lang('inventory_count.upc_code')</th>
                                                <th>@lang('inventory_count.expected')</th>
                                                <th>@lang('inventory_count.quantity')</th>
                                                <th><i class="fa fa-trash"></i></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $total_qty = 0; @endphp
                                            @foreach($count_details as $item)
                                            @php $total_qty = $total_qty + $item->frozen_quantity @endphp
                                            <tr>
                                                <td>{{ $item->product_name }}</td>
                                                <td>{{ $item->sku }}</td>
                                                <td>{{ $item->upc }}</td>
                                                <td>{{ $item->frozen_quantity }}</td>
                                                <td><input type="number" class="form-control qty" name="count_quantity[{{ $item->id }}]" value="{{ $item->count_quantity }}" required step="any" /></td>
                                                <td>
                                                    <button type="button" class="count-detail-remove btn btn-md btn-danger" data-count_id="{{ $item->id }}">Delete</button>
                                                    
                                                </td>
                                            <tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="tfoot active">
                                            <th colspan="2">@lang('inventory_count.total')</th>
                                            <th></th>
                                            <th>{{ $total_qty }}</th>
                                            <th id="total-qty" colspan="2">0</th>
                                            <th><i class="dripicons-trash"></i></th>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('inventory_count.note')</label>
                                    <textarea rows="5" class="form-control" name="post_note"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="submit" value="Submit" class="btn btn-primary" id="submit-button">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<!-- /.content -->
@stop
@section('javascript')
	<script src="{{ asset('js/inventory_count.js?v=' . $asset_v) }}"></script>
@endsection