<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\InternalNotificationController;


Route::apiResource('users', UserController::class);
Route::apiResource('roles', RoleController::class);
Route::apiResource('addresses', AddressController::class);
Route::apiResource('internal-notifications', InternalNotificationController::class);
