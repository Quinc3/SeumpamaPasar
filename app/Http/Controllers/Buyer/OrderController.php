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

    public function complete(Request $request, Order $order, OrderStatusService $service): RedirectResponse
    {
        abort_unless($order->buyer_id === $request->user()->id, 403);

        $ok = $service->transition($order, OrderStatus::Completed->value);
        return back()->with($ok ? 'status' : 'error', $ok ? 'order-completed' : 'Transisi status tidak valid.');
    }
}