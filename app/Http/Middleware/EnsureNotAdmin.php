<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureNotAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->role === UserRole::Admin) {
            abort(403, 'Admin hanya dapat mengakses panel admin.');
        }
        return $next($request);
    }
}