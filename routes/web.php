<?php

use App\Enums\SellerStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Buyer\AddressController;
use App\Http\Controllers\Buyer\DashboardController as BuyerDashboardController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/katalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/produk/{product:slug}', [CatalogController::class, 'show'])->name('catalog.show');

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
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Member
    Route::post('/akun/member/activate', [MemberController::class, 'activate'])->name('member.activate');

    // Cart
    Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
    Route::post('/keranjang/tambah/{product:slug}', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/keranjang/{cartItem}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/keranjang/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');

    // Addresses
    Route::get('/akun/alamat', [AddressController::class, 'index'])->name('addresses.index');
    Route::post('/akun/alamat', [AddressController::class, 'store'])->name('addresses.store');
    Route::patch('/akun/alamat/{address}/default', [AddressController::class, 'setDefault'])->name('addresses.default');
    Route::delete('/akun/alamat/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');

    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/{checkout:reference}/bayar', [CheckoutController::class, 'showPay'])->name('checkout.pay');
    Route::post('/checkout/{checkout:reference}/bayar', [CheckoutController::class, 'pay'])->name('checkout.pay.submit');
    Route::get('/checkout/{checkout:reference}/hasil', [CheckoutController::class, 'result'])->name('checkout.result');

        // Buyer Orders
        Route::get('/akun/pesanan', [\App\Http\Controllers\Buyer\OrderController::class, 'index'])->name('buyer.orders.index');
        Route::get('/akun/pesanan/{order}', [\App\Http\Controllers\Buyer\OrderController::class, 'show'])->name('buyer.orders.show');
        Route::post('/akun/pesanan/{order}/selesai', [\App\Http\Controllers\Buyer\OrderController::class, 'complete'])->name('buyer.orders.complete');

            // Business profile
    Route::get('/akun/bisnis', [\App\Http\Controllers\Buyer\BusinessProfileController::class, 'show'])->name('buyer.business.show');
    Route::post('/akun/bisnis', [\App\Http\Controllers\Buyer\BusinessProfileController::class, 'store'])->name('buyer.business.store');

    Route::get('/akun/afiliasi', [\App\Http\Controllers\Buyer\AffiliateController::class, 'show'])->name('buyer.affiliate.show');
    Route::post('/akun/afiliasi', [\App\Http\Controllers\Buyer\AffiliateController::class, 'activate'])->name('buyer.affiliate.activate');
});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/seller.php';
require __DIR__.'/affiliate.php';