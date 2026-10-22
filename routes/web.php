<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use Illuminate\Support\Facades\Route;

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Authenticated / Dashboard Dedicated Web Pages
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/', [DashboardController::class, 'index'])->name('home');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/chat', [DashboardController::class, 'chat'])->name('web.chat');
Route::get('/contacts', [DashboardController::class, 'contacts'])->name('web.contacts');
Route::get('/campaigns', [DashboardController::class, 'campaigns'])->name('web.campaigns');
Route::get('/templates', [DashboardController::class, 'templates'])->name('web.templates');
Route::get('/bot', [DashboardController::class, 'bot'])->name('web.bot');
Route::get('/integrations', [DashboardController::class, 'integrations'])->name('web.integrations');
Route::get('/billing', [DashboardController::class, 'billing'])->name('web.billing');
Route::get('/settings', [DashboardController::class, 'settings'])->name('web.settings');
