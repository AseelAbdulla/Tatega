<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Auth Controllers
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
// Resource Controllers
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
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

/*
|--------------------------------------------------------------------------
| 1. PUBLIC ROUTES (بدون حماية)
|--------------------------------------------------------------------------
*/

// Authentication
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Public Reads
Route::get('/wallets', [WalletController::class, 'index']);
Route::get('/banners/active', [BannerController::class, 'active']);
Route::get('/settings', [SettingController::class, 'index']);
Route::get('/settings/{setting}', [SettingController::class, 'show']);
Route::get('/reviews/approved', [ReviewController::class, 'approved']);

// Public Catalog API Resources (Read-Only)
Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
Route::apiResource('products', ProductController::class)->only(['index', 'show']);
Route::apiResource('product-images', ProductImageController::class)->only(['index', 'show']);
Route::apiResource('product-units', ProductUnitController::class)->only(['index', 'show']);
Route::apiResource('features', FeatureController::class)->only(['index', 'show']);
Route::apiResource('partners', PartnerController::class)->only(['index', 'show']);


/*
|--------------------------------------------------------------------------
| 2. PROTECTED ROUTES (Sanctum Authenticated Users)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // Auth & Profile
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store']);

    // Client Dashboard Summary
    Route::get('/client/dashboard-summary', function (Request $request) {
        $user = $request->user();
        return response()->json([
            'stats' => [
                ['id' => 1, 'title' => 'الطلبات النشطة', 'value' => $user->orders()->where('status', 'processing')->count(), 'icon' => 'local_shipping', 'color' => 'text-primary', 'bg' => 'bg-primary/10'],
                ['id' => 2, 'title' => 'إجمالي الطلبات', 'value' => $user->orders()->count(), 'icon' => 'shopping_bag', 'color' => 'text-secondary', 'bg' => 'bg-secondary/10'],
            ],
            'recent_orders' => $user->orders()->latest()->take(5)->get()
        ]);
    });

    // Customer Profile & Internal Notifications
    Route::prefix('customer')->group(function () {
        Route::get('/user', function (Request $request) {
            return response()->json([
                'data' => [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'roles' => $request->user()->getRoleNames(),
                ]
            ]);
        });

        // ✅ المسار الجديد للتعديل
        Route::put('/user/update', [UserController::class, 'updateProfile']);
        Route::put('/user/profile', [UserController::class, 'profile']);
        Route::put('/user/changePassword', [UserController::class, 'changePassword']);
        Route::get('/notifications/unread-count', [InternalNotificationController::class, 'unreadCount']);
        Route::get('/notifications', [InternalNotificationController::class, 'index']);
        Route::patch('/notifications/read-all', [InternalNotificationController::class, 'markAllAsRead']);
        Route::get('/notifications/{id}', [InternalNotificationController::class, 'customerShow']);
        Route::patch('/notifications/{id}/read', [InternalNotificationController::class, 'markAsRead']);
    });

    // Reviews (Authenticated User Action)
    Route::post('/reviews', [ReviewController::class, 'store']);

    // Cart Management
    Route::get('/cart/count', [CartController::class, 'count']);
    Route::get('/cart', [CartController::class, 'index']);
    Route::delete('/cart/clear', [CartController::class, 'clear']);
    Route::apiResource('cart/items', CartItemController::class)->only(['store', 'update', 'destroy']);

    // User Orders Management
    Route::apiResource('orders', OrderController::class)->only(['store', 'index', 'show']);
    Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel']);

    /*
    |--------------------------------------------------------------------------
    | 3. ADMIN & CONTROL PANEL ROUTES (Role/Permission Protected)
    |--------------------------------------------------------------------------
    */

    Route::prefix('admin')->group(function () {

        // Admin Access Test
        Route::middleware('role:admin')->get('/test-admin', fn() => response()->json(['message' => 'Admin access granted.']));

        // Catalog Management (Admin Role Only)
        Route::middleware('role:admin')->group(function () {
            Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);
            Route::apiResource('products', ProductController::class)->except(['index', 'show']);
            Route::apiResource('product-images', ProductImageController::class)->except(['index', 'show']);
            Route::apiResource('product-units', ProductUnitController::class)->except(['index', 'show']);
            Route::apiResource('internal-notifications', InternalNotificationController::class);
        });

        // Users Management (Permission-based)
        Route::middleware('permission:view-users')->get('/users', [UserController::class, 'index']);
        Route::middleware('permission:view-users')->get('/users/{user}', [UserController::class, 'show']);
        Route::middleware('permission:create-users')->post('/users', [UserController::class, 'store']);
        Route::middleware('permission:update-users')->match(['put', 'patch'], '/users/{user}', [UserController::class, 'update']);
        Route::middleware('permission:delete-users')->delete('/users/{user}', [UserController::class, 'destroy']);

        // Roles Management (Permission-based)
        Route::middleware('permission:view-roles')->get('/roles', [RoleController::class, 'index']);
        Route::middleware('permission:view-roles')->get('/roles/{role}', [RoleController::class, 'show']);
        Route::middleware('permission:create-roles')->post('/roles', [RoleController::class, 'store']);
        Route::middleware('permission:update-roles')->match(['put', 'patch'], '/roles/{role}', [RoleController::class, 'update']);
        Route::middleware('permission:delete-roles')->delete('/roles/{role}', [RoleController::class, 'destroy']);

        // Permissions Management
        Route::middleware('permission:view-roles')->get('/permissions', [RoleController::class, 'permissions']);
        Route::middleware('permission:view-roles')->get('/permissions/{id}', [RoleController::class, 'showPermission']);
        Route::middleware('permission:create-roles')->post('/permissions', [RoleController::class, 'storePermission']);
        Route::middleware('permission:update-roles')->match(['put', 'patch'], '/permissions/{id}', [RoleController::class, 'updatePermission']);
        Route::middleware('permission:delete-roles')->delete('/permissions/{id}', [RoleController::class, 'destroyPermission']);

        // Role-Permission Assignment
        Route::middleware('permission:update-roles')->post('/roles/{roleId}/permissions', [RoleController::class, 'assignPermission']);
        Route::middleware('permission:update-roles')->delete('/roles/{roleId}/permissions', [RoleController::class, 'removePermission']);

        // Banners Management
        Route::middleware('permission:manage-banners')->group(function () {
            Route::get('/banners', [BannerController::class, 'index']);
            Route::post('/banners', [BannerController::class, 'store']);
            Route::match(['put', 'patch'], '/banners/{banner}', [BannerController::class, 'update']);
            Route::delete('/banners/{banner}', [BannerController::class, 'destroy']);
        });

        // Features Management
        Route::middleware('permission:manage-features')->group(function () {
            Route::post('/features', [FeatureController::class, 'store']);
            Route::match(['put', 'patch'], '/features/{feature}', [FeatureController::class, 'update']);
            Route::delete('/features/{feature}', [FeatureController::class, 'destroy']);
        });

        // Partners Management
        Route::middleware('permission:manage-partners')->group(function () {
            Route::post('/partners', [PartnerController::class, 'store']);
            Route::match(['put', 'patch'], '/partners/{partner}', [PartnerController::class, 'update']);
            Route::delete('/partners/{partner}', [PartnerController::class, 'destroy']);
        });

        // Settings Management
        Route::middleware('permission:manage-settings')->group(function () {
            Route::post('/settings', [SettingController::class, 'store']);
            Route::match(['put', 'patch'], '/settings/{setting}', [SettingController::class, 'update']);
            Route::delete('/settings/{setting}', [SettingController::class, 'destroy']);
        });

        // Admin Orders Management
        Route::middleware('permission:view-orders')->group(function () {
            Route::get('/orders', [OrderController::class, 'adminIndex']);
            Route::get('/orders/{order}', [OrderController::class, 'adminShow']);
            Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus']);
            Route::get('/dashboard/stats', [OrderController::class, 'dashboardStats']);
            Route::apiResource('orders', OrderController::class)->except(['index', 'show']);
        });

        // Admin Wallets Management
        Route::middleware('permission:manage-wallets')->group(function () {
            Route::post('/wallets', [WalletController::class, 'store']);
            Route::get('/wallets/{wallet}', [WalletController::class, 'show']);
            Route::put('/wallets/{wallet}', [WalletController::class, 'update']);
            Route::delete('/wallets/{wallet}', [WalletController::class, 'destroy']);
        });

        // Admin Reviews Management
        Route::get('/reviews', [ReviewController::class, 'index']);
        Route::get('/reviews/{review}', [ReviewController::class, 'show']);
        Route::patch('/reviews/{review}', [ReviewController::class, 'update']);
        Route::delete('/reviews/{review}', [ReviewController::class, 'destroy']);
    });
});
