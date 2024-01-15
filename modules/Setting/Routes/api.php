<?php

use Illuminate\Support\Facades\Route;

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

Route::group([
    'prefix' => 'admin',
], function () {
    Route::group([
        'middleware' => 'auth:api',
    ], function () {
        Route::group([
            'prefix' => 'config',
        ], function () {
            Route::get('/shipping-discount', [\Modules\Setting\Http\Controllers\SettingController::class, 'getShippingDiscount']);
            Route::post('/shipping-discount', [\Modules\Setting\Http\Controllers\SettingController::class, 'createShippingDiscount']);
            Route::get('/leave-limit', [\Modules\Setting\Http\Controllers\SettingController::class, 'getLeaveLimit']);
            Route::post('/leave-limit', [\Modules\Setting\Http\Controllers\SettingController::class, 'createLeaveLimit']);
        });

        Route::group([
            'prefix' => 'delivery-settings',
        ], function () {
            Route::get('prices', [\Modules\Setting\Http\Controllers\SettingController::class, 'getDeliveryPrices']);
            Route::post('prices', [\Modules\Setting\Http\Controllers\SettingController::class, 'saveDeliveryPrices']);
        });

        Route::group([
            'prefix' => 'attendance-settings',
        ], function () {
            Route::get('/location', [\Modules\Setting\Http\Controllers\SettingController::class, 'getTimeLocation']);
            Route::post('/location', [\Modules\Setting\Http\Controllers\SettingController::class, 'updateTimeLocation']);
            Route::get('/wifi-address', [\Modules\Setting\Http\Controllers\SettingController::class, 'getWifiAddress']);
            Route::post('/wifi-address', [\Modules\Setting\Http\Controllers\SettingController::class, 'updateWifiAddress']);
            Route::get('/work-time', [\Modules\Setting\Http\Controllers\SettingController::class, 'getWorkTime']);
            Route::post('/work-time', [\Modules\Setting\Http\Controllers\SettingController::class, 'saveWorkTime']);
        });
    });
});
