<?php

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