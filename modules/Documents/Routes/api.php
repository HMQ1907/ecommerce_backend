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
        'prefix' => 'documents',
    ], function () {
        Route::post('{document}', [\Modules\Documents\Http\Controllers\DocumentsController::class, 'update']);
        Route::get('{id}/download', [\Modules\Documents\Http\Controllers\DocumentsController::class, 'download']);
    });
    Route::apiResource('documents', \Modules\Documents\Http\Controllers\DocumentsController::class)->except('update');
    Route::get('document-categories', [\Modules\Documents\Http\Controllers\DocumentsController::class, 'getDocumentCategories']);
});
