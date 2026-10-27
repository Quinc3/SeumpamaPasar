<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderStatusService
{
    private const TRANSITIONS = [
        'processing' => [OrderStatus::PendingProcessing],
        'shipped'    => [OrderStatus::Processing],
        'completed'  => [OrderStatus::Shipped],
    ];

    /**
     * @return bool true jika berhasil, false jika transisi tidak valid
     */
    public function transition(Order $order, string $to): bool
    {
        $target = OrderStatus::tryFrom($to);
        if ($target === null) {
            return false;
        }

        $allowedFrom = self::TRANSITIONS[$to] ?? [];
        if (! in_array($order->status, $allowedFrom, true)) {
            return false;
        }

        return DB::transaction(function () use ($order, $target) {
            $locked = Order::query()->where('id', $order->id)->lockForUpdate()->firstOrFail();
            if (! in_array($locked->status, self::TRANSITIONS[$target->value] ?? [], true)) {
                return false;
            }
            $locked->forceFill(['status' => $target])->save();
            return true;
        });
    }
}