<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BusinessController extends Controller
{
    public function index(): View
    {
        $profiles = BusinessProfile::query()
            ->with('user')
            ->orderByRaw('verified_at IS NULL DESC')
            ->orderByDesc('id')
            ->paginate(15);

        return view('admin.business.index', compact('profiles'));
    }

    public function verify(Request $request, BusinessProfile $profile): RedirectResponse
    {
        $profile->forceFill([
            'verified_at' => now(),
            'verified_by' => $request->user()->id,
        ])->save();

        return back()->with('status', 'verified');
    }

    public function unverify(BusinessProfile $profile): RedirectResponse
    {
        $profile->forceFill([
            'verified_at' => null,
            'verified_by' => null,
        ])->save();

        return back()->with('status', 'unverified');
    }
}