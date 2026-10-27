<?php

namespace App\Services;

use App\Enums\AccountType;
use App\Enums\SalesChannel;
use App\Models\Product;
use App\Models\User;

class PriceResolver
{
    public function isVerifiedBusiness(?User $user): bool
    {
        if ($user === null) {
            return false;
        }
        return $user->account_type === AccountType::Business
            && $user->businessProfile !== null
            && $user->businessProfile->verified_at !== null;
    }

    public function canView(?User $user, Product $product): bool
    {
        if ($product->sales_channel === SalesChannel::B2C) {
            return true;
        }
        if ($product->sales_channel === SalesChannel::B2B) {
            return $this->isVerifiedBusiness($user);
        }
        return true; // both
    }

    public function canBuy(?User $user, Product $product): bool
    {
        if ($product->sales_channel === SalesChannel::B2C) {
            return true;
        }
        if ($product->sales_channel === SalesChannel::B2B) {
            return $this->isVerifiedBusiness($user);
        }
        return true; // both
    }

    /**
     * @return array{unit_price: float, min_qty: int, applies_tier: bool}
     */
    public function resolve(?User $buyer, Product $product, int $quantity): array
    {
        $channel = $product->sales_channel;
        $isVerified = $this->isVerifiedBusiness($buyer);

        if ($channel === SalesChannel::B2C || ! $isVerified) {
            return [
                'unit_price' => (float) $product->price,
                'min_qty' => 1,
                'applies_tier' => false,
            ];
        }

        $minQty = max(1, (int) $product->min_purchase_qty);
        $tier = $product->wholesalePriceTiers()
            ->where('min_quantity', '<=', $quantity)
            ->orderByDesc('min_quantity')
            ->first();

        return [
            'unit_price' => $tier ? (float) $tier->price : (float) $product->price,
            'min_qty' => $minQty,
            'applies_tier' => $tier !== null,
        ];
    }
}