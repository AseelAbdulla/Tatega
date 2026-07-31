<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CartItemController;
use App\Http\Controllers\InternalNotificationController;
use App\Http\Controllers\OrderController;

Route::apiResource('users', UserController::class);
Route::apiResource('roles', RoleController::class);
Route::apiResource('addresses', AddressController::class);
Route::apiResource('internal-notifications', InternalNotificationController::class);



// Route::middleware('auth:sanctum')->group(function () {

//  routs for cart item in resources controlers 
Route::apiResource('cart/items', CartItemController::class)->only([
    'store',
    'update',
    'destroy',
]);
//  exipt cart form resources controlers 
Route::get('/cart', [CartController::class, 'index']);

Route::delete('/cart/clear', [CartController::class, 'clear']);

//  routs for orders in resources controlers 
Route::apiResource('orders', OrderController::class)->only([
    'store',
    'show',
    'index',
]);;

//  add cancel route for order form resources controlers 
Route::get('/orders/{order}/cancel', [OrderController::class, 'cancel']);



// });
