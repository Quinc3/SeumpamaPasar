<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        $user = $request->user();

        // Cek sudah pernah review
        if (Review::where('user_id', $user->id)->where('product_id', $product->id)->exists()) {
            return back()->with('error', 'Anda sudah pernah memberi ulasan untuk produk ini.');
        }

        // Cek punya order completed untuk produk ini
        $orderItem = OrderItem::query()
            ->where('product_id', $product->id)
            ->whereHas('order', fn ($q) => $q
                ->where('buyer_id', $user->id)
                ->where('status', \App\Enums\OrderStatus::Completed->value)
            )
            ->first();

        if (! $orderItem) {
            return back()->with('error', 'Anda hanya dapat mengulas produk yang sudah Anda beli dan selesaikan.');
        }

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        Review::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'order_id' => $orderItem->order_id,
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
        ]);

        // Update rating agregat produk
        $avg = Review::where('product_id', $product->id)->avg('rating');
        $product->forceFill(['rating' => round((float) $avg, 2)])->save();

        return back()->with('status', 'review-submitted');
    }
}