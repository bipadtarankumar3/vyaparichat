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
    // 1. Dashboard
    Route::get('/', [\App\Http\Controllers\Admin\AdminVendorController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard', [\App\Http\Controllers\Admin\AdminVendorController::class, 'dashboard'])->name('console');

    // 2. User Plans (Auto Recurring & Manual/Prepaid)
    Route::get('/plans/subscriptions', [\App\Http\Controllers\Admin\AdminVendorController::class, 'plansSubscriptions'])->name('plans.subscriptions');
    Route::get('/plans/manual', [\App\Http\Controllers\Admin\AdminVendorController::class, 'plansManual'])->name('plans.manual');

    // 3. Users / Vendors Management & Impersonation
    Route::get('/vendors', [\App\Http\Controllers\Admin\AdminVendorController::class, 'index'])->name('vendors');
    Route::post('/vendors', [\App\Http\Controllers\Admin\AdminVendorController::class, 'store'])->name('vendors.store');
    Route::post('/vendors/{vendor}/status', [\App\Http\Controllers\Admin\AdminVendorController::class, 'toggleStatus'])->name('vendors.status');
    Route::post('/vendors/{vendor}/permissions', [\App\Http\Controllers\Admin\AdminVendorController::class, 'updatePermissions'])->name('vendors.permissions');
    Route::post('/vendors/{vendor}/impersonate', [\App\Http\Controllers\Admin\AdminVendorController::class, 'impersonate'])->name('vendors.impersonate');
    Route::post('/impersonate/leave', [\App\Http\Controllers\Admin\AdminVendorController::class, 'leaveImpersonation'])->name('impersonate.leave');

    // 4. Static / CMS Landing Pages
    Route::get('/pages', [\App\Http\Controllers\Admin\AdminVendorController::class, 'pages'])->name('pages');

    // 5. Languages & Translations
    Route::get('/languages', [\App\Http\Controllers\Admin\AdminVendorController::class, 'languages'])->name('languages');

    // 6. Setup & System Environment
    Route::get('/setup', [\App\Http\Controllers\Admin\AdminVendorController::class, 'setup'])->name('setup');

    // 7. Embedded Signup (Meta WhatsApp Cloud API Onboarding)
    Route::get('/embedded-signup', [\App\Http\Controllers\Admin\AdminVendorController::class, 'embeddedSignup'])->name('embedded-signup');

    // 8. Global System Settings (General, User, Currency, Payments, Plans, Email, Logins, Appearance)
    Route::get('/settings/{tab?}', [\App\Http\Controllers\Admin\AdminVendorController::class, 'settings'])->name('settings');
});
