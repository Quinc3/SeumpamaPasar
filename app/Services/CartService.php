<?php

namespace App\Services;

use App\Enums\ProductStatus;
use App\Enums\SellerStatus;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class CartService
{
    public function __construct(private PriceResolver $priceResolver) {}

    public function add(User $user, Product $product, int $qty): CartItem
    {
        if ($product->status !== ProductStatus::Active
            || $product->sellerProfile->status !== SellerStatus::Active) {
            throw new InvalidArgumentException('Produk tidak tersedia.');
        }

        if (! $this->priceResolver->canBuy($user, $product)) {
            throw new InvalidArgumentException('Produk ini hanya untuk pembeli bisnis terverifikasi.');
        }

        $qty = max(1, $qty);
        $resolved = $this->priceResolver->resolve($user, $product, $qty);

        if ($qty < $resolved['min_qty']) {
            throw new InvalidArgumentException("Jumlah minimum pembelian untuk produk ini adalah {$resolved['min_qty']}.");
        }

        return DB::transaction(function () use ($user, $product, $qty) {
            $item = CartItem::query()
                ->where('user_id', $user->id)
                ->where('product_id', $product->id)
                ->lockForUpdate()
                ->first();

            $newQty = ($item?->quantity ?? 0) + $qty;

            if ($newQty > $product->stock) {
                throw new InvalidArgumentException("Stok tidak mencukupi. Tersedia: {$product->stock}.");
            }

            if ($item) {
                $item->update(['quantity' => $newQty]);
                return $item;
            }

            return CartItem::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'quantity' => $newQty,
            ]);
        });
    }

    public function update(User $user, int $cartItemId, int $qty): void
    {
        $item = CartItem::query()
            ->where('user_id', $user->id)
            ->where('id', $cartItemId)
            ->firstOrFail();

        if ($qty <= 0) {
            $item->delete();
            return;
        }

        if ($qty > $item->product->stock) {
            throw new InvalidArgumentException("Stok tidak mencukupi. Tersedia: {$item->product->stock}.");
        }

        $resolved = $this->priceResolver->resolve($user, $item->product, $qty);
        if ($qty < $resolved['min_qty']) {
            throw new InvalidArgumentException("Jumlah minimum pembelian adalah {$resolved['min_qty']}.");
        }

        $item->update(['quantity' => $qty]);
    }

    public function remove(User $user, int $cartItemId): void
    {
        CartItem::query()
            ->where('user_id', $user->id)
            ->where('id', $cartItemId)
            ->delete();
    }
}