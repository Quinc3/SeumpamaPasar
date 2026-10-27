<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $affiliate = $request->user()->affiliateProfile;
        $affiliate->load(['referrals', 'commissions.order']);

        $approvedSum = $affiliate->commissions
            ->where('status', \App\Enums\CommissionStatus::Approved)
            ->sum('amount');

        $pendingSum = $affiliate->commissions
            ->where('status', \App\Enums\CommissionStatus::Pending)
            ->sum('amount');

        return view('affiliate.dashboard', compact('affiliate', 'approvedSum', 'pendingSum'));
    }
}