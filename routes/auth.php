<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
| Register / Login / Forgot Password / Reset Password
| موجودة في routes/api.php
|--------------------------------------------------------------------------
*/
// Authentication
Route::post('/login', [AuthenticatedSessionController::class, 'store']);

/*
|--------------------------------------------------------------------------
| Email Verification
|--------------------------------------------------------------------------
*/

// رابط التحقق الذي يصل إلى البريد الإلكتروني
Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');


Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth:sanctum')
    ->name('logout');
// إعادة إرسال رابط التحقق
Route::post('/email/verification-notification', [
    EmailVerificationNotificationController::class,
    'store'
])
    ->middleware([
        'auth:sanctum',
        'throttle:6,1'
    ])
    ->name('verification.send');
