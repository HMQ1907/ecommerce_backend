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
    'middleware' => 'guest:api',
], function () {
    Route::get('config', [\App\Http\Controllers\ConfigController::class, 'index']);
});

Route::group([
    'prefix' => 'public',
], function () {
    Route::get('provinces', [\App\Http\Controllers\PublicController::class, 'getProvinces']);
    Route::get('districts', [\App\Http\Controllers\PublicController::class, 'getDistricts']);
    Route::get('wards', [\App\Http\Controllers\PublicController::class, 'getWards']);
});
