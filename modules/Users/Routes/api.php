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
    Route::prefix('devices')->group(function () {
        Route::post('/', [\Modules\Users\Http\Controllers\DeviceController::class, 'store']);
        Route::delete('/', [\Modules\Users\Http\Controllers\DeviceController::class, 'destroy']);
    });

    Route::group(['prefix' => 'notifications'], function () {
        Route::get('/', [\Modules\Users\Http\Controllers\NotificationController::class, 'getNotifications']);
        Route::get('mark-all-as-read', [\Modules\Users\Http\Controllers\NotificationController::class, 'markAllAsRead']);
        Route::get('mark-as-read/{id}', [\Modules\Users\Http\Controllers\NotificationController::class, 'markAsRead']);
    });

    Route::post('profile', [\Modules\Users\Http\Controllers\ProfileController::class, 'updateProfile']);
    Route::put('setting', [\Modules\Users\Http\Controllers\ProfileController::class, 'updateUserSetting']);
    Route::put('password', [\Modules\Users\Http\Controllers\ProfileController::class, 'changePassword']);

    Route::group(['prefix' => 'users'], function () {
        Route::put('{id}/password', [\Modules\Users\Http\Controllers\UsersController::class, 'changePassword']);
        Route::post('{id}/roles', [\Modules\Users\Http\Controllers\UsersController::class, 'updateRoles']);
        Route::get('{id}/permissions', [\Modules\Users\Http\Controllers\UsersController::class, 'getPermissions']);
        Route::post('{id}/permissions', [\Modules\Users\Http\Controllers\UsersController::class, 'updatePermissions']);
        Route::post('{id}/status', [\Modules\Users\Http\Controllers\UsersController::class, 'toggleStatus']);
    });
    Route::apiResource('users', \Modules\Users\Http\Controllers\UsersController::class);
    Route::post('current-branch', [\Modules\Users\Http\Controllers\UsersController::class, 'assignBranch']);
});
