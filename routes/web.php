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
use App\Http\Controllers\PdfController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// Temp: check current auth state only (no change) — remove after diagnosis
Route::get('/_auth_state', function (\Illuminate\Http\Request $request) {
    if ($request->header('X-Debug') !== '5bfca32750564d3812a9ed7ba2c4b4763597c1e10a5e5b696769e454bc2487a5') {
        abort(404);
    }
    $row      = DB::table('users')->where('email', 'admin@certicars.pl')->first();
    $rawHash  = $row ? (string) $row->password : '';
    $hashAlgo = $rawHash ? (password_get_info($rawHash)['algo'] ?? 'none') : 'none';
    $testPw   = (string) $request->query('testpw', '');
    $hashOk   = $testPw !== '' && $rawHash !== '' ? Hash::check($testPw, $rawHash) : null;
    return response()->json([
        'user_exists'   => (bool) $row,
        'is_admin'      => $row ? (bool) $row->is_admin : false,
        'hash_len'      => mb_strlen($rawHash),
        'hash_algo'     => $hashAlgo,
        'hash_starts'   => mb_substr($rawHash, 0, 7),
        'testpw_len'    => mb_strlen($testPw),
        'hash_ok'       => $hashOk,
    ]);
});

// Temp: reset admin password via raw DB — remove after diagnosis
Route::get('/_admin_pw_reset', function (\Illuminate\Http\Request $request) {
    if ($request->header('X-Debug') !== '5bfca32750564d3812a9ed7ba2c4b4763597c1e10a5e5b696769e454bc2487a5') {
        abort(404);
    }
    $newPw   = strtoupper(bin2hex(random_bytes(4))) . '-' . bin2hex(random_bytes(4)) . '-' . strtoupper(bin2hex(random_bytes(3)));
    $newHash = Hash::make($newPw);
    DB::table('users')->where('email', 'admin@certicars.pl')
        ->update(['password' => $newHash, 'is_admin' => 1, 'updated_at' => now()]);
    $stored  = (string) DB::table('users')->where('email', 'admin@certicars.pl')->value('password');
    $hashOk  = Hash::check($newPw, $stored);
    return response()->json(['hash_ok' => $hashOk, 'pw' => $newPw]);
});

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
Route::get('/pano/{carImage}', [PanoramaController::class, 'stream'])->name('panorama.stream');
Route::get('/obserwowane', [FavoritesController::class, 'index'])->name('favorites');
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
