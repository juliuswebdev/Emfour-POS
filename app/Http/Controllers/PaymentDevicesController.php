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
use Illuminate\Support\Facades\Http;

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
            if($payment_devices->count() == 0){
                return "";
            }else{
                return view('payment_device.list', compact('payment_devices'));
            }
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

    public function paymentXmlResponse(Request $request){
        $xml = "<xmp>
        <response>
        <RefId>0062</RefId>
        <RegisterId>116058001</RegisterId>
        <TransNum>16</TransNum>
        <ResultCode>0</ResultCode>
        <RespMSG>APPROVAL%20TAS961</RespMSG>
        <Message>Approved</Message>
        <AuthCode>TAS961</AuthCode>
        <PNRef>314808500011</PNRef>
        <PaymentType>Credit</PaymentType>
        <Voided>false</Voided>
        <TransType>Sale</TransType>
        <SN>WP31501Q43201497</SN>
        <ExtData>Amount=1.00,InvNum=16,CardType=VISA,BatchNum=2,Tip=0.00,CashBack=0.00,Fee=0.00,AcntLast4=8679,BIN=402321,Name=CARDHOLDER%2fVISA,SVC=0.00,TotalAmt=1.00,DISC=0.00,Donation=0.00,SHFee=0.00,RwdPoints=0,RwdBalance=0,RwdIssued=,EBTFSLedgerBalance=,EBTFSAvailBalance=,EBTFSBeginBalance=,EBTCashLedgerBalance=,EBTCashAvailBalance=,EBTCashBeginBalance=,RewardCode=,AcqRefData=,ProcessData=,RefNo=,RewardQR=,Language=English,EntryType=CHIP Contactless,table_num=0,clerk_id=,ticket_num=,ControlNum=,TaxCity=0.00,TaxState=0.00,TaxReducedState=0.00,Cust1=,Cust1Value=,Cust2=,Cust2Value=,Cust3=,Cust3Value=,AcntFirst4=4023,TaxAmount=0.00,AVSRsp=,CVVRsp=,TransactionID=000000000360595,ExtraHostData=00%2dAPPROVAL%2dApproved%20and%20Completed</ExtData>
        <EMVData>AID=A0000000980840,AppName=US DEBIT,TVR=0000000000,TSI=0000,IAD=01020304050607080000,ARC=</EMVData>
        <Receipt>Merchant=%3cIMG%20src%3d%22%22%2f%3e%3cBR%2f%3e%3cLG%3e%3cC%3eEM%20FOUR%20SOLUTIONS%3c%2fC%3e%3c%2fLG%3e%3cBR%2f%3e%3cC%3eQD4%20TEST%20TERMINAL%3c%2fC%3e%3cBR%2f%3e%3cBR%2f%3e%3cL%3e05%2f28%2f2023%3c%2fL%3e%3cR%3e%204%3a38%3c%2fR%3e%3cBR%2f%3e%3cBR%2f%3e%3cLG%3e%3cC%3eSale%3c%2fC%3e%3c%2fLG%3e%3cBR%2f%3e%3cBR%2f%3e%3cLG%3e%3cL%3eTrans%20%23%3a%2016%3c%2fL%3e%3cR%3eBatch%20%23%3a%202%3c%2fR%3e%3c%2fLG%3e%3cBR%2f%3e%3cBR%2f%3e%3cL%3eCREDIT%20CARD%3c%2fL%3e%3cBR%2f%3e%3cL%3eVISA%3c%2fL%3e%3cR%3eCHIP%20READ%3c%2fR%3e%3cBR%2f%3e%3cL%3eEntry%20Type%3a%3c%2fL%3e%3cR%3eCONTACTLESS%3c%2fR%3e%3cBR%2f%3e%3cL%3e*****8679%3c%2fL%3e%3cR%3e%2f*%3c%2fR%3e%3cBR%2f%3e%3cL%3eReference%20Id%3a%3c%2fL%3e%3cR%3e0062%3c%2fR%3e%3cBR%2f%3e%3cBR%2f%3e%3cBR%2f%3e%3cLG%3e%3cL%3eAMOUNT%3a%3c%2fL%3e%3cR%3eUSD%20%241%2e00%3c%2fR%3e%3c%2fLG%3e%3cBR%2f%3e%3cBR%2f%3e%3cBR%2f%3e%3cBR%2f%3e%3cLG%3e%3cL%3eTIP%20AMT%3a%3c%2fL%3e%3cR%3e%24%5f%5f%5f%5f%5f%2e%5f%5f%3c%2fR%3e%3c%2fLG%3e%3cBR%2f%3e%3cBR%2f%3e%3cLG%3e%3cL%3eTOTAL%20AMT%3a%3c%2fL%3e%3cR%3eUSD%20%24%5f%5f%5f%5f%5f%2e%5f%5f%3c%2fR%3e%3c%2fLG%3e%3cBR%2f%3e%3cBR%2f%3e%3cBR%2f%3e%3cB%3e%3cC%3eTip%20Suggestions%3c%2fC%3e%3c%2fB%3e%3cBR%2f%3e%3cBR%2f%3e%3cL%3ePercent%20Tip%3c%2fL%3e%3cR%3eTotal%3c%2fR%3e%3cBR%2f%3e%3cL%3e15%25%20%20%20%20%20%240%2e15%3c%2fL%3e%3cR%3e1%2e15%3c%2fR%3e%3cBR%2f%3e%3cL%3e20%25%20%20%20%20%20%240%2e20%3c%2fL%3e%3cR%3e1%2e20%3c%2fR%3e%3cBR%2f%3e%3cL%3e25%25%20%20%20%20%20%240%2e25%3c%2fL%3e%3cR%3e1%2e25%3c%2fR%3e%3cBR%2f%3e%3cBR%2f%3e%3cBR%2f%3e%3cBR%2f%3e%3cL%3eResp%3a%3c%2fL%3e%3cR%3eAPPROVAL%20TAS961%3c%2fR%3e%3cBR%2f%3e%3cL%3eCode%3a%3c%2fL%3e%3cR%3eTAS961%3c%2fR%3e%3cBR%2f%3e%3cL%3eRef%20%23%3a%3c%2fL%3e%3cR%3e314808500011%3c%2fR%3e%3cBR%2f%3e%3cL%3eTransID%3a%3c%2fL%3e%3cR%3e000000000360595%3c%2fR%3e%3cBR%2f%3e%3cBR%2f%3e%3cL%3eApp%20Name%3a%3c%2fL%3e%3cR%3eUS%20DEBIT%3c%2fR%3e%3cBR%2f%3e%3cL%3eAID%3a%3c%2fL%3e%3cR%3eA0000000980840%3c%2fR%3e%3cBR%2f%3e%3cL%3eTVR%3a%3c%2fL%3e%3cR%3e0000000000%3c%2fR%3e%3cBR%2f%3e%3cL%3eATC%3a%3c%2fL%3e%3cR%3e002C%3c%2fR%3e%3cBR%2f%3e%3cL%3eTC%3a%3c%2fL%3e%3cR%3eCDE1D49CC743F0CD%3c%2fR%3e%3cBR%2f%3e%3cL%3eIAD%3a%3c%2fL%3e%3cR%3e06011203A00000%3c%2fR%3e%3cBR%2f%3e%3cBR%2f%3e%3cBR%2f%3e%3cBR%2f%3e%3cC%3eCardholder%20acknowledges%3c%2fC%3e%3cBR%2f%3e%3cC%3ereceipt%20of%20goods%20and%3c%2fC%3e%3cBR%2f%3e%3cC%3eobligations%20set%20forth%3c%2fC%3e%3cBR%2f%3e%3cC%3eby%20the%20cardholder%26apos%3bs%3c%2fC%3e%3cBR%2f%3e%3cC%3eagreement%20with%20issuer%2e%3c%2fC%3e%3cBR%2f%3e%3cBR%2f%3e%3cIMG%20src%3d%22sig%2d20230528%2d043853%22%2f%3e%3cBR%2f%3e%3cBR%2f%3e%3cC%3eX%5f%5f%5f%5f%5f%5f%5f%5f%5f%5f%5f%5f%5f%5f%5f%5f%5f%5f%5f%5f%3c%2fC%3e%3cBR%2f%3e%3cC%3eCARDHOLDER%2fVISA%3c%2fC%3e%3cBR%2f%3e%3cBR%2f%3e%3cB%3e%3cC%3eMERCHANT%20COPY%3c%2fC%3e%3c%2fB%3e%3cBR%2f%3e%3cBR%2f%3e%3cBR%2f%3e%3cBR%2f%3e%3cBR%2f%3e%3cBR%2f%3e</Receipt>
        <CVMResult>2</CVMResult>
        </response>
        </xmp>";
        return response($xml, 200)->header('Content-Type', 'application/xml');
    }


    public function checkTerminalIsActive($register_id){
        $url = "https://spinpos.net/spin/GetTerminalStatus?RegisterID=".$register_id;
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }   
    /**
     * Function use for create the payment in terminal device
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
    */
    public function paymentInit($payment_type, $trans_type, $amount, $invoice_num){

        try {
            $active_payment_device = PaymentDevice::select('settings')->where('id', auth()->user()->default_payment_device)->first();
            $payment_setting = json_decode($active_payment_device->settings, true);
            
            $register_id = $payment_setting['register_id'];
            $auth_key = $payment_setting['auth_key'];
            //$amount = "1.10";

            $check_terminal = $this->checkTerminalIsActive($register_id);
            if($check_terminal == "Online"){
            
                $xml = '<request>
                            <PaymentType>'. $payment_type .'</PaymentType>
                            <TransType>'. $trans_type .'</TransType>
                            <Amount>'. $amount .'</Amount>
                            <Tip></Tip>
                            <CustomFee>0</CustomFee>
                            <Frequency>OneTime</Frequency>
                            <InvNum></InvNum>
                            <RefId>'. $invoice_num .'</RefId>
                            <RegisterId>'. $register_id .'</RegisterId>
                            <AuthKey>'. $auth_key .'</AuthKey>
                            <PrintReceipt>No</PrintReceipt>
                            <SigCapture>No</SigCapture>
                        </request>';

                $request_url = "HTTPS://spinpos.net:443/spin/cgi.html?TerminalTransaction=".$xml;

                $response = Http::timeout(1000)->get($request_url);
                $xml_data = $response->body();
                $xml_response = simplexml_load_string($xml_data);
                $json_response = json_encode($xml_response);
                $array_response = json_decode($json_response,TRUE);

                $payment_response_message = $array_response['response']['Message'];
                //$payment_response_message = "Approved";
                if($payment_response_message == "Approved"){
                    $output = [
                        'success' => 1,
                        'msg' => __('business.settings_updated_success'),
                        'data' => $array_response
                    ];    
                }else{
                    $output = [
                        'success' => 0,
                        'msg' => $payment_response_message,
                        'data' => $array_response
                    ];  
                }
            }else{
                $output = [
                    'success' => 0,
                    'msg' => __('business.your_device_is_offline'),
                    'data' => []
                ];  
            }

        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }


}