<?php

namespace App\Http\Controllers;

use App\Models\LandingPage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class LandingPageController extends Controller
{
    public function show(string $slug): View|Response
    {
        $page = LandingPage::where('slug', $slug)
            ->where('is_published', true)
            ->first();

        if (! $page) {
            abort(404);
        }

        $relatedPages = LandingPage::where('is_published', true)
            ->where('id', '!=', $page->id)
            ->latest()
            ->limit(6)
            ->get();

        return view('landing', [
            'page' => $page,
            'relatedPages' => $relatedPages,
        ]);
    }
}