<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Controllers
|--------------------------------------------------------------------------
*/

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
use App\Http\Controllers\WalletController;

use App\Http\Controllers\Api\Customer\InternationalImportRequestController;
use App\Http\Controllers\Api\Admin\InternationalImportRequestController
    as AdminInternationalImportRequestController;
use App\Http\Controllers\Api\Admin\CustomerController;

use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\CustomerPasswordController;
use App\Http\Controllers\CustomerPaymentMethodController;
use App\Http\Controllers\CustomerProfileController;


/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| AUTHENTICATION
|--------------------------------------------------------------------------
*/

Route::post('/login', [
    AuthController::class,
    'login'
]);

Route::post('/register', [
    AuthController::class,
    'register'
]);


/*
|--------------------------------------------------------------------------
| CATEGORIES - PUBLIC READ
|--------------------------------------------------------------------------
*/

Route::apiResource('categories', CategoryController::class)
    ->only([
        'index',
        'show'
    ]);


/*
|--------------------------------------------------------------------------
| PRODUCTS - PUBLIC READ
|--------------------------------------------------------------------------
*/

Route::apiResource('products', ProductController::class)
    ->only([
        'index',
        'show'
    ]);


/*
|--------------------------------------------------------------------------
| PRODUCT IMAGES - PUBLIC READ
|--------------------------------------------------------------------------
*/

Route::apiResource('product-images', ProductImageController::class)
    ->only([
        'index',
        'show'
    ]);


/*
|--------------------------------------------------------------------------
| PRODUCT UNITS - PUBLIC READ
|--------------------------------------------------------------------------
*/

Route::apiResource('product-units', ProductUnitController::class)
    ->only([
        'index',
        'show'
    ]);


/*
|--------------------------------------------------------------------------
| BANNERS - PUBLIC READ
|--------------------------------------------------------------------------
*/

Route::apiResource('banners', BannerController::class)
    ->only([
        'index',
        'show'
    ]);


/*
|--------------------------------------------------------------------------
| FEATURES - PUBLIC READ
|--------------------------------------------------------------------------
*/

Route::apiResource('features', FeatureController::class)
    ->only([
        'index',
        'show'
    ]);


/*
|--------------------------------------------------------------------------
| PARTNERS - PUBLIC READ
|--------------------------------------------------------------------------
*/

Route::apiResource('partners', PartnerController::class)
    ->only([
        'index',
        'show'
    ]);


/*
|--------------------------------------------------------------------------
| SETTINGS - PUBLIC READ
|--------------------------------------------------------------------------
*/

Route::get('/settings', [
    SettingController::class,
    'index'
]);

Route::get('/settings/{setting}', [
    SettingController::class,
    'show'
]);


/*
|--------------------------------------------------------------------------
| REVIEWS - PUBLIC
|--------------------------------------------------------------------------
*/

Route::get('/reviews', [
    ReviewController::class,
    'index'
])->name('reviews.index');

Route::get('/reviews/{review}', [
    ReviewController::class,
    'show'
])->name('reviews.show');

Route::get('/reviews/approved', [
    ReviewController::class,
    'approved'
]);


/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
|
| Authorization: Bearer TOKEN
|
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | AUTHENTICATED USER
    |--------------------------------------------------------------------------
    */

    Route::get('/me', [
        AuthController::class,
        'me'
    ]);

    Route::post('/logout', [
        AuthController::class,
        'logout'
    ]);

    Route::post('/email/verification-notification', [
        EmailVerificationNotificationController::class,
        'store'
    ]);


    /*
    |--------------------------------------------------------------------------
    | ACTIVE BANNERS
    |--------------------------------------------------------------------------
    */

    Route::get('/banners/active', [
        BannerController::class,
        'active'
    ]);


    /*
    |--------------------------------------------------------------------------
    | CUSTOMER PROFILE
    |--------------------------------------------------------------------------
    */

    Route::get('/customer/profile', [
        CustomerProfileController::class,
        'show'
    ]);

    Route::put('/customer/profile', [
        CustomerProfileController::class,
        'update'
    ]);


    /*
    |--------------------------------------------------------------------------
    | CUSTOMER PASSWORD
    |--------------------------------------------------------------------------
    */

    Route::patch('/customer/password', [
        CustomerPasswordController::class,
        'update'
    ]);


    /*
    |--------------------------------------------------------------------------
    | CUSTOMER PAYMENT METHODS
    |--------------------------------------------------------------------------
    */

    Route::get('/customer/payment-methods', [
        CustomerPaymentMethodController::class,
        'index'
    ]);

    Route::post('/customer/payment-methods', [
        CustomerPaymentMethodController::class,
        'store'
    ]);

    Route::get('/customer/payment-methods/{paymentMethod}', [
        CustomerPaymentMethodController::class,
        'show'
    ]);

    Route::put('/customer/payment-methods/{paymentMethod}', [
        CustomerPaymentMethodController::class,
        'update'
    ]);

    Route::delete('/customer/payment-methods/{paymentMethod}', [
        CustomerPaymentMethodController::class,
        'destroy'
    ]);

    Route::patch('/customer/payment-methods/{paymentMethod}/default', [
        CustomerPaymentMethodController::class,
        'setDefault'
    ]);


    /*
    |--------------------------------------------------------------------------
    | CUSTOMER
    |--------------------------------------------------------------------------
    */

    Route::prefix('customer')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | CUSTOMER NOTIFICATIONS
        |--------------------------------------------------------------------------
        */

        Route::get('/notifications', [
            InternalNotificationController::class,
            'customerIndex'
        ]);

        Route::get('/notifications/unread-count', [
            InternalNotificationController::class,
            'unreadCount'
        ]);

        Route::patch('/notifications/read-all', [
            InternalNotificationController::class,
            'markAllAsRead'
        ]);

        Route::get('/notifications/{id}', [
            InternalNotificationController::class,
            'customerShow'
        ]);

        Route::patch('/notifications/{id}/read', [
            InternalNotificationController::class,
            'markAsRead'
        ]);


        /*
        |--------------------------------------------------------------------------
        | CUSTOMER ORDER HISTORY
        |--------------------------------------------------------------------------
        */

        Route::get('/order-history', [
            CustomerOrderController::class,
            'index'
        ]);


        /*
        |--------------------------------------------------------------------------
        | INTERNATIONAL IMPORT
        |--------------------------------------------------------------------------
        */

        Route::get('/international-import', [
            InternationalImportRequestController::class,
            'index'
        ]);

        Route::post('/international-import', [
            InternationalImportRequestController::class,
            'store'
        ]);

        Route::get('/international-import/{internationalImportRequest}', [
            InternationalImportRequestController::class,
            'show'
        ]);

        Route::put('/international-import/{internationalImportRequest}', [
            InternationalImportRequestController::class,
            'update'
        ]);

        Route::delete('/international-import/{internationalImportRequest}', [
            InternationalImportRequestController::class,
            'destroy'
        ]);

    });


    /*
    |--------------------------------------------------------------------------
    | CART
    |--------------------------------------------------------------------------
    */

    Route::get('/cart/count', [
        CartController::class,
        'count'
    ]);

    Route::get('/cart', [
        CartController::class,
        'index'
    ]);

    Route::delete('/cart/clear', [
        CartController::class,
        'clear'
    ]);

    Route::apiResource('cart/items', CartItemController::class)
        ->only([
            'store',
            'update',
            'destroy'
        ]);


    /*
    |--------------------------------------------------------------------------
    | CUSTOMER ORDERS
    |--------------------------------------------------------------------------
    */

    Route::post('/orders', [
        OrderController::class,
        'store'
    ]);

    Route::get('/orders', [
        OrderController::class,
        'index'
    ]);

    Route::get('/orders/{order}', [
        OrderController::class,
        'show'
    ]);

    Route::patch('/orders/{order}/cancel', [
        OrderController::class,
        'cancel'
    ]);


    /*
    |--------------------------------------------------------------------------
    | CUSTOMER REVIEWS
    |--------------------------------------------------------------------------
    */

    Route::post('/reviews', [
        ReviewController::class,
        'store'
    ]);


    /*
    |--------------------------------------------------------------------------
    | ADMIN / EMPLOYEE - INTERNATIONAL IMPORT
    |--------------------------------------------------------------------------
    |
    | المدير والموظف يستطيعان مشاهدة وإدارة طلبات الاستيراد الدولي.
    |
    */

    Route::prefix('admin')
        ->middleware(['role:admin|employee'])
        ->group(function () {

            Route::get('/international-imports', [
                AdminInternationalImportRequestController::class,
                'index'
            ]);

            Route::get('/international-imports/{internationalImportRequest}', [
                AdminInternationalImportRequestController::class,
                'show'
            ]);

            Route::patch('/international-imports/{internationalImportRequest}/approve', [
                AdminInternationalImportRequestController::class,
                'approve'
            ]);

            Route::patch('/international-imports/{internationalImportRequest}/reject', [
                AdminInternationalImportRequestController::class,
                'reject'
            ]);

        });


    /*
    |--------------------------------------------------------------------------
    | ADMIN ROUTES
    |--------------------------------------------------------------------------
    */

    Route::prefix('admin')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | CUSTOMERS
        |--------------------------------------------------------------------------
        */

        Route::middleware([
            'role:admin|employee',
            'permission:view-users'
        ])->group(function () {

            Route::get('/customers', [
                CustomerController::class,
                'index'
            ]);

            Route::get('/customers/{id}', [
                CustomerController::class,
                'show'
            ]);

            Route::get('/customers/{id}/orders', [
                CustomerController::class,
                'orders'
            ]);

        });


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
            ->match(
                ['put', 'patch'],
                '/users/{user}',
                [
                    UserController::class,
                    'update'
                ]
            );

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
            ->match(
                ['put', 'patch'],
                '/roles/{role}',
                [
                    RoleController::class,
                    'update'
                ]
            );

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
            ->match(
                ['put', 'patch'],
                '/permissions/{id}',
                [
                    RoleController::class,
                    'updatePermission'
                ]
            );

        Route::middleware('permission:delete-roles')
            ->delete('/permissions/{id}', [
                RoleController::class,
                'destroyPermission'
            ]);


        /*
        |--------------------------------------------------------------------------
        | ROLE - PERMISSION ASSIGNMENTS
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:update-roles')
            ->post('/roles/{roleId}/permissions', [
                RoleController::class,
                'assignPermission'
            ]);

        Route::middleware('permission:update-roles')
            ->delete('/roles/{roleId}/permissions', [
                RoleController::class,
                'removePermission'
            ]);


        /*
        |--------------------------------------------------------------------------
        | CATEGORIES MANAGEMENT
        |--------------------------------------------------------------------------
        */

        Route::middleware('role_or_permission:admin|manage-categories')
            ->apiResource('categories', CategoryController::class)
            ->except([
                'index',
                'show'
            ]);


        /*
        |--------------------------------------------------------------------------
        | PRODUCTS MANAGEMENT
        |--------------------------------------------------------------------------
        */

        Route::middleware('role_or_permission:admin|manage-products')
            ->apiResource('products', ProductController::class)
            ->except([
                'index',
                'show'
            ]);


        /*
        |--------------------------------------------------------------------------
        | PRODUCT IMAGES MANAGEMENT
        |--------------------------------------------------------------------------
        */

        Route::middleware('role_or_permission:admin|manage-products')
            ->apiResource('product-images', ProductImageController::class)
            ->except([
                'index',
                'show'
            ]);


        /*
        |--------------------------------------------------------------------------
        | PRODUCT UNITS MANAGEMENT
        |--------------------------------------------------------------------------
        */

        Route::middleware('role_or_permission:admin|manage-products')
            ->apiResource('product-units', ProductUnitController::class)
            ->except([
                'index',
                'show'
            ]);


        /*
        |--------------------------------------------------------------------------
        | BANNERS MANAGEMENT
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:manage-banners')
            ->get('/banners', [
                BannerController::class,
                'index'
            ]);

        Route::middleware('permission:manage-banners')
            ->post('/banners', [
                BannerController::class,
                'store'
            ]);

        Route::middleware('permission:manage-banners')
            ->match(
                ['put', 'patch'],
                '/banners/{banner}',
                [
                    BannerController::class,
                    'update'
                ]
            );

        Route::middleware('permission:manage-banners')
            ->delete('/banners/{banner}', [
                BannerController::class,
                'destroy'
            ]);


        /*
        |--------------------------------------------------------------------------
        | FEATURES MANAGEMENT
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:manage-features')
            ->post('/features', [
                FeatureController::class,
                'store'
            ]);

        Route::middleware('permission:manage-features')
            ->match(
                ['put', 'patch'],
                '/features/{feature}',
                [
                    FeatureController::class,
                    'update'
                ]
            );

        Route::middleware('permission:manage-features')
            ->delete('/features/{feature}', [
                FeatureController::class,
                'destroy'
            ]);


        /*
        |--------------------------------------------------------------------------
        | PARTNERS MANAGEMENT
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:manage-partners')
            ->post('/partners', [
                PartnerController::class,
                'store'
            ]);

        Route::middleware('permission:manage-partners')
            ->match(
                ['put', 'patch'],
                '/partners/{partner}',
                [
                    PartnerController::class,
                    'update'
                ]
            );

        Route::middleware('permission:manage-partners')
            ->delete('/partners/{partner}', [
                PartnerController::class,
                'destroy'
            ]);


        /*
        |--------------------------------------------------------------------------
        | SETTINGS MANAGEMENT
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:manage-settings')
            ->post('/settings', [
                SettingController::class,
                'store'
            ]);

        Route::middleware('permission:manage-settings')
            ->match(
                ['put', 'patch'],
                '/settings/{setting}',
                [
                    SettingController::class,
                    'update'
                ]
            );

        Route::middleware('permission:manage-settings')
            ->delete('/settings/{setting}', [
                SettingController::class,
                'destroy'
            ]);


        /*
        |--------------------------------------------------------------------------
        | INTERNAL NOTIFICATIONS - ADMIN
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
        */

        Route::get('/reviews', [
            ReviewController::class,
            'index'
        ]);

        Route::get('/reviews/{review}', [
            ReviewController::class,
            'show'
        ]);

        Route::put('/reviews/{review}', [
            ReviewController::class,
            'update'
        ])->name('reviews.update');

        Route::patch('/reviews/{review}', [
            ReviewController::class,
            'update'
        ]);

        Route::delete('/reviews/{review}', [
            ReviewController::class,
            'destroy'
        ])->name('reviews.destroy');


        /*
        |--------------------------------------------------------------------------
        | ORDERS - ADMIN
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:view-all-orders')
            ->get('/orders', [
                OrderController::class,
                'adminIndex'
            ]);

        Route::middleware('permission:view-all-orders')
            ->get('/orders/{order}', [
                OrderController::class,
                'adminShow'
            ]);

        Route::middleware('permission:edit-all-orders')
            ->patch('/orders/{order}/status', [
                OrderController::class,
                'updateStatus'
            ]);

        Route::middleware('permission:edit-all-orders')
            ->match(
                ['put', 'patch'],
                '/orders/{order}',
                [
                    OrderController::class,
                    'update'
                ]
            );

        Route::middleware('permission:delete-all-orders')
            ->delete('/orders/{order}', [
                OrderController::class,
                'destroy'
            ]);

        Route::middleware('permission:view-all-orders')
            ->get('/dashboard/stats', [
                OrderController::class,
                'dashboardStats'
            ]);


        /*
        |--------------------------------------------------------------------------
        | WALLETS - ADMIN
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:manage-wallets')->group(function () {

            Route::get('/wallets', [
                WalletController::class,
                'index'
            ]);

            Route::post('/wallets', [
                WalletController::class,
                'store'
            ]);

            Route::get('/wallets/{wallet}', [
                WalletController::class,
                'show'
            ]);

            Route::put('/wallets/{wallet}', [
                WalletController::class,
                'update'
            ]);

            Route::delete('/wallets/{wallet}', [
                WalletController::class,
                'destroy'
            ]);

        });

    });


    /*
    |--------------------------------------------------------------------------
    | TEST ADMIN ACCESS
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:admin')
        ->get('/test-admin', function () {

            return response()->json([
                'success' => true,
                'message' => 'Admin access granted.'
            ]);

        });

});
