<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

/**
 * Jedno miejsce na decyzje pomiarowe. Middleware, kontrolery i endpoint
 * /zdarzenie wszystkie wołają stąd, żeby "co liczymy jako bota", "co jest
 * ruchem organicznym" i "skąd bierze się visitor_id" miało jedną definicję.
 */
class Analytics
{
    /** Ciasteczko odwiedzającego — 1 rok, bez danych osobowych (losowy uuid). */
    public const VISITOR_COOKIE = 'cs_vid';

    private const VISITOR_TTL_MINUTES = 525600; // 365 dni

    private const BOT_PATTERNS = ['bot', 'crawl', 'spider', 'slurp', 'bing', 'duckduck', 'lighthouse', 'headless', 'curl', 'wget'];

    /** Nazwy zdarzeń, które wolno przysłać z przeglądarki. Nic poza tą listą nie trafi do bazy. */
    public const CLIENT_EVENTS = [
        'phone_click',
        'favorite_add',
        'favorite_remove',
        'pano_open',
        'gallery_open',
        'filter_use',
        'inquiry_form_open',
    ];

    /** Zdarzenia zapisywane po stronie serwera — klient nie może ich podrobić. */
    public const SERVER_EVENTS = [
        'pdf_download',
        'certicheck_open',
        'inquiry_submitted',
        'contact_submitted',
    ];

    /** Zdarzenia liczone jako kontakt (lejek) — używane przez dashboard. */
    public const CONTACT_EVENTS = ['phone_click', 'inquiry_submitted', 'contact_submitted'];

    /**
     * Stabilny identyfikator przeglądarki. Czyta ciasteczko; gdy go nie ma,
     * generuje uuid i KOLEJKUJE ciasteczko na tę odpowiedź — dzięki temu
     * pierwsza odsłona nowego użytkownika też ma visitor_id (nie jest
     * przypisana do nulla i nie zawyża "unikalnych").
     */
    public static function visitorId(Request $request): string
    {
        $existing = $request->cookie(self::VISITOR_COOKIE);

        if (is_string($existing) && preg_match('/^[0-9a-f-]{36}$/i', $existing)) {
            return $existing;
        }

        $id = (string) Str::uuid();

        Cookie::queue(Cookie::make(
            name: self::VISITOR_COOKIE,
            value: $id,
            minutes: self::VISITOR_TTL_MINUTES,
            httpOnly: true,
            sameSite: 'lax',
        ));

        // Ta sama odpowiedź może zapisać kilka wierszy (page_view + event) —
        // trzymamy id w atrybutach requestu, żeby nie wygenerować dwóch.
        $request->attributes->set('cs_visitor_id', $id);

        return $id;
    }

    /** Odczyt bez generowania — dla ścieżek, które nie ustawiają ciasteczek. */
    public static function existingVisitorId(Request $request): ?string
    {
        $fromRequest = $request->attributes->get('cs_visitor_id');
        if (is_string($fromRequest)) return $fromRequest;

        $cookie = $request->cookie(self::VISITOR_COOKIE);

        return is_string($cookie) && preg_match('/^[0-9a-f-]{36}$/i', $cookie) ? $cookie : null;
    }

    public static function isBot(?string $userAgent): bool
    {
        $ua = strtolower((string) $userAgent);
        if ($ua === '') return true;

        foreach (self::BOT_PATTERNS as $p) {
            if (str_contains($ua, $p)) return true;
        }

        return false;
    }

    public static function device(?string $userAgent): string
    {
        $ua = strtolower((string) $userAgent);

        if (str_contains($ua, 'ipad') || str_contains($ua, 'tablet')) return 'tablet';
        if (str_contains($ua, 'mobi') || str_contains($ua, 'iphone') || str_contains($ua, 'android')) return 'mobile';

        return 'desktop';
    }

    /** UTM-y z query stringa, przycięte do długości kolumny. */
    public static function utm(Request $request): array
    {
        $take = fn(string $key) => ($v = $request->query($key)) && is_string($v)
            ? mb_substr($v, 0, 120)
            : null;

        return [
            'utm_source'   => $take('utm_source'),
            'utm_medium'   => $take('utm_medium'),
            'utm_campaign' => $take('utm_campaign'),
        ];
    }

    /**
     * Kanał wejścia. Liczony TYLKO z pierwszej odsłony w sesji — inaczej
     * własna domena zalewałaby raport jako "referral".
     */
    public static function channel(?string $referer, ?string $utmSource, ?string $utmMedium): string
    {
        $medium = strtolower((string) $utmMedium);
        if (in_array($medium, ['cpc', 'ppc', 'paid', 'paid_social'], true)) return 'Płatne';
        if ($medium === 'email' || $medium === 'newsletter')               return 'E-mail';

        if ($utmSource) return 'Kampania';

        $host = strtolower((string) parse_url((string) $referer, PHP_URL_HOST));

        if ($host === '') return 'Bezpośrednio';

        // Wejścia z własnej domeny to nawigacja wewnętrzna, nie nowe źródło.
        $ownHost = strtolower((string) parse_url((string) config('app.url'), PHP_URL_HOST));
        if ($ownHost !== '' && str_contains($host, $ownHost)) return 'Bezpośrednio';

        foreach (['google', 'bing', 'duckduckgo', 'yahoo', 'ecosia', 'yandex'] as $engine) {
            if (str_contains($host, $engine)) return 'Wyszukiwarki';
        }

        foreach (['facebook', 'instagram', 'tiktok', 'youtube', 'linkedin', 't.co', 'twitter', 'x.com'] as $social) {
            if (str_contains($host, $social)) return 'Social';
        }

        if (str_contains($host, 'otomoto') || str_contains($host, 'olx')) return 'Portale ogłoszeniowe';

        return 'Odesłania';
    }

    /** Ładna nazwa hosta do tabeli źródeł. */
    public static function refererHost(?string $referer): string
    {
        $host = (string) parse_url((string) $referer, PHP_URL_HOST);

        return $host !== '' ? preg_replace('/^www\./', '', $host) : 'Bezpośrednio';
    }

    /** Etykiety zdarzeń po polsku — dashboard i tylko dashboard. */
    public static function eventLabel(string $name): string
    {
        return [
            'phone_click'       => 'Kliknięcie w telefon',
            'pdf_download'      => 'Pobranie raportu PDF',
            'certicheck_open'   => 'Otwarcie CertiCheck',
            'favorite_add'      => 'Dodanie do obserwowanych',
            'favorite_remove'   => 'Usunięcie z obserwowanych',
            'pano_open'         => 'Otwarcie panoramy 360',
            'gallery_open'      => 'Otwarcie galerii',
            'filter_use'        => 'Użycie filtrów',
            'inquiry_form_open' => 'Otwarcie formularza zapytania',
            'inquiry_submitted' => 'Wysłane zapytanie o auto',
            'contact_submitted' => 'Wysłany formularz kontaktowy',
        ][$name] ?? $name;
    }
}
