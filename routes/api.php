<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
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
| المسارات التي يمكن الوصول إليها بدون تسجيل دخول
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| AUTHENTICATION
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);

Route::post('/register', [AuthController::class, 'register']);


/*
|--------------------------------------------------------------------------
| CATEGORIES - PUBLIC READ
|--------------------------------------------------------------------------
*/

Route::apiResource('categories', CategoryController::class)
    ->only(['index', 'show']);

// Products
Route::apiResource('products', ProductController::class)
    ->only(['index', 'show']);

// Product Images
Route::apiResource('product-images', ProductImageController::class)
    ->only(['index', 'show']);

// Product Units
Route::apiResource('product-units', ProductUnitController::class)
    ->only(['index', 'show']);

// إدارة المنتجات والصور والوحدات للمدير فقط
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::apiResource('categories', CategoryController::class)
        ->except(['index', 'show']);

    Route::apiResource('products', ProductController::class)
        ->except(['index', 'show']);

    Route::apiResource('product-images', ProductImageController::class)
        ->except(['index', 'show']);

    Route::apiResource('product-units', ProductUnitController::class)
        ->except(['index', 'show']);
});

// Banners
Route::apiResource('banners', BannerController::class)
    ->only(['index', 'show']);


/*
|--------------------------------------------------------------------------
| FEATURES - PUBLIC READ
|--------------------------------------------------------------------------
*/

Route::apiResource('features', FeatureController::class)
    ->only(['index', 'show']);


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
| الزوار يستطيعون مشاهدة التقييمات المقبولة من خلال Controller
|--------------------------------------------------------------------------
*/

Route::get('/reviews', [ReviewController::class, 'index'])
    ->name('reviews.index');

Route::get('/reviews/{review}', [ReviewController::class, 'show'])
    ->name('reviews.show');


