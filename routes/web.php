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
Route::get('/templates', [DashboardController::class, 'templates'])->name('web.templates');
Route::get('/campaigns', [DashboardController::class, 'campaigns'])->name('web.campaigns');

// Contacts & Submenu
Route::get('/contacts', [DashboardController::class, 'contacts'])->name('web.contacts');
Route::get('/contacts/groups', [DashboardController::class, 'contactGroups'])->name('web.contacts.groups');
Route::get('/contacts/custom-fields', [DashboardController::class, 'customFields'])->name('web.contacts.custom-fields');

// Chatbot & Flow Maker
Route::get('/bot', [DashboardController::class, 'bot'])->name('web.bot');
Route::get('/bot/flows', [DashboardController::class, 'botFlows'])->name('web.bot.flows');

// Agents, QR Code, My Plan, Setup
Route::get('/agents', [DashboardController::class, 'agents'])->name('web.agents');
Route::post('/agents', [DashboardController::class, 'storeAgent'])->name('web.agents.store');
Route::get('/qr-code', [DashboardController::class, 'qrCode'])->name('web.qr-code');
Route::get('/my-plan', [DashboardController::class, 'billing'])->name('web.my-plan');
Route::get('/billing', [DashboardController::class, 'billing'])->name('web.billing');
Route::get('/settings', [DashboardController::class, 'settings'])->name('web.settings');
Route::get('/integrations', [DashboardController::class, 'integrations'])->name('web.integrations');

// Super Admin Central Console & Impersonation
Route::middleware(['auth', \App\Http\Middleware\SuperAdminMiddleware::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\AdminVendorController::class, 'index'])->name('dashboard');
    Route::get('/vendors', [\App\Http\Controllers\Admin\AdminVendorController::class, 'index'])->name('vendors');
    Route::post('/vendors', [\App\Http\Controllers\Admin\AdminVendorController::class, 'store'])->name('vendors.store');
    Route::post('/vendors/{vendor}/status', [\App\Http\Controllers\Admin\AdminVendorController::class, 'toggleStatus'])->name('vendors.status');
    Route::post('/vendors/{vendor}/permissions', [\App\Http\Controllers\Admin\AdminVendorController::class, 'updatePermissions'])->name('vendors.permissions');
    Route::post('/vendors/{vendor}/impersonate', [\App\Http\Controllers\Admin\AdminVendorController::class, 'impersonate'])->name('vendors.impersonate');
    Route::post('/impersonate/leave', [\App\Http\Controllers\Admin\AdminVendorController::class, 'leaveImpersonation'])->name('impersonate.leave');
});
