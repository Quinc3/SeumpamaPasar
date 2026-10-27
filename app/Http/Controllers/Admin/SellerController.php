<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SellerStatus;
use App\Http\Controllers\Controller;
use App\Models\SellerProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SellerController extends Controller
{
    public function index(): View
    {
        $sellers = SellerProfile::query()
            ->with(['user', 'products'])
            ->orderByRaw("FIELD(status, 'pending', 'active', 'suspended')")
            ->orderByDesc('id')
            ->paginate(15);

        return view('admin.sellers.index', compact('sellers'));
    }

    public function approve(SellerProfile $seller): RedirectResponse
    {
        $seller->forceFill(['status' => SellerStatus::Active->value])->save();
        return back()->with('status', 'approved');
    }

    public function suspend(SellerProfile $seller): RedirectResponse
    {
        $seller->forceFill(['status' => SellerStatus::Suspended->value])->save();
        return back()->with('status', 'suspended');
    }

    public function activate(SellerProfile $seller): RedirectResponse
    {
        $seller->forceFill(['status' => SellerStatus::Active->value])->save();
        return back()->with('status', 'activated');
    }
}