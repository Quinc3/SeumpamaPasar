<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $seller = $request->user()->sellerProfile;

        return view('seller.dashboard', [
            'seller' => $seller,
            'productCount' => $seller->products()->count(),
            'activeProductCount' => $seller->products()->where('status', 'active')->count(),
            'pendingReviewCount' => $seller->products()->where('status', 'pending_review')->count(),
            'orderCount' => $seller->orders()->count(),
            'pendingProcessingCount' => $seller->orders()->where('status', 'pending_processing')->count(),
        ]);
    }
}