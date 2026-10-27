<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AffiliateProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AffiliateController extends Controller
{
    public function index(): View
    {
        $affiliates = AffiliateProfile::query()
            ->with(['user', 'commissions'])
            ->orderByDesc('id')
            ->paginate(15);
        return view('admin.affiliates.index', compact('affiliates'));
    }

    public function suspend(AffiliateProfile $affiliate): RedirectResponse
    {
        $affiliate->forceFill(['status' => 'suspended'])->save();
        return back()->with('status', 'suspended');
    }

    public function activate(AffiliateProfile $affiliate): RedirectResponse
    {
        $affiliate->forceFill(['status' => 'active'])->save();
        return back()->with('status', 'activated');
    }
}