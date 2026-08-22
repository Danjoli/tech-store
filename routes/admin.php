<?php

use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductVariantController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth', 'verified', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/', function () {
            return Inertia::render('Admin/Dashboard');
        })->name('dashboard');

        Route::resource('brands', BrandController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('products', ProductController::class);

        Route::prefix('products/{product}/variants')
            ->name('products.variants.')
            ->scopeBindings()
            ->group(function (): void {
                Route::get('/', [ProductVariantController::class, 'index'])
                    ->name('index');

                Route::get('/create', [ProductVariantController::class, 'create'])
                    ->name('create');

                Route::post('/', [ProductVariantController::class, 'store'])
                    ->name('store');

                Route::get('/{variant}/edit', [ProductVariantController::class, 'edit'])
                    ->name('edit');

                Route::put('/{variant}', [ProductVariantController::class, 'update'])
                    ->name('update');

                Route::delete('/{variant}', [ProductVariantController::class, 'destroy'])
                    ->name('destroy');
            });
    });
