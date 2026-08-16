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
*/

Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
Route::apiResource('products', ProductController::class)->only(['index', 'show']);
Route::apiResource('product-images', ProductImageController::class)->only(['index', 'show']);
Route::apiResource('product-units', ProductUnitController::class)->only(['index', 'show']);
Route::apiResource('banners', BannerController::class)->only(['index', 'show']);
Route::apiResource('features', FeatureController::class)->only(['index', 'show']);
Route::apiResource('partners', PartnerController::class)->only(['index', 'show']);
Route::apiResource('settings', SettingController::class)->only(['index', 'show']);
Route::apiResource('reviews', ReviewController::class)->only(['index', 'show']);


/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES (Sanctum)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store']);

    /*
    |--------------------------------------------------------------------------
    | ADMIN ONLY (Full Admin Resources)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('users', UserController::class);
        Route::apiResource('roles', RoleController::class);
        Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);
        Route::apiResource('products', ProductController::class)->except(['index', 'show']);
        Route::apiResource('product-images', ProductImageController::class)->except(['index', 'show']);
        Route::apiResource('product-units', ProductUnitController::class)->except(['index', 'show']);
        Route::apiResource('banners', BannerController::class);
        Route::apiResource('features', FeatureController::class);
        Route::apiResource('partners', PartnerController::class)->except(['index', 'show']);
        Route::apiResource('settings', SettingController::class);
        Route::apiResource('internal-notifications', InternalNotificationController::class);
    });

    /*
    |--------------------------------------------------------------------------
    | ADMIN PREFIX WITH PERMISSIONS
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->group(function () {

        /* Users */
        Route::middleware('permission:view-users')->get('/users', [UserController::class, 'index']);
        Route::middleware('permission:view-users')->get('/users/{user}', [UserController::class, 'show']);
        Route::middleware('permission:create-users')->post('/users', [UserController::class, 'store']);
        Route::middleware('permission:update-users')->match(['put', 'patch'], '/users/{user}', [UserController::class, 'update']);
        Route::middleware('permission:delete-users')->delete('/users/{user}', [UserController::class, 'destroy']);

        /* Roles */
        Route::middleware('permission:view-roles')->get('/roles', [RoleController::class, 'index']);
        Route::middleware('permission:view-roles')->get('/roles/{role}', [RoleController::class, 'show']);
        Route::middleware('permission:create-roles')->post('/roles', [RoleController::class, 'store']);
        Route::middleware('permission:update-roles')->match(['put', 'patch'], '/roles/{role}', [RoleController::class, 'update']);
        Route::middleware('permission:delete-roles')->delete('/roles/{role}', [RoleController::class, 'destroy']);

        /* Permissions */
        Route::middleware('permission:view-roles')->get('/permissions', [RoleController::class, 'permissions']);
        Route::middleware('permission:view-roles')->get('/permissions/{id}', [RoleController::class, 'showPermission']);
        Route::middleware('permission:create-roles')->post('/permissions', [RoleController::class, 'storePermission']);
        Route::middleware('permission:update-roles')->match(['put', 'patch'], '/permissions/{id}', [RoleController::class, 'updatePermission']);
        Route::middleware('permission:delete-roles')->delete('/permissions/{id}', [RoleController::class, 'destroyPermission']);

        /* Role-Permission Assignments */
        Route::middleware('permission:update-roles')->post('/roles/{roleId}/permissions', [RoleController::class, 'assignPermission']);
        Route::middleware('permission:update-roles')->delete('/roles/{roleId}/permissions', [RoleController::class, 'removePermission']);

        /* Banners, Features, Partners, Settings */
        Route::middleware('permission:manage-banners')->post('/banners', [BannerController::class, 'store']);
        Route::middleware('permission:manage-banners')->match(['put', 'patch'], '/banners/{banner}', [BannerController::class, 'update']);
        Route::middleware('permission:manage-banners')->delete('/banners/{banner}', [BannerController::class, 'destroy']);

        Route::middleware('permission:manage-features')->post('/features', [FeatureController::class, 'store']);
        Route::middleware('permission:manage-features')->match(['put', 'patch'], '/features/{feature}', [FeatureController::class, 'update']);
        Route::middleware('permission:manage-features')->delete('/features/{feature}', [FeatureController::class, 'destroy']);

        Route::middleware('permission:manage-partners')->post('/partners', [PartnerController::class, 'store']);
        Route::middleware('permission:manage-partners')->match(['put', 'patch'], '/partners/{partner}', [PartnerController::class, 'update']);
        Route::middleware('permission:manage-partners')->delete('/partners/{partner}', [PartnerController::class, 'destroy']);

        Route::middleware('permission:manage-settings')->post('/settings', [SettingController::class, 'store']);
        Route::middleware('permission:manage-settings')->match(['put', 'patch'], '/settings/{setting}', [SettingController::class, 'update']);
        Route::middleware('permission:manage-settings')->delete('/settings/{setting}', [SettingController::class, 'destroy']);

        /* 🛒 إدارة الطلبات للآدمن */
        Route::middleware('permission:view-orders')->group(function () {
            // استخدام apiResource لمعالجة store, update, destroy
            Route::apiResource('orders', OrderController::class)->except(['index', 'show']);

            // مسارات العرض الخاصة بالأدمن (لتفادي التعارض مع عرض طلبات الزبون)
            Route::get('/orders', [OrderController::class, 'adminIndex']);
            Route::get('/orders/{order}', [OrderController::class, 'adminShow']);

            // مسار تغيير الحالة والإحصائيات
            Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus']);
            Route::get('/dashboard/stats', [OrderController::class, 'dashboardStats']);
        });
    });

    /*
    |--------------------------------------------------------------------------
    | REVIEWS, CART & USER ORDERS
    |--------------------------------------------------------------------------
    */
    Route::post('/reviews', [ReviewController::class, 'store']);

    Route::get('/cart/count', [CartController::class, 'count']);
    Route::get('/cart', [CartController::class, 'index']);
    Route::delete('/cart/clear', [CartController::class, 'clear']);
    Route::apiResource('cart/items', CartItemController::class)->only(['store', 'update', 'destroy']);

    Route::apiResource('orders', OrderController::class)->only(['store', 'index', 'show']);
    Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel']);

    /* Test Admin Access */
    Route::middleware('role:admin')->get('/test-admin', function () {
        return response()->json(['message' => 'Admin access granted.']);
    });
});
