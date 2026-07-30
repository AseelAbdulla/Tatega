<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\InternalNotificationController;



Route::apiResource('users', UserController::class);
Route::apiResource('roles', RoleController::class);
Route::apiResource('addresses', AddressController::class);
Route::apiResource(
    'internal-notifications',
    InternalNotificationController::class
);
