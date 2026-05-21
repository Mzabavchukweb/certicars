<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        @header_remove('X-Powered-By');

        /** @var Response $response */
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('X-XSS-Protection', '0');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=(), payment=(), usb=(), interest-cohort=()');
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin-allow-popups');

        if ($request->isSecure() || app()->environment('production')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        // Skip CSP for admin panel — it uses many CDN resources (Chart.js, Lucide, etc.)
        if ($request->is('admin/*') || $request->is('admin')) {
            $response->headers->remove('X-Powered-By');
            $response->headers->remove('Server');
            return $response;
        }

        if (! $response->headers->has('Content-Security-Policy')) {
            $csp = [
                "default-src 'self'",
                "base-uri 'self'",
                "object-src 'none'",
                "frame-ancestors 'self'",
                "form-action 'self'",
                "img-src 'self' data: blob: https:",
                "font-src 'self' data: https://fonts.gstatic.com",
                "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net",
                "script-src 'self' 'unsafe-inline' https://unpkg.com https://cdn.jsdelivr.net",
                "connect-src 'self' https://cdn.jsdelivr.net",
                "frame-src 'self' https://www.youtube.com https://player.vimeo.com https://www.google.com",
                "upgrade-insecure-requests",
            ];
            $response->headers->set('Content-Security-Policy', implode('; ', $csp));
        }

        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        return $response;
    }
}
