<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $provided = $request->header('X-API-Key') ?? $request->query('api_key');
        $expected = config('app.api_key');

        if (! $expected) {
            abort(500, 'API key is not configured');
        }

        if (! hash_equals($expected, (string) $provided)) {
            abort(401, 'Unauthorized');
        }

        return $next($request);
    }
}
