<?php

namespace App\Http\Middleware;

use App\Models\PageView;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackPageView
{
    private const DEDUPE_MINUTES = 30;

    private const SKIP_PATH_PREFIXES = ['admin', 'storage', 'up', '_debugbar', 'livewire', '_ping'];

    private const BOT_PATTERNS = ['bot', 'crawl', 'spider', 'slurp', 'bing', 'duckduck', 'lighthouse', 'headless', 'curl', 'wget'];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!$this->shouldTrack($request, $response)) {
            return $response;
        }

        $sessionId = $request->hasSession() ? $request->session()->getId() : null;
        $path      = $request->path();
        $routeName = $request->route()?->getName();
        $ip        = $request->ip();
        $referer   = substr((string) $request->headers->get('referer'), 0, 500) ?: null;
        $ua        = substr((string) $request->userAgent(), 0, 500) ?: null;

        // Defer DB writes until after the response is sent to the client.
        defer(function () use ($sessionId, $path, $routeName, $ip, $referer, $ua) {
            try {
                $alreadyLogged = PageView::where('session_id', $sessionId)
                    ->where('path', $path)
                    ->where('created_at', '>=', now()->subMinutes(self::DEDUPE_MINUTES))
                    ->exists();

                if ($alreadyLogged) return;

                PageView::create([
                    'path'       => substr($path, 0, 500),
                    'route_name' => $routeName,
                    'session_id' => $sessionId,
                    'ip'         => $ip,
                    'referer'    => $referer,
                    'user_agent' => $ua,
                ]);
            } catch (\Throwable) {
                // Don't break the site on tracking failures.
            }
        });

        return $response;
    }

    private function shouldTrack(Request $request, Response $response): bool
    {
        if (!$request->isMethod('GET')) return false;
        if ($response->getStatusCode() !== 200) return false;

        $path = $request->path();
        foreach (self::SKIP_PATH_PREFIXES as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix . '/')) return false;
        }

        $ua = strtolower((string) $request->userAgent());
        if ($ua === '') return false;
        foreach (self::BOT_PATTERNS as $p) {
            if (str_contains($ua, $p)) return false;
        }

        return true;
    }
}
