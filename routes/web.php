<?php

use App\Http\Controllers\Store\FavoriteController;
use App\Http\Controllers\Store\HomeController;
use App\Http\Controllers\Store\ProductController;
use Illuminate\Support\Facades\Route;

Route::name('store.')->group(function (): void {
    Route::get('/', HomeController::class)
        ->name('home');

    Route::get('/produtos', [ProductController::class, 'index'])
        ->name('products.index');

    Route::get('/produtos/{product:slug}', [ProductController::class, 'show'])
        ->name('products.show');
});

Route::middleware('auth')->name('store.favorites.')->group(function (): void {
    Route::get('/favoritos', [FavoriteController::class, 'index'])
        ->name('index');
    Route::post('/favoritos/{product:slug}/alternar', [FavoriteController::class, 'toggle'])
        ->name('toggle');
});

require __DIR__.'/admin.php';
