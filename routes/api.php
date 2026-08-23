
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

use App\Http\Controllers\Api\Customer\InternationalImportRequestController;
use App\Http\Controllers\Api\Admin\InternationalImportRequestController
    as AdminInternationalImportRequestController;

use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\CustomerPasswordController;
use App\Http\Controllers\CustomerProfileController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\Api\Admin\CustomerController;


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

Route::apiResource('settings', SettingController::class)
    ->only([
        'index',
        'show'
    ]);


/*
|--------------------------------------------------------------------------
| REVIEWS - PUBLIC READ
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


/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
|
| كل ما بداخل هذه المجموعة يحتاج:
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
    | CUSTOMER ROUTES
    |--------------------------------------------------------------------------
    */

    Route::prefix('customer')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | PAYMENT METHODS
        |--------------------------------------------------------------------------
        */

        Route::get('/payment-methods', [
            PaymentMethodController::class,
            'index'
        ]);

        Route::post('/payment-methods', [
            PaymentMethodController::class,
            'store'
        ]);

        Route::get('/payment-methods/{paymentMethod}', [
            PaymentMethodController::class,
            'show'
        ]);

        Route::put('/payment-methods/{paymentMethod}', [
            PaymentMethodController::class,
            'update'
        ]);

        Route::patch('/payment-methods/{paymentMethod}/default', [
            PaymentMethodController::class,
            'setDefault'
        ]);

        Route::delete('/payment-methods/{paymentMethod}', [
            PaymentMethodController::class,
            'destroy'
        ]);


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
    | CUSTOMER - CART
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
    | CUSTOMER - ORDERS
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
    | CUSTOMER - REVIEWS
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
        |
        | المدير والموظف يستطيعان مشاهدة العملاء.
        |
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
        | USERS - CREATE / UPDATE / DELETE
        |--------------------------------------------------------------------------
        |
        | المدير فقط.
        |
        */

        Route::middleware('role:admin')->group(function () {

            Route::post('/users', [
                UserController::class,
                'store'
            ]);

            Route::match(['put', 'patch'], '/users/{user}', [
                UserController::class,
                'update'
            ]);

            Route::delete('/users/{user}', [
                UserController::class,
                'destroy'
            ]);


            /*
            |--------------------------------------------------------------------------
            | ROLES
            |--------------------------------------------------------------------------
            */

            Route::apiResource('roles', RoleController::class);


            /*
            |--------------------------------------------------------------------------
            | CATEGORIES MANAGEMENT
            |--------------------------------------------------------------------------
            */

            Route::apiResource('categories', CategoryController::class)
                ->except([
                    'index',
                    'show'
                ]);


            /*
            |--------------------------------------------------------------------------
            | PRODUCTS MANAGEMENT
            |--------------------------------------------------------------------------
            */

            Route::apiResource('products', ProductController::class)
                ->except([
                    'index',
                    'show'
                ]);


            /*
            |--------------------------------------------------------------------------
            | PRODUCT IMAGES MANAGEMENT
            |--------------------------------------------------------------------------
            */

            Route::apiResource('product-images', ProductImageController::class)
                ->except([
                    'index',
                    'show'
                ]);


            /*
            |--------------------------------------------------------------------------
            | PRODUCT UNITS MANAGEMENT
            |--------------------------------------------------------------------------
            */

            Route::apiResource('product-units', ProductUnitController::class)
                ->except([
                    'index',
                    'show'
                ]);


            /*
            |--------------------------------------------------------------------------
            | BANNERS MANAGEMENT
            |--------------------------------------------------------------------------
            */

            Route::apiResource('banners', BannerController::class)
                ->except([
                    'index',
                    'show'
                ]);


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
                ->except([
                    'index',
                    'show'
                ]);


            /*
            |--------------------------------------------------------------------------
            | SETTINGS MANAGEMENT
            |--------------------------------------------------------------------------
            */

            Route::apiResource('settings', SettingController::class);


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

        });


        /*
        |--------------------------------------------------------------------------
        | ROLES & PERMISSIONS
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


        /*
        |--------------------------------------------------------------------------
        | ORDERS - ADMIN
        |--------------------------------------------------------------------------
        |
        | ملاحظة:
        | الأدمن لديه:
        | view-all-orders
        | edit-all-orders
        | delete-all-orders
        |
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


        /*
        |--------------------------------------------------------------------------
        | ORDER STATUS
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:edit-all-orders')
            ->patch('/orders/{order}/status', [
                OrderController::class,
                'updateStatus'
            ]);


        /*
        |--------------------------------------------------------------------------
        | UPDATE ORDER
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:edit-all-orders')
            ->match(['put', 'patch'], '/orders/{order}', [
                OrderController::class,
                'update'
            ]);


        /*
        |--------------------------------------------------------------------------
        | DELETE ORDER
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:delete-all-orders')
            ->delete('/orders/{order}', [
                OrderController::class,
                'destroy'
            ]);


        /*
        |--------------------------------------------------------------------------
        | DASHBOARD STATISTICS
        |--------------------------------------------------------------------------
        |
        | الأدمن يستطيع مشاهدة إحصائيات جميع الطلبات.
        |
        */

        Route::middleware('permission:view-all-orders')
            ->get('/dashboard/stats', [
                OrderController::class,
                'dashboardStats'
            ]);

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

