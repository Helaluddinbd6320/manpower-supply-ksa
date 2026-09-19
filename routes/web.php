<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\QuotationPdfController;
use App\Http\Controllers\CandidateLeadPdfController;




Route::get('/', function () {
    return view('welcome');
});

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

Route::middleware('auth')->group(function () {
    Route::get('/quotations/{quotation}/pdf', [QuotationPdfController::class, 'show'])->name('quotations.pdf');
});

Route::middleware('auth')->group(function () {
    Route::get('/candidate-leads/{candidateLead}/pdf', [CandidateLeadPdfController::class, 'show'])
        ->name('candidate-leads.pdf');
});


// ⚠️ এই রুটটা সবসময় ফাইলের সবার শেষে রাখতে হবে,
// নাহলে অন্য রুট (যেমন /admin) ভুলভাবে এখানে ম্যাচ করে যেতে পারে
Route::get('/{slug}', [LandingPageController::class, 'show'])
    ->where('slug', '^(?!admin).*$')
    ->name('landing.show');