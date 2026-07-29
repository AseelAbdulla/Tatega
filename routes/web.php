<?php

use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\ProductUnitController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});


// Reviews
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


// Categories
Route::resource('categories', CategoryController::class);



Route::resource('products', ProductController::class);

Route::resource('product-images', ProductImageController::class);

Route::resource('product-units', ProductUnitController::class);