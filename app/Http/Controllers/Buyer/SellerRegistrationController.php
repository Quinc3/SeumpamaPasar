<?php

namespace App\Http\Controllers\Buyer;

use App\Enums\AccountType;
use App\Enums\SellerStatus;
use App\Enums\SellerType;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSellerProfileRequest;
use App\Models\SellerProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SellerRegistrationController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();
        $seller = $user->sellerProfile;

        return view('buyer.seller-registration', [
            'user' => $user,
            'seller' => $seller,
            'businessVerified' => $user->isVerifiedBusiness(),
        ]);
    }

    public function store(StoreSellerProfileRequest $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->sellerProfile) {
            return back()->withErrors(['store_name' => 'Anda sudah memiliki profil toko.']);
        }

        $data = $request->validated();
        $type = SellerType::from($data['seller_type']);

        if ($type === SellerType::Business) {
            if (! $user->isVerifiedBusiness()) {
                return back()
                    ->withInput()
                    ->withErrors(['seller_type' => 'Untuk membuka toko bisnis, lengkapi dan verifikasi Profil Bisnis terlebih dahulu.']);
            }

            if ($user->account_type !== AccountType::Business) {
                $user->forceFill(['account_type' => AccountType::Business])->save();
            }
        }

        SellerProfile::create([
            'user_id' => $user->id,
            'seller_type' => $type->value,
            'store_name' => $data['store_name'],
            'description' => $data['description'],
            'status' => SellerStatus::Pending->value,
        ]);

        return redirect()
            ->route('profile.show')
            ->with('status', 'seller-submitted');
    }
}