<?php

use App\Http\Controllers\BannerController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/reviews', [ReviewController::class, 'index'])
    ->name('reviews.index');

Route::get('/reviews/{id}', [ReviewController::class, 'show'])
    ->name('reviews.show');

Route::post('/reviews', [ReviewController::class, 'store'])
    ->name('reviews.store');

Route::put('/reviews/{id}', [ReviewController::class, 'update'])
    ->name('reviews.update');

Route::delete('/reviews/{id}', [ReviewController::class, 'destroy'])
    ->name('reviews.destroy');

    Route::get('/banners', [BannerController::class, 'index'])
    ->name('banners.index');

Route::get('/banners/{id}', [BannerController::class, 'show'])
    ->name('banners.show');

Route::post('/banners', [BannerController::class, 'store'])
    ->name('banners.store');

Route::put('/banners/{id}', [BannerController::class, 'update'])
    ->name('banners.update');

Route::delete('/banners/{id}', [BannerController::class, 'destroy'])
    ->name('banners.destroy');


    Route::get('/features', [FeatureController::class, 'index'])
    ->name('features.index');

Route::get('/features/{id}', [FeatureController::class, 'show'])
    ->name('features.show');

Route::post('/features', [FeatureController::class, 'store'])
    ->name('features.store');

Route::put('/features/{id}', [FeatureController::class, 'update'])
    ->name('features.update');

Route::delete('/features/{id}', [FeatureController::class, 'destroy'])
    ->name('features.destroy');


    Route::get('/settings', [SettingController::class, 'index'])
    ->name('settings.index');

Route::get('/settings/{id}', [SettingController::class, 'show'])
    ->name('settings.show');

Route::post('/settings', [SettingController::class, 'store'])
    ->name('settings.store');

Route::put('/settings/{id}', [SettingController::class, 'update'])
    ->name('settings.update');

Route::delete('/settings/{id}', [SettingController::class, 'destroy'])
    ->name('settings.destroy');


    Route::get('/partners', [PartnerController::class, 'index'])
    ->name('partners.index');

Route::get('/partners/{id}', [PartnerController::class, 'show'])
    ->name('partners.show');

Route::post('/partners', [PartnerController::class, 'store'])
    ->name('partners.store');

Route::put('/partners/{id}', [PartnerController::class, 'update'])
    ->name('partners.update');

Route::delete('/partners/{id}', [PartnerController::class, 'destroy'])
    ->name('partners.destroy');