<?php

namespace App\Http\Controllers\Restaurant;

use App\TransactionSellLine;
use App\Utils\RestaurantUtil;
use App\Utils\BusinessUtil;

use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class KitchenController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $commonUtil;

    protected $restUtil;

    protected $businessUtil;

    /**
     * Constructor
     *
     * @param  Util  $commonUtil
     * @param  RestaurantUtil  $restUtil
     * @return void
     */
    public function __construct(Util $commonUtil, RestaurantUtil $restUtil, BusinessUtil $businessUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->restUtil = $restUtil;
        $this->businessUtil = $businessUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        // if (!auth()->user()->can('sell.view')) {
        //     abort(403, 'Unauthorized action.');
        // }

        $business_id = request()->session()->get('user.business_id');
        $orders = $this->restUtil->getAllOrders($business_id, ['line_order_status' => 'received']);
        //dd($orders);

        $business_details = $this->businessUtil->getDetails($business_id);
        return view('restaurant.kitchen.index', compact('orders', 'business_details'));
    }

    
    /**
     * function use for update the cooking timestamp in cook start end cook end column.
     *
     * @return json $output
     */
    public function updateCookProgress($stage, $id, $product_id){
        try {
            $business_id = request()->session()->get('user.business_id');
            $sl = TransactionSellLine::leftJoin('transactions as t', 't.id', '=', 'transaction_sell_lines.transaction_id')
                        ->where('t.business_id', $business_id)
                        ->where('transaction_id', $id)
                        ->where('product_id', $product_id)
                        ->update([$stage => date('Y-m-d H:i:s')]);

            $output = [
                'success' => 1,
                'msg' => __('lang_v1.cooking_state_update_message'),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            $output = ['success' => 0,
                'msg' => trans('messages.something_went_wrong'),
            ];
        }

        return $output;
    }


    /**
     * Marks an order as cooked
     *
     * @return json $output
     */
    public function markAsCooked($id)
    {
        // if (!auth()->user()->can('sell.update')) {
        //     abort(403, 'Unauthorized action.');
        // }
        try {
            $business_id = request()->session()->get('user.business_id');
            $sl = TransactionSellLine::leftJoin('transactions as t', 't.id', '=', 'transaction_sell_lines.transaction_id')
                        ->where('t.business_id', $business_id)
                        ->where('transaction_id', $id)
                        ->where(function ($q) {
                            $q->whereNull('res_line_order_status')
                                ->orWhere('res_line_order_status', 'received');
                        })
                        ->update(['res_line_order_status' => 'cooked']);

            $output = ['success' => 1,
                'msg' => trans('restaurant.order_successfully_marked_cooked'),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => 0,
                'msg' => trans('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    /**
     * Retrives fresh orders
     *
     * @return Json $output
     */
    public function refreshOrdersList(Request $request)
    {

        // if (!auth()->user()->can('sell.view')) {
        //     abort(403, 'Unauthorized action.');
        // }
        $business_id = request()->session()->get('user.business_id');
        $orders_for = $request->orders_for;
        $filter = [];
        $service_staff_id = request()->session()->get('user.id');

        if (! $this->restUtil->is_service_staff($service_staff_id) && ! empty($request->input('service_staff_id'))) {
            $service_staff_id = $request->input('service_staff_id');
        }

        if ($orders_for == 'kitchen') {
            $filter['line_order_status'] = 'received';
        } elseif ($orders_for == 'waiter') {
            $filter['waiter_id'] = $service_staff_id;
        }

        $orders = $this->restUtil->getAllOrders($business_id, $filter);
        $business_details = $this->businessUtil->getDetails($business_id);
        return view('restaurant.partials.show_orders', compact('orders', 'orders_for', 'business_details'));
    }

    /**
     * Retrives fresh orders
     *
     * @return Json $output
     */
    public function refreshLineOrdersList(Request $request)
    {

        // if (!auth()->user()->can('sell.view')) {
        //     abort(403, 'Unauthorized action.');
        // }
        $business_id = request()->session()->get('user.business_id');
        $orders_for = $request->orders_for;
        $filter = [];
        $service_staff_id = request()->session()->get('user.id');

        if (! $this->restUtil->is_service_staff($service_staff_id) && ! empty($request->input('service_staff_id'))) {
            $service_staff_id = $request->input('service_staff_id');
        }

        if ($orders_for == 'kitchen') {
            $filter['order_status'] = 'received';
        } elseif ($orders_for == 'waiter') {
            $filter['waiter_id'] = $service_staff_id;
        }

        $line_orders = $this->restUtil->getLineOrders($business_id, $filter);

        return view('restaurant.partials.line_orders', compact('line_orders', 'orders_for'));
    }
}
