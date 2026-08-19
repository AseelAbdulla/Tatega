<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
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
| العمليات التي يمكن للزائر الوصول إليها بدون تسجيل دخول
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| AUTHENTICATION
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthenticatedSessionController::class, 'store']);


/*
|--------------------------------------------------------------------------
| CATEGORIES - PUBLIC READ
|--------------------------------------------------------------------------
*/

Route::apiResource('categories', CategoryController::class)
    ->only(['index', 'show']);


/*
|--------------------------------------------------------------------------
| PRODUCTS - PUBLIC READ
|--------------------------------------------------------------------------
*/

Route::apiResource('products', ProductController::class)
    ->only(['index', 'show']);


/*
|--------------------------------------------------------------------------
| PRODUCT IMAGES - PUBLIC READ
|--------------------------------------------------------------------------
*/

Route::apiResource('product-images', ProductImageController::class)
    ->only(['index', 'show']);


/*
|--------------------------------------------------------------------------
| PRODUCT UNITS - PUBLIC READ
|--------------------------------------------------------------------------
*/

Route::apiResource('product-units', ProductUnitController::class)
    ->only(['index', 'show']);


/*
|--------------------------------------------------------------------------
| BANNERS - PUBLIC READ
|--------------------------------------------------------------------------
*/

Route::apiResource('banners', BannerController::class)
    ->only(['index', 'show']);


/*
|--------------------------------------------------------------------------
| FEATURES - PUBLIC READ
|--------------------------------------------------------------------------
*/

Route::get('/features', [FeatureController::class, 'index']);

Route::get('/features/{feature}', [FeatureController::class, 'show']);


/*
|--------------------------------------------------------------------------
| FEATURES - ADMIN
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {

    Route::post('/features', [FeatureController::class, 'store']);

    Route::put('/features/{feature}', [FeatureController::class, 'update']);

    Route::patch('/features/{feature}', [FeatureController::class, 'update']);

    Route::delete('/features/{feature}', [FeatureController::class, 'destroy']);

});


/*
|--------------------------------------------------------------------------
| PARTNERS - PUBLIC READ
|--------------------------------------------------------------------------
*/

Route::apiResource('partners', PartnerController::class)
    ->only(['index', 'show']);


/*
|--------------------------------------------------------------------------
| SETTINGS - PUBLIC READ
|--------------------------------------------------------------------------
*/

Route::apiResource('settings', SettingController::class)
    ->only(['index', 'show']);


/*
|--------------------------------------------------------------------------
| REVIEWS - PUBLIC READ
|--------------------------------------------------------------------------
| أي زائر يستطيع مشاهدة التقييمات
|--------------------------------------------------------------------------
*/

Route::get('/reviews', [ReviewController::class, 'index'])
    ->name('reviews.index');

Route::get('/reviews/{review}', [ReviewController::class, 'show'])
    ->name('reviews.show');


