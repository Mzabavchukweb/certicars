<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $cars = Car::where('status', 'active')
            ->where('is_sold', false)
            ->where('noindex', false)
            ->orderByDesc('updated_at')
            ->get(['slug', 'updated_at']);

        $urls = [
            ['loc' => url('/'),            'priority' => '1.0',  'changefreq' => 'daily'],
            ['loc' => url('/samochody'),   'priority' => '0.9',  'changefreq' => 'daily'],
            ['loc' => url('/o-nas'),       'priority' => '0.5',  'changefreq' => 'monthly'],
            ['loc' => url('/kontakt'),     'priority' => '0.6',  'changefreq' => 'monthly'],
        ];

        foreach ($cars as $car) {
            $urls[] = [
                'loc'        => url('/samochody/'.$car->slug),
                'lastmod'    => optional($car->updated_at)->toAtomString(),
                'priority'   => '0.8',
                'changefreq' => 'weekly',
            ];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
        foreach ($urls as $u) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>'.e($u['loc']).'</loc>'."\n";
            if (!empty($u['lastmod'])) $xml .= '    <lastmod>'.$u['lastmod'].'</lastmod>'."\n";
            $xml .= '    <changefreq>'.$u['changefreq'].'</changefreq>'."\n";
            $xml .= '    <priority>'.$u['priority'].'</priority>'."\n";
            $xml .= "  </url>\n";
        }
        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=utf-8']);
    }
}
