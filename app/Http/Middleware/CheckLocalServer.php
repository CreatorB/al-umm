<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckLocalServer
{
    function getUrlContent($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
    public function handle($request, Closure $next)
    {
        Log::info('Middleware CheckLocalServer is running');

        $allowedPublicIp = env('ATTENDANCE_SERVER_PUBLIC', '103.178.146.98');
        // $userIp = file_get_contents('https://api.ipify.org?format=json');
        $userIp = $this->getUrlContent('https://api.ipify.org?format=json');
        $userIp = json_decode($userIp, true)['ip'] ?? '';

        if ($userIp !== $allowedPublicIp) {
            Log::warning("Unauthorized IP: {$userIp}");
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Afwan, Absen hanya bisa dilakukan dalam jaringan kantor.'], 403);
            }
            return abort(403, 'Afwan, Absen hanya bisa dilakukan dalam jaringan Wi-Fi / LAN Mahad Syathiby.');
        }

        return $next($request);
    }
}