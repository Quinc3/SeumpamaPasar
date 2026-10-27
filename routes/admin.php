<?php

use App\Http\Controllers\Admin\BusinessController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/bisnis', [BusinessController::class, 'index'])->name('business.index');
        Route::patch('/bisnis/{profile}/verifikasi', [BusinessController::class, 'verify'])->name('business.verify');
        Route::patch('/bisnis/{profile}/cabut', [BusinessController::class, 'unverify'])->name('business.unverify');
    });