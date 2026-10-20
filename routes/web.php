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
Route::get('/projek', [\App\Http\Controllers\PublicProjectController::class, 'index'])->name('projects.public');
Route::get('/catatan', [\App\Http\Controllers\PublicNoteController::class, 'index'])->name('notes.public');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::get('/api/events/{year}/{month}', [EventApiController::class, 'getMonthEvents'])->name('api.events');
Route::post('/api/events/{event}/update-link', [EventApiController::class, 'updateLink'])->name('api.events.update-link');
Route::post('/api/verify-public-pin', [\App\Http\Controllers\PublicAccessController::class, 'verifyPin'])->name('api.verify-public-pin');

// Admin Routes
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    // Shared Routes
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/account', [Admin\AccountController::class, 'edit'])->name('account.edit');
    Route::put('/account', [Admin\AccountController::class, 'update'])->name('account.update');
    Route::put('/account/members/{id}', [Admin\AccountController::class, 'updateMemberAccount'])->name('account.update-member');

    // Notes Routes
    Route::post('/notes/folders', [Admin\NoteController::class, 'storeFolder'])->name('notes.folders.store');
    Route::delete('/notes/folders/{folder}', [Admin\NoteController::class, 'destroyFolder'])->name('notes.folders.destroy');
    Route::post('/notes/{note}/toggle-pin', [Admin\NoteController::class, 'togglePin'])->name('notes.toggle-pin');
    Route::post('/notes/{note}/move', [Admin\NoteController::class, 'moveNote'])->name('notes.move');
    Route::post('/notes/reorder', [Admin\NoteController::class, 'reorder'])->name('notes.reorder');
    Route::resource('notes', Admin\NoteController::class);

    // Project Kanban Board Routes
    Route::get('/projects', [Admin\ProjectController::class, 'index'])->name('projects.index');
    Route::patch('/projects/{event}/status', [Admin\ProjectController::class, 'updateStatus'])->name('projects.update-status');

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
        Route::patch('/events/{event}/move', [Admin\EventController::class, 'move'])->name('events.move');
        Route::post('/events/quick-store', [Admin\EventController::class, 'quickStore'])->name('events.quick-store');
        Route::patch('/events/{event}/inline-update', [Admin\EventController::class, 'inlineUpdate'])->name('events.inline-update');
        Route::post('/events/columns', [Admin\EventController::class, 'addColumn'])->name('events.columns.store');
        Route::delete('/events/columns/{columnId}', [Admin\EventController::class, 'deleteColumn'])->name('events.columns.destroy');
        Route::post('/events/columns/order', [Admin\EventController::class, 'updateColumnOrder'])->name('events.columns.order');
        Route::resource('events', Admin\EventController::class);
        Route::resource('news', Admin\NewsController::class);
        Route::resource('metrics', Admin\MetricController::class)->except(['show', 'edit', 'update']);
        Route::get('/instagram-posts/export', [Admin\InstagramPostController::class, 'export'])->name('instagram-posts.export');
        Route::post('/instagram-posts/quick-store', [Admin\InstagramPostController::class, 'quickStore'])->name('instagram-posts.quick-store');
        Route::patch('/instagram-posts/{instagramPost}/inline-update', [Admin\InstagramPostController::class, 'inlineUpdate'])->name('instagram-posts.inline-update');
        Route::post('/instagram-posts/columns', [Admin\InstagramPostController::class, 'addColumn'])->name('instagram-posts.columns.store');
        Route::delete('/instagram-posts/columns/{columnId}', [Admin\InstagramPostController::class, 'deleteColumn'])->name('instagram-posts.columns.destroy');
        Route::post('/instagram-posts/columns/order', [Admin\InstagramPostController::class, 'updateColumnOrder'])->name('instagram-posts.columns.order');
        Route::resource('instagram-posts', Admin\InstagramPostController::class);
        Route::get('/tiktok-posts/export', [Admin\TiktokPostController::class, 'export'])->name('tiktok-posts.export');
        Route::post('/tiktok-posts/quick-store', [Admin\TiktokPostController::class, 'quickStore'])->name('tiktok-posts.quick-store');
        Route::patch('/tiktok-posts/{tiktokPost}/inline-update', [Admin\TiktokPostController::class, 'inlineUpdate'])->name('tiktok-posts.inline-update');
        Route::post('/tiktok-posts/columns', [Admin\TiktokPostController::class, 'addColumn'])->name('tiktok-posts.columns.store');
        Route::delete('/tiktok-posts/columns/{columnId}', [Admin\TiktokPostController::class, 'deleteColumn'])->name('tiktok-posts.columns.destroy');
        Route::post('/tiktok-posts/columns/order', [Admin\TiktokPostController::class, 'updateColumnOrder'])->name('tiktok-posts.columns.order');
        Route::resource('tiktok-posts', Admin\TiktokPostController::class);
        Route::resource('admission-leaders', Admin\AdmissionLeaderController::class);
        Route::get('/settings', [Admin\SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [Admin\SettingController::class, 'update'])->name('settings.update');
    });
});
