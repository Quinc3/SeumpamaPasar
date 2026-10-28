<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $seller = $request->user()->sellerProfile;

        $totalRevenue = (float) $seller->orders()
            ->whereNotNull('stock_deducted_at')
            ->sum('subtotal');

        $thisMonthRevenue = (float) $seller->orders()
            ->whereNotNull('stock_deducted_at')
            ->where('created_at', '>=', now()->startOfMonth())
            ->sum('subtotal');

        $ordersByStatus = $seller->orders()
            ->selectRaw('status, count(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status')
            ->all();

        $topProducts = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.seller_profile_id', $seller->id)
            ->whereNotNull('orders.stock_deducted_at')
            ->selectRaw('order_items.product_name, sum(order_items.quantity) as total_qty, sum(order_items.subtotal) as total_revenue')
            ->groupBy('order_items.product_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        return view('seller.dashboard', [
            'seller' => $seller,
            'productCount' => $seller->products()->count(),
            'activeProductCount' => $seller->products()->where('status', 'active')->count(),
            'pendingReviewCount' => $seller->products()->where('status', 'pending_review')->count(),
            'orderCount' => $seller->orders()->count(),
            'pendingProcessingCount' => $seller->orders()->where('status', 'pending_processing')->count(),
            'totalRevenue' => $totalRevenue,
            'thisMonthRevenue' => $thisMonthRevenue,
            'ordersByStatus' => $ordersByStatus,
            'topProducts' => $topProducts,
            'escrowHeldTotal' => (float) $seller->orders()->escrowHeld()->sum('escrow_amount'),
            'escrowHeldCount' => $seller->orders()->escrowHeld()->count(),
            'walletBalance' => (float) ($request->user()->wallet?->balance ?? 0),
        ]);
    }
}