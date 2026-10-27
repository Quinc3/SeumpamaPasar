<?php

namespace App\Http\Controllers;

use App\Enums\ProductStatus;
use App\Enums\SellerStatus;
use App\Models\Category;
use App\Models\Product;
use App\Services\PriceResolver;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request, PriceResolver $priceResolver): View
    {
        $isVerified = $priceResolver->isVerifiedBusiness($request->user());
        $channels = $isVerified ? ['b2c', 'b2b', 'both'] : ['b2c', 'both'];

        $baseQuery = fn () => Product::query()
            ->with(['images', 'sellerProfile'])
            ->where('status', ProductStatus::Active->value)
            ->whereIn('sales_channel', $channels)
            ->whereHas('sellerProfile', fn ($q) => $q->where('status', SellerStatus::Active->value));

        $featured = $baseQuery()
            ->where('is_featured', true)
            ->orderByDesc('sold_count')
            ->limit(4)
            ->get();

        $bestsellers = $baseQuery()
            ->orderByDesc('sold_count')
            ->limit(8)
            ->get();

        $newest = $baseQuery()
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        $categories = Category::query()
            ->withCount(['products' => fn ($q) => $q->where('status', ProductStatus::Active->value)])
            ->orderBy('name')
            ->get();

        return view('home', compact('featured', 'bestsellers', 'newest', 'categories', 'isVerified'));
    }
}