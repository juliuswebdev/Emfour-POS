<?php

namespace App\Http\Controllers\Restaurant;

use App\TransactionSellLine;
use App\Transaction;
use App\User;
use App\Utils\RestaurantUtil;
use App\Utils\BusinessUtil;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class OrderController extends Controller
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
        $business_details = $this->businessUtil->getDetails($business_id);
        $user_id = request()->session()->get('user.id');

        $is_service_staff = false;
        $orders = [];
        $service_staff = [];
        $line_orders = [];
        if ($this->restUtil->is_service_staff($user_id)) {
            $is_service_staff = true;
            $orders = $this->restUtil->getAllOrders($business_id, ['waiter_id' => $user_id]);

            $line_orders = $this->restUtil->getLineOrders($business_id, ['waiter_id' => $user_id]);
        } elseif (! empty(request()->service_staff)) {
            $orders = $this->restUtil->getAllOrders($business_id, ['waiter_id' => request()->service_staff]);
            $line_orders = $this->restUtil->getLineOrders($business_id, ['waiter_id' => request()->service_staff]);

        } else if ( empty(request()->service_staff) ) {
            $orders = $this->restUtil->getAllOrders($business_id, ['waiter_id' => 'all']);
            $line_orders = $this->restUtil->getLineOrders($business_id, ['waiter_id' => 'all']);

        }

        if(request()->service_staff == 'all') {
            $orders = $this->restUtil->getAllOrders($business_id, ['waiter_id' => 'all']);
            $line_orders = $this->restUtil->getLineOrders($business_id, ['waiter_id' => 'all']);
        }

        if (! $is_service_staff) {
            $service_staff = $this->restUtil->service_staff_dropdown($business_id);
        }

        return view('restaurant.orders.index', compact('orders', 'is_service_staff', 'service_staff', 'line_orders', 'business_details'));
    }

    /**
     * Marks an order as served
     *
     * @return json $output
     */
    public function markAsServed($id)
    {
        // if (!auth()->user()->can('sell.update')) {
        //     abort(403, 'Unauthorized action.');
        // }
        try {
            $business_id = request()->session()->get('user.business_id');
            $user_id = request()->session()->get('user.id');

            $query = TransactionSellLine::leftJoin('transactions as t', 't.id', '=', 'transaction_sell_lines.transaction_id')
                        ->where('t.business_id', $business_id)
                        ->where('transaction_id', $id);

            if ($this->restUtil->is_service_staff($user_id)) {
                $query->where('res_waiter_id', $user_id);
            }

            $query->update(['res_line_order_status' => 'served']);

            $output = ['success' => 1,
                'msg' => trans('restaurant.order_successfully_marked_served'),
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
     * function use for update the is served column.
     *
     * @return json $output
     */
    public function updateServed($stage, $id, $product_id){
        try {
            $business_id = request()->session()->get('user.business_id');
            $sl = TransactionSellLine::leftJoin('transactions as t', 't.id', '=', 'transaction_sell_lines.transaction_id')
                        ->where('t.business_id', $business_id)
                        ->where('transaction_id', $id)
                        ->where('product_id', $product_id)
                        ->update([$stage => date('Y-m-d H:i:s')]);

            $output = [
                'success' => 1,
                'msg' =>  __('lang_v1.order_item_served_message'),
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
     * Marks an line order as served
     *
     * @return json $output
     */
    public function markLineOrderAsServed($id)
    {
        try {
            $business_id = request()->session()->get('user.business_id');
            $user_id = request()->session()->get('user.id');

            $query = TransactionSellLine::where('id', $id);

            if ($this->restUtil->is_service_staff($user_id)) {
                $query->where('res_service_staff_id', $user_id);
            }
            $sell_line = $query->first();

            if (! empty($sell_line)) {
                $sell_line->res_line_order_status = 'served';
                $sell_line->save();
                $output = ['success' => 1,
                    'msg' => trans('restaurant.order_successfully_marked_served'),
                ];
            } else {
                $output = ['success' => 0,
                    'msg' => trans('messages.something_went_wrong'),
                ];
            }
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => 0,
                'msg' => trans('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    public function printLineOrder(Request $request)
    {
        try {
            $business_id = request()->session()->get('user.business_id');
            $waiter_id = request()->session()->get('user.id');
            $line_id = $request->input('line_id');
            if (! empty($request->input('service_staff_id'))) {
                $waiter_id = $request->input('service_staff_id');
            }

            $line_orders = $this->restUtil->getLineOrders($business_id, ['waiter_id' => $waiter_id, 'line_id' => $line_id]);
            $order = $line_orders[0];
            $html_content = view('restaurant.partials.print_line_order', compact('order'))->render();
            $output = [
                'success' => 1,
                'msg' => trans('lang_v1.success'),
                'html_content' => $html_content,
            ];
        } catch (Exception $e) {
            $output = [
                'success' => 0,
                'msg' => trans('messages.something_went_wrong'),
            ];
        }

        return $output;
    }


    public function searchOrderByStatus(Request $request, $res_line_order_status)
    {

        if(request()->ajax()) {
            $q = $request->input('search_query');

            $query = Transaction::leftJoin('transaction_sell_lines', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
            ->leftJoin('res_tables', 'res_tables.id', '=', 'transactions.res_table_id');

            if($q) {
                $query->where('transactions.invoice_no', '=', $q)->orWhere('res_tables.name', '=', $q);
            }

            $transactions = $query->where('transaction_sell_lines.res_line_order_status', '=', $res_line_order_status)
            ->where('transactions.final_total', '=', 0.0000)
            ->select(
                'transactions.id as id',
                'transactions.invoice_no as invoice_no',
                'transactions.res_table_id as res_table_id',
                'transactions.res_waiter_id as res_waiter_id',
                'res_tables.name as table_name',
                'transactions.final_total as final_total'
            )
            ->with('sell_lines')
            ->groupBy('transactions.id')
            ->orderBy('transactions.id', 'DESC')
            ->get();
            
            if(count($transactions) > 0 && $q != '') {
                return view('restaurant.orders.checkout-result', compact('transactions'));
            } else {
                return '<div style="margin-top: 20px">No Result Found!</div>';
            }
        }

    }


    public function userCheckHasPin(Request $request)
    {
        $user_id = $request->input('user_id');
        $user = User::where('id', $user_id)->first();
        if(isset($user->sale_return_pin)) {
            $output = [
                'success' => true
            ];
        } else {
            $output = [
                'success' => false,
                'msg' => __('business.user_no_pin'),
            ];
        }
        return $output;
    }

    public function userCheckPin(Request $request)
    {

        $user_id = $request->input('user_id');
        $pin = $request->input('pin');
        $user = User::where('id', $user_id)->where('sale_return_pin', $pin)->first();

        if($user) {
            $output = [
                'success' => true,
                'msg' => __('business.sucess_pin')
            ];
        } else {
            $output = [
                'success' => false,
                'msg' => __('business.invalid_pin')
            ];
        }
        return $output;

    }


}
