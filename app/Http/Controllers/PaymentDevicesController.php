<?php

namespace App\Http\Controllers;



use Datatables;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

use App\PaymentDeviceModel;
use App\PaymentDevice;
use App\BusinessLocation;
use App\User;

class PaymentDevicesController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function index()
    {
        if (! auth()->user()->can('access_payment_devices')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {

            $business_id = request()->session()->get('user.business_id');

            $payment_devices = PaymentDevice::where('payment_devices.business_id', $business_id)
            ->leftJoin('business_locations', 'business_locations.id', '=', 'payment_devices.location_id')
            ->leftJoin('payment_device_models', 'payment_device_models.id', '=', 'payment_devices.device_model_id')
            ->select(
                'payment_devices.id as id',
                'payment_devices.device_model_id as device_model_id',
                'payment_devices.name as payment_device_name',
                'business_locations.name as business_location_name',
                'payment_device_models.name as payment_device_model_name',
                'payment_devices.settings as payment_device_settings',
                
            );    
            return Datatables::of($payment_devices)
                ->editColumn(
                    'payment_device_settings',
                    function($row) {
                        $settings = json_decode($row->payment_device_settings);
                        $html = '';
                        foreach($settings as $key => $item){
                            $html .= '<strong>'.__('payment_device.'.$key).':  </strong><i>'.$item. '</i>, ';
                        }
                        return substr($html, 0, -2);;
                    }
                )
                ->addColumn(
                    'action',
                    '
                    <a href="{{action(\'App\Http\Controllers\PaymentDevicesController@edit\', [$id])}}" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</a>
                        &nbsp;
                    <button data-href="{{action(\'App\Http\Controllers\PaymentDevicesController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_printer_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                    '
                )
                ->removeColumn(['id', 'device_model_id'])
                ->rawColumns(['payment_device_settings', 'action'])
                ->make(true);
        }

        return view('payment_device.index');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function create()
    {

        if (! auth()->user()->can('access_payment_devices')) {
            abort(403, 'Unauthorized action.');
        }

        $payment_device_model = PaymentDeviceModel::all();

        $business_id = request()->session()->get('user.business_id');
        $business_locations = BusinessLocation::where('business_id', $business_id)->get();

        return view('payment_device.create', compact('payment_device_model', 'business_locations'));

    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
    */
    public function store(Request $request)
    {
        try {

            if (! auth()->user()->can('access_payment_devices')) {
                abort(403, 'Unauthorized action.');
            }

            $business_id = request()->session()->get('user.business_id');
            $user_id = request()->session()->get('user.id');

            $device_model_id = $request->input('payment_device_model');
            $payment_devices = new PaymentDevice;
            $payment_devices->name = $request->input('name');
            $payment_devices->business_id = $business_id;
            $payment_devices->location_id = $request->input('business_locations');
            $payment_devices->device_model_id = $device_model_id;

            if($device_model_id == 1) {
                $payment_devices->settings = json_encode($request->input('settings1'));
            }

            $payment_devices->created_by = $user_id;
            $payment_devices->save();

            $output = ['success' => 1,
                'msg' => __('payment_device.added_success'),
            ];

        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return redirect('modules/payment-devices')->with('status', $output);
        
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! auth()->user()->can('access_payment_devices')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $business_locations = BusinessLocation::where('business_id', $business_id)->get();

        $payment_device_model = PaymentDeviceModel::all();
        $payment_device = PaymentDevice::where('id', $id)->where('business_id', $business_id)->first();

        return view('payment_device.edit', compact('payment_device', 'business_locations', 'payment_device_model'));

    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {

            if (! auth()->user()->can('access_payment_devices')) {
                abort(403, 'Unauthorized action.');
            }

            $business_id = request()->session()->get('user.business_id');
            $user_id = request()->session()->get('user.id');

            $device_model_id = $request->input('payment_device_model');
            $payment_devices = PaymentDevice::find($id);
            $payment_devices->name = $request->input('name');
            $payment_devices->business_id = $business_id;
            $payment_devices->location_id = $request->input('business_locations');
            $payment_devices->device_model_id = $device_model_id;

            if($device_model_id == 1) {
                $payment_devices->settings = json_encode($request->input('settings1'));
            }

            $payment_devices->created_by = $user_id;
            $payment_devices->update();

            $output = ['success' => 1,
                'msg' => __('payment_device.update_success'),
            ];

        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return redirect('modules/payment-devices')->with('status', $output);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! auth()->user()->can('access_payment_devices')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->user()->business_id;

                $payment_devices = PaymentDevice::where('business_id', $business_id)->findOrFail($id);
                $payment_devices->delete();

                $output = ['success' => true,
                    'msg' => __('payment_device.deleted_success'),
                ];
            } catch (\Exception $e) {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = ['success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $location_id
     * @return \Illuminate\Http\Response
     */
    public function list($location_id)
    {
        if (! auth()->user()->can('access_payment_devices')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $payment_devices = PaymentDevice::where('location_id', $location_id)->get();
            return view('payment_device.list', compact('payment_devices'));
        } 
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
    */
    public function selectDefault(Request $request)
    {
        if (! auth()->user()->can('access_payment_devices')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $user_id = request()->user()->id;

            try {
                $user = User::find($user_id);
                $user->default_payment_device = $request->input('payment_device');
                $user->update();

                $output = ['success' => true,
                            'msg' => __('payment_device.added_success'),
                        ];

            } catch (\Exception $e) {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = ['success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }
            return $output;
        } 
    }


    public function paymentDejavoo(Request $request)
    {

        try {

            $payment_type = $request->input('payment_type');
            $trans_type = $request->input('trans_type');
            $amount = $request->input('amount');
            $tip = $request->input('tip');
            $inv_num = $request->input('invoice_num');
            $ref_id = $request->input('ref_id');

            $register_id = '116058001';
            $auth_key = 'cyzdiH7Ca6';

            $xml = '<request>
                        <PaymentType>'. $payment_type .'</PaymentType>
                        <TransType>'. $trans_type .'</TransType>
                        <Amount>'. $amount .'</Amount>
                        <Tip>'. $tip .'</Tip>
                        <CustomFee>0</CustomFee>
                        <Frequency>OneTime</Frequency>
                        <InvNum>'. $inv_num .'</InvNum>
                        <RefId>'. $ref_id .'</RefId>
                        <RegisterId>'. $register_id .'</RegisterId>
                        <AuthKey>'. $auth_key .'</AuthKey>
                        <PrintReceipt>No</PrintReceipt>
                        <SigCapture>No</SigCapture>
                    </request>';

            $response = Http::get('HTTPS://spinpos.net:443/spin/cgi.html?TerminalTransaction='.$xml);
            $xml_data = $response->body();
            $data = simplexml_load_string($xml_data);
            
            $output = ['success' => 1,
                'msg' => __('business.settings_updated_success'),
                'data' => $data
            ];

        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
        
    }

}