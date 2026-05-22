<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Auth;

use App\Livewire\LandingPage;

Route::get('/', LandingPage::class)->name('home');

// Google SSO Routes
Route::get('/auth/google', [SocialiteController::class, 'redirect'])->name('sso.google.login');
Route::get('/auth/google/callback', [SocialiteController::class, 'callback'])->name('sso.google.callback');

// Auth redirection to Filament Login
Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');

// Protected Student Portal routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [CourseController::class, 'dashboard'])->name('dashboard');
    Route::post('/checkout', [CheckoutController::class, 'checkout'])->name('checkout');
    Route::post('/courses/{course}/learn', [CourseController::class, 'startLearning'])->name('courses.learn');
    Route::get('/courses/{course}/lessons/{lesson}', [CourseController::class, 'showLocalLesson'])->name('lessons.show');

    // Simulated sandbox payment gateway views
    Route::get('/payment/mock/{reference}', [CheckoutController::class, 'showMockPaymentPage'])->name('payment.mock');
    Route::post('/payment/mock/{reference}/complete', [CheckoutController::class, 'completeMockPayment'])->name('payment.complete');
});

// Student Logout
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/admin/login');
})->name('logout');

// Webhook endpoint (CSRF-exempted in bootstrap/app.php)
Route::post('/webhook/payment', [WebhookController::class, 'handle'])->name('payment.webhook');

