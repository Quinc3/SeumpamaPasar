<?php

namespace App\Http\Controllers\Buyer;

use App\Enums\AccountType;
use App\Http\Controllers\Controller;
use App\Models\BusinessProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BusinessProfileController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();
        $profile = $user->businessProfile;

        return view('buyer.business', compact('user', 'profile'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:150'],
            'tax_id' => ['nullable', 'string', 'max:50'],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:500'],
        ]);

        $user = $request->user();

        $profile = $user->businessProfile;
        if ($profile && $profile->verified_at !== null) {
            return back()->withErrors(['company_name' => 'Profil bisnis sudah terverifikasi dan tidak dapat diubah dari halaman ini.']);
        }

        BusinessProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'company_name' => $data['company_name'],
                'tax_id' => $data['tax_id'] ?? null,
                'phone' => $data['phone'],
                'address' => $data['address'],
                'verified_at' => null,
                'verified_by' => null,
            ]
        );

        if ($user->account_type !== AccountType::Business) {
            $user->forceFill(['account_type' => AccountType::Business])->save();
        }

        return redirect()->route('buyer.business.show')->with('status', 'business-submitted');
    }
}