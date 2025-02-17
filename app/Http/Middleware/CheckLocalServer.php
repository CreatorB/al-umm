<?php

namespace App\Http\Middleware;

use Closure;
use App\Utils\NetworkUtils;

class CheckLocalServer
{
    public function handle($request, Closure $next)
    {
        $userIp = $request->ip();
        $segments = explode('.', $userIp);

        if (
            $segments[0] == '192' && $segments[1] == '168' &&
            $segments[2] >= 10 && $segments[2] <= 100
        ) {
            return $next($request);
        }

        return response()->json(['error' => 'Afwan, absen hanya bisa dilakukan dalam jaringan Wi-Fi / LAN Mahad Syathiby.'], 403);

    }
}
