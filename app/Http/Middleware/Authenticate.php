<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            return route('login');
        }
    }
    protected function unauthenticated($request, array $guards)
    {
        \Log::info('Unauthenticated request detected', ['guards' => $guards]);

        throw new \Illuminate\Auth\AuthenticationException(
            'Unauthenticated.',
            $guards,
            $this->redirectTo($request)
        );
    }

    public function handle($request, \Closure $next, ...$guards)
    {
        \Log::info('Auth Middleware Triggered', ['guards' => $guards]);

        $this->authenticate($request, $guards);

        return $next($request);
    }
}
