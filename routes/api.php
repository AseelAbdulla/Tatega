
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Authentication Controllers
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
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


/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
|
| هذه المسارات متاحة بدون تسجيل دخول.
|
*/

Route::apiResource('categories', CategoryController::class)
    ->only(['index', 'show']);

Route::apiResource('products', ProductController::class)
    ->only(['index', 'show']);

Route::apiResource('banners', BannerController::class)
    ->only(['index', 'show']);

Route::apiResource('features', FeatureController::class)
    ->only(['index', 'show']);

Route::apiResource('partners', PartnerController::class)
    ->only(['index', 'show']);

Route::apiResource('settings', SettingController::class)
    ->only(['index', 'show']);

Route::apiResource('reviews', ReviewController::class)
    ->only(['index', 'show']);


/*
|--------------------------------------------------------------------------
| Protected Routes - Sanctum
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {


    /*
    |--------------------------------------------------------------------------
    | Current User / Logout
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
    | 1. Dashboard - Categories
    |--------------------------------------------------------------------------
    */

    Route::prefix('dashboard')->group(function () {

        Route::middleware('permission:create-categories')
            ->post('/categories', [
                CategoryController::class,
                'store'
            ]);

        Route::middleware('permission:update-categories')
            ->match(['put', 'patch'], '/categories/{category}', [
                CategoryController::class,
                'update'
            ]);

        Route::middleware('permission:delete-categories')
            ->delete('/categories/{category}', [
                CategoryController::class,
                'destroy'
            ]);


        /*
        |--------------------------------------------------------------------------
        | Dashboard - Products
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:create-products')
            ->post('/products', [
                ProductController::class,
                'store'
            ]);

        Route::middleware('permission:update-products')
            ->match(['put', 'patch'], '/products/{product}', [
                ProductController::class,
                'update'
            ]);

        Route::middleware('permission:delete-products')
            ->delete('/products/{product}', [
                ProductController::class,
                'destroy'
            ]);


        /*
        |--------------------------------------------------------------------------
        | Dashboard - Product Images
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:manage-product-images')
            ->apiResource(
                'product-images',
                ProductImageController::class
            );


        /*
        |--------------------------------------------------------------------------
        | Dashboard - Product Units
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:manage-product-units')
            ->apiResource(
                'product-units',
                ProductUnitController::class
            );


        /*
        |--------------------------------------------------------------------------
        | Dashboard - Orders
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:view-orders')
            ->get('/orders', [
                OrderController::class,
                'index'
            ]);

        Route::middleware('permission:view-orders')
            ->get('/orders/{order}', [
                OrderController::class,
                'show'
            ]);

        Route::middleware('permission:create-orders')
            ->post('/orders', [
                OrderController::class,
                'store'
            ]);

        Route::middleware('permission:update-orders')
            ->match(['put', 'patch'], '/orders/{order}', [
                OrderController::class,
                'update'
            ]);

        Route::middleware('permission:cancel-orders')
            ->patch('/orders/{order}/cancel', [
                OrderController::class,
                'cancel'
            ]);

        Route::middleware('permission:manage-orders')
            ->delete('/orders/{order}', [
                OrderController::class,
                'destroy'
            ]);
    });


    /*
    |--------------------------------------------------------------------------
    | 2. Admin Dashboard
    |--------------------------------------------------------------------------
    |
    | الوصول هنا يعتمد على Permissions.
    |
    */

    Route::prefix('admin')->group(function () {


        /*
        |--------------------------------------------------------------------------
        | Users
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
        | Roles
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
        | Permissions
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
        | Assign Permission To Role
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:update-roles')
            ->post('/roles/{roleId}/permissions', [
                RoleController::class,
                'assignPermission'
            ]);


        /*
        |--------------------------------------------------------------------------
        | Remove Permission From Role
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:update-roles')
            ->delete('/roles/{roleId}/permissions', [
                RoleController::class,
                'removePermission'
            ]);


        /*
        |--------------------------------------------------------------------------
        | Banners
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
        | Features
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
        | Partners
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
        | Settings
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
    | 3. Customer Dashboard
    |--------------------------------------------------------------------------
    |
    | لا نعتمد على role هنا.
    | الـ Permission هي التي تحدد الوصول.
    |
    */

    Route::middleware('permission:manage-addresses')
        ->apiResource('addresses', AddressController::class);


    Route::middleware('permission:manage-reviews')
        ->apiResource('reviews', ReviewController::class)
        ->except(['index', 'show']);


    /*
    |--------------------------------------------------------------------------
    | Cart
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
    | My Orders
    |--------------------------------------------------------------------------
    */

    Route::middleware('permission:manage-my-orders')
        ->get('/my-orders', [
            OrderController::class,
            'index'
        ]);

    Route::middleware('permission:manage-my-orders')
        ->post('/my-orders', [
            OrderController::class,
            'store'
        ]);

    Route::middleware('permission:manage-my-orders')
        ->get('/my-orders/{order}', [
            OrderController::class,
            'show'
        ]);


    /*
    |--------------------------------------------------------------------------
    | 4. International Client
    |--------------------------------------------------------------------------
    */

    Route::middleware('permission:view-internal-notifications')
        ->prefix('international')
        ->group(function () {

            Route::apiResource(
                'internal-notifications',
                InternalNotificationController::class
            );
        });

});

