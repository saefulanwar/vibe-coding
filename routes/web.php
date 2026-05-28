<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Auth;

use App\Http\Middleware\EnsureProfileComplete;

use App\Livewire\LandingPage;

// Google SSO Routes
Route::get('/auth/google', [SocialiteController::class, 'redirect'])->name('sso.google.login');
Route::get('/auth/google/callback', [SocialiteController::class, 'callback'])->name('sso.google.callback');

// Auth redirection to Filament Login
Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');

// Locale Switcher Route for non-prefixed routes (e.g. admin login)
Route::get('/change-locale/{locale}', function ($locale) {
    if (in_array($locale, ['id', 'en'])) {
        session(['locale' => $locale]);
        cookie()->queue('locale', $locale, 60 * 24 * 365); // 1 year
    }
    return redirect()->back();
})->name('change-locale');

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => [ 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath' ]
], function() {
    Route::get('/', LandingPage::class)->name('home');
    Route::get('/courses/{slug}', \App\Livewire\CourseDetail::class)->name('course.detail');

    // Protected Student Portal routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/dashboard', [CourseController::class, 'dashboard'])->name('dashboard');
        Route::get('/courses/{course}/lessons/{lesson}', [CourseController::class, 'showLocalLesson'])->name('lessons.show');
        Route::post('/courses/{course}/reviews', [CourseController::class, 'storeReview'])->name('courses.reviews.store');

        // Simulated sandbox payment gateway views
        Route::get('/payment/mock/{reference}', [CheckoutController::class, 'showMockPaymentPage'])->name('payment.mock');
        Route::post('/payment/mock/{reference}/complete', [CheckoutController::class, 'completeMockPayment'])->name('payment.complete');

        // Transaction routes: gated by profile completeness
        Route::middleware([EnsureProfileComplete::class])->group(function () {
            Route::post('/checkout', [CheckoutController::class, 'checkout'])->name('checkout');
            Route::post('/courses/{course}/learn', [CourseController::class, 'startLearning'])->name('courses.learn');
        });
    });
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

// Public Certificate Verification
Route::get('/verify/{uuid}', [\App\Http\Controllers\VerifyCertificateController::class, 'show'])->name('verify.certificate');
