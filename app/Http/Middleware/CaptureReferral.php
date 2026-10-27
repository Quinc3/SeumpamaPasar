<?php

namespace App\Http\Middleware;

use App\Models\AffiliateProfile;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CaptureReferral
{
    public function handle(Request $request, Closure $next): Response
    {
        $code = $request->query('ref');
        if ($code) {
            $affiliate = AffiliateProfile::query()
                ->where('referral_code', $code)
                ->where('status', 'active')
                ->first();

            if ($affiliate) {
                session(['affiliate_ref_code' => $affiliate->referral_code]);
                cookie()->queue(
                    cookie('affiliate_ref_code', $affiliate->referral_code, 60 * 24 * 30)
                );
            }
        }

        return $next($request);
    }
}