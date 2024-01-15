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
        'prefix' => 'teams',
    ], function () {
        Route::get('{teamId}/users', [\Modules\Teams\Http\Controllers\TeamsController::class, 'getTeamUsers']);
        Route::post('{teamId}/users', [\Modules\Teams\Http\Controllers\TeamsController::class, 'addTeamUsers']);
        Route::delete('{teamId}/users/{userId}', [\Modules\Teams\Http\Controllers\TeamsController::class, 'deleteUserInTeam']);
    });
    Route::apiResource('teams', \Modules\Teams\Http\Controllers\TeamsController::class);
});

/**
 * Teamwork routes
 */
//Route::group(['prefix' => 'teams'], function () {
//    Route::get('/', [\Modules\Teams\Http\Controllers\Teamwork\TeamController::class, 'index'])->name('teams.index');
//    Route::get('create', [\Modules\Teams\Http\Controllers\Teamwork\TeamController::class, 'create'])->name('teams.create');
//    Route::post('teams', [\Modules\Teams\Http\Controllers\Teamwork\TeamController::class, 'store'])->name('teams.store');
//    Route::get('edit/{id}', [\Modules\Teams\Http\Controllers\Teamwork\TeamController::class, 'edit'])->name('teams.edit');
//    Route::put('edit/{id}', [\Modules\Teams\Http\Controllers\Teamwork\TeamController::class, 'update'])->name('teams.update');
//    Route::delete('destroy/{id}', [\Modules\Teams\Http\Controllers\Teamwork\TeamController::class, 'destroy'])->name('teams.destroy');
//    Route::get('switch/{id}', [\Modules\Teams\Http\Controllers\Teamwork\TeamController::class, 'switchTeam'])->name('teams.switch');
//
//    Route::get('members/{id}', [\Modules\Teams\Http\Controllers\Teamwork\TeamMemberController::class, 'show'])->name('teams.members.show');
//    Route::get('members/resend/{invite_id}', [\Modules\Teams\Http\Controllers\Teamwork\TeamMemberController::class, 'resendInvite'])->name('teams.members.resend_invite');
//    Route::post('members/{id}', [\Modules\Teams\Http\Controllers\Teamwork\TeamMemberController::class, 'invite'])->name('teams.members.invite');
//    Route::delete('members/{id}/{user_id}', [\Modules\Teams\Http\Controllers\Teamwork\TeamMemberController::class, 'destroy'])->name('teams.members.destroy');
//});
