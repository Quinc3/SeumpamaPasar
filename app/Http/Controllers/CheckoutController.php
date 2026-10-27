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
    public function index(Request $request, \App\Services\PriceResolver $priceResolver): View|RedirectResponse
    {
        $user = $request->user();
        $items = $user->cartItems()->with(['product.sellerProfile'])->get();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->withErrors(['cart' => 'Keranjang kosong.']);
        }

        $addresses = $user->addresses()->orderByDesc('is_default')->orderByDesc('id')->get();
        $defaultAddress = $addresses->firstWhere('is_default', true) ?? $addresses->first();

        $isMember = $user->isMember();

        // Hitung per item dengan resolver
        $lineItems = $items->map(function ($item) use ($user, $priceResolver, $isMember) {
            $resolved = $priceResolver->resolve($user, $item->product, $item->quantity);
            $unitPrice = $resolved['unit_price'];
            if ($isMember) {
                $unitPrice = round($unitPrice * 0.95, 2);
            }
            $lineTotal = round($unitPrice * $item->quantity, 2);

            return [
                'cart_item' => $item,
                'unit_price' => $unitPrice,
                'line_total' => $lineTotal,
                'min_qty' => $resolved['min_qty'],
                'applies_tier' => $resolved['applies_tier'],
            ];
        });

        $baseSubtotal = $lineItems->sum(fn ($li) => $li['line_total']);

        $methods = [
            'demo_transfer' => 'Transfer Bank (Demo)',
            'demo_ewallet' => 'E-Wallet (Demo)',
            'demo_va' => 'Virtual Account (Demo)',
        ];

        return view('checkout.index', [
            'items' => $items,
            'lineItems' => $lineItems,
            'addresses' => $addresses,
            'defaultAddress' => $defaultAddress,
            'baseSubtotal' => $baseSubtotal,
            'isMember' => $isMember,
            'discount' => 0, // diskon sudah masuk per unit_price di lineItems
            'total' => $baseSubtotal,
            'methods' => $methods,
        ]);
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