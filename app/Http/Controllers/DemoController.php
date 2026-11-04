<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DemoController extends Controller
{
    public function index(): View
    {
        $accounts = [
            [
                'role' => 'Admin',
                'email' => 'admin@seumpama.com',
                'desc' => 'Kelola seluruh platform — moderasi produk, verifikasi bisnis, kelola penjual & afiliasi, lihat laporan.',
                'icon' => 'check-circle',
                'color' => 'amber',
            ],
            [
                'role' => 'Seller Individu',
                'email' => 'seller@seumpama.com',
                'desc' => 'Toko Demo — penjual individu. Kelola produk, proses pesanan, lihat pendapatan toko.',
                'icon' => 'home',
                'color' => 'primary',
            ],
            [
                'role' => 'Seller Bisnis (B2B)',
                'email' => 'business@seumpama.com',
                'desc' => 'Grosir Demo — penjual bisnis terverifikasi. Akses harga grosir, tier B2B, minimum order quantity.',
                'icon' => 'credit-card',
                'color' => 'green',
            ],
            [
                'role' => 'Buyer (Member + Affiliate)',
                'email' => 'buyer@seumpama.com',
                'desc' => 'Pembeli personal dengan diskon member 5% dan bisa promosikan produk lewat link afiliasi.',
                'icon' => 'user',
                'color' => 'blue',
            ],
        ];

        return view('demo', compact('accounts'));
    }

    public function loginAs(Request $request, string $email)
    {
        $user = User::where('email', $email)->first();

        if (! $user) {
            return back()->with('error', 'Akun demo tidak ditemukan. Pastikan seeder sudah dijalankan.');
        }

        Auth::login($user, false);
        $request->session()->regenerate();

        return match ($user->role) {
            UserRole::Admin => redirect()->route('admin.dashboard'),
            default => $user->sellerProfile
                ? redirect()->route('seller.dashboard')
                : redirect()->route('profile.show'),
        };
    }
}