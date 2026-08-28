<?php

use App\Http\Controllers\Admin\ShipmentController;
use Illuminate\Support\Facades\Route;

Route::resource('shipments', ShipmentController::class)->only(['index', 'edit', 'update']);