/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES - SANCTUM
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | AUTHENTICATED USER
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
    | USERS - VIEW
    |--------------------------------------------------------------------------
    | Admin و Employee يستطيعون عرض المستخدمين
    | بشرط امتلاك permission:view-users
    |--------------------------------------------------------------------------
    */

    Route::middleware([
        'role:admin|employee',
        'permission:view-users'
    ])->group(function () {

        Route::get('/users', [UserController::class, 'index']);

        Route::get('/users/{user}', [UserController::class, 'show']);
    });


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

        Route::post('/users', [UserController::class, 'store']);

        Route::match(
            ['put', 'patch'],
            '/users/{user}',
            [UserController::class, 'update']
        );

        Route::delete(
            '/users/{user}',
            [UserController::class, 'destroy']
        );


        /*
        |--------------------------------------------------------------------------
        | ROLES MANAGEMENT
        |--------------------------------------------------------------------------
        */

        Route::apiResource('roles', RoleController::class);


        /*
        |--------------------------------------------------------------------------
        | BANNERS
        |--------------------------------------------------------------------------
        */

        Route::apiResource('banners', BannerController::class)
            ->except(['index', 'show']);


        /*
        |--------------------------------------------------------------------------
        | FEATURES MANAGEMENT
        |--------------------------------------------------------------------------
        */

        Route::apiResource('features', FeatureController::class);


        /*
        |--------------------------------------------------------------------------
        | PARTNERS MANAGEMENT
        |--------------------------------------------------------------------------
        */

        Route::apiResource('partners', PartnerController::class)
            ->except(['index', 'show']);


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
        | Admin يستطيع الموافقة / تعديل / حذف التقييمات
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
    | ADMIN - PERMISSION BASED
    |--------------------------------------------------------------------------
    */

    Route::prefix('admin')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | USERS
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:view-users')
            ->get('/users', [UserController::class, 'index']);

        Route::middleware('permission:view-users')
            ->get('/users/{user}', [UserController::class, 'show']);

        Route::middleware('permission:create-users')
            ->post('/users', [UserController::class, 'store']);

        Route::middleware('permission:update-users')
            ->match(
                ['put', 'patch'],
                '/users/{user}',
                [UserController::class, 'update']
            );

        Route::middleware('permission:delete-users')
            ->delete(
                '/users/{user}',
                [UserController::class, 'destroy']
            );


        /*
        |--------------------------------------------------------------------------
        | ROLES
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:view-roles')
            ->get('/roles', [RoleController::class, 'index']);

        Route::middleware('permission:view-roles')
            ->get('/roles/{role}', [RoleController::class, 'show']);

        Route::middleware('permission:create-roles')
            ->post('/roles', [RoleController::class, 'store']);

        Route::middleware('permission:update-roles')
            ->match(
                ['put', 'patch'],
                '/roles/{role}',
                [RoleController::class, 'update']
            );

        Route::middleware('permission:delete-roles')
            ->delete(
                '/roles/{role}',
                [RoleController::class, 'destroy']
            );


        /*
        |--------------------------------------------------------------------------
        | PERMISSIONS
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:view-roles')
            ->get(
                '/permissions',
                [RoleController::class, 'permissions']
            );

        Route::middleware('permission:view-roles')
            ->get(
                '/permissions/{id}',
                [RoleController::class, 'showPermission']
            );

        Route::middleware('permission:create-roles')
            ->post(
                '/permissions',
                [RoleController::class, 'storePermission']
            );

        Route::middleware('permission:update-roles')
            ->match(
                ['put', 'patch'],
                '/permissions/{id}',
                [RoleController::class, 'updatePermission']
            );

        Route::middleware('permission:delete-roles')
            ->delete(
                '/permissions/{id}',
                [RoleController::class, 'destroyPermission']
            );


        /*
        |--------------------------------------------------------------------------
        | ROLE - PERMISSION ASSIGNMENTS
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:update-roles')
            ->post(
                '/roles/{roleId}/permissions',
                [RoleController::class, 'assignPermission']
            );

        Route::middleware('permission:update-roles')
            ->delete(
                '/roles/{roleId}/permissions',
                [RoleController::class, 'removePermission']
            );


        /*
        |--------------------------------------------------------------------------
        | BANNERS
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:manage-banners')
            ->post(
                '/banners',
                [BannerController::class, 'store']
            );

        Route::middleware('permission:manage-banners')
            ->match(
                ['put', 'patch'],
                '/banners/{banner}',
                [BannerController::class, 'update']
            );

        Route::middleware('permission:manage-banners')
            ->delete(
                '/banners/{banner}',
                [BannerController::class, 'destroy']
            );


        /*
        |--------------------------------------------------------------------------
        | FEATURES
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:manage-features')
            ->post(
                '/features',
                [FeatureController::class, 'store']
            );

        Route::middleware('permission:manage-features')
            ->match(
                ['put', 'patch'],
                '/features/{feature}',
                [FeatureController::class, 'update']
            );

        Route::middleware('permission:manage-features')
            ->delete(
                '/features/{feature}',
                [FeatureController::class, 'destroy']
            );


        /*
        |--------------------------------------------------------------------------
        | PARTNERS
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:manage-partners')
            ->post(
                '/partners',
                [PartnerController::class, 'store']
            );

        Route::middleware('permission:manage-partners')
            ->match(
                ['put', 'patch'],
                '/partners/{partner}',
                [PartnerController::class, 'update']
            );

        Route::middleware('permission:manage-partners')
            ->delete(
                '/partners/{partner}',
                [PartnerController::class, 'destroy']
            );


        /*
        |--------------------------------------------------------------------------
        | SETTINGS
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:manage-settings')
            ->post(
                '/settings',
                [SettingController::class, 'store']
            );

        Route::middleware('permission:manage-settings')
            ->match(
                ['put', 'patch'],
                '/settings/{setting}',
                [SettingController::class, 'update']
            );

        Route::middleware('permission:manage-settings')
            ->delete(
                '/settings/{setting}',
                [SettingController::class, 'destroy']
            );


        /*
        |--------------------------------------------------------------------------
        | ORDERS - ADMIN
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:view-orders')->group(function () {

            /*
            | Create / Update / Delete Orders
            */

            Route::apiResource('orders', OrderController::class)
                ->except(['index', 'show']);


            /*
            | View All Orders
            */

            Route::get(
                '/orders',
                [OrderController::class, 'adminIndex']
            );


            /*
            | View Single Order
            */

            Route::get(
                '/orders/{order}',
                [OrderController::class, 'adminShow']
            );


            /*
            | Change Order Status
            */

            Route::patch(
                '/orders/{order}/status',
                [OrderController::class, 'updateStatus']
            );


            /*
            | Dashboard Statistics
            */

            Route::get(
                '/dashboard/stats',
                [OrderController::class, 'dashboardStats']
            );
        });

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

    Route::get(
        '/cart/count',
        [CartController::class, 'count']
    );

    Route::get(
        '/cart',
        [CartController::class, 'index']
    );

    Route::delete(
        '/cart/clear',
        [CartController::class, 'clear']
    );

    Route::apiResource(
        'cart/items',
        CartItemController::class
    )->only(['store', 'update', 'destroy']);


    /*
    |--------------------------------------------------------------------------
    | USER ORDERS
    |--------------------------------------------------------------------------
    */

    Route::apiResource(
        'orders',
        OrderController::class
    )->only(['store', 'index', 'show']);

    Route::patch(
        '/orders/{order}/cancel',
        [OrderController::class, 'cancel']
    );


    /*
    |--------------------------------------------------------------------------
    | TEST ADMIN ACCESS
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:admin')
        ->get('/test-admin', function () {

            return response()->json([
                'message' => 'Admin access granted.'
            ]);
        });

});