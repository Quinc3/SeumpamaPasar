<?php

use App\Enums\SellerStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Buyer\DashboardController as BuyerDashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->role === UserRole::Admin) {
        return redirect()->route('admin.dashboard');
    }
    if ($user->sellerProfile?->status === SellerStatus::Active) {
        return redirect()->route('seller.dashboard');
    }
    return redirect()->route('buyer.dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('/akun', [BuyerDashboardController::class, 'index'])
    ->middleware(['auth'])->name('buyer.dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/seller.php';