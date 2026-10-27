<?php

namespace App\Services;

use App\Enums\CheckoutStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Checkout;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PaymentService
{
    public function __construct(private PriceResolver $priceResolver) {}

    /**
     * @return string 'success' | 'failed' | 'already_processed'
     */
    public function settle(Checkout $checkout, bool $simulateSuccess): string
    {
        DB::beginTransaction();
        try {
            $payment = Payment::query()->where('checkout_id', $checkout->id)->lockForUpdate()->first();
            if (! $payment || $payment->status !== PaymentStatus::Pending) {
                DB::rollBack();
                return 'already_processed';
            }

            // Cek expiry
            if ($payment->isExpired()) {
                DB::rollBack();
                $this->recordFailure($checkout->id, 'expired');
                return 'failed';
            }

            $items = OrderItem::query()
                ->whereHas('order', fn ($q) => $q->where('checkout_id', $checkout->id))
                ->orderBy('product_id')
                ->get();

            $productIds = $items->pluck('product_id')->unique()->sort()->values();
            $products = Product::query()->whereIn('id', $productIds)->orderBy('id')->lockForUpdate()->get()->keyBy('id');

            $orders = Order::query()->where('checkout_id', $checkout->id)->orderBy('id')->lockForUpdate()->get();

            $buyer = $checkout->user;
            $isMember = $buyer->isMember();

            foreach ($orders as $order) {
                if ($order->status !== OrderStatus::PendingPayment) {
                    throw new RuntimeException('order_status_changed');
                }
            }

            foreach ($items as $item) {
                $product = $products[$item->product_id] ?? null;
                if (! $product) {
                    throw new RuntimeException('product_inactive');
                }
                if ($product->status !== \App\Enums\ProductStatus::Active) {
                    throw new RuntimeException('product_inactive');
                }
                if ($product->sellerProfile->status !== \App\Enums\SellerStatus::Active) {
                    throw new RuntimeException('seller_inactive');
                }
                if (! $this->priceResolver->canBuy($buyer, $product)) {
                    throw new RuntimeException('buyer_not_eligible');
                }
                if ($product->stock < $item->quantity) {
                    throw new RuntimeException('insufficient_stock');
                }

                $resolved = $this->priceResolver->resolve($buyer, $product, $item->quantity);
                $expected = $resolved['unit_price'];
                if ($isMember && $product->sales_channel === \App\Enums\SalesChannel::B2C) {
                    $expected = round($expected * 0.95, 2);
                }
                if (abs($expected - (float) $item->unit_price) > 0.01) {
                    throw new RuntimeException('price_changed');
                }
            }

            foreach ($items as $item) {
                $products[$item->product_id]->decrement('stock', $item->quantity);
            }

            foreach ($orders as $order) {
                $order->forceFill([
                    'status' => OrderStatus::PendingProcessing,
                    'stock_deducted_at' => now(),
                ])->save();
            }

            $payment->forceFill([
                'status' => PaymentStatus::Paid,
                'paid_at' => now(),
            ])->save();

            $checkout->forceFill([
                'status' => CheckoutStatus::Paid,
            ])->save();

            DB::commit();
            return 'success';
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->recordFailure($checkout->id, $this->mapReason($e));
            return 'failed';
        }
    }

    public function recordFailure(int $checkoutId, string $reason): void
    {
        DB::transaction(function () use ($checkoutId, $reason) {
            $payment = Payment::query()->where('checkout_id', $checkoutId)->lockForUpdate()->first();
            if (! $payment || $payment->status !== PaymentStatus::Pending) {
                return;
            }

            $affected = Payment::query()
                ->where('id', $payment->id)
                ->where('status', PaymentStatus::Pending->value)
                ->update([
                    'status' => PaymentStatus::Failed->value,
                    'failure_reason' => $reason,
                    'failed_at' => now(),
                ]);

            if ($affected !== 1) {
                return;
            }

            Order::query()
                ->where('checkout_id', $checkoutId)
                ->where('status', OrderStatus::PendingPayment->value)
                ->update(['status' => OrderStatus::Cancelled->value]);

            Checkout::query()
                ->where('id', $checkoutId)
                ->where('status', CheckoutStatus::PendingPayment->value)
                ->update(['status' => CheckoutStatus::Cancelled->value]);
        });
    }

    private function mapReason(\Throwable $e): string
    {
        $known = [
            'insufficient_stock',
            'price_changed',
            'product_inactive',
            'seller_inactive',
            'buyer_not_eligible',
            'order_status_changed',
            'expired',
        ];
        return in_array($e->getMessage(), $known, true) ? $e->getMessage() : 'checkout_validation_failed';
    }
}