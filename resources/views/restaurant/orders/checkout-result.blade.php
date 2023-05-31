@if(count($transactions) > 0)
    <div style="margin-top: 20px">
    @foreach($transactions as $transaction)
        @php
            $total_items = 0;
            $products = $transaction->sell_lines;
            foreach($products as $product) {
                $total_items = $total_items + $product->quantity;
            }

        @endphp
        <div class="orders-section" style="margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #ccc;">
            <div class="col-left">
            <strong>Invoice No:</strong><span> {{ $transaction->invoice_no }}</span><br>
            <strong>Table :</strong><span> {{ $transaction->table_name }}</span><br>
            <strong>Items: </strong><span> {{ $total_items }}</span>
            </div>
            <div class="col-right">
                <a href="{{action([\App\Http\Controllers\SellPosController::class, 'edit'], [$transaction->id])}}" class="btn btn-xs btn-primary btn-products-checkout">@lang('lang_v1.checkout')</a>
            </div>
        </div>
    @endforeach
    </div>
@endif