<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

/**
 * Sitemapa w dwoch formatach z JEDNEGO zrodla prawdy (metoda urls()).
 *
 *   GET /sitemap.xml — pelny format z lastmod, priority i obrazkami
 *   GET /sitemap.txt — plaska lista URL-i, jeden na linie
 *
 * Lista jest cache'owana pod kluczem CACHE_KEY. Model Car czysci ten klucz
 * na kazdy zapis i kazde usuniecie (patrz Car::booted), wiec auta pojawiaja
 * sie i znikaja z sitemapy natychmiast, a nie po wygasnieciu godzinnego TTL.
 */
class SitemapController extends Controller
{
    public const CACHE_KEY = 'sitemap.urls';

    public function index(): Response
    {
        return response($this->buildXml(), 200, [
            'Content-Type'  => 'application/xml; charset=utf-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /** Plaska lista URL-i — format obslugiwany przez Google i Bing. */
    public function text(): Response
    {
        $body = collect($this->urls())->pluck('loc')->implode("\n") . "\n";

        return response($body, 200, [
            'Content-Type'  => 'text/plain; charset=utf-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    private function urls(): array
    {
        return Cache::remember(self::CACHE_KEY, now()->addHour(), fn () => $this->build());
    }

    private function build(): array
    {
        $cars = Car::with(['images'])
            ->where('status', 'active')
            ->where('is_sold', false)
            ->where('noindex', false)
            ->orderByDesc('updated_at')
            ->get(['id', 'slug', 'updated_at', 'has_certicheck']);

        $urls = [
            ['loc' => url('/'),          'priority' => '1.0', 'changefreq' => 'daily'],
            ['loc' => url('/samochody'), 'priority' => '0.9', 'changefreq' => 'daily'],
            // Zakladka CertiCheck — wczesniej jej tu brakowalo.
            ['loc' => url('/certicheck'),'priority' => '0.7', 'changefreq' => 'monthly'],
            ['loc' => url('/o-nas'),     'priority' => '0.5', 'changefreq' => 'monthly'],
            ['loc' => url('/kontakt'),   'priority' => '0.6', 'changefreq' => 'monthly'],
        ];
        // /obserwowane celowo pominiete — noindex (lista zyje w localStorage).

        foreach ($cars as $car) {
            $entry = [
                'loc'        => url('/samochody/'.$car->slug),
                'lastmod'    => optional($car->updated_at)->toAtomString(),
                'priority'   => '0.8',
                'changefreq' => 'weekly',
                'images'     => $car->images
                    ->where('type', 'gallery')
                    ->filter(fn($i) => $i->path)
                    ->map(fn($i) => $i->url)
                    ->filter(fn($u) => !str_ends_with($u, 'placeholder-car.svg'))
                    ->values()
                    ->all(),
            ];
            $urls[] = $entry;

            if ($car->has_certicheck) {
                $urls[] = [
                    'loc'        => url('/samochody/'.$car->slug.'/certicheck'),
                    'lastmod'    => optional($car->updated_at)->toAtomString(),
                    'priority'   => '0.6',
                    'changefreq' => 'monthly',
                ];
            }
        }

        return $urls;
    }

    private function buildXml(): string
    {
        $xml  = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">'."\n";
        foreach ($this->urls() as $u) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>'.e($u['loc']).'</loc>'."\n";
            if (!empty($u['lastmod'])) $xml .= '    <lastmod>'.$u['lastmod'].'</lastmod>'."\n";
            $xml .= '    <changefreq>'.$u['changefreq'].'</changefreq>'."\n";
            $xml .= '    <priority>'.$u['priority'].'</priority>'."\n";
            foreach ($u['images'] ?? [] as $imgUrl) {
                $xml .= "    <image:image>\n";
                $xml .= '      <image:loc>'.e($imgUrl).'</image:loc>'."\n";
                $xml .= "    </image:image>\n";
            }
            $xml .= "  </url>\n";
        }
        $xml .= '</urlset>';

        return $xml;
    }
}
