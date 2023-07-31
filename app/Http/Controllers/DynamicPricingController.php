<?php

namespace App\Http\Controllers;
use App\DynamicPricing;
use App\Business;
use Illuminate\Http\Request;

class DynamicPricingController extends Controller
{

    public function store(Request $request)
    {
        $business_id = $request->input('business_id');
        $rules = $request->input('rules');

        $dp = DynamicPricing::where('business_id', $business_id)->first();

        if($dp) {
            $dp->rules = $rules;
            $dp->update();
        } else {
            $dp = new DynamicPricing;
            $dp->business_id = $business_id;
            $dp->rules = $rules;
            $dp->save();
        }
        return response()->json($dp);
    }

    public function test()
    {

        $active_rules = [];
        $dp = DynamicPricing::where('business_id', 12)->first()->toArray();
        $rules = json_decode($dp['rules']);
        $rules = $rules->rules;
        foreach($rules as $rule) {
            if($rule->active) {
                $active_rules[] = $rule;
            }
        }
        echo '<pre>';
        print_r($active_rules);
        echo '</pre>';
    }

}
