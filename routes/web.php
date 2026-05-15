<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\Auth\SocialiteController;

Route::get('/auth/google', [SocialiteController::class, 'redirect'])->name('sso.google.login');
Route::get('/auth/google/callback', [SocialiteController::class, 'callback'])->name('sso.google.callback');
