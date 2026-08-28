<?php

use Illuminate\Support\Facades\Route;

Route::name('store.')->group(function (): void {
    require __DIR__.'/store/catalog.php';
});

Route::middleware(['auth', 'active', 'verified', 'throttle:favorites'])->name('store.favorites.')->group(function (): void {
    require __DIR__.'/store/favorites.php';
});

Route::middleware(['auth', 'active'])->name('store.profile.')->group(function (): void {
    require __DIR__.'/store/profile.php';
});

Route::middleware(['auth', 'active', 'verified', 'throttle:cart'])->name('store.cart.')->group(function (): void {
    require __DIR__.'/store/cart.php';
});

Route::middleware(['auth', 'active', 'verified', 'throttle:checkout'])->name('store.checkout.')->group(function (): void {
    require __DIR__.'/store/checkout.php';
});

Route::middleware(['auth', 'active', 'verified'])->name('store.orders.')->group(function (): void {
    require __DIR__.'/store/orders.php';
});

require __DIR__.'/admin.php';
