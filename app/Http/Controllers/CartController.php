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

    public function index(Request $request, \App\Services\PriceResolver $priceResolver): View
    {
        $user = $request->user();
        $items = $user->cartItems()
            ->with(['product.sellerProfile', 'product.images'])
            ->get();

        $isMember = $user->isMember();

        $lineItems = $items->map(function ($item) use ($user, $priceResolver, $isMember) {
            $resolved = $priceResolver->resolve($user, $item->product, $item->quantity);
            $unitPrice = $resolved['unit_price'];
            if ($isMember) {
                $unitPrice = round($unitPrice * 0.95, 2);
            }
            return [
                'cart_item' => $item,
                'unit_price' => $unitPrice,
                'line_total' => round($unitPrice * $item->quantity, 2),
                'applies_tier' => $resolved['applies_tier'],
                'min_qty' => $resolved['min_qty'],
            ];
        });

        $subtotal = $lineItems->sum(fn ($li) => $li['line_total']);

        return view('cart.index', compact('items', 'lineItems', 'subtotal', 'isMember'));
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