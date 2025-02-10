<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class EnsureTokenIsValid
{
    public function handle(Request $request, Closure $next)
    {
        Log::info('Custom Auth Middleware Triggered');
        if (!$request->hasHeader('Authorization')) {
            return response()->json([
                'success' => false,
                'message' => 'Token missing.',
                'data' => [],
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
