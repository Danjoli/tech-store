<?php

use App\Http\Controllers\Store\CartController;
use Illuminate\Support\Facades\Route;

Route::get('/carrinho', [CartController::class, 'index'])->name('index');
Route::post('/carrinho/itens', [CartController::class, 'store'])->name('items.store');
Route::put('/carrinho/itens/{cartItem}', [CartController::class, 'update'])->name('items.update');
Route::delete('/carrinho/itens/{cartItem}', [CartController::class, 'destroy'])->name('items.destroy');
