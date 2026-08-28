<?php

use App\Http\Controllers\Store\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/perfil', [ProfileController::class, 'show'])->name('show');
Route::put('/perfil', [ProfileController::class, 'update'])->name('update');
