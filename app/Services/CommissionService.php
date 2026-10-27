<?php

namespace App\Services;

use App\Enums\CommissionStatus;
use App\Enums\OrderStatus;
use App\Models\Commission;
use App\Models\Order;
use App\Models\Referral;
use Illuminate\Support\Facades\DB;

class CommissionService
{
    public const RATE = 0.05;

    public function awardForOrder(Order $order): bool
    {
        return DB::transaction(function () use ($order) {
            $locked = Order::query()->where('id', $order->id)->lockForUpdate()->firstOrFail();

            if ($locked->status !== OrderStatus::Completed) {
                return false;
            }

            $referral = Referral::query()
                ->where('checkout_id', $locked->checkout_id)
                ->first();

            if (! $referral) {
                return false;
            }

            $affiliate = $referral->affiliate;

            if (! $affiliate || $affiliate->status !== 'active') {
                return false;
            }

            if ($affiliate->user_id === $locked->buyer_id) {
                return false;
            }

            if ($affiliate->user->email === $locked->buyer->email) {
                return false;
            }

            $exists = Commission::query()->where('order_id', $locked->id)->exists();
            if ($exists) {
                return false;
            }

            $amount = round((float) $locked->subtotal * self::RATE, 2);

            $commission = Commission::create([
                'affiliate_id' => $affiliate->id,
                'order_id' => $locked->id,
                'amount' => $amount,
            ]);

            $commission->forceFill([
                'status' => CommissionStatus::Approved,
                'approved_at' => now(),
            ])->save();

            return true;
        });
    }

    public function cancelForOrder(Order $order, string $reason): void
    {
        DB::transaction(function () use ($order, $reason) {
            Commission::query()
                ->where('order_id', $order->id)
                ->whereIn('status', [CommissionStatus::Pending->value, CommissionStatus::Approved->value])
                ->update([
                    'status' => CommissionStatus::Cancelled->value,
                    'cancelled_at' => now(),
                    'cancel_reason' => $reason,
                ]);
        });
    }
}