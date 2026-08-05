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

Route::post('/login', [AuthenticatedSessionController::class, 'store']);

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store']);

Route::post('/reset-password', [NewPasswordController::class, 'store']);


/*
|--------------------------------------------------------------------------
| Email Verification Link
|--------------------------------------------------------------------------
|
| لا نضع auth:sanctum هنا لأن الرابط يأتي من البريد
| ويحتوي على signature للتحقق
|
*/

Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');



/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
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
*/

Route::middleware('auth:sanctum')->group(function () {


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
    | Send Email Verification Notification
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/email/verification-notification',
        [EmailVerificationNotificationController::class, 'store']
    );



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