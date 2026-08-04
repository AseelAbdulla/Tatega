<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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
| Public API Routes (Authentication)
|--------------------------------------------------------------------------
| مسارات عامة للتسجيل وتسجيل الدخول وإرجاع التوكن
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Public Resource Routes
|--------------------------------------------------------------------------
| يمكن لأي زائر الوصول إليها دون الحاجة لتسجيل دخول
*/

Route::apiResource('categories', CategoryController::class);

Route::apiResource('products', ProductController::class);

Route::apiResource('product-images', ProductImageController::class);

Route::apiResource('product-units', ProductUnitController::class);

Route::apiResource('banners', BannerController::class);

Route::apiResource('features', FeatureController::class);

Route::apiResource('partners', PartnerController::class);

Route::apiResource('settings', SettingController::class);

Route::apiResource('reviews', ReviewController::class);


/*
|--------------------------------------------------------------------------
| Protected Routes (Sanctum)
|--------------------------------------------------------------------------
| تتطلب إرسال Bearer Token في الهيدر للوصول إليها
*/

Route::middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Authentication Management
    |--------------------------------------------------------------------------
    */

    Route::post('/logout', [AuthController::class, 'logout']);

    /*
    |--------------------------------------------------------------------------
    | Current User
    |--------------------------------------------------------------------------
    */

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    /*
    |--------------------------------------------------------------------------
    | Users & Roles
    |--------------------------------------------------------------------------
    */

    Route::apiResource('users', UserController::class);

    Route::apiResource('roles', RoleController::class);

    /*
    |--------------------------------------------------------------------------
    | Addresses
    |--------------------------------------------------------------------------
    */

    Route::apiResource('addresses', AddressController::class);

    /*
    |--------------------------------------------------------------------------
    | Internal Notifications
    |--------------------------------------------------------------------------
    */

    Route::apiResource(
        'internal-notifications',
        InternalNotificationController::class
    );

    /*
    |--------------------------------------------------------------------------
    | Cart
    |--------------------------------------------------------------------------
    */

    Route::get('/cart', [CartController::class, 'index']);

    Route::delete('/cart/clear', [CartController::class, 'clear']);

    Route::apiResource('cart/items', CartItemController::class)
        ->only([
            'store',
            'update',
            'destroy',
        ]);

    /*
    |--------------------------------------------------------------------------
    | Orders
    |--------------------------------------------------------------------------
    */

    Route::apiResource('orders', OrderController::class)
        ->only([
            'index',
            'store',
            'show',
        ]);

    Route::patch(
        '/orders/{order}/cancel',
        [OrderController::class, 'cancel']
    );
});