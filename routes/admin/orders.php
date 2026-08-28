<?php

use App\Http\Controllers\Admin\OrderController;
use Illuminate\Support\Facades\Route;

Route::resource('orders', OrderController::class)->only(['index', 'show']);
