
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
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

        Route::apiResource('partners', PartnerController::class)->except(['index', 'show']);


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
    | REVIEWS
    |--------------------------------------------------------------------------
    | المستخدم المسجل يستطيع إضافة مراجعة
    |--------------------------------------------------------------------------
    |
    | لا نعتمد على role هنا.
    | الـ Permission هي التي تحدد الوصول.
    |
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
