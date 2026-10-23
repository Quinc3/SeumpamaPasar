<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('buyer.dashboard', [
            'user' => $user,
            'orderCount' => $user->orders()->count(),
            'digitalTransactionCount' => $user->digitalTransactions()->count(),
            'addressCount' => $user->addresses()->count(),
        ]);
    }
}