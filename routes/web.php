<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\MidtransWebhookController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController as EventAdminController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Organizer\AuthController as OrganizerAuthController;
use App\Http\Controllers\Organizer\DashboardController as OrganizerDashboardController;
use App\Http\Controllers\Organizer\EventController as OrganizerEventController;
use App\Http\Controllers\Organizer\CheckInController as OrganizerCheckInController;
use App\Http\Controllers\Admin\TenantController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
Route::post('/events/{event}/reviews', [ReviewController::class, 'store'])->name('reviews.store');

Route::get('/auth/google/redirect', [SocialAuthController::class, 'redirect'])->name('social.google.redirect');
Route::get('/auth/google/callback', [SocialAuthController::class, 'callback'])->name('social.google.callback');
Route::post('/logout', [SocialAuthController::class, 'logout'])->name('logout');

Route::get('/checkout/{event}', [CheckoutController::class, 'create'])->name('checkout.create');
Route::post('/checkout/{event}', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/payment/{order_id}', [CheckoutController::class, 'payment'])->name('checkout.payment');
Route::get('/success/{order_id}', [CheckoutController::class, 'success'])->name('checkout.success');

Route::post('/midtrans/callback', [MidtransWebhookController::class, 'handle'])->name('midtrans.callback');

Route::prefix('organizer')->name('organizer.')->group(function () {
    Route::get('/register', [OrganizerAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [OrganizerAuthController::class, 'register'])->name('register.post');
    Route::get('/login', [OrganizerAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [OrganizerAuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [OrganizerAuthController::class, 'logout'])->name('logout');

    Route::middleware(['auth', 'organizer'])->group(function () {
        Route::get('/dashboard', [OrganizerDashboardController::class, 'index'])->name('dashboard');
        Route::resource('events', OrganizerEventController::class)->except(['show']);
        Route::get('/check-in', [OrganizerCheckInController::class, 'index'])->name('check-in.index');
        Route::post('/check-in', [OrganizerCheckInController::class, 'verify'])->name('check-in.verify');
    });
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/transactions', [DashboardController::class, 'indexTransaction'])->name('transactions.index');
        Route::resource('events', EventAdminController::class);
        Route::get('/tenants', [TenantController::class, 'index'])->name('tenants.index');
        Route::patch('/tenants/{tenant}/approve', [TenantController::class, 'approve'])->name('tenants.approve');
        Route::patch('/tenants/{tenant}/reject', [TenantController::class, 'reject'])->name('tenants.reject');

        Route::get('/partners', [PartnerController::class, 'index'])->name('partners.index');
        Route::post('/partners', [PartnerController::class, 'store'])->name('partners.store');
        Route::put('/partners/{partner}', [PartnerController::class, 'update'])->name('partners.update');
        Route::delete('/partners/{partner}', [PartnerController::class, 'destroy'])->name('partners.destroy');

        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    });
});
