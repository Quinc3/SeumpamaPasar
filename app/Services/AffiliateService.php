<?php

namespace App\Services;

use App\Models\AffiliateProfile;
use App\Models\Checkout;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Support\Str;

class AffiliateService
{
    public function activate(User $user): AffiliateProfile
    {
        $existing = $user->affiliateProfile;
        if ($existing) {
            return $existing;
        }

        return AffiliateProfile::create([
            'user_id' => $user->id,
            'referral_code' => $this->generateCode(),
        ]);
    }

    public function resolveFromSession(): ?AffiliateProfile
    {
        $code = session('affiliate_ref_code');
        if (! $code) {
            return null;
        }
        return AffiliateProfile::query()
            ->where('referral_code', $code)
            ->where('status', 'active')
            ->first();
    }

    public function recordReferral(Checkout $checkout, ?User $buyer): void
    {
        if (! $buyer) {
            return;
        }

        $affiliate = $this->resolveFromSession();
        if (! $affiliate) {
            return;
        }

        if ($affiliate->user_id === $buyer->id) {
            return; // self-referral
        }

        if ($affiliate->user->email === $buyer->email) {
            return; // email sama
        }

        $alreadyAttributed = Referral::query()
            ->where('checkout_id', $checkout->id)
            ->exists();
        if ($alreadyAttributed) {
            return;
        }

        Referral::create([
            'affiliate_id' => $affiliate->id,
            'referred_user_id' => $buyer->id,
            'referral_code' => $affiliate->referral_code,
            'session_id' => session()->getId(),
            'landed_at' => now(),
            'checkout_id' => $checkout->id,
        ]);
    }

    private function generateCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (AffiliateProfile::where('referral_code', $code)->exists());

        return $code;
    }
}