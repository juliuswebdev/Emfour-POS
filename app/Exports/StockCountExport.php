<?php

namespace App\Exports;

use App\Product;
use Auth;
use DB;
use App\CountHeader;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataType;


class StockCountExport implements FromCollection, WithHeadings, WithColumnWidths
{

 
    public $data, $count_type;
    function __construct($data, $count_type) {
        $this->data = $data;
        $this->count_type = $count_type;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //
     
        if($this->count_type == 'mixed_skus') {
            return $this->data->where('products.id', 0)->get();
        } else {
            $products = $this->data
                ->select([
                    'products.sku AS sku',
                    DB::raw('CONCAT(products.upc) AS upc_code'),
                    DB::raw('
                        IF(variations.name = "DUMMY",
                            CONCAT(products.name),
                            CONCAT(products.name," - ",variations.name)  
                        )
                    AS product_name'),
                    DB::raw("( SELECT COALESCE(SUM(qty_available), 0) FROM variation_location_details WHERE product_id = products.id
                    ) as expected"),
                    DB::raw('CONCAT("") AS counted')
                ])
            ->get();

            return $products;
        }

    }

    /**
    * @return \Illuminate\Support\Heading
    */
    public function headings(): array
    {
        return ["SKU", "UPC CODE", "PRODUCT NAME", "EXPECTED", "COUNTED"];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 170,
            'B' => 170,
            'C' => 1150,
            'D' => 170,
            'E' => 710            
        ];
    }




}
