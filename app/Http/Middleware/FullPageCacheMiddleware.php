<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class FullPageCacheMiddleware
{
    /**
     * Handle full-page caching for guest visitors on GET requests.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Don't cache admin routes, non-GET requests or logged-in users
        if (! $request->isMethod('GET') || auth()->check() || $request->is('admin*') || $request->is('api*') || $request->is('livewire*')) {
            return $next($request);
        }

        $cacheKey = 'larapress_fpc_' . md5($request->fullUrl());

        if (Cache::has($cacheKey)) {
            $cachedContent = Cache::get($cacheKey);
            return response($cachedContent)
                ->header('X-LaraPress-Cache', 'HIT');
        }

        $response = $next($request);

        if ($response->isSuccessful() && !empty($response->getContent())) {
            Cache::put($cacheKey, $response->getContent(), 3600); // 1 hour cache
            $response->headers->set('X-LaraPress-Cache', 'MISS');
        }

        return $response;
    }
}
