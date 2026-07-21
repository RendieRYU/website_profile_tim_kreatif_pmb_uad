<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventApiController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\Admin;

// Public Routes
Route::get('/', [DashboardController::class, 'index'])->name('home');
Route::get('/kalender', [CalendarController::class, 'index'])->name('calendar');
Route::get('/tentang-kami', [AboutController::class, 'index'])->name('about');
Route::get('/portofolio', [PortfolioController::class, 'index'])->name('portfolio.index');
Route::get('/portofolio/anggota/{slug}', [PortfolioController::class, 'show'])->name('portfolio.show');
Route::get('/portofolio/{id}/pdf', [PortfolioController::class, 'exportPdf'])->name('portfolio.pdf');
Route::get('/berita/{slug}', [NewsController::class, 'show'])->name('news.show');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::get('/api/events/{year}/{month}', [EventApiController::class, 'getMonthEvents'])->name('api.events');

// Admin Routes
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    // Shared Routes
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/account', [Admin\AccountController::class, 'edit'])->name('account.edit');
    Route::put('/account', [Admin\AccountController::class, 'update'])->name('account.update');

    // Routes exclusively for Anggota
    Route::middleware(['anggota'])->group(function () {
        Route::resource('works', Admin\WorkController::class);
    });

    // Routes exclusively for Admin
    Route::middleware(['admin'])->group(function () {
        Route::resource('members', Admin\MemberController::class);
        Route::resource('periods', Admin\PeriodController::class);
        Route::resource('divisions', Admin\DivisionController::class);
        Route::resource('categories', Admin\CategoryController::class);
        Route::get('/events/export', [Admin\EventController::class, 'export'])->name('events.export');
        Route::resource('events', Admin\EventController::class);
        Route::resource('news', Admin\NewsController::class);
        Route::resource('metrics', Admin\MetricController::class)->except(['show', 'edit', 'update']);
        Route::get('/instagram-posts/export', [Admin\InstagramPostController::class, 'export'])->name('instagram-posts.export');
        Route::resource('instagram-posts', Admin\InstagramPostController::class);
        Route::get('/tiktok-posts/export', [Admin\TiktokPostController::class, 'export'])->name('tiktok-posts.export');
        Route::resource('tiktok-posts', Admin\TiktokPostController::class);
        Route::resource('admission-leaders', Admin\AdmissionLeaderController::class);
        Route::get('/settings', [Admin\SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [Admin\SettingController::class, 'update'])->name('settings.update');
    });
});
