<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Authentication Controllers
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Api\AuthController;

// Controllers
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CartItemController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\InternalNotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\ProductUnitController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SettingController;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [PasswordResetLinkController::class, 'store']);
Route::post('/reset-password', [NewPasswordController::class, 'store']);

Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
Route::apiResource('products', ProductController::class)->only(['index', 'show']);
Route::apiResource('banners', BannerController::class)->only(['index', 'show']);
Route::apiResource('features', FeatureController::class)->only(['index', 'show']);
Route::apiResource('partners', PartnerController::class)->only(['index', 'show']);
Route::apiResource('settings', SettingController::class)->only(['index', 'show']);
Route::apiResource('reviews', ReviewController::class)->only(['index', 'show']);

/*
|--------------------------------------------------------------------------
| Protected Routes (Sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // بيانات المستخدم الحالي وتسجيل الخروج
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store']);

    /*
    |--------------------------------------------------------------------------
    | 1. لوحة تحكم الأدمن والموظف (Admin & Employee Dashboard)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin|employee')->prefix('dashboard')->group(function () {
        
        // إدارة المحتوى (إضافة، تعديل، حذف)
        Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);
        Route::apiResource('products', ProductController::class)->except(['index', 'show']);
        Route::apiResource('product-images', ProductImageController::class);
        Route::apiResource('product-units', ProductUnitController::class);
        
        // جميع الطلبات
        Route::apiResource('orders', OrderController::class);
        Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel']);
    });

    /*
    |--------------------------------------------------------------------------
    | 2. حصرية بمدير النظام فقط (Super Admin Dashboard Only)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::apiResource('users', UserController::class);
        Route::apiResource('roles', RoleController::class);
        Route::apiResource('banners', BannerController::class)->except(['index', 'show']);
        Route::apiResource('features', FeatureController::class)->except(['index', 'show']);
        Route::apiResource('partners', PartnerController::class)->except(['index', 'show']);
        Route::apiResource('settings', SettingController::class)->except(['index', 'show']);
    });

    /*
    |--------------------------------------------------------------------------
    | 3. لوحة تحكم العملاء (محلي ودولي)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:local-client|international-client')->group(function () {
        Route::apiResource('addresses', AddressController::class);
        Route::apiResource('reviews', ReviewController::class)->except(['index', 'show']);
        
        // السلة
        Route::get('/cart', [CartController::class, 'index']);
        Route::delete('/cart/clear', [CartController::class, 'clear']);
        Route::apiResource('cart/items', CartItemController::class)->only(['store', 'update', 'destroy']);

        // طلبات العملاء الخاصة
        Route::get('/my-orders', [OrderController::class, 'index']);
        Route::post('/my-orders', [OrderController::class, 'store']);
        Route::get('/my-orders/{order}', [OrderController::class, 'show']);
    });

    /*
    |--------------------------------------------------------------------------
    | 4. لوحة العميل الدولي المخصصة (International Client Special)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:international-client')->prefix('international')->group(function () {
        Route::apiResource('internal-notifications', InternalNotificationController::class);
    });

});

