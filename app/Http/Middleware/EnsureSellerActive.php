<?php

namespace App\Http\Middleware;

use App\Enums\SellerStatus;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSellerActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null) {
            abort(401);
        }

        $seller = $user->sellerProfile;

        if ($seller === null || $seller->status !== SellerStatus::Active) {
            abort(403);
        }

        return $next($request);
    }
}