<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\CountHeader;
use App\CountDetail;
use App\CountFrozenInventoryBalance;
use App\BusinessLocation;
use App\Category;
use App\Brands;
use App\Product;
use App\Variation;
use App\ProductVariation;
use App\VariationLocationDetails;
use App\Transaction;
use App\PurchaseLine;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StockCountExport;
use App\Imports\CountDetailImport;
use App\StockAdjustmentLine;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;

use Carbon\Carbon;

use Datatables;
use Auth;
use DB;
use Config;


class InventoryCountController extends Controller
{
    
    /**
     * All Utils instance.
     */
    protected $productUtil;

    protected $transactionUtil;

    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param  ProductUtils  $product
     * @return void
     */
    public function __construct(ProductUtil $productUtil, TransactionUtil $transactionUtil, ModuleUtil $moduleUtil)
    {
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function inventory_count()
    {
        if (! auth()->user()->can('purchase.create')) {
            abort(403, 'Unauthorized action.');
        }
        $request = request();
        if (request()->ajax()) {
 
            $inventory_count = CountHeader::where('count_header.business_id', Auth::user()->business_id)
                ->leftJoin('business_locations', function($join){
                    $join->on('business_locations.id','=','count_header.business_location_id');
                })
                ->select([
                    'count_header.id AS id',
                    'count_header.final_file AS final_file',
                    'count_header.created_at AS date',
                    'count_header.count_reference',
                    'count_header.description AS count_description',
                    DB::raw('CONCAT( business_locations.name, " [", business_locations.location_id, "]") AS location'),
                    DB::raw('( 
                        SELECT GROUP_CONCAT(c.name) FROM categories as c WHERE FIND_IN_SET(c.id, count_header.categories)
                    ) 
                    AS categories '),
                    DB::raw('( 
                        SELECT GROUP_CONCAT(c.name) FROM categories as c WHERE FIND_IN_SET(c.id, count_header.sub_categories)
                    ) 
                    AS sub_categories '),
                    DB::raw('( 
                        SELECT GROUP_CONCAT(c.name) FROM brands as c WHERE FIND_IN_SET(c.id, count_header.brands)
                    ) 
                    AS brands '),
                    'count_header.count_type AS type',
                    'count_header.status'
                ]);
            return Datatables::of($inventory_count)
                ->addColumn(
                    'action',
                    function($row) {
                        $html = '<div class="btn-group" class="inventory_count_dropdown">
                            <button type="button" class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">Actions<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>
                            <ul class="action dropdown-menu dropdown-menu-left" role="menu">';

                            if($row->status == 1) {
                                $html .=  '<li>
                                    <a href="#" data-toggle="modal" data-target="#freeze_confirm" class="action_modal_form freeze_confirm" data-action="'.action('App\Http\Controllers\InventoryCountController@freeze_count', [$row->id]).'"  data-count_reference="'.$row->count_reference.'">
                                        <i class="fa fa-file" aria-hidden="true" style="margin-right: 10px"></i>'.__("inventory_count.freeze_count").'
                                    </a>
                                </li>';
                            } else {
                                $html .=  '<li class="disabled">
                                    <a href="#">
                                        <i class="fa fa-file" aria-hidden="true" style="margin-right: 10px"></i>'.__("inventory_count.freeze_count").'
                                    </a>
                                </li>';  
                            }

                            if($row->status == 2) {
                            $html .=  '<li>
                                    <a class="download_inventory_count" href="'.action('App\Http\Controllers\InventoryCountController@download_initial_file', [$row->id]).'" data-count_reference="'.$row->count_reference.'">
                                        <i class="fa fa-download" aria-hidden="true"></i>'.__("inventory_count.download_count").'
                                    </a>
                                </li>
                                <li>
                                    <a href="#" data-toggle="modal" data-target="#upload_count" class="action_modal_form" data-action="'.action('App\Http\Controllers\InventoryCountController@upload_final_file', [$row->id]).'">
                                        <i class="fa fa-upload" aria-hidden="true" style="margin-right: 10px"></i>'.__("inventory_count.upload_count").'
                                    </a>
                                </li>';
                            } else {
                                $html .=  '<li class="disabled">
                                    <a href="#">
                                        <i class="fa fa-download" aria-hidden="true"></i>'.__("inventory_count.download_count").'
                                    </a>
                                </li>
                                <li class="disabled">
                                    <a href="#">
                                        <i class="fa fa-upload" aria-hidden="true" style="margin-right: 10px"></i>'.__("inventory_count.upload_count").'
                                    </a>
                                </li>';
                            }

                            if($row->status == 3 || $row->status == 4) {
                                $html .=  '
                                    <li class="divider"></li>
                                    <li>
                                        <a href="#" class="final_report" data-toggle="modal" data-target="#final_report" data-action="'.action('App\Http\Controllers\InventoryCountController@view_inventory_count', [$row->id]).'">
                                            <i class="fa fa-file" aria-hidden="true"></i>'.__("inventory_count.final_report").'
                                        </a>
                                    </li>';
                            } 

                            $html .=  '</ul>
                            </div>';
                    return $html;
                    })

                ->editColumn(
                    'type',
                    function($row) {
                        $text = 'N/A';
                        $type = 'default';
                        if($row->type == 'entire_location') {
                            $type = 'info';
                            $text = 'Entire Location';
                        } else if ($row->type == 'partial') {
                            $type = 'primary';
                            $text = 'Partial';
                        } else if ($row->type == 'mixed_skus') {
                            $type = 'warning';
                            $text = 'Mixed SKUs';
                        }
                        return '<span class="btn btn-xs btn-'.$type.'">'.$text.'</span>';
                    }
                )
                ->editColumn(
                    'status',
                    function($row) {
                        $text = 'N/A';
                        if($row->status == 1) {
                            $text = 'Batch Created';
                        } else if ($row->status == 2) {
                            $text = 'Frozen';
                        } else if ($row->status == 3) {
                            $text = 'Count Updated';
                        } else if ($row->status == 4) {
                            $text = 'Posted';
                        }
                        return $text;
                    }
                )
                ->filter(function ($instance) use ($request) {
                    if ($request->get('status')) {
                        $instance->where('status', $request->get('status'));
                    }
                })
                ->rawColumns(['action', 'type'])
                ->make(true);
        }

        $business_locations = BusinessLocation::where('business_id',  Auth::user()->business_id)
                            ->select(['id', 'name', 'location_id'])
                            ->get();

        $categories = Category::where('business_id',  Auth::user()->business_id)
                        ->where('parent_id' , 0)
                        ->select(['id', 'name', 'short_code'])
                        ->get();

        $brands = Brands::where('business_id',  Auth::user()->business_id)
                        ->select(['id', 'name'])
                        ->get();
        return view('inventory_count.index', [
            'business_locations' => $business_locations,
            'categories' => $categories,
            'brands' => $brands
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
    */
    public function store(Request $request)
    {
        $count_header = CountHeader::create([
            'count_reference' => 'sc-'.date("Ymd-his"),
            'business_id' => Auth::user()->business_id,
            'business_location_id' => $request['business_location_id'],
            'description' => $request['description'],
            'count_type' => $request['count_type'],
            'count_sku_with_zero_stock_onhand' => $request['count_sku_with_zero_stock_onhand'],
            'frozen_count' => 0,
            'status' =>  1,
            'categories' => ($request['count_type'] == 'partial') ? $request['categories'] ? implode(',', $request['categories']) : '' : null,
            'sub_categories' => ($request['count_type'] == 'partial') ? $request['sub_categories'] ? implode(',', $request['sub_categories']) : '' : null,
            'brands' => ($request['count_type'] == 'partial') ? $request['brands'] ? implode(',', $request['brands']): '' : null,
            'created_by' => Auth::user()->id,
            'user_froze_count' => 0,
            'user_posted_count' => 0
        ]);

        if($count_header->count_type == 'partial' || $count_header->count_type == 'entire_location' ) {

            $products_query = $this->filter_products($count_header);
            
            $products = $products_query->select([
                DB::raw('CONCAT("'.$count_header->id.'") AS count_header_id'),
                'variations.id AS product_id',
                DB::raw('
                    IF(
                        products.type = "variable",
                        variations.sub_sku, 
                        products.sku
                    )
                AS sku'),
                DB::raw('products.upc AS upc'),
                DB::raw('CURRENT_TIMESTAMP() AS created_at')
            ]);

            CountDetail::insertUsing([
                'count_header_id',
                'product_id',
                'sku',
                'upc',
                'created_at'
            ],$products);


        }
        return redirect()->back();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function freeze_count($id)
    {
        // Freeze count
        $count_header = CountHeader::find($id);
        $count_header->status = 2;
        $count_header->user_froze_count = Auth::user()->id;
        $count_header->save();

        if($count_header->count_type == 'partial' || $count_header->count_type == 'entire_location') {
            $products_query = $this->filter_products($count_header);

            $products = $products_query->select([
                DB::raw('CONCAT("'.$count_header->id.'") AS count_header_id'),
                DB::raw('
                    IF(
                        products.type = "variable",
                        variations.sub_sku, 
                        products.sku
                    )
                AS sku'),
                'variations.id AS product_id',
                DB::raw('products.upc AS upc'),
                DB::raw("( SELECT COALESCE(SUM(qty_available), 0) FROM variation_location_details WHERE variation_id = variations.id
                    ) as frozen_quantity"),
                DB::raw('CURRENT_TIMESTAMP() AS created_at'),
                DB::raw('CURRENT_TIMESTAMP() AS updated_at')
            ]);

            CountFrozenInventoryBalance::insertUsing([
                'count_header_id',
                'sku',
                'product_id',
                'upc',
                'frozen_quantity',
                'created_at',
                'updated_at'
            ],$products);

            foreach($products->get() as $item) {
                CountDetail::where('count_header_id', $item->count_header_id)->where('sku', $item->sku)->update(['frozen_quantity' => $item->frozen_quantity]);
            }
        }
        
        return redirect()->back();
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function download_initial_file($id) 
    {
        
        $count_header = CountHeader::find($id);
        $file_name = $count_header->count_reference ?? 'sc-'.$id;
        Config::set('excel.exports.csv.delimiter', ',');
        return Excel::download(new StockCountExport($this->filter_products($count_header), $count_header->count_type), $file_name.'.csv');
        // echo '<pre>';
        // print_r($this->filter_products($count_header)->select([
        //     DB::raw('CONCAT("'.$count_header->id.'") AS count_header_id'),
        //     'variations.id AS product_id',
        //     DB::raw('
        //         IF(
        //             products.type = "variable",
        //             variations.sub_sku, 
        //             products.sku
        //         )
        //     AS sku'),
        //     DB::raw('products.upc AS upc'),
        //     DB::raw('CURRENT_TIMESTAMP() AS created_at')
        // ])->get());
        // echo '</pre>';
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function upload_final_file(Request $request, $id)
    {

        $request->validate([
            "final_file" => 'required|mimetypes:text/csv,text/plain,application/csv,text/comma-separated-values,text/anytext,application/octet-stream,application/txt'
        ]);

        if ($request->hasFile('final_file')) {
            $file = $request->file('final_file');
            $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $fileName = 'sc-'.date("Ymd-his") . "." . $file->getClientOriginalExtension();
            $file->storeAs('csv', $fileName, 'local');
            $csv = $fileName;

            $count_header = CountHeader::find($id);
            $count_header->count_note = $request->input('count_note');
            $count_header->final_file = $csv;
            $count_header->status = 3;
            $count_header->save();

        }

        Excel::import(new CountDetailImport($id, $count_header->count_type),request()->file('final_file'));
        return back();

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function filter_products($count_header)
    {
        $products_query = DB::table('products')
        ->where('products.business_id', Auth::user()->business_id)
        // added
        ->leftJoin('variations', function($join){
            $join->on('variations.product_id', '=', 'products.id');
        })
        ->leftJoin('variation_location_details', function($join) {
            $join->on('variation_location_details.product_id', '=', 'products.id');
        })->leftJoin('product_locations', function($join) {
            $join->on('product_locations.product_id', '=', 'products.id');
        })->where('product_locations.location_id', $count_header->business_location_id);
        if($count_header->count_sku_with_zero_stock_onhand == 1) {
            $products_query->whereNull('variation_location_details.qty_available');
        }
        if($count_header->count_type == 'partial') {
            $products_query->whereIn('products.category_id', [$count_header->categories])
                    ->orWhereIn('products.sub_category_id', [$count_header->sub_categories])
                    ->orWhereIn('products.brand_id', [$count_header->brands]);
        }
        return $products_query->groupBy('variations.id');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function view_inventory_count($id)
    {
        if(request()->ajax()) {

            $count_header = CountHeader::find($id);

            $business_location =  BusinessLocation::where('id', $count_header->business_location_id)
                        ->select(['name', 'location_id'])
                        ->first();

            $count_detail = CountDetail::where('count_detail.count_header_id', $count_header->id)
                    ->leftJoin('variations', function($join){
                        $join->on('variations.id', 'count_detail.product_id');
                    })
                    ->leftJoin('products', function($join){
                        $join->on('products.id', 'variations.product_id');
                    })
                    ->select([
                        'count_detail.sku as sku',
                        'count_detail.upc as upc',
                        'products.name as product_name',
                        'count_detail.frozen_quantity as frozen_quantity',
                        'count_detail.count_quantity as count_quantity'
                    ])
                    ->get();
          
            $html = '
                <p><strong>'.__('inventory_count.date').': </strong>'. $count_header->created_at .'</p>
                <p><strong>'.__('inventory_count.location').': </strong>'. $business_location->name.' ['.$business_location->location_id.']</p>
                <p><strong>'.__('inventory_count.count_type').': </strong>';
                $text = 'N/A';
                $type = 'default';
                if($count_header->count_type == 'entire_location') {
                    $type = 'info';
                    $text = 'Entire Location';
                } else if ($count_header->count_type == 'partial') {
                    $type = 'primary';
                    $text = 'Partial';
                } else if ($count_header->count_type == 'mixed_skus') {
                    $type = 'warning';
                    $text = 'Mixed SKUs';
                }
                $html .= '<span class="btn btn-xs btn-'.$type.'">'.$text.'</span>';
               $html .= '</p>
                <p>
                    <span>
                        <strong>'.__("inventory_count.files").': </strong>
                        &nbsp;&nbsp;<a class="btn btn-sm btn-primary download_inventory_count" href="'.action('App\Http\Controllers\InventoryCountController@download_initial_file', [$count_header->id]).'" data-count_reference="'.$count_header->count_reference.'"><i class="fa fa-download"></i> '.__("inventory_count.initial_file").'</a>
                        &nbsp;&nbsp;<a class="btn btn-sm btn-info" href="'.asset('uploads/csv') .'/'. $count_header->final_file.'"><i class="fa fa-download"></i> '.__("inventory_count.final_file").'</a>
                    </span>
                </p>
              
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>'.__("inventory_count.sku").'</th>
                            <th>'.__("inventory_count.upc_code").'</th>
                            <th>'.__("inventory_count.product_name").'</th>
                            <th>'.__("inventory_count.expected").'</th>
                            <th>'.__("inventory_count.counted").'</th>
                        </tr>
                    </thead>
                    <tbody>';
                    $count = 1;
                    foreach($count_detail as $item) {
                        $html .= '<tr>
                                <td><strong>'.$count++.'</strong></td>
                                <td>'.$item->sku.'</td>
                                <td>'.$item->upc.'</td>
                                <td>'.$item->product_name.'</td>
                                <td>'.$item->frozen_quantity.'</td>
                                <td>'.$item->count_quantity.'</td>
                            </tr>';
                    }

            $html  .= '</tbody>
                </table>';
            if($count_header->status == 3) {
                $html .= '<a class="btn btn-primary d-print-none" href="'.action('App\Http\Controllers\InventoryCountController@qty_adjustment', [$count_header->id]).'"><i class="fa fa-plus"></i> Add Adjustment</a></div>';
            }
            return $html;
        }
    }

    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function qty_adjustment($id) 
    {
        $count_header = CountHeader::find($id);

        if ($count_header == null || $count_header->status == 4) {
            abort(404);
        }

        $business_locations = BusinessLocation::where('business_id',  Auth::user()->business_id)
            ->select(['id', 'name', 'location_id'])
            ->get();

        $count_details = CountDetail::where('count_detail.count_header_id', $count_header->id)
                ->leftJoin('products', function($join){
                    $join->on('products.id', 'count_detail.product_id');
                })
                ->select([
                    'count_detail.id as id',
                    'count_detail.count_header_id as count_header_id',
                    'count_detail.sku as sku',
                    'count_detail.upc as upc',
                    'count_detail.product_id as product_id',
                    'products.name as product_name',
                    'count_detail.frozen_quantity as frozen_quantity',
                    'count_detail.count_quantity as count_quantity'
                ])
                ->get();

        return view('inventory_count.qty_adjustment', [
            'count_header' => $count_header,
            'business_locations' => $business_locations,
            'count_details' => $count_details
        ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function post_count(Request $request, $id)
    {
        try {
            $count_header = CountHeader::find($id);

            $user_id = Auth::user()->id;

            if ($count_header == null || $count_header->status == 4) {
                abort(404);
            }

            $fileName = null;
            if ($request->hasFile('attach_document')) {
                $file = $request->file('attach_document');
                $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $fileName = date("Ymd-his") . "." . $file->getClientOriginalExtension();
                $file->storeAs('post_count', $fileName, 'local');
            }

            $count_header->count_note = $request->input('post_note');
            $count_header->user_posted_count = Auth::user()->id;
            $count_header->attach_document = $fileName;
            $count_header->status = 4;
            $count_header->save();

            // Remove count details
            $count_detail_delete_id_arr = explode(',', $request->input('count_detail_id_deleted'));
            $count_details = CountDetail::whereIn('id', $count_detail_delete_id_arr)->delete();
            
            foreach($request->count_quantity as $key => $count_quantity) {

                if($count_quantity != 0 && is_numeric($count_quantity)) {
                    $count_detail = CountDetail::where('id', $key)->where('count_header_id', $count_header->id)->first();
                    $count_detail->count_quantity = $count_quantity;
                    $count_detail->save();

                    $variation_qry = Variation::find($count_detail->product_id);
                    $product_id  = $variation_qry->product_id;
                    $variation_id = $variation_qry->id;
                    $product_variation_id = $variation_qry->product_variation_id;

                    $variation_location_details = VariationLocationDetails::where('product_id', $product_id)->where('product_variation_id', $variation_id)->first(); 

                    $old_qty_available = $variation_location_details->qty_available ?? 0;

                    $variation = Variation::find($product_id);
                    $product_q = Product::find($variation->product_id);
                    //$product_q = Product::find($product_id);
                    $product_q->enable_stock = 1;
                    $product_q->save();

                    //$product_variation_qry  = ProductVariation::where('product_id', $product_id)->get();
                    //Add quantity in VariationLocationDetails

                    //foreach($product_variation_qry as $pv) {
                        
                        $variation_location_d = VariationLocationDetails::where('variation_id', $variation_id)
                                ->where('product_id', $product_id)
                                ->where('product_variation_id', $product_variation_id)
                                ->where('location_id', $count_header->business_location_id)
                                ->first();

                        if (empty($variation_location_d)) {
                            $variation_location_d = new VariationLocationDetails();
                            $variation_location_d->variation_id = $variation_id;
                            $variation_location_d->product_id = $product_id;
                            $variation_location_d->location_id = $count_header->business_location_id;
                            $variation_location_d->product_variation_id = $product_variation_id;
                            $variation_location_d->qty_available = $count_quantity;
                            $variation_location_d->save();
                        }

                        $variation_location_d->qty_available = $count_quantity;
                        $variation_location_d->save();
                    //}

                    
                
                        $transaction_date = request()->session()->get('financial_year.start');
                        $transaction_date = \Carbon::createFromFormat('Y-m-d', $transaction_date)->toDateTimeString();
                        $transaction = Transaction::create(
                            [
                                'type' => 'physical_count_adjustment',
                                'opening_stock_product_id' => $product_id,
                                'status' => 'received',
                                'business_id' => $count_header->business_id,
                                'transaction_date' => $transaction_date,
                                'total_before_tax' => 0,
                                'location_id' => $count_header->business_location_id,
                                'final_total' => $count_quantity,
                                'payment_status' => 'paid',
                                'created_by' => $user_id,
                                'ref_no' => $count_header->count_reference
                            ]
                        );

                        StockAdjustmentLine::create(
                            [
                                'transaction_id' => $transaction->id,
                                'product_id' => $product_id,
                                'variation_id' => $variation_id,
                                'quantity' => $count_quantity
                            ]
                        );

                        // Update purchase line history
                        $purchase_line = PurchaseLine::create(
                            [
                                'transaction_id' => $transaction->id,
                                'product_id' => $product_id,
                                'variation_id' => $variation_id,
                                'quantity' => $count_quantity
                            ]
                        );

                    
                }

            }
            return redirect()->route('inventory_count');
        } catch (\Exception $e) {
            throw new \Exception(__('lang_v1.invalid_date_format_at', ['row' => $row_index]));
        }
    }
}