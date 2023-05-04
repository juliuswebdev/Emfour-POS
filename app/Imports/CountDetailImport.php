<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

use App\CountDetail;
use App\CountFrozenInventoryBalance;
use App\Product;
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
                $product = Product::where('sku', $row['sku'])->where('business_id', Auth::user()->business_id)->first();
                if($product) {
                    $quantity = VariationLocationDetails::where('product_id', $product->id)->sum('qty_available');
                    CountDetail::create([
                        'count_header_id' => $this->id,
                        'sku' => trim($row['sku']),
                        'product_id' => $product->id,
                        'frozen_quantity' => $quantity,
                        'count_quality' => trim($row['counted'])
                    ]);
                    CountFrozenInventoryBalance::create([
                        'count_header_id' => $this->id,
                        'sku' => trim($row['sku']),
                        'product_id' => $product->id,
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
