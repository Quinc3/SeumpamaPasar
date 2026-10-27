<?php

use App\Http\Controllers\Admin\BusinessController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProductModerationController;
use App\Http\Controllers\Admin\AffiliateController;

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/bisnis', [BusinessController::class, 'index'])->name('business.index');
        Route::patch('/bisnis/{profile}/verifikasi', [BusinessController::class, 'verify'])->name('business.verify');
        Route::patch('/bisnis/{profile}/cabut', [BusinessController::class, 'unverify'])->name('business.unverify');

        Route::get('/moderasi', [ProductModerationController::class, 'index'])->name('moderation.index');
Route::post('/moderasi/{product}/setujui', [ProductModerationController::class, 'approve'])->name('moderation.approve');
Route::post('/moderasi/{product}/tolak', [ProductModerationController::class, 'reject'])->name('moderation.reject');

Route::get('/afiliasi', [AffiliateController::class, 'index'])->name('affiliates.index');
Route::patch('/afiliasi/{affiliate}/tangguhkan', [AffiliateController::class, 'suspend'])->name('affiliates.suspend');
Route::patch('/afiliasi/{affiliate}/aktifkan', [AffiliateController::class, 'activate'])->name('affiliates.activate');
    });