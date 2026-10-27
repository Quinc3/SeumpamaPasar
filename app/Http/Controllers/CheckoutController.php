<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Checkout;
use App\Services\CheckoutService;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class CheckoutController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $items = $user->cartItems()->with(['product.sellerProfile'])->get();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->withErrors(['cart' => 'Keranjang kosong.']);
        }

        $addresses = $user->addresses()->orderByDesc('is_default')->orderByDesc('id')->get();
        $defaultAddress = $addresses->firstWhere('is_default', true) ?? $addresses->first();

        $baseSubtotal = $items->sum(fn ($i) => (float) $i->product->price * $i->quantity);
        $isMember = $user->isMember();
        $discount = $isMember ? round($baseSubtotal * 0.05, 2) : 0;
        $total = round($baseSubtotal - $discount, 2);

        $methods = [
            'demo_transfer' => 'Transfer Bank (Demo)',
            'demo_ewallet' => 'E-Wallet (Demo)',
            'demo_va' => 'Virtual Account (Demo)',
        ];

        return view('checkout.index', compact('items', 'addresses', 'defaultAddress', 'baseSubtotal', 'isMember', 'discount', 'total', 'methods'));
    }

    public function store(Request $request, CheckoutService $service): RedirectResponse
    {
        $data = $request->validate([
            'address_id' => ['required', 'integer'],
            'method' => ['required', 'in:demo_transfer,demo_ewallet,demo_va'],
        ]);

        $address = Address::query()
            ->where('id', $data['address_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        try {
            $checkout = $service->createFromCart($request->user(), $address);
            $checkout->payment->update(['method' => $data['method']]);

            return redirect()->route('checkout.pay', $checkout->reference);
        } catch (RuntimeException $e) {
            return back()->withErrors(['checkout' => $e->getMessage()])->withInput();
        }
    }

    public function showPay(Request $request, Checkout $checkout): View|RedirectResponse
    {
        abort_unless($checkout->user_id === $request->user()->id, 403);

        if ($checkout->status !== \App\Enums\CheckoutStatus::PendingPayment) {
            return redirect()->route('checkout.result', $checkout->reference);
        }

        $checkout->load(['orders.items', 'payment']);
        return view('checkout.pay', compact('checkout'));
    }

    public function pay(Request $request, Checkout $checkout, PaymentService $service): RedirectResponse
    {
        abort_unless($checkout->user_id === $request->user()->id, 403);

        $data = $request->validate(['outcome' => ['required', 'in:success,fail']]);
        $service->settle($checkout, $data['outcome'] === 'success');

        return redirect()->route('checkout.result', $checkout->reference);
    }

    public function result(Request $request, Checkout $checkout): View
    {
        abort_unless($checkout->user_id === $request->user()->id, 403);
        $checkout->load(['orders.items', 'payment']);
        return view('checkout.result', compact('checkout'));
    }
}