<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request, \App\Services\PriceResolver $priceResolver): View
    {
        $user = $request->user();
        $isVerified = $priceResolver->isVerifiedBusiness($user);

        $query = Product::query()
            ->with(['sellerProfile', 'images', 'category'])
            ->where('status', \App\Enums\ProductStatus::Active)
            ->whereHas('sellerProfile', fn ($q) => $q->where('status', \App\Enums\SellerStatus::Active));

        if ($isVerified) {
            $query->whereIn('sales_channel', ['b2c', 'b2b', 'both']);
        } else {
            $query->whereIn('sales_channel', ['b2c', 'both']);
        }

        if ($search = trim((string) $request->query('q'))) {
            $query->where('name', 'like', "%{$search}%");
        }
        if ($catSlug = $request->query('kategori')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $catSlug));
        }
        $sort = $request->query('sort', 'terbaru');
        match ($sort) {
            'termurah' => $query->orderBy('price'),
            'termahal' => $query->orderByDesc('price'),
            default    => $query->orderByDesc('created_at'),
        };

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::query()->orderBy('name')->get();

        return view('catalog.index', compact('products', 'categories', 'search', 'catSlug', 'sort', 'isVerified'));
    }

    public function show(Request $request, Product $product, \App\Services\PriceResolver $priceResolver): View
    {
        abort_unless(
            $product->status === \App\Enums\ProductStatus::Active
            && $product->sellerProfile->status === \App\Enums\SellerStatus::Active
            && $priceResolver->canView($request->user(), $product),
            404
        );

        $product->load(['sellerProfile', 'images', 'category', 'wholesalePriceTiers']);

        $resolved = $priceResolver->resolve($request->user(), $product, max(1, (int) $product->min_purchase_qty));
        $isVerified = $priceResolver->isVerifiedBusiness($request->user());
        $isMember = $request->user()?->isMember() ?? false;

        // Terapkan diskon member ke harga yang ditampilkan
        $baseUnitPrice = $resolved['unit_price'];
        $displayUnitPrice = $isMember ? round($baseUnitPrice * 0.95, 2) : $baseUnitPrice;
        $memberDiscount = $isMember ? round($baseUnitPrice * 0.05, 2) : 0;

        $related = Product::query()
            ->where('status', \App\Enums\ProductStatus::Active)
            ->whereIn('sales_channel', $isVerified ? ['b2c', 'b2b', 'both'] : ['b2c', 'both'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        return view('catalog.show', compact(
            'product', 'related', 'resolved', 'isVerified',
            'isMember', 'displayUnitPrice', 'memberDiscount'
        ));
    }
}