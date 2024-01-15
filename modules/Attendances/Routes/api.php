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
    Route::group([
        'prefix' => 'attendances',
    ], function () {
        Route::post('check-in', [\Modules\Attendances\Http\Controllers\AttendancesController::class, 'checkIn']);
        Route::post('check-out', [\Modules\Attendances\Http\Controllers\AttendancesController::class, 'checkOut']);
        Route::get('employees', [\Modules\Attendances\Http\Controllers\AttendancesController::class, 'getEmployeeAttendances']);
        Route::get('employees/{id}', [\Modules\Attendances\Http\Controllers\AttendancesController::class, 'getEmployeeAttendance']);
        Route::delete('bulk/destroy', [\Modules\Attendances\Http\Controllers\AttendancesController::class, 'bulkDestroy']);
        Route::get('total-delay', [\Modules\Attendances\Http\Controllers\AttendancesController::class, 'getTotalDelayTime']);
        Route::get('total-early', [\Modules\Attendances\Http\Controllers\AttendancesController::class, 'getTotalEarlyTime']);
        Route::get('total-work', [\Modules\Attendances\Http\Controllers\AttendancesController::class, 'getTotalWorkTime']);
        Route::get('count-attendance', [\Modules\Attendances\Http\Controllers\AttendancesController::class, 'getEmployeeAttendanceCount']);
        Route::get('export', [\Modules\Attendances\Http\Controllers\AttendancesController::class, 'exportAttendance']);
    });
    Route::apiResource('attendances', \Modules\Attendances\Http\Controllers\AttendancesController::class);
});
