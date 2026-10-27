<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class CartController extends Controller
{
    public function __construct(private CartService $cart) {}

    public function index(Request $request): View
    {
        $items = $request->user()
            ->cartItems()
            ->with(['product.sellerProfile', 'product.images'])
            ->get();

        $subtotal = $items->sum(fn ($i) => (float) $i->product->price * $i->quantity);

        return view('cart.index', compact('items', 'subtotal'));
    }

    public function add(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate(['quantity' => 'required|integer|min:1|max:999']);

        try {
            $this->cart->add($request->user(), $product, (int) $data['quantity']);
            return back()->with('status', 'cart-added');
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['quantity' => $e->getMessage()]);
        }
    }

    public function update(Request $request, int $cartItem): RedirectResponse
    {
        $data = $request->validate(['quantity' => 'required|integer|min:0|max:999']);

        try {
            $this->cart->update($request->user(), $cartItem, (int) $data['quantity']);
            return back()->with('status', 'cart-updated');
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['quantity' => $e->getMessage()]);
        }
    }

    public function remove(Request $request, int $cartItem): RedirectResponse
    {
        $this->cart->remove($request->user(), $cartItem);
        return back()->with('status', 'cart-removed');
    }
}