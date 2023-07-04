<?php

namespace App\Http\Controllers;

use Datatables;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

use App\BusinessAllowedIP;
use App\BusinessLocation;
use App\User;
use DB;
use App\Utils\ModuleUtil;
use Illuminate\Support\Facades\Http;

class BusinessAllowedIPController extends Controller
{


    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param  Util  $commonUtil
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        $is_admin =  $this->moduleUtil->is_admin(auth()->user(), $business_id);
        if (!$is_admin) {
            abort(403, 'Unauthorized action.');
        }


        if (request()->ajax()) {

            $business_ips = BusinessAllowedIP::where('business_allowed_ips.business_id', $business_id)
            ->leftJoin('business_locations', 'business_locations.id', '=', 'business_allowed_ips.location_id')
            ->select(
                'business_allowed_ips.id as id',
                'business_allowed_ips.name as name',
                'business_allowed_ips.ip_address as ip_address',
                DB::raw("CONCAT(business_locations.name, '-', business_locations.location_id) as location"),
                'business_allowed_ips.description as description',
            )->orderBy('business_allowed_ips.location_id', 'ASC'); 

            return Datatables::of($business_ips)
                ->addColumn(
                    'action',
                    '
                    <a href="{{action(\'App\Http\Controllers\BusinessAllowedIPController@edit\', [$id])}}" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</a>
                        &nbsp;
                    <button data-href="{{action(\'App\Http\Controllers\BusinessAllowedIPController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_printer_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                    '
                )
                ->removeColumn(['id'])
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('business_allowed_ips.index');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function create()
    {

        $business_id = request()->session()->get('user.business_id');
        $is_admin =  $this->moduleUtil->is_admin(auth()->user(), $business_id);
        if (!$is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $business_locations = BusinessLocation::where('business_id', $business_id)->get();

        return view('business_allowed_ips.create', compact('business_locations'));

    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
    */
    public function store(Request $request)
    {

        $business_id = request()->session()->get('user.business_id');
        $is_admin =  $this->moduleUtil->is_admin(auth()->user(), $business_id);
        if (!$is_admin) {
            abort(403, 'Unauthorized action.');
        }

        try {

            $user_id = request()->session()->get('user.id');

            $business_ip = new BusinessAllowedIP;
            $business_ip->name = $request->input('name');
            $business_ip->business_id = $business_id;
            $business_ip->location_id = $request->input('location_id');
            $business_ip->description = $request->input('description');
            $business_ip->ip_address = $request->input('ip_address');
            $business_ip->created_by = $user_id;
            $business_ip->save();

            $output = ['success' => 1,
                'msg' => __('business_ip.added_success'),
            ];

        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return redirect('modules/business-ips')->with('status', $output);
        
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $business_id = request()->session()->get('user.business_id');
        $is_admin =  $this->moduleUtil->is_admin(auth()->user(), $business_id);
        if (!$is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $business_locations = BusinessLocation::where('business_id', $business_id)->get();

        $business_ip = BusinessAllowedIP::where('id', $id)->where('business_id', $business_id)->first();

        return view('business_allowed_ips.edit', compact('business_ip', 'business_locations'));

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

        $business_id = request()->session()->get('user.business_id');
        $is_admin =  $this->moduleUtil->is_admin(auth()->user(), $business_id);
        if (!$is_admin) {
            abort(403, 'Unauthorized action.');
        }

        try {

            $user_id = request()->session()->get('user.id');

            $business_ip = BusinessAllowedIP::find($id);
            $business_ip->name = $request->input('name');
            $business_ip->location_id = $request->input('location_id');
            $business_ip->description = $request->input('description');
            $business_ip->ip_address = $request->input('ip_address');
            $business_ip->update();

            $output = ['success' => 1,
                'msg' => __('business_ip.update_success'),
            ];

        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return redirect('modules/business-ips')->with('status', $output);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $business_id = request()->session()->get('user.business_id');
        $is_admin =  $this->moduleUtil->is_admin(auth()->user(), $business_id);
        if (!$is_admin) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {

                $business_ip = BusinessAllowedIP::where('business_id', $business_id)->findOrFail($id);
                $business_ip->delete();

                $output = ['success' => true,
                    'msg' => __('business_ip.delete_success'),
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


}