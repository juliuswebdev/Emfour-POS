<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/pos/amount', function() {
    $data = array('amount' => 1);
    return response($data, 200);
});

Route::post('/authorize-net', [App\Http\Controllers\AuthorizeNetController::class, 'store'])->name('api.authorize-net');

Route::get('/get-locations/{id}', function($id) {
    $locations = App\BusinessLocation::where('business_id', $id)->get();
    return response($locations, 200);
});
