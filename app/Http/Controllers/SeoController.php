<?php

namespace App\Http\Controllers;

use App\Models\Region;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    /**
     * Generate dynamic sitemap.xml.
     */
    public function sitemap(): Response
    {
        $states = Region::orderBy('updated_at', 'desc')->get();
        $baseUrl = config('app.url', 'https://youthpolicytracker.ng');

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // Core pages
        $xml .= '<url><loc>' . $baseUrl . '/</loc><changefreq>daily</changefreq><priority>1.0</priority></url>';
        $xml .= '<url><loc>' . $baseUrl . '/dashboard</loc><changefreq>daily</changefreq><priority>0.9</priority></url>';
        $xml .= '<url><loc>' . $baseUrl . '/documents</loc><changefreq>weekly</changefreq><priority>0.8</priority></url>';
        $xml .= '<url><loc>' . $baseUrl . '/privacy-policy</loc><changefreq>monthly</changefreq><priority>0.5</priority></url>';

        // State Deep-links
        foreach ($states as $state) {
            $xml .= '<url>';
            $xml .= '<loc>' . $baseUrl . '/states/' . $state->slug . '</loc>';
            $xml .= '<lastmod>' . ($state->updated_at ? $state->updated_at->toAtomString() : date('c')) . '</lastmod>';
            $xml .= '<changefreq>weekly</changefreq>';
            $xml .= '<priority>0.8</priority>';
            $xml .= '</url>';
        }

        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }

    /**
     * Generate robots.txt.
     */
    public function robots(): Response
    {
        $baseUrl = config('app.url', 'https://youthpolicytracker.ng');
        $content = "User-agent: *\n";
        $content .= "Allow: /\n";
        $content .= "Disallow: /admin\n";
        $content .= "Disallow: /documents/*/download\n";
        $content .= "Disallow: /documents/*/stream\n";
        $content .= "Sitemap: {$baseUrl}/sitemap.xml\n";

        return response($content, 200, ['Content-Type' => 'text/plain']);
    }
}
