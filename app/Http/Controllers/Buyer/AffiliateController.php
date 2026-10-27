<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Services\AffiliateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AffiliateController extends Controller
{
    public function show(Request $request): View
    {
        return view('buyer.affiliate', ['profile' => $request->user()->affiliateProfile]);
    }

    public function activate(Request $request, AffiliateService $service): RedirectResponse
    {
        $service->activate($request->user());
        return redirect()->route('buyer.affiliate.show')->with('status', 'affiliate-activated');
    }
}