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
            // Pannellum fetches panorama tiles via XHR — needs the CDN host in connect-src.
            $cdnUrl    = rtrim((string) config('filesystems.disks.public.url', ''), '/');
            $cdnOrigin = $cdnUrl ? parse_url($cdnUrl, PHP_URL_SCHEME) . '://' . parse_url($cdnUrl, PHP_URL_HOST) : '';

            $connectSrc = "'self' https://cdn.jsdelivr.net https://unpkg.com";
            if ($cdnOrigin) {
                $connectSrc .= ' ' . $cdnOrigin;
            }

            // media-src: needed for the <video> element on the public car page.
            // Without an explicit media-src, the browser falls back to
            // default-src 'self' and blocks the engine-work-recording video
            // served from R2 (production CSP violation). Reuses the same
            // config-driven R2 origin as connect-src (Pannellum).
            $mediaSrc = "'self'";
            if ($cdnOrigin) {
                $mediaSrc .= ' ' . $cdnOrigin;
            }

            $csp = [
                "default-src 'self'",
                "base-uri 'self'",
                "object-src 'none'",
                "frame-ancestors 'self'",
                "form-action 'self'",
                "img-src 'self' data: blob: https:",
                "media-src $mediaSrc",
                "font-src 'self' data: https://fonts.gstatic.com",
                "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net",
                "script-src 'self' 'unsafe-inline' https://unpkg.com https://cdn.jsdelivr.net",
                "connect-src $connectSrc",
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
