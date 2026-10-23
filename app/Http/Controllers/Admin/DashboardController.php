<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\DigitalProduct;
use App\Models\Order;
use App\Models\Product;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'userCount' => User::count(),
            'activeSellerCount' => SellerProfile::where('status', 'active')->count(),
            'activeProductCount' => Product::where('status', 'active')->count(),
            'orderCount' => Order::count(),
            'categoryCount' => Category::count(),
            'activeDigitalProductCount' => DigitalProduct::where('is_active', true)->count(),
        ]);
    }
}