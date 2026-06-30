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
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('members', Admin\MemberController::class);
    Route::resource('periods', Admin\PeriodController::class);
    Route::resource('divisions', Admin\DivisionController::class);
    Route::resource('categories', Admin\CategoryController::class);
    Route::resource('events', Admin\EventController::class);
    Route::resource('news', Admin\NewsController::class);
    Route::resource('metrics', Admin\MetricController::class)->except(['show', 'edit', 'update']);

    Route::get('/settings', [Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [Admin\SettingController::class, 'update'])->name('settings.update');

    Route::get('/account', [Admin\AccountController::class, 'edit'])->name('account.edit');
    Route::put('/account', [Admin\AccountController::class, 'update'])->name('account.update');
});
