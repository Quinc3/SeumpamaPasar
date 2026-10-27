<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderCancellationService
{
    /**
     * @return string one of: 'cancelled' | 'already_cancelled' | 'invalid_status' | 'invalid_integrity' | 'already_refunded' | 'already_restored'
     */
    public function cancel(Order $order): string
    {
        return DB::transaction(function () use ($order) {
            $productIds = $order->items()
                ->whereNotNull('product_id')
                ->orderBy('product_id')
                ->pluck('product_id')
                ->unique()
                ->values();

            Product::query()->whereIn('id', $productIds)->orderBy('id')->lockForUpdate()->get();

            $locked = Order::query()->where('id', $order->id)->lockForUpdate()->firstOrFail();

            if ($locked->status === OrderStatus::Cancelled) {
                return 'already_cancelled';
            }
            if (! in_array($locked->status, [OrderStatus::PendingProcessing, OrderStatus::Processing], true)) {
                return 'invalid_status';
            }
            if ($locked->stock_deducted_at === null) {
                return 'invalid_integrity';
            }
            if ($locked->refunded_at !== null) {
                return 'already_refunded';
            }
            if ($locked->stock_restored_at !== null) {
                return 'already_restored';
            }

            foreach ($locked->items as $item) {
                if ($item->product_id !== null) {
                    Product::query()->where('id', $item->product_id)->increment('stock', $item->quantity);
                }
            }

            $locked->forceFill([
                'status' => OrderStatus::Cancelled,
                'stock_restored_at' => now(),
                'refunded_amount' => $locked->subtotal,
                'refunded_at' => now(),
            ])->save();

            return 'cancelled';
        });
    }
}