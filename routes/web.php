<?php

use App\Http\Controllers\Store\CartController;
use App\Http\Controllers\Store\CheckoutController;
use App\Http\Controllers\Store\FavoriteController;
use App\Http\Controllers\Store\HomeController;
use App\Http\Controllers\Store\OrderController;
use App\Http\Controllers\Store\ProductController;
use App\Http\Controllers\Store\ProfileController;
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

Route::middleware('auth')->name('store.profile.')->group(function (): void {
    Route::get('/perfil', [ProfileController::class, 'show'])->name('show');
    Route::put('/perfil', [ProfileController::class, 'update'])->name('update');
});

Route::middleware('auth')->name('store.cart.')->group(function (): void {
    Route::get('/carrinho', [CartController::class, 'index'])->name('index');
    Route::post('/carrinho/itens', [CartController::class, 'store'])->name('items.store');
    Route::put('/carrinho/itens/{cartItem}', [CartController::class, 'update'])->name('items.update');
    Route::delete('/carrinho/itens/{cartItem}', [CartController::class, 'destroy'])->name('items.destroy');
});

Route::middleware('auth')->name('store.checkout.')->group(function (): void {
    Route::get('/checkout', [CheckoutController::class, 'create'])->name('create');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('store');
});

Route::middleware('auth')->name('store.orders.')->group(function (): void {
    Route::get('/pedidos', [OrderController::class, 'index'])->name('index');
    Route::get('/pedidos/{order}', [OrderController::class, 'show'])->name('show');
});

require __DIR__.'/admin.php';
