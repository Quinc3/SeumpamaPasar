<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SellerStatus;
use App\Http\Controllers\Controller;
use App\Models\SellerProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SellerController extends Controller
{
    public function index(): View
    {
        $sellers = SellerProfile::query()
            ->with(['user', 'products'])
            ->orderByRaw("FIELD(status, 'pending', 'active', 'suspended')")
            ->orderByDesc('id')
            ->paginate(15);

        return view('admin.sellers.index', compact('sellers'));
    }

    public function approve(SellerProfile $seller): RedirectResponse
    {
        $seller->forceFill(['status' => SellerStatus::Active->value])->save();
        return back()->with('status', 'approved');
    }

    public function suspend(SellerProfile $seller, \App\Services\OrderCancellationService $cancellation): RedirectResponse
    {
        $orders = $seller->orders()
            ->whereIn('status', ['pending_processing', 'processing'])
            ->get();

        $cancelled = 0;
        foreach ($orders as $order) {
            $result = $cancellation->cancel($order);
            if ($result === 'cancelled') {
                $cancelled++;
            }
        }

        $seller->forceFill(['status' => SellerStatus::Suspended->value])->save();

        $msg = 'Penjual ditangguhkan.';
        if ($cancelled > 0) {
            $msg .= " {$cancelled} pesanan aktif dibatalkan & stok dikembalikan.";
        }

        return back()->with('status', $msg);
    }

    public function activate(SellerProfile $seller): RedirectResponse
    {
        $seller->forceFill(['status' => SellerStatus::Active->value])->save();
        return back()->with('status', 'activated');
    }
}