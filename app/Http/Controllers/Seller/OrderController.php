<?php

namespace App\Http\Controllers\Seller;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderCancellationService;
use App\Services\OrderStatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $seller = $request->user()->sellerProfile;

        $orders = Order::query()
            ->where('seller_profile_id', $seller->id)
            ->with(['buyer', 'items'])
            ->orderByDesc('id')
            ->paginate(10);

        return view('seller.orders.index', compact('orders'));
    }

    public function show(Request $request, Order $order): View
    {
        abort_unless($order->seller_profile_id === $request->user()->sellerProfile->id, 403);
        $order->load(['buyer', 'items', 'checkout.payment']);

        return view('seller.orders.show', compact('order'));
    }

    public function process(Request $request, Order $order, OrderStatusService $service): RedirectResponse
    {
        abort_unless($order->seller_profile_id === $request->user()->sellerProfile->id, 403);
        $ok = $service->transition($order, OrderStatus::Processing->value);
        return back()->with($ok ? 'status' : 'error', $ok ? 'order-processing' : 'Transisi status tidak valid.');
    }

    public function ship(Request $request, Order $order, OrderStatusService $service): RedirectResponse
    {
        abort_unless($order->seller_profile_id === $request->user()->sellerProfile->id, 403);
        $ok = $service->transition($order, OrderStatus::Shipped->value);
        return back()->with($ok ? 'status' : 'error', $ok ? 'order-shipped' : 'Transisi status tidak valid.');
    }

    public function cancel(Request $request, Order $order, OrderCancellationService $service, \App\Services\CommissionService $commissionService): RedirectResponse
    {
        abort_unless($order->seller_profile_id === $request->user()->sellerProfile->id, 403);

        $result = $service->cancel($order);

        if ($result === 'cancelled') {
            $commissionService->cancelForOrder($order, 'order_cancelled_by_seller');
        }

        return match ($result) {
            'cancelled' => back()->with('status', 'order-cancelled'),
            'invalid_status' => back()->with('error', 'Hanya pesanan berstatus "Menunggu Diproses" yang dapat dibatalkan.'),
            'already_cancelled' => back()->with('error', 'Pesanan sudah dibatalkan.'),
            default => back()->with('error', 'Pembatalan gagal.'),
        };
    }

    public function bulkStatus(Request $request, OrderStatusService $service): RedirectResponse
    {
        $seller = $request->user()->sellerProfile;

        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
            'action' => ['required', 'in:process,ship'],
        ]);

        $target = $data['action'] === 'process'
            ? \App\Enums\OrderStatus::Processing
            : \App\Enums\OrderStatus::Shipped;

        $orders = Order::query()
            ->whereIn('id', $data['ids'])
            ->where('seller_profile_id', $seller->id)
            ->get();

        $ok = 0;
        $fail = 0;
        foreach ($orders as $order) {
            if ($service->transition($order, $target->value)) {
                $ok++;
            } else {
                $fail++;
            }
        }

        $msg = "{$ok} pesanan diubah ke " . $target->value;
        if ($fail > 0) $msg .= ", {$fail} dilewati (status tidak valid)";

        return back()->with('status', $msg);
    }
}