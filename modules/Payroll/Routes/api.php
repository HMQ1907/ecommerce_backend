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
    'prefix' => 'public',
], function () {
    Route::get('payslip', [\Modules\Payroll\Http\Controllers\PayrollController::class, 'payslip'])->name('payslip_pdf')->middleware('signed');
});

Route::group([
    'middleware' => 'auth:api',
], function () {
    Route::group([
        'prefix' => 'salary-tds',
    ], function () {
        Route::delete('bulk/destroy', [\Modules\Payroll\Http\Controllers\SalaryTdsController::class, 'bulkDestroy']);
    });
    Route::apiResource('salary-tds', \Modules\Payroll\Http\Controllers\SalaryTdsController::class);

    Route::group([
        'prefix' => 'salary-components',
    ], function () {
        Route::delete('bulk/destroy', [\Modules\Payroll\Http\Controllers\SalaryComponentsController::class, 'bulkDestroy']);
        Route::get('allowances', [\Modules\Payroll\Http\Controllers\SalaryComponentsController::class, 'getMainAllowances']);
    });
    Route::apiResource('salary-components', \Modules\Payroll\Http\Controllers\SalaryComponentsController::class);

    Route::group([
        'prefix' => 'salary-groups',
    ], function () {
        Route::delete('bulk/destroy', [\Modules\Payroll\Http\Controllers\SalaryGroupsController::class, 'bulkDestroy']);
        Route::post('{id}/assign', [\Modules\Payroll\Http\Controllers\SalaryGroupsController::class, 'assign']);
    });
    Route::apiResource('salary-groups', \Modules\Payroll\Http\Controllers\SalaryGroupsController::class);

    Route::group([
        'prefix' => 'employee-salaries',
    ], function () {
        Route::get('{id}/salary-groups', [\Modules\Payroll\Http\Controllers\EmployeeSalaryController::class, 'getEmployeeSalaryGroup']);
    });
    Route::apiResource('employee-salaries', \Modules\Payroll\Http\Controllers\EmployeeSalaryController::class);

    Route::group([
        'prefix' => 'payslips',
    ], function () {
        Route::post('generate', [\Modules\Payroll\Http\Controllers\PayrollController::class, 'generatePaySlips']);
        Route::post('pay', [\Modules\Payroll\Http\Controllers\PayrollController::class, 'payPaySlips']);
        Route::get('{id}/export-payslip', [\Modules\Payroll\Http\Controllers\PayrollController::class, 'payslipSigned']);
        Route::post('/send-mail', [\Modules\Payroll\Http\Controllers\PayrollController::class, 'sendMailPayslip']);
        Route::post('/import', [\Modules\Payroll\Http\Controllers\PayrollController::class, 'importPayslips']);
    });
    Route::apiResource('payslips', \Modules\Payroll\Http\Controllers\PayrollController::class);

    Route::get('report/lao-record', [\Modules\Payroll\Http\Controllers\EmployeeSalaryController::class, 'reportLaoRecord']);
});
