<?php

namespace App\Http\Controllers\Buyer;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderStatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::query()
            ->where('buyer_id', $request->user()->id)
            ->with(['sellerProfile', 'items', 'checkout'])
            ->orderByDesc('id')
            ->paginate(10);

        return view('buyer.orders.index', compact('orders'));
    }

    public function show(Request $request, Order $order): View
    {
        abort_unless($order->buyer_id === $request->user()->id, 403);
        $order->load(['sellerProfile', 'items', 'checkout.payment']);

        return view('buyer.orders.show', compact('order'));
    }

    public function complete(Request $request, Order $order, OrderStatusService $service, \App\Services\CommissionService $commissionService, \App\Services\WalletService $walletService): RedirectResponse
    {
        abort_unless($order->buyer_id === $request->user()->id, 403);

        $ok = $service->transition($order, \App\Enums\OrderStatus::Completed->value);

        if ($ok) {
            $order->refresh();

            // Release escrow ke wallet seller
            if ($order->isEscrowHeld() && (float) $order->escrow_amount > 0) {
                $sellerUser = $order->sellerProfile->user;
                if ($sellerUser) {
                    try {
                        $walletService->credit(
                            $sellerUser,
                            (float) $order->escrow_amount,
                            'refund',
                            'Pencairan pesanan #' . $order->id . ' (' . $order->checkout->reference . ')',
                            $order
                        );
                    } catch (\Throwable $e) {
                        \Log::warning('Gagal release escrow ke seller', [
                            'order_id' => $order->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
                $order->forceFill(['released_at' => now()])->save();
            }

            // Award komisi affiliate (existing)
            $commissionService->awardForOrder($order);
        }

        return back()->with($ok ? 'status' : 'error', $ok ? 'order-completed' : 'Transisi status tidak valid.');
    }
}