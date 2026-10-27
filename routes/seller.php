<?php

use App\Http\Controllers\Seller\DashboardController;
use App\Http\Controllers\Seller\OrderController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'seller.active'])
    ->prefix('toko')
    ->name('seller.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/pesanan', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/pesanan/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('/pesanan/{order}/proses', [OrderController::class, 'process'])->name('orders.process');
        Route::post('/pesanan/{order}/kirim', [OrderController::class, 'ship'])->name('orders.ship');
        Route::post('/pesanan/{order}/batal', [OrderController::class, 'cancel'])->name('orders.cancel');

        Route::post('/pesanan/bulk-status', [\App\Http\Controllers\Seller\OrderController::class, 'bulkStatus'])->name('orders.bulk-status');
        Route::post('/produk/bulk-archive', [\App\Http\Controllers\Seller\ProductController::class, 'bulkArchive'])->name('products.bulk-archive');

        Route::get('/produk', [\App\Http\Controllers\Seller\ProductController::class, 'index'])->name('products.index');
        Route::get('/produk/baru', [\App\Http\Controllers\Seller\ProductController::class, 'create'])->name('products.create');
        Route::post('/produk', [\App\Http\Controllers\Seller\ProductController::class, 'store'])->name('products.store');
        Route::get('/produk/{product}/edit', [\App\Http\Controllers\Seller\ProductController::class, 'edit'])->name('products.edit');
        Route::patch('/produk/{product}', [\App\Http\Controllers\Seller\ProductController::class, 'update'])->name('products.update');
        Route::post('/produk/{product}/arsip', [\App\Http\Controllers\Seller\ProductController::class, 'archive'])->name('products.archive');
        Route::post('/produk/{product}/tier', [\App\Http\Controllers\Seller\WholesaleTierController::class, 'store'])->name('products.tiers.store');
        Route::delete('/produk/{product}/tier/{tier}', [\App\Http\Controllers\Seller\WholesaleTierController::class, 'destroy'])->name('products.tiers.destroy');
    });