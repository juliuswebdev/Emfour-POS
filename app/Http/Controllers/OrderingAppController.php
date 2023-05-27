<?php

namespace App\Http\Controllers;

use App\User;
use App\Category;
use App\Product;
use App\Variation;
use App\Transaction;
use App\BusinessLocation;
use App\Restaurant\ResTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Hash;
use Validator;

class OrderingAppController extends Controller
{

    public function login(Request $request)
    {
        $rules = [
            'username' => 'required',
            'password' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response([
                'message' => 'Username and password not matched.',
                'errors' => $validator->messages()
            ], 401);
        } else {

            $user = User::where('username', $request->username)
            ->select(
                'id',
                'user_type',
                'first_name',
                'last_name', 
                'username',
                'email',
                'password',
                'business_id',
                'location_id',
                'address',
                'contact_no',
                'language'
            )    
            ->first();

            if(!$user || !Hash::check($request->password, $user->password)) {
                return response([
                    'message' => 'Username and password not matched.'
                ], 401);
            }

            $business_id = $user->business_id;

            $categories = Category::where('business_id', $business_id)
                ->selectRaw("
                    id,
                    name,
                    business_id,
                    short_code,
                    parent_id,
                    category_type,
                    CONCAT('".env('APP_URL')."/uploads/category_logos/', logo) as banner
                ")
                ->where('parent_id', '=',  0)
                ->with(['sub_categories'])
                ->get();

            $tables = ResTable::where('business_id', $business_id)
                ->select(
                    'id',
                    'name',
                    'description',
                    'business_id',
                    'location_id'
                    )
                ->get();

            $business_locations = BusinessLocation::where('business_id', $business_id)
                ->select(
                    'id',
                    'business_id',
                    'name',
                    'landmark',
                    'country',
                    'state',
                    'city',
                    'zip_code',
                    'mobile',
                    'website'
                )    
                ->get();


            $token = $user->createToken('auth_token')->accessToken;

            return response([
                'message' => 'Login Success!',
                'user' => $user,
                'permitted_locations' => $user->permitted_locations(),
                'categories' => $categories,
                'business_locations' => $business_locations,
                'tables' => $tables,
                'token' => $token
            ], 200);
        }

    }

    public function getProducts(Request $request)
    {
        $get = $request->all();

        $rules = [
            'location_id' => 'required',
            'category_id' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return response([
                'message' => 'Missing parameters!',
                'errors' => $validator->messages()
            ], 401);

        } else {

            $query = Product::leftJoin('product_locations', 'product_locations.product_id', '=', 'products.id');
                                
            if(isset($get['q'])) {
                $query->where('products.name', 'LIKE', "%{$get['q']}%");
            }
                        
            $products = $query->where('product_locations.location_id', '=', $get['location_id'])
            ->where('products.category_id', '=', $get['category_id'])
            ->orWhere('products.sub_category_id', '=', $get['category_id'])->with(['variations'])->get();

            return response([
                $products,
            ], 200);

        }
    }

    public function getProduct($product_id)
    {

        $product = Product::where('id', $product_id)->with(['variations'])->first();
        $modifier_sets_temp = DB::table('res_product_modifier_sets')->where('product_id', $product->id)->get();

        $modifier_sets = [];
        foreach($modifier_sets_temp as $item) {
            $modifier_sets[] = Product::where('id', $item->modifier_set_id)->with(['variations'])->first();
        }
    
        return response([
            'product' => $product,
            'add_ons' => $modifier_sets
        ], 200);
        
    }

    public function getOrders(Request $request)
    {

        $get = $request->all();

        $rules = [
            'location_id' => 'required',
            'waiter_id' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return response([
                'message' => 'Missing parameters!',
                'errors' => $validator->messages()
            ], 401);

        } else {

            $orders = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                ->leftjoin(
                    'business_locations AS bl',
                    'transactions.location_id',
                    '=',
                    'bl.id'
                )
                ->leftjoin(
                    'res_tables AS rt',
                    'transactions.res_table_id',
                    '=',
                    'rt.id'
                )
                ->where('transactions.location_id', $get['location_id'])
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final')
                ->where('transactions.res_waiter_id', $get['waiter_id'])
                ->select(
                    'transactions.*',
                    'contacts.name as customer_name',
                    'bl.name as business_location',
                    'rt.name as table_name'
                )
                ->orderBy('created_at', 'desc')
                ->get();;

            return response([
                $orders,
            ], 200);

        }

    }


    public function getOrder(Request $request, $order_id)
    {

        $get = $request->all();

        $rules = [
            'location_id' => 'required',
            'waiter_id' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return response([
                'message' => 'Missing parameters!',
                'errors' => $validator->messages()
            ], 401);

        } else {

            $order = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                ->leftjoin(
                    'business_locations AS bl',
                    'transactions.location_id',
                    '=',
                    'bl.id'
                )
                ->leftjoin(
                    'res_tables AS rt',
                    'transactions.res_table_id',
                    '=',
                    'rt.id'
                )
                ->where('transactions.id', $order_id)
                ->where('transactions.location_id', $get['location_id'])
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final')
                ->where('transactions.res_waiter_id', $get['waiter_id'])
                ->select(
                    'transactions.*',
                    'contacts.name as customer_name',
                    'bl.name as business_location',
                    'rt.name as table_name'
                )
                ->with(['sell_lines'])
                ->orderBy('created_at', 'desc')
                ->first();

            return response([
                $order,
            ], 200);

        }

    }

}