<?php
namespace App\Utils;

use Log;
use Exception;

class NetworkUtils
{
    public static function isLocalServerAccessible()
    {
        $serverIp = env('ATTENDANCE_SERVER_IP', '192.168.50.100');
        $serverWeb = env('ATTENDANCE_SERVER_WEB', 'http://192.168.50.100');

        Log::info('Network Accessibility Check', [
            'serverIp' => $serverIp,
            'serverWeb' => $serverWeb
        ]);

        try {
            $socket = @fsockopen($serverIp, 80, $errno, $errstr, 5);
            if ($socket) {
                fclose($socket);
                Log::info('Server accessible via socket');
                return true;
            }
        } catch (Exception $e) {
            Log::warning('Socket connection failed', ['error' => $e->getMessage()]);
        }

        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 5,
                    'ignore_errors' => true
                ]
            ]);

            $response = @file_get_contents($serverWeb, false, $context);

            if ($response !== false) {
                Log::info('Server accessible via HTTP');
                return true;
            }
        } catch (Exception $e) {
            Log::warning('HTTP request failed', ['error' => $e->getMessage()]);
        }

        if (function_exists('curl_init')) {
            try {
                $ch = curl_init($serverWeb);
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($httpCode === 200 || $httpCode === 302) {
                    Log::info('Server accessible via cURL');
                    return true;
                }
            } catch (Exception $e) {
                Log::warning('cURL request failed', ['error' => $e->getMessage()]);
            }
        }

        Log::warning('Server not accessible through any method');
        return false;
    }
}