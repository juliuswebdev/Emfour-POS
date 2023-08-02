<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
class WebviewController extends Controller
{

    public function initialWebview(Request $request){
        
        if($request->has('uid') && $request->filled('uid')){
            $user_id = $request->uid;
            $redirect_to = $request->redirect;
            $random = $request->redirect;
            

            Auth::loginUsingId($user_id);
            
            if(Auth::check()){
                if($redirect_to == "POS"){
                    return redirect()->to(url('cash-register/create?ran='.$random));
                }
                if($redirect_to == "ORDERS"){
                    return redirect()->to(url('modules/orders?ran='.$random));
                }   
            }
        }
    }
}
