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
    });