<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class CheckLocalServer
{
    public function handle($request, Closure $next)
    {
        Log::info('Middleware CheckLocalServer is running');

        $allowedPublicIp = env('ATTENDANCE_SERVER_PUBLIC', '103.178.146.98');
        $allowedIps = explode(',', $allowedPublicIp); 

        $deviceInfo = $request->input('device_info');
        $clientIp = null;

        if ($deviceInfo) {

            if (is_string($deviceInfo) && strpos($deviceInfo, 'ipAddress:') !== false) {
                preg_match('/ipAddress:\s*([^,]+)/', $deviceInfo, $matches);
                if (isset($matches[1])) {
                    $clientIp = trim($matches[1]);
                    Log::info("Client IP from device_info: {$clientIp}");
                }
            }
        }

        if (!$clientIp) {
            Log::warning("No IP address found in device_info");
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Afwan, tidak bisa memverifikasi jaringan.'
                ], 403);
            }
            return abort(403, 'Afwan, tidak bisa memverifikasi jaringan.');
        }

        $isAllowed = false;
        foreach ($allowedIps as $ip) {
            if (trim($ip) === $clientIp) {
                $isAllowed = true;
                break;
            }
        }

        if (!$isAllowed) {
            Log::warning("Unauthorized IP: {$clientIp}");
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Afwan, Absen hanya bisa dilakukan dalam jaringan Wi-Fi / LAN Mahad Syathiby.'
                ], 403);
            }
            return abort(403, 'Afwan, Absen hanya bisa dilakukan dalam jaringan Wi-Fi / LAN Mahad Syathiby.');
        }

        return $next($request);
    }
}