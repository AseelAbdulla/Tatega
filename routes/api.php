<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
| PUBLIC ROUTES
|--------------------------------------------------------------------------
| هذه العمليات يمكن للزائر الوصول إليها بدون تسجيل دخول
|--------------------------------------------------------------------------
*/


// Categories - Public Read
Route::apiResource('categories', CategoryController::class)
    ->only(['index', 'show']);


// Products - Public Read
Route::apiResource('products', ProductController::class)
    ->only(['index', 'show']);


// Product Images - Public Read
Route::apiResource('product-images', ProductImageController::class)
    ->only(['index', 'show']);


// Product Units - Public Read
Route::apiResource('product-units', ProductUnitController::class)
    ->only(['index', 'show']);


// Banners - Public
Route::apiResource('banners', BannerController::class)
    ->only(['index', 'show']);


// Features - Public
Route::apiResource('features', FeatureController::class)
    ->only(['index', 'show']);


// Partners - Public
Route::apiResource('partners', PartnerController::class)
    ->only(['index', 'show']);


// Settings - Public Read
Route::apiResource('settings', SettingController::class)
    ->only(['index', 'show']);


// Reviews - Public Read
Route::apiResource('reviews', ReviewController::class)
    ->only(['index', 'show']);


/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES
|--------------------------------------------------------------------------
| تحتاج تسجيل دخول باستخدام Sanctum
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {


    /*
    |--------------------------------------------------------------------------
    | CURRENT USER
    |--------------------------------------------------------------------------
    */

    Route::get('/user', function (Request $request) {
        return $request->user();
    });


    /*
    |--------------------------------------------------------------------------
    | ADMIN ONLY
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:admin')->group(function () {


        /*
        |--------------------------------------------------------------------------
        | Users Management
        |--------------------------------------------------------------------------
        */

        Route::apiResource('users', UserController::class);


        /*
        |--------------------------------------------------------------------------
        | Roles Management
        |--------------------------------------------------------------------------
        */

        Route::apiResource('roles', RoleController::class);


        /*
        |--------------------------------------------------------------------------
        | Categories Management
        |--------------------------------------------------------------------------
        */

        Route::apiResource('categories', CategoryController::class)
            ->except(['index', 'show']);


        /*
        |--------------------------------------------------------------------------
        | Products Management
        |--------------------------------------------------------------------------
        */

        Route::apiResource('products', ProductController::class)
            ->except(['index', 'show']);


        /*
        |--------------------------------------------------------------------------
        | Product Images Management
        |--------------------------------------------------------------------------
        */

        Route::apiResource('product-images', ProductImageController::class)
            ->except(['index', 'show']);


        /*
        |--------------------------------------------------------------------------
        | Product Units Management
        |--------------------------------------------------------------------------
        */

        Route::apiResource('product-units', ProductUnitController::class)
            ->except(['index', 'show']);


        /*
        |--------------------------------------------------------------------------
        | Banners Management
        |--------------------------------------------------------------------------
        */

        Route::apiResource('banners', BannerController::class);


        /*
        |--------------------------------------------------------------------------
        | Features Management
        |--------------------------------------------------------------------------
        */

        Route::apiResource('features', FeatureController::class);


        /*
        |--------------------------------------------------------------------------
        | Partners Management
        |--------------------------------------------------------------------------
        */

        Route::apiResource('partners', PartnerController::class);


        /*
        |--------------------------------------------------------------------------
        | Settings Management
        |--------------------------------------------------------------------------
        */

        Route::apiResource('settings', SettingController::class);


        /*
        |--------------------------------------------------------------------------
        | Internal Notifications
        |--------------------------------------------------------------------------
        | Admin فقط
        |--------------------------------------------------------------------------
        */

        Route::apiResource(
            'internal-notifications',
            InternalNotificationController::class
        );

    });


    /*
    |--------------------------------------------------------------------------
    | ADDRESSES
    |--------------------------------------------------------------------------
    | المستخدم المسجل يستطيع إدارة عناوينه
    |--------------------------------------------------------------------------
    */

    Route::apiResource('addresses', AddressController::class);


    /*
    |--------------------------------------------------------------------------
    | REVIEWS
    |--------------------------------------------------------------------------
    | المستخدم المسجل يستطيع إضافة مراجعة
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/reviews',
        [ReviewController::class, 'store']
    );


    /*
    |--------------------------------------------------------------------------
    | CART
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
    | ORDERS
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


    /*
    |--------------------------------------------------------------------------
    | TEMPORARY ADMIN TEST
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:admin')->get('/test-admin', function () {

        return response()->json([
            'message' => 'Admin access granted.'
        ]);

    });

});