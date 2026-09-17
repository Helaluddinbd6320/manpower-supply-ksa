<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\QuotationPdfController;



Route::get('/', function () {
    return view('welcome');
});

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

Route::middleware('auth')->group(function () {
    Route::get('/quotations/{quotation}/pdf', [QuotationPdfController::class, 'show'])->name('quotations.pdf');
});


// ⚠️ এই রুটটা সবসময় ফাইলের সবার শেষে রাখতে হবে,
// নাহলে অন্য রুট (যেমন /admin) ভুলভাবে এখানে ম্যাচ করে যেতে পারে
Route::get('/{slug}', [LandingPageController::class, 'show'])
    ->where('slug', '^(?!admin).*$')
    ->name('landing.show');