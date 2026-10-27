<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWholesaleTierRequest;
use App\Models\Product;
use App\Models\WholesalePriceTier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WholesaleTierController extends Controller
{
    public function store(StoreWholesaleTierRequest $request, Product $product): RedirectResponse
    {
        abort_unless($product->seller_profile_id === $request->user()->sellerProfile->id, 403);

        if ($product->sales_channel->value === 'b2c') {
            return back()->withErrors(['min_quantity' => 'Tier harga hanya untuk produk B2B/Both.']);
        }

        $data = $request->validated();

        if ((float) $data['price'] >= (float) $product->price) {
            return back()->withErrors(['price' => 'Harga tier harus lebih rendah dari harga dasar.']);
        }

        $exists = WholesalePriceTier::where('product_id', $product->id)
            ->where('min_quantity', $data['min_quantity'])
            ->exists();
        if ($exists) {
            return back()->withErrors(['min_quantity' => 'Tier dengan minimum qty ini sudah ada.']);
        }

        $product->wholesalePriceTiers()->create([
            'min_quantity' => $data['min_quantity'],
            'price' => $data['price'],
        ]);

        return back()->with('status', 'tier-added');
    }

    public function destroy(Request $request, Product $product, WholesalePriceTier $tier): RedirectResponse
    {
        abort_unless($product->seller_profile_id === $request->user()->sellerProfile->id, 403);
        abort_unless($tier->product_id === $product->id, 404);

        $tier->delete();

        return back()->with('status', 'tier-deleted');
    }
}