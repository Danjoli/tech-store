<?php

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

require __DIR__.'/admin.php';
