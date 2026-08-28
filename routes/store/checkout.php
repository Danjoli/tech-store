<?php

use App\Http\Controllers\Store\CheckoutController;
use Illuminate\Support\Facades\Route;

Route::get('/checkout', [CheckoutController::class, 'create'])->name('create');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('store');
