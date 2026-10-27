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
        $selectedIds = $request->query('cart_item_ids', []);

        $itemsQuery = $user->cartItems()->with(['product.sellerProfile']);
        if (! empty($selectedIds)) {
            $itemsQuery->whereIn('id', $selectedIds);
        }
        $items = $itemsQuery->get();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->withErrors(['cart' => 'Pilih minimal 1 produk untuk checkout.']);
        }

        $addresses = $user->addresses()->orderByDesc('is_default')->orderByDesc('id')->get();
        $defaultAddress = $addresses->firstWhere('is_default', true) ?? $addresses->first();

        $isMember = $user->isMember();

        $lineItems = $items->map(function ($item) use ($user, $priceResolver, $isMember) {
            $resolved = $priceResolver->resolve($user, $item->product, $item->quantity);
            $unitPrice = $resolved['unit_price'];
            if ($isMember && $item->product->sales_channel === \App\Enums\SalesChannel::B2C) {
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

        $baseSubtotal = $lineItems->sum(fn ($li) => $li['line_total']);

        $methods = [
            'Virtual Account' => [
                'va_bca' => 'BCA',
                'va_bni' => 'BNI',
                'va_bri' => 'BRI',
                'va_mandiri' => 'Mandiri',
                'va_bsi' => 'BSI',
            ],
            'E-Wallet' => [
                'ewallet_seumpamapay' => 'SeumpamaPay',
                'ewallet_ovo' => 'OVO',
                'ewallet_gopay' => 'GoPay',
                'ewallet_dana' => 'DANA',
            ],
            'Gerai Retail' => [
                'retail_indomaret' => 'Indomaret',
                'retail_alfamart' => 'Alfamart',
            ],
        ];

        return view('checkout.index', [
            'items' => $items,
            'lineItems' => $lineItems,
            'addresses' => $addresses,
            'defaultAddress' => $defaultAddress,
            'baseSubtotal' => $baseSubtotal,
            'isMember' => $isMember,
            'discount' => 0,
            'total' => $baseSubtotal,
            'methods' => $methods,
            'selectedIds' => $items->pluck('id')->all(),
            'walletBalance' => (float) ($user->wallet?->balance ?? 0),
        ]);
    }

    public function store(Request $request, CheckoutService $service, PaymentService $paymentService, \App\Services\WalletService $walletService): RedirectResponse
    {
        $data = $request->validate([
            'address_id' => ['required', 'integer'],
            'method' => ['required', 'string', 'in:va_bca,va_bni,va_bri,va_mandiri,va_bsi,retail_indomaret,retail_alfamart,ewallet_seumpamapay,ewallet_ovo,ewallet_gopay,ewallet_dana'],
            'cart_item_ids' => ['required', 'array', 'min:1'],
            'cart_item_ids.*' => ['integer'],
        ], [
            'cart_item_ids.required' => 'Pilih minimal 1 produk untuk checkout.',
            'cart_item_ids.min' => 'Pilih minimal 1 produk untuk checkout.',
        ]);

        $address = Address::query()
            ->where('id', $data['address_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        try {
            $checkout = $service->createFromCart($request->user(), $address, $data['cart_item_ids']);
            $checkout->payment->update(['method' => $data['method']]);

            // SeumpamaPay: cek saldo → debit → settle
            if ($data['method'] === 'ewallet_seumpamapay') {
                $balance = $walletService->balance($request->user());
                $total = (float) $checkout->total_amount;

                if ($balance < $total) {
                    return redirect()->route('wallet.topup', ['return_to' => $checkout->reference])
                        ->with('error', 'Saldo SeumpamaPay kurang Rp ' . number_format($total - $balance, 0, ',', '.') . '. Top up dulu.');
                }

                try {
                    $walletService->debit($request->user(), $total, 'payment', 'Checkout ' . $checkout->reference, $checkout);
                } catch (\Throwable $e) {
                    return back()->withErrors(['checkout' => 'Gagal debit saldo: ' . $e->getMessage()]);
                }

                $result = $paymentService->settle($checkout, true);

                if ($result !== 'success') {
                    // Credit back kalau settle gagal
                    $walletService->credit($request->user(), $total, 'refund', 'Refund otomatis: ' . $checkout->reference, $checkout);
                }

                return redirect()->route('checkout.result', $checkout->reference);
            }

            return redirect()->route('checkout.pay', $checkout->reference);
        } catch (RuntimeException $e) {
            return back()->withErrors(['checkout' => $e->getMessage()])->withInput();
        }
    }

    public function showPay(Request $request, Checkout $checkout, PaymentService $paymentService, \App\Services\WalletService $walletService): View|RedirectResponse
    {
        abort_unless($checkout->user_id === $request->user()->id, 403);
        $checkout->load(['payment', 'orders.items']);

        if ($checkout->status !== \App\Enums\CheckoutStatus::PendingPayment) {
            return redirect()->route('checkout.result', $checkout->reference);
        }

        if ($checkout->payment->status === \App\Enums\PaymentStatus::Pending && $checkout->payment->isExpired()) {
            $paymentService->recordFailure($checkout->id, 'expired');
            return redirect()->route('checkout.result', $checkout->reference);
        }

        // SeumpamaPay: auto-settle (misal baru balik dari topup)
        if ($checkout->payment->method === 'ewallet_seumpamapay' && $checkout->payment->status === \App\Enums\PaymentStatus::Pending) {
            $balance = $walletService->balance($request->user());
            $total = (float) $checkout->total_amount;

            if ($balance < $total) {
                return redirect()->route('wallet.topup', ['return_to' => $checkout->reference])
                    ->with('error', 'Saldo masih kurang Rp ' . number_format($total - $balance, 0, ',', '.') . '.');
            }

            try {
                $walletService->debit($request->user(), $total, 'payment', 'Checkout ' . $checkout->reference, $checkout);
            } catch (\Throwable $e) {
                return redirect()->route('checkout.result', $checkout->reference);
            }

            $result = $paymentService->settle($checkout, true);
            if ($result !== 'success') {
                $walletService->credit($request->user(), $total, 'refund', 'Refund otomatis: ' . $checkout->reference, $checkout);
            }

            return redirect()->route('checkout.result', $checkout->reference);
        }

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