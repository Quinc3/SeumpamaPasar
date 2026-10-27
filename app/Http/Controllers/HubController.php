<?php

namespace App\Http\Controllers;

use App\Enums\CommissionStatus;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class HubController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('hub', [
            'user' => $user,
            'orderCount' => $user->orders()->count(),
            'addressCount' => $user->addresses()->count(),
            'cartCount' => $user->cartItems()->count(),
            'sellerProductCount' => $user->sellerProfile?->products()->count() ?? 0,
            'sellerOrderCount' => $user->sellerProfile?->orders()->count() ?? 0,
            'affiliateCommissionTotal' => $user->affiliateProfile
                ? $user->affiliateProfile->commissions()->where('status', CommissionStatus::Approved->value)->sum('amount')
                : 0,
        ]);
    }
}