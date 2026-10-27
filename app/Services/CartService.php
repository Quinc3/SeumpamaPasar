<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class CartService
{
    public function add(User $user, Product $product, int $qty): CartItem
    {
        if ($product->status !== 'active' || $product->sellerProfile->status !== 'active') {
            throw new InvalidArgumentException('Produk tidak tersedia.');
        }

        $qty = max(1, $qty);

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