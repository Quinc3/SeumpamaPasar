<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AffiliateProfile;
use App\Models\Category;
use App\Models\Commission;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalRevenue = (float) Order::query()
            ->whereNotNull('stock_deducted_at')
            ->sum('subtotal');

        $ordersByStatus = Order::query()
            ->selectRaw('status, count(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status')
            ->all();

        $topProducts = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereNotNull('orders.stock_deducted_at')
            ->selectRaw('order_items.product_name, sum(order_items.quantity) as total_qty, sum(order_items.subtotal) as total_revenue')
            ->groupBy('order_items.product_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        $topSellersRaw = Order::query()
            ->whereNotNull('stock_deducted_at')
            ->selectRaw('seller_profile_id, sum(subtotal) as total, count(*) as order_count')
            ->groupBy('seller_profile_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $sellerMap = SellerProfile::query()
            ->whereIn('id', $topSellersRaw->pluck('seller_profile_id'))
            ->get()
            ->keyBy('id');

        $topSellers = $topSellersRaw->map(fn ($row) => (object) [
            'store_name' => $sellerMap[$row->seller_profile_id]->store_name ?? '(tidak tersedia)',
            'total' => $row->total,
            'order_count' => $row->order_count,
        ]);

        $totalCommission = (float) Commission::query()
            ->where('status', 'approved')
            ->sum('amount');

        $recentOrders = Order::query()
            ->with(['sellerProfile', 'buyer'])
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        return view('admin.dashboard', [
            'userCount' => User::count(),
            'activeSellerCount' => SellerProfile::where('status', 'active')->count(),
            'activeAffiliateCount' => AffiliateProfile::where('status', 'active')->count(),
            'activeProductCount' => Product::where('status', 'active')->count(),
            'orderCount' => Order::count(),
            'categoryCount' => Category::count(),
            'totalRevenue' => $totalRevenue,
            'ordersByStatus' => $ordersByStatus,
            'topProducts' => $topProducts,
            'topSellers' => $topSellers,
            'totalCommission' => $totalCommission,
            'recentOrders' => $recentOrders,
        ]);
    }
}