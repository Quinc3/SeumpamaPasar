<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ProductStatus;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductModerationController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->with(['sellerProfile.user', 'category'])
            ->whereIn('status', [ProductStatus::PendingReview->value, ProductStatus::Rejected->value])
            ->orderByRaw("FIELD(status, 'pending_review', 'rejected')")
            ->orderByDesc('id')
            ->paginate(15);

        return view('admin.moderation.index', compact('products'));
    }

    public function approve(Product $product): RedirectResponse
    {
        $product->forceFill([
            'status' => ProductStatus::Active,
            'rejection_note' => null,
        ])->save();

        return back()->with('status', 'approved');
    }

    public function reject(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate(['rejection_note' => ['required', 'string', 'max:500']]);

        $product->forceFill([
            'status' => ProductStatus::Rejected,
            'rejection_note' => $data['rejection_note'],
        ])->save();

        return back()->with('status', 'rejected');
    }
}