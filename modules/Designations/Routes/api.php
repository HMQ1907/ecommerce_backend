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
    'middleware' => 'auth:api',
], function () {
    Route::group(['prefix' => 'designations'], function () {
        Route::delete('bulk/destroy', [\Modules\Designations\Http\Controllers\DesignationsController::class, 'bulkDestroy']);
    });
    Route::apiResource('designations', \Modules\Designations\Http\Controllers\DesignationsController::class);
});
