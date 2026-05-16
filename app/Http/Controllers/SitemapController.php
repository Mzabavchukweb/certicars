<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $xml = Cache::remember('sitemap.xml', now()->addHour(), function () {
            return $this->build();
        });

        return response($xml, 200, [
            'Content-Type'  => 'application/xml; charset=utf-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    private function build(): string
    {
        $cars = Car::with(['primaryImage'])
            ->where('status', 'active')
            ->where('is_sold', false)
            ->where('noindex', false)
            ->orderByDesc('updated_at')
            ->get(['id', 'slug', 'updated_at']);

        $urls = [
            ['loc' => url('/'),          'priority' => '1.0', 'changefreq' => 'daily'],
            ['loc' => url('/samochody'), 'priority' => '0.9', 'changefreq' => 'daily'],
            ['loc' => url('/o-nas'),     'priority' => '0.5', 'changefreq' => 'monthly'],
            ['loc' => url('/kontakt'),   'priority' => '0.6', 'changefreq' => 'monthly'],
        ];

        foreach ($cars as $car) {
            $entry = [
                'loc'        => url('/samochody/'.$car->slug),
                'lastmod'    => optional($car->updated_at)->toAtomString(),
                'priority'   => '0.8',
                'changefreq' => 'weekly',
            ];
            if ($car->primaryImage) {
                $entry['image'] = $car->primaryImage->url;
            }
            $urls[] = $entry;
        }

        $xml  = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">'."\n";
        foreach ($urls as $u) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>'.e($u['loc']).'</loc>'."\n";
            if (!empty($u['lastmod'])) $xml .= '    <lastmod>'.$u['lastmod'].'</lastmod>'."\n";
            $xml .= '    <changefreq>'.$u['changefreq'].'</changefreq>'."\n";
            $xml .= '    <priority>'.$u['priority'].'</priority>'."\n";
            if (!empty($u['image'])) {
                $xml .= "    <image:image>\n";
                $xml .= '      <image:loc>'.e($u['image']).'</image:loc>'."\n";
                $xml .= "    </image:image>\n";
            }
            $xml .= "  </url>\n";
        }
        $xml .= '</urlset>';

        return $xml;
    }
}
