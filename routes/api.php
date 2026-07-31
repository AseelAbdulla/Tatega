<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CartItemController;
use App\Http\Controllers\InternalNotificationController;
use App\Http\Controllers\OrderController;


// Controllers الخاصة بك
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\ProductUnitController;



Route::apiResource('users', UserController::class);

Route::apiResource('roles', RoleController::class);

Route::apiResource('addresses', AddressController::class);

Route::apiResource(
    'internal-notifications',
    InternalNotificationController::class
);



// =============================
// Routes الخاصة بك
// =============================


Route::apiResource(
    'categories',
    CategoryController::class
);


Route::apiResource(
    'products',
    ProductController::class
);


Route::apiResource(
    'product-images',
    ProductImageController::class
);


Route::apiResource(
    'product-units',
    ProductUnitController::class
);




// Route::middleware('auth:sanctum')->group(function () {


//  routes for cart item

Route::apiResource('cart/items', CartItemController::class)
    ->only([
        'store',
        'update',
        'destroy',
    ]);



// cart routes

Route::get(
    '/cart',
    [CartController::class, 'index']
);


Route::delete(
    '/cart/clear',
    [CartController::class, 'clear']
);



// order routes

Route::apiResource('orders', OrderController::class)
    ->only([
        'store',
        'show',
        'index',
    ]);



// cancel order

Route::get(
    '/orders/{order}/cancel',
    [OrderController::class, 'cancel']
);



// });