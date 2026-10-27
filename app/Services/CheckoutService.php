<?php

namespace App\Services;

use App\Enums\CheckoutStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Address;
use App\Models\CartItem;
use App\Models\Checkout;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class CheckoutService
{
    public function createFromCart(User $user, Address $address): Checkout
    {
        return DB::transaction(function () use ($user, $address) {
            $items = CartItem::query()
                ->where('user_id', $user->id)
                ->with(['product.sellerProfile'])
                ->lockForUpdate()
                ->get();

            if ($items->isEmpty()) {
                throw new RuntimeException('Keranjang kosong.');
            }

            foreach ($items as $item) {
                $p = $item->product;
                if (! $p
    || $p->status !== \App\Enums\ProductStatus::Active
    || $p->sellerProfile->status !== \App\Enums\SellerStatus::Active) {
                    throw new RuntimeException("Produk '{$item->product->name}' tidak lagi tersedia.");
                }
                if ($p->stock < $item->quantity) {
                    throw new RuntimeException("Stok '{$p->name}' tidak mencukupi.");
                }
            }

            $isMember = $user->isMember();

            $checkout = Checkout::create([
                'user_id' => $user->id,
                'reference' => $this->generateReference(),
                'total_amount' => 0,
                'status' => CheckoutStatus::PendingPayment,
            ]);

            $itemsBySeller = $items->groupBy(fn ($i) => $i->product->seller_profile_id);

            foreach ($itemsBySeller as $sellerId => $sellerItems) {
                $orderSubtotal = 0;
                $order = Order::create([
                    'checkout_id' => $checkout->id,
                    'seller_profile_id' => $sellerId,
                    'buyer_id' => $user->id,
                    'subtotal' => 0,
                    'status' => OrderStatus::PendingPayment,
                    'shipping_recipient_name' => $address->recipient_name,
                    'shipping_phone' => $address->phone,
                    'shipping_line1' => $address->line1,
                    'shipping_city' => $address->city,
                    'shipping_province' => $address->province,
                    'shipping_postal_code' => $address->postal_code,
                ]);

                foreach ($sellerItems as $item) {
                    $basePrice = (float) $item->product->price;
                    $unitPrice = $isMember ? round($basePrice * 0.95, 2) : $basePrice;
                    $lineTotal = round($unitPrice * $item->quantity, 2);

                    $order->items()->create([
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name,
                        'unit_price' => $unitPrice,
                        'quantity' => $item->quantity,
                        'subtotal' => $lineTotal,
                    ]);

                    $orderSubtotal += $lineTotal;
                }

                $order->update(['subtotal' => round($orderSubtotal, 2)]);
            }

            $total = (float) $checkout->orders()->sum('subtotal');
            $checkout->update(['total_amount' => round($total, 2)]);

            Payment::create([
                'checkout_id' => $checkout->id,
                'method' => 'demo_transfer',
                'status' => PaymentStatus::Pending,
                'reference' => 'PAY-'.strtoupper(Str::random(12)),
            ]);

            CartItem::where('user_id', $user->id)->delete();

            return $checkout->fresh(['orders.items', 'payment']);
        });
    }

    private function generateReference(): string
    {
        return 'CHK-'.now()->format('Ymd').'-'.strtoupper(Str::random(6));
    }
}