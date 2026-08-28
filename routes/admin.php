<?php

use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'active', 'verified', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/', DashboardController::class)
            ->name('dashboard');

        require __DIR__.'/admin/catalog.php';
        require __DIR__.'/admin/orders.php';
        require __DIR__.'/admin/shipments.php';
    });
