<?php

use App\Http\Controllers\Affiliate\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])
    ->prefix('afiliasi')
    ->name('affiliate.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    });