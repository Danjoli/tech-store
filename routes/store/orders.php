<?php

use App\Http\Controllers\Store\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/pedidos', [OrderController::class, 'index'])->name('index');
Route::get('/pedidos/{order}', [OrderController::class, 'show'])->name('show');
