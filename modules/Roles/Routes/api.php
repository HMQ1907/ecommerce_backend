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
    Route::apiResource('roles', \Modules\Roles\Http\Controllers\RolesController::class);
    Route::get('module-permissions', [\Modules\Roles\Http\Controllers\RolesController::class, 'getModulePermissions']);
});

Route::get('permissions', [\Modules\Roles\Http\Controllers\RolesController::class, 'getPermissions']);
