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
    'prefix' => 'auth',
], function () {
    Route::post('login', [\Modules\Auth\Http\Controllers\LoginController::class, 'login']);
    Route::post('register', [\Modules\Auth\Http\Controllers\RegisterController::class, 'register']);

    Route::post('password/email', [\Modules\Auth\Http\Controllers\ForgotPasswordController::class, 'sendResetLinkEmail']);
    Route::post('password/reset', [\Modules\Auth\Http\Controllers\ResetPasswordController::class, 'reset'])->name('password.reset');

    Route::post('email/verify/{user}', [\Modules\Auth\Http\Controllers\VerificationController::class, 'verify'])->name('verification.verify');
    Route::post('email/resend', [\Modules\Auth\Http\Controllers\VerificationController::class, 'resend']);

    Route::post('oauth/{driver}', [\Modules\Auth\Http\Controllers\OAuthController::class, 'redirect']);
    Route::get('oauth/{driver}/callback', [\Modules\Auth\Http\Controllers\OAuthController::class, 'handleCallback'])->name('oauth.callback');
});

Route::group([
    'middleware' => 'auth:api',
    'prefix' => 'auth',
], function () {
    Route::post('password/confirm', [\Modules\Auth\Http\Controllers\ConfirmPasswordController::class, 'confirm']);

    Route::post('logout', [\Modules\Auth\Http\Controllers\LoginController::class, 'logout']);
    Route::get('user', [\Modules\Auth\Http\Controllers\UserController::class, 'user']);
});
