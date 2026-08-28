<?php

use App\Http\Controllers\Store\FavoriteController;
use Illuminate\Support\Facades\Route;

Route::get('/favoritos', [FavoriteController::class, 'index'])->name('index');
Route::post('/favoritos/{product:slug}/alternar', [FavoriteController::class, 'toggle'])->name('toggle');
