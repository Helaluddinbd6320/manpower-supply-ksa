<?php

namespace App\Http\Controllers;

use App\Models\LandingPage;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $pages = LandingPage::where('is_published', true)
            ->orderBy('updated_at', 'desc')
            ->get();

        $urls = [
            [
                'loc' => url('/'),
                'lastmod' => now()->toAtomString(),
                'priority' => '1.0',
            ],
        ];

        foreach ($pages as $page) {
            $urls[] = [
                'loc' => url('/' . $page->slug),
                'lastmod' => $page->updated_at->toAtomString(),
                'priority' => '0.8',
            ];
        }

        $xml = view('sitemap', ['urls' => $urls])->render();

        return response($xml, 200)
            ->header('Content-Type', 'application/xml');
    }
}