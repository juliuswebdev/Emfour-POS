<?php

use Illuminate\Http\Request;

use App\Http\Controllers\OrderingAppController;
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

Route::post('/authorize-net', [App\Http\Controllers\AuthorizeNetController::class, 'store'])->name('api.authorize-net');

Route::get('/get-locations/{id}', function($id) {
    $locations = App\BusinessLocation::where('business_id', $id)->get();
    return response($locations, 200);
});

Route::middleware(['cors'])->group(function () {
    Route::post('/dynamic-pricing/store', [App\Http\Controllers\DynamicPricingController::class, 'store']);
    Route::get('/dynamic-pricing/test', [App\Http\Controllers\DynamicPricingController::class, 'test']);
});


// API Ordering APP
Route::prefix('v1')->group(function() {

    Route::post('/login', [OrderingAppController::class, 'login']);

    Route::middleware('auth:api')->group(function() {
        Route::get('/products', [OrderingAppController::class, 'getProducts']);
        Route::get('/product/{product_id}', [OrderingAppController::class, 'getProduct']);
        Route::get('/orders', [OrderingAppController::class, 'getOrders']);
        Route::get('/order/{order_id}', [OrderingAppController::class, 'getOrder']);
        Route::post('/order/mark-as-completed/{order_id}', [OrderingAppController::class, 'markAsCompleted']);
    });

});

