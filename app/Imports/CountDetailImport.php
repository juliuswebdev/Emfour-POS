<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

use App\CountDetail;
use App\CountFrozenInventoryBalance;
use App\Product;
use App\Variation;
use App\VariationLocationDetails;
use Auth;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CountDetailImport implements ToCollection, WithHeadingRow
{

    public $id, $count_type;
    function __construct($id, $count_type) {
        $this->id = $id;
        $this->count_type = $count_type;
    }
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\ToCollection|null
    */
    public function collection(Collection $rows)
    {
        foreach($rows as $row) {
            if($this->count_type == 'mixed_skus') {
                if($row['type'] == 'single') {
                    $product = Product::where('sku', $row['sku'])->where('business_id', Auth::user()->business_id)->first();
                    $variation = Variation::where('product_id', $product->id)->first();
                    $product_id = $product->id;
                    $variation_id = $variation->id;
                } else {
                    $product = Variation::where('sub_sku', $row['sku'])
                    ->join('products', function($join){
                        $join->on('products.id', '=', 'variations.product_id');
                    })
                    ->where('products.business_id', Auth::user()->business_id)
                    ->select([
                        'products.id AS product_id',
                        'variations.id AS variation_id'
                    ])
                    ->first();
                    $product_id = $product->product_id;
                    $variation_id = $product->variation_id;
                }
                if($product_id) {
                    $quantity = VariationLocationDetails::where('product_id', $product_id)->select('qty_available')->first();
                    //$quantity = VariationLocationDetails::where('product_id', $product_id)->sum('qty_available');
                    CountDetail::create([
                        'count_header_id' => $this->id,
                        'sku' => trim($row['sku']),
                        'product_id' => $variation_id,
                        'frozen_quantity' => $quantity,
                        'count_quality' => trim($row['counted'])
                    ]);
                    CountFrozenInventoryBalance::create([
                        'count_header_id' => $this->id,
                        'sku' => trim($row['sku']),
                        'product_id' => $variation_id,
                        'frozen_quantity' => $quantity,
                        'count_quality' => trim($row['counted'])
                    ]);
                }
            } else {
                CountDetail::where('count_header_id', $this->id)->where('sku', trim($row['sku']))->update([
                    'count_quantity'=>trim($row['counted'])
                ]);
            }
        }
    }
}
