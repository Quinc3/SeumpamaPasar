<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::query()
            ->with(['sellerProfile', 'images', 'category'])
            ->where('status', 'active')
            ->whereHas('sellerProfile', fn ($q) => $q->where('status', 'active'))
            ->whereIn('sales_channel', ['b2c']);

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

        return view('catalog.index', compact('products', 'categories', 'search', 'catSlug', 'sort'));
    }

    public function show(Product $product): View
    {
        abort_unless(
            $product->status === \App\Enums\ProductStatus::Active
            && $product->sellerProfile->status === \App\Enums\SellerStatus::Active
            && $product->sales_channel === \App\Enums\SalesChannel::B2C,
            404
        );

        $product->load(['sellerProfile', 'images', 'category']);

        $related = Product::query()
            ->where('status', \App\Enums\ProductStatus::Active)
            ->where('sales_channel', \App\Enums\SalesChannel::B2C)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        return view('catalog.show', compact('product', 'related'));
    }
}