<?php

use App\Http\Controllers\Admin\InquiryController as AdminInquiryController;
use App\Http\Controllers\FavoritesController;
use App\Http\Controllers\Admin\BrandController as AdminBrandController;
use App\Http\Controllers\Admin\CarController as AdminCarController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MessageController as AdminMessageController;
use App\Http\Controllers\Admin\OtomotoImportController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PanoramaController;
use App\Http\Controllers\BrochurePdfController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TrackController;
use Illuminate\Support\Facades\Route;

// Public
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/samochody', [CatalogController::class, 'index'])->name('catalog');
Route::get('/samochody/{car:slug}', [CatalogController::class, 'show'])->name('catalog.show');
Route::get('/samochody/{car:slug}/certicheck', [CatalogController::class, 'certicheck'])->name('catalog.certicheck');
// Public download — serves the CACHED brochure file only. Returns 404 (no
// attachment header) when the brochure isn't ready. No synchronous render
// happens here — that's the architectural fix for download.html/.json.
Route::get('/samochody/{car:slug}/pdf', [BrochurePdfController::class, 'download'])
    ->middleware('throttle:30,1')
    ->name('car.pdf');
Route::get('/o-nas', [PageController::class, 'about'])->name('about');
Route::get('/kontakt', [PageController::class, 'contact'])->name('contact');
Route::post('/kontakt', [PageController::class, 'contactSubmit'])->middleware('throttle:5,1')->name('contact.submit');
Route::get('/certicheck', [PageController::class, 'certicheckLanding'])->name('certicheck.landing');
Route::post('/zapytanie', [InquiryController::class, 'store'])->middleware('throttle:10,1')->name('inquiry.store');
Route::get('/pano/{carImage}', [PanoramaController::class, 'stream'])->name('panorama.stream');
Route::get('/obserwowane', [FavoritesController::class, 'index'])->name('favorites');
// Zdarzenia z przeglądarki (sendBeacon). Throttle bo endpoint jest publiczny.
Route::post('/zdarzenie', [TrackController::class, 'store'])
    ->middleware('throttle:60,1')
    ->name('track.store');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Auth
Route::get('/admin/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/admin/login', [LoginController::class, 'login'])->middleware('throttle:10,1')->name('login.attempt');
Route::post('/admin/logout', [LoginController::class, 'logout'])->name('logout');

// Password reset
Route::get('/admin/password/reset', [PasswordResetController::class, 'showRequestForm'])->name('password.request');
Route::post('/admin/password/email', [PasswordResetController::class, 'sendLink'])->middleware('throttle:5,1')->name('password.email');
Route::get('/admin/password/reset/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/admin/password/update', [PasswordResetController::class, 'reset'])->middleware('throttle:5,1')->name('password.update');

// Admin Panel
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::post('cars/bulk', [AdminCarController::class, 'bulk'])->name('admin.cars.bulk');
    Route::resource('cars', AdminCarController::class)->names('admin.cars');
    Route::patch('cars/{car}/toggle-featured', [AdminCarController::class, 'toggleFeatured'])->name('admin.cars.toggle-featured');
    Route::patch('cars/{car}/toggle-sold', [AdminCarController::class, 'toggleSold'])->name('admin.cars.toggle-sold');
    Route::post('cars/{car}/upload-image', [AdminCarController::class, 'uploadImage'])->name('admin.cars.upload-image');
    Route::get('cars/{car}/pdf', [BrochurePdfController::class, 'download'])->name('admin.cars.pdf');
    // Synchronously regenerate one car's cached brochure. Admin waits.
    Route::post('cars/{car}/pdf/regenerate', [BrochurePdfController::class, 'regenerate'])->name('admin.cars.pdf.regenerate');
    // Diagnostic: returns JSON manifest of what would be embedded without
    // invoking Chromium. Useful for figuring out why a production brochure
    // shipped empty (skipped image reasons are recorded per-path).
    Route::get('cars/{car}/pdf/diagnostic', [BrochurePdfController::class, 'diagnostic'])->name('admin.cars.pdf.diagnostic');
    // Chromium liveness check. Hit after every deploy that touches the
    // Dockerfile / runtime to confirm the binary is actually executable
    // before customers report broken downloads.
    Route::get('pdf/health', [BrochurePdfController::class, 'health'])->name('admin.pdf.health');
    // P0 production debug: list ALL CertiCheck cars with their current
    // brochure state in a single JSON response. Hit when customers report
    // a stuck "preparing" modal — we see at a glance which row is stuck
    // in which phase (missing/generating/failed/ready) and any recorded
    // error. Admin-only via the surrounding middleware group.
    Route::get('brochures/status', [BrochurePdfController::class, 'statusOverview'])->name('admin.brochures.status');
    Route::post('otomoto-import', [OtomotoImportController::class, 'scrape'])->name('admin.otomoto.import');

    Route::resource('brands', AdminBrandController::class)->names('admin.brands')->except(['create', 'show', 'edit']);

    Route::post('messages/bulk', [AdminMessageController::class, 'bulk'])->name('admin.messages.bulk');
    Route::patch('messages/{message}/unread', [AdminMessageController::class, 'markUnread'])->name('admin.messages.unread');
    Route::resource('messages', AdminMessageController::class)->names('admin.messages')->only(['index', 'show', 'destroy']);

    Route::post('inquiries/bulk', [AdminInquiryController::class, 'bulk'])->name('admin.inquiries.bulk');
    Route::patch('inquiries/{inquiry}/unread', [AdminInquiryController::class, 'markUnread'])->name('admin.inquiries.unread');
    Route::resource('inquiries', AdminInquiryController::class)->names('admin.inquiries')->only(['index', 'show', 'destroy']);

    Route::get('profile', [AdminProfileController::class, 'edit'])->name('admin.profile.edit');
    Route::patch('profile', [AdminProfileController::class, 'update'])->name('admin.profile.update');
    Route::patch('profile/password', [AdminProfileController::class, 'password'])->name('admin.profile.password');
});
