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

Route::get('/get-locations/{id}', [App\Http\Controllers\Restaurant\BookingController::class, 'getLocations']);
Route::post('/booking/store', [App\Http\Controllers\Restaurant\BookingController::class, 'postPublicBookingAPI']);


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


