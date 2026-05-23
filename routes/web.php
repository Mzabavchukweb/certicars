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
use App\Http\Controllers\PdfController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

// Public
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/samochody', [CatalogController::class, 'index'])->name('catalog');
Route::get('/samochody/{car:slug}', [CatalogController::class, 'show'])->name('catalog.show');
Route::get('/samochody/{car:slug}/certicheck', [CatalogController::class, 'certicheck'])->name('catalog.certicheck');
Route::get('/samochody/{car:slug}/pdf', [PdfController::class, 'generate'])
    ->middleware('throttle:10,1')
    ->name('car.pdf');
Route::get('/o-nas', [PageController::class, 'about'])->name('about');
Route::get('/kontakt', [PageController::class, 'contact'])->name('contact');
Route::post('/kontakt', [PageController::class, 'contactSubmit'])->middleware('throttle:5,1')->name('contact.submit');
Route::post('/zapytanie', [InquiryController::class, 'store'])->middleware('throttle:10,1')->name('inquiry.store');
Route::get('/obserwowane', [FavoritesController::class, 'index'])->name('favorites');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// TEMP diagnostic — remove after incident resolved
Route::get('/_dbg', function (\Illuminate\Http\Request $request) {
    if ($request->header('X-Debug') !== hash('sha256', 'certicars-dbg-2026')) {
        abort(404);
    }
    $out = [];
    try {
        $out['db'] = \Illuminate\Support\Facades\DB::select('SELECT 1 as ok')[0]->ok ?? 'fail';
    } catch (\Throwable $e) { $out['db_err'] = $e->getMessage(); }
    try {
        $out['cars_count'] = \App\Models\Car::count();
    } catch (\Throwable $e) { $out['cars_err'] = $e->getMessage(); }
    try {
        $cars = \App\Models\Car::with(['brand', 'images'])->available()->paginate(12);
        $out['cars_paginate'] = 'ok (' . $cars->total() . ' total)';
    } catch (\Throwable $e) { $out['paginate_err'] = $e->getMessage(); }
    try {
        $out['cache'] = \Illuminate\Support\Facades\Cache::remember('_dbg_test', 10, fn() => 'hit');
    } catch (\Throwable $e) { $out['cache_err'] = $e->getMessage(); }
    // Try to render the catalog view to capture the actual view error
    try {
        $cars2 = \App\Models\Car::with(['brand', 'images'])->available()->paginate(12);
        [$brands2, $cats2, $fuels2] = \Illuminate\Support\Facades\Cache::remember('_dbg_filters', 10, fn() => [
            \App\Models\Brand::orderBy('name')->get(),
            \App\Models\Car::available()->whereNotNull('category')->distinct()->pluck('category'),
            \App\Models\Car::available()->whereNotNull('fuel_type')->distinct()->pluck('fuel_type'),
        ]);
        ob_start();
        echo view('catalog.index', ['cars' => $cars2, 'brands' => $brands2, 'categories' => $cats2, 'fuelTypes' => $fuels2])->render();
        ob_end_clean();
        $out['view_render'] = 'ok';
    } catch (\Throwable $e) {
        $out['view_err'] = get_class($e) . ': ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine();
    }
    // Get last error from log
    try {
        if (file_exists(storage_path('logs/laravel.log'))) {
            $lines = file(storage_path('logs/laravel.log'));
            // Find last [datetime] line (start of log entry)
            $lastEntry = '';
            for ($i = count($lines) - 1; $i >= 0; $i--) {
                $lastEntry = $lines[$i] . $lastEntry;
                if (preg_match('/^\[\d{4}-\d{2}-\d{2}/', $lines[$i]) && strlen($lastEntry) > 50) break;
            }
            $out['last_log'] = substr($lastEntry, 0, 2000);
        }
    } catch (\Throwable $e) { $out['log_err'] = $e->getMessage(); }
    return response()->json($out);
});

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
    Route::get('cars/{car}/pdf', [PdfController::class, 'generate'])->name('admin.cars.pdf');
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
