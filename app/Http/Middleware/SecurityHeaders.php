<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        // Skip document security headers for Livewire internal AJAX/update requests
        if ($request->hasHeader('X-Livewire') || $request->is('livewire*') || $request->is('livewire-*') || $request->is('*/livewire*')) {
            return $response;
        }

        // Only attach headers if Content-Type is HTML or not specified
        $contentType = $response->headers->get('Content-Type');
        if ($contentType && ! str_contains($contentType, 'text/html')) {
            return $response;
        }

        // 1. Legacy XSS Protection Header
        if (! $response->headers->has('X-XSS-Protection')) {
            $response->headers->set('X-XSS-Protection', '1; mode=block');
        }

        // 2. Content Security Policy (CSP) Header
        if (! $response->headers->has('Content-Security-Policy')) {
            $csp = implode('; ', [
                "default-src 'self'",
                "script-src 'self' 'unsafe-inline' 'unsafe-eval' blob: https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://cdn.tailwindcss.com https://unpkg.com https://challenges.cloudflare.com",
                "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://unpkg.com",
                "font-src 'self' data: https://fonts.gstatic.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net https://unpkg.com",
                "img-src 'self' data: https: http: blob:",
                "connect-src 'self' https: http: ws: wss:",
                "frame-src 'self' https://challenges.cloudflare.com",
                "frame-ancestors 'self'",
                "object-src 'none'",
                "base-uri 'self'",
            ]);
            $response->headers->set('Content-Security-Policy', $csp);
        }

        // 3. Anti-Clickjacking Header
        if (! $response->headers->has('X-Frame-Options')) {
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        }

        // 4. Prevent MIME-type Sniffing Header
        if (! $response->headers->has('X-Content-Type-Options')) {
            $response->headers->set('X-Content-Type-Options', 'nosniff');
        }

        // 5. Referrer Policy Header
        if (! $response->headers->has('Referrer-Policy')) {
            $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        }

        // 6. Permissions Policy Header
        if (! $response->headers->has('Permissions-Policy')) {
            $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        }

        return $response;
    }
}