/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES
|--------------------------------------------------------------------------
| تحتاج إلى تسجيل دخول باستخدام Sanctum
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {


    /*
    |--------------------------------------------------------------------------
    | CURRENT USER
    |--------------------------------------------------------------------------
    */

    Route::get('/me', [AuthController::class, 'me']);

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post(
        '/email/verification-notification',
        [EmailVerificationNotificationController::class, 'store']
    );


    /*
    |--------------------------------------------------------------------------
    | ADMIN ONLY
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:admin')->group(function () {


        /*
        |--------------------------------------------------------------------------
        | USERS MANAGEMENT
        |--------------------------------------------------------------------------
        */

        Route::apiResource('users', UserController::class);


        /*
        |--------------------------------------------------------------------------
        | ROLES MANAGEMENT
        |--------------------------------------------------------------------------
        */

        Route::apiResource('roles', RoleController::class);


        /*
        |--------------------------------------------------------------------------
        | CATEGORIES MANAGEMENT
        |--------------------------------------------------------------------------
        */

        Route::apiResource('categories', CategoryController::class)
            ->except(['index', 'show']);


        /*
        |--------------------------------------------------------------------------
        | PRODUCTS MANAGEMENT
        |--------------------------------------------------------------------------
        */

        Route::apiResource('products', ProductController::class)
            ->except(['index', 'show']);


        /*
        |--------------------------------------------------------------------------
        | PRODUCT IMAGES MANAGEMENT
        |--------------------------------------------------------------------------
        */

        Route::apiResource('product-images', ProductImageController::class)
            ->except(['index', 'show']);


        /*
        |--------------------------------------------------------------------------
        | PRODUCT UNITS MANAGEMENT
        |--------------------------------------------------------------------------
        */

        Route::apiResource('product-units', ProductUnitController::class)
            ->except(['index', 'show']);


        /*
        |--------------------------------------------------------------------------
        | BANNERS MANAGEMENT
        |--------------------------------------------------------------------------
        */

        Route::apiResource('banners', BannerController::class)
            ->except(['index', 'show']);


        /*
        |--------------------------------------------------------------------------
        | PARTNERS MANAGEMENT
        |--------------------------------------------------------------------------
        */

        Route::apiResource('partners', PartnerController::class);


        /*
        |--------------------------------------------------------------------------
        | SETTINGS MANAGEMENT
        |--------------------------------------------------------------------------
        */

        Route::apiResource('settings', SettingController::class);


        /*
        |--------------------------------------------------------------------------
        | INTERNAL NOTIFICATIONS
        |--------------------------------------------------------------------------
        */

        Route::apiResource(
            'internal-notifications',
            InternalNotificationController::class
        );


        /*
        |--------------------------------------------------------------------------
        | REVIEWS MANAGEMENT
        |--------------------------------------------------------------------------
        | Admin فقط يستطيع تعديل وحذف التقييمات
        |--------------------------------------------------------------------------
        */

        Route::put(
            '/reviews/{review}',
            [ReviewController::class, 'update']
        )->name('reviews.update');

        Route::patch(
            '/reviews/{review}',
            [ReviewController::class, 'update']
        );

        Route::delete(
            '/reviews/{review}',
            [ReviewController::class, 'destroy']
        )->name('reviews.destroy');

    });


    /*
    |--------------------------------------------------------------------------
    | ADDRESSES
    |--------------------------------------------------------------------------
    | المستخدم المسجل يستطيع إدارة عناوينه حسب الصلاحيات
    |--------------------------------------------------------------------------
    */

    Route::prefix('admin')->group(function () {


        /*
        |--------------------------------------------------------------------------
        | USERS
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:view-users')
            ->get('/users', [
                UserController::class,
                'index'
            ]);

        Route::middleware('permission:view-users')
            ->get('/users/{user}', [
                UserController::class,
                'show'
            ]);

        Route::middleware('permission:create-users')
            ->post('/users', [
                UserController::class,
                'store'
            ]);

        Route::middleware('permission:update-users')
            ->match(['put', 'patch'], '/users/{user}', [
                UserController::class,
                'update'
            ]);

        Route::middleware('permission:delete-users')
            ->delete('/users/{user}', [
                UserController::class,
                'destroy'
            ]);


        /*
        |--------------------------------------------------------------------------
        | ROLES
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:view-roles')
            ->get('/roles', [
                RoleController::class,
                'index'
            ]);

        Route::middleware('permission:view-roles')
            ->get('/roles/{role}', [
                RoleController::class,
                'show'
            ]);

        Route::middleware('permission:create-roles')
            ->post('/roles', [
                RoleController::class,
                'store'
            ]);

        Route::middleware('permission:update-roles')
            ->match(['put', 'patch'], '/roles/{role}', [
                RoleController::class,
                'update'
            ]);

        Route::middleware('permission:delete-roles')
            ->delete('/roles/{role}', [
                RoleController::class,
                'destroy'
            ]);


        /*
        |--------------------------------------------------------------------------
        | PERMISSIONS
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:view-roles')
            ->get('/permissions', [
                RoleController::class,
                'permissions'
            ]);

        Route::middleware('permission:view-roles')
            ->get('/permissions/{id}', [
                RoleController::class,
                'showPermission'
            ]);

        Route::middleware('permission:create-roles')
            ->post('/permissions', [
                RoleController::class,
                'storePermission'
            ]);

        Route::middleware('permission:update-roles')
            ->match(['put', 'patch'], '/permissions/{id}', [
                RoleController::class,
                'updatePermission'
            ]);

        Route::middleware('permission:delete-roles')
            ->delete('/permissions/{id}', [
                RoleController::class,
                'destroyPermission'
            ]);


        /*
        |--------------------------------------------------------------------------
        | ASSIGN PERMISSION TO ROLE
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:update-roles')
            ->post('/roles/{roleId}/permissions', [
                RoleController::class,
                'assignPermission'
            ]);


        /*
        |--------------------------------------------------------------------------
        | REMOVE PERMISSION FROM ROLE
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:update-roles')
            ->delete('/roles/{roleId}/permissions', [
                RoleController::class,
                'removePermission'
            ]);


        /*
        |--------------------------------------------------------------------------
        | BANNERS
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:manage-banners')
            ->post('/banners', [
                BannerController::class,
                'store'
            ]);

        Route::middleware('permission:manage-banners')
            ->match(['put', 'patch'], '/banners/{banner}', [
                BannerController::class,
                'update'
            ]);

        Route::middleware('permission:manage-banners')
            ->delete('/banners/{banner}', [
                BannerController::class,
                'destroy'
            ]);


        /*
        |--------------------------------------------------------------------------
        | FEATURES
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:manage-features')
            ->post('/features', [
                FeatureController::class,
                'store'
            ]);

        Route::middleware('permission:manage-features')
            ->match(['put', 'patch'], '/features/{feature}', [
                FeatureController::class,
                'update'
            ]);

        Route::middleware('permission:manage-features')
            ->delete('/features/{feature}', [
                FeatureController::class,
                'destroy'
            ]);


        /*
        |--------------------------------------------------------------------------
        | PARTNERS
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:manage-partners')
            ->post('/partners', [
                PartnerController::class,
                'store'
            ]);

        Route::middleware('permission:manage-partners')
            ->match(['put', 'patch'], '/partners/{partner}', [
                PartnerController::class,
                'update'
            ]);

        Route::middleware('permission:manage-partners')
            ->delete('/partners/{partner}', [
                PartnerController::class,
                'destroy'
            ]);


        /*
        |--------------------------------------------------------------------------
        | SETTINGS
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:manage-settings')
            ->post('/settings', [
                SettingController::class,
                'store'
            ]);

        Route::middleware('permission:manage-settings')
            ->match(['put', 'patch'], '/settings/{setting}', [
                SettingController::class,
                'update'
            ]);

        Route::middleware('permission:manage-settings')
            ->delete('/settings/{setting}', [
                SettingController::class,
                'destroy'
            ]);

    });


    /*
    |--------------------------------------------------------------------------
    | REVIEWS - CREATE
    |--------------------------------------------------------------------------
    | المستخدم المسجل يستطيع إضافة تقييم
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

    Route::middleware('permission:manage-cart')
        ->get('/cart', [
            CartController::class,
            'index'
        ]);

    Route::middleware('permission:manage-cart')
        ->delete('/cart/clear', [
            CartController::class,
            'clear'
        ]);

    Route::middleware('permission:manage-cart')
        ->apiResource('cart/items', CartItemController::class)
        ->only([
            'store',
            'update',
            'destroy'
        ]);


    /*
    |--------------------------------------------------------------------------
    | ORDERS
    |--------------------------------------------------------------------------
    */

    Route::middleware('permission:manage-my-orders')
        ->get('/my-orders', [
            OrderController::class,
            'index'
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