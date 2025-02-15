<?php

namespace App\Http\Middleware;

use Closure;
use App\Utils\NetworkUtils;

class CheckLocalServer
{
    public function handle($request, Closure $next)
    {
        if (!NetworkUtils::isLocalServerAccessible()) {
            return response()->json(['message' => 'Akses hanya di jaringan kantor'], 403);
        }

        return $next($request);
    }
}
