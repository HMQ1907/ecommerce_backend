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
        'prefix' => 'employees',
    ], function () {
        Route::delete('bulk/destroy', [\Modules\Employees\Http\Controllers\EmployeesController::class, 'bulkDestroy']);
        Route::post('{employeeId}/message', [\Modules\Employees\Http\Controllers\EmployeesController::class, 'sendMessage']);
        Route::apiResource('{employeeId}/attachments', \Modules\Employees\Http\Controllers\EmployeeAttachmentsController::class);
        Route::get('{employeeId}/attachments/{id}/download', [\Modules\Employees\Http\Controllers\EmployeeAttachmentsController::class, 'download']);
        Route::get('sales-working-month', [\Modules\Employees\Http\Controllers\EmployeesController::class, 'getSalesWorkingMonth']);
        Route::get('working-month', [\Modules\Employees\Http\Controllers\EmployeesController::class, 'getEmployeeWorkingMonth']);
        Route::put('{id}/personal', [\Modules\Employees\Http\Controllers\EmployeesController::class, 'updatePersonal']);
        Route::put('{id}/bank-account', [\Modules\Employees\Http\Controllers\EmployeesController::class, 'updateBankAccount']);
        Route::put('{id}/company', [\Modules\Employees\Http\Controllers\EmployeesController::class, 'updateCompany']);
        Route::get('export/report/salary', [\Modules\Employees\Http\Controllers\EmployeesController::class, 'exportReportSalary']);
        Route::get('report/salary', [\Modules\Employees\Http\Controllers\EmployeesController::class, 'reportSalary']);
        Route::get('statistic', [\Modules\Employees\Http\Controllers\EmployeesController::class, 'employeeStatistic']);
    });

    Route::group([
        'prefix' => 'employee-contracts',
    ], function () {
        Route::get('employee/{employeeId}', [\Modules\Employees\Http\Controllers\EmployeeContractsController::class, 'getEmployeeContractByEmployeeId']);
        Route::get('{id}/files', [\Modules\Employees\Http\Controllers\EmployeeContractsController::class, 'getFiles']);
        Route::delete('{id}/file/{fileId}', [\Modules\Employees\Http\Controllers\EmployeeContractsController::class, 'deleteFile']);
        Route::get('employee-by-type', [\Modules\Employees\Http\Controllers\EmployeeContractsController::class, 'getEmployeeContractByType']);
    });

    Route::group([
        'prefix' => 'reports',
    ], function () {
        Route::get('salary/export', [\Modules\Employees\Http\Controllers\EmployeesController::class, 'reportSalaries']);
    });

    Route::group([
        'prefix' => 'employee-terminations',
    ], function () {
        Route::delete('bulk/destroy', [\Modules\Employees\Http\Controllers\EmployeeTerminationAllowanceController::class, 'bulkDestroy']);
    });

    Route::group([
        'prefix' => 'employee-awards',
    ], function () {
        Route::delete('delete-employee-award/{id}', [\Modules\Employees\Http\Controllers\EmployeeAwardsController::class, 'deleteEmployeeAward']);
        Route::delete('bulk/destroy-employee-award', [\Modules\Employees\Http\Controllers\EmployeeAwardsController::class, 'bulkDeleteEmployeeAward']);
        Route::delete('bulk/destroy', [\Modules\Employees\Http\Controllers\EmployeeAwardsController::class, 'bulkDestroy']);
        Route::get('employee-of-award/{id}', [\Modules\Employees\Http\Controllers\EmployeeAwardsController::class, 'getEmployeeOfAwards']);
        Route::get('export', [\Modules\Employees\Http\Controllers\EmployeeAwardsController::class, 'exportEmployeeAward']);
    });

    Route::group([
        'prefix' => 'employee-retaliations',
    ], function () {
        Route::get('export', [\Modules\Employees\Http\Controllers\EmployeesController::class, 'reportRetaliations']);
        Route::delete('bulk/destroy', [\Modules\Employees\Http\Controllers\RetaliationsController::class, 'bulkDestroy']);
    });

    Route::apiResource('employees', \Modules\Employees\Http\Controllers\EmployeesController::class)->except('update');
    Route::post('employees/{employee}', [\Modules\Employees\Http\Controllers\EmployeesController::class, 'update']);
    Route::apiResource('employee-contracts', \Modules\Employees\Http\Controllers\EmployeeContractsController::class)->except('update');
    Route::post('employee-contracts/{employeeContract}', [\Modules\Employees\Http\Controllers\EmployeeContractsController::class, 'update']);
    Route::apiResource('employee-terminations', \Modules\Employees\Http\Controllers\EmployeeTerminationAllowanceController::class);
    Route::apiResource('employee-transfers', \Modules\Employees\Http\Controllers\EmployeeTransfersController::class);
    Route::apiResource('employee-awards', \Modules\Employees\Http\Controllers\EmployeeAwardsController::class);
    Route::apiResource('employee-retaliations', \Modules\Employees\Http\Controllers\RetaliationsController::class);
});
