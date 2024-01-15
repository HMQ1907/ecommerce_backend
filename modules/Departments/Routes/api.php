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
    Route::group(['prefix' => 'departments'], function () {
        Route::delete('bulk/destroy', [\Modules\Departments\Http\Controllers\DepartmentsController::class, 'bulkDestroy']);
        Route::delete('{departmentId}/teams/{teamId}', [\Modules\Departments\Http\Controllers\DepartmentsController::class, 'deleteTeamInDepartment']);
        Route::get('overview-chart', [\Modules\Departments\Http\Controllers\DepartmentsController::class, 'getOverviewChart']);

        Route::get('export/salary-report', [\Modules\Departments\Http\Controllers\DepartmentsController::class, 'exportSalary']);
        Route::get('export/remuneration', [\Modules\Departments\Http\Controllers\DepartmentsController::class, 'export']);
        Route::get('export/gender', [\Modules\Departments\Http\Controllers\DepartmentsController::class, 'exportGender']);
    });
    Route::apiResource('departments', \Modules\Departments\Http\Controllers\DepartmentsController::class);
});
