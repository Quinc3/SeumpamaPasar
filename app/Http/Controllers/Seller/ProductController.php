<?php

namespace App\Http\Controllers\Seller;

use App\Enums\ProductStatus;
use App\Enums\SellerType;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $seller = $request->user()->sellerProfile;
        $products = $seller->products()
            ->with(['category', 'images'])
            ->orderByDesc('id')
            ->paginate(15);

        return view('seller.products.index', compact('products'));
    }

    public function create(Request $request): View
    {
        $categories = Category::orderBy('name')->get();
        $seller = $request->user()->sellerProfile;

        return view('seller.products.create', compact('categories', 'seller'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $seller = $request->user()->sellerProfile;
        $data = $request->validated();

        if (! $this->channelAllowed($seller->seller_type, $data['sales_channel'])) {
            return back()->withErrors(['sales_channel' => 'Seller individu hanya dapat menjual produk B2C.'])->withInput();
        }

        if ($data['sales_channel'] === 'b2c') {
            $data['min_purchase_qty'] = 1;
        } else {
            $data['min_purchase_qty'] = $data['min_purchase_qty'] ?? 1;
        }

        $product = $seller->products()->create([
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'slug' => $this->generateSlug($data['name']),
            'description' => $data['description'],
            'price' => $data['price'],
            'stock' => $data['stock'],
            'sales_channel' => $data['sales_channel'],
            'min_purchase_qty' => $data['min_purchase_qty'],
        ]);

        $product->forceFill(['status' => ProductStatus::PendingReview])->save();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store("products/{$product->id}", 'public');
            $product->images()->create(['path' => $path, 'sort_order' => 0]);
        }

        return redirect()->route('seller.products.index')->with('status', 'product-created');
    }

    public function edit(Request $request, Product $product): View
    {
        $this->authorizeOwnership($request, $product);
        $categories = Category::orderBy('name')->get();
        $product->load(['images', 'wholesalePriceTiers']);
        $seller = $request->user()->sellerProfile;

        return view('seller.products.edit', compact('product', 'categories', 'seller'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $this->authorizeOwnership($request, $product);
        $seller = $request->user()->sellerProfile;
        $data = $request->validated();

        if ($product->status === ProductStatus::Archived) {
            return back()->withErrors(['name' => 'Produk yang sudah diarsipkan tidak dapat diubah.']);
        }

        if (! $this->channelAllowed($seller->seller_type, $data['sales_channel'])) {
            return back()->withErrors(['sales_channel' => 'Seller individu hanya dapat menjual produk B2C.'])->withInput();
        }

        if ($data['sales_channel'] === 'b2c') {
            $data['min_purchase_qty'] = 1;
        } else {
            $data['min_purchase_qty'] = $data['min_purchase_qty'] ?? 1;
        }

        $product->update([
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'stock' => $data['stock'],
            'sales_channel' => $data['sales_channel'],
            'min_purchase_qty' => $data['min_purchase_qty'],
        ]);

        if ($request->hasFile('image')) {
            foreach ($product->images as $img) {
                Storage::disk('public')->delete($img->path);
                $img->delete();
            }
            $path = $request->file('image')->store("products/{$product->id}", 'public');
            $product->images()->create(['path' => $path, 'sort_order' => 0]);
        }

        return redirect()->route('seller.products.edit', $product->id)->with('status', 'product-updated');
    }

    public function archive(Request $request, Product $product): RedirectResponse
    {
        $this->authorizeOwnership($request, $product);

        if ($product->status === ProductStatus::Archived) {
            return back()->with('status', 'already-archived');
        }

        $product->forceFill(['status' => ProductStatus::Archived])->save();

        return back()->with('status', 'product-archived');
    }

    public function bulkArchive(Request $request): RedirectResponse
    {
        $seller = $request->user()->sellerProfile;

        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ]);

        $products = Product::query()
            ->whereIn('id', $data['ids'])
            ->where('seller_profile_id', $seller->id)
            ->where('status', '!=', ProductStatus::Archived->value)
            ->get();

        $count = 0;
        foreach ($products as $p) {
            $p->forceFill(['status' => ProductStatus::Archived])->save();
            $count++;
        }

        return back()->with('status', "{$count} produk diarsipkan");
    }

    private function authorizeOwnership(Request $request, Product $product): void
    {
        abort_unless($product->seller_profile_id === $request->user()->sellerProfile->id, 403);
    }

    private function channelAllowed(SellerType $sellerType, string $channel): bool
    {
        if ($sellerType === SellerType::Individual) {
            return $channel === 'b2c';
        }
        return true;
    }

    private function generateSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = $base . '-' . (++$i);
        }
        return $slug;
    }
}