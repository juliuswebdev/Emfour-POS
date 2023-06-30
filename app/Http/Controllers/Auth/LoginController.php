<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\User;
use App\BusinessAllowedIP;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * All Utils instance.
     */
    protected $businessUtil;

    protected $moduleUtil;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(BusinessUtil $businessUtil, ModuleUtil $moduleUtil)
    {
        $this->middleware('guest')->except('logout');
        $this->businessUtil = $businessUtil;
        $this->moduleUtil = $moduleUtil;
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Change authentication from email to username
     *
     * @return void
     */
    public function username()
    {
        return 'username';
    }

    public function logout()
    {
        $user_id = request()->session()->get('user.id');
        $user = User::find($user_id);
        $user->default_payment_device = 0;
        $user->update();
        
        $this->businessUtil->activityLog(auth()->user(), 'logout');

        request()->session()->flush();
        \Auth::logout();

   


        return redirect('/login');
    }

    /**
     * The user has been authenticated.
     * Check if the business is active or not.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        $this->businessUtil->activityLog($user, 'login', null, [], false, $user->business_id);

        if (! $user->business->is_active) {
            \Auth::logout();

            return redirect('/login')
              ->with(
                  'status',
                  ['success' => 0, 'msg' => __('lang_v1.business_inactive')]
              );
        } elseif ($user->status != 'active') {
            \Auth::logout();

            return redirect('/login')
              ->with(
                  'status',
                  ['success' => 0, 'msg' => __('lang_v1.user_inactive')]
              );
        } elseif (! $user->allow_login) {
            \Auth::logout();

            return redirect('/login')
                ->with(
                    'status',
                    ['success' => 0, 'msg' => __('lang_v1.login_not_allowed')]
                );
        } elseif (($user->user_type == 'user_customer') && ! $this->moduleUtil->hasThePermissionInSubscription($user->business_id, 'crm_module')) {
            \Auth::logout();

            return redirect('/login')
                ->with(
                    'status',
                    ['success' => 0, 'msg' => __('lang_v1.business_dont_have_crm_subscription')]
                );

        } else if( !$this->userAllowed($user) ) {

            \Auth::logout();
            return redirect('/login')
                ->with(
                    'status',
                    ['success' => 0, 'msg' => __('lang_v1.user_ip_login_failed')]
                );

        }
    }

    protected function redirectTo()
    {
        $user = \Auth::user();
        if (! $user->can('dashboard.data') && $user->can('sell.create')) {
            return '/pos/create';
        }

        if ($user->user_type == 'user_customer') {
            return 'contact/contact-dashboard';
        }

        return '/home';
    }

    public function filterIP($ip) {
        $ip_arr = explode('.', $ip);
        array_pop($ip_arr);
        $ip = implode('.', $ip_arr);
        return $ip;
    }

    public function userAllowed($user) {
        
        $is_admin = $this->moduleUtil->is_admin($user);
        $enable_ip_restriction = $user->business->enable_ip_restriction;
        $allowed = false;
        if( (!$is_admin) && ($enable_ip_restriction) ){
            
            $clientIP = \Request::getClientIp(true);
            $user_locations = $user->permitted_locations($user->business_id);
            if($user_locations == 'all') {
                $business_allowed_ips = BusinessAllowedIP::where('business_id', $user->business_id)->get();
            } else {
                $business_allowed_ips = BusinessAllowedIP::whereIn('location_id', $user_locations)->get();
            }

            $whitelist_ips = [];
            foreach($business_allowed_ips as $item) {
                $ip = $this->filterIP($item->ip_address);
                array_push($whitelist_ips, $ip);
            }
            
            $client_ip = $this->filterIP($clientIP);
            $allowed = false;

            if(in_array($client_ip, $whitelist_ips)) {
                $allowed = true;
            }
        } else {
            $allowed = true;
        }

        return $allowed;

    }
}
