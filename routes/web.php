<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\Vendor as VendorWeb;
use App\Http\Controllers\Admin as AdminWeb;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes: VyapariChat Multi-Vendor Cloud Architecture
|--------------------------------------------------------------------------
| Cleanly divided into:
| 1. Guest Authentication Routes
| 2. Vendor Portal Routes (protected with auth and 'vendor' middleware)
| 3. Super Admin Central Console (protected with auth and 'admin' middleware)
| Each activity maps to its own dedicated Controller and activity View directory.
*/

// ==========================================
// 1. Guest Authentication
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==========================================
// 2. Vendor Portal Activities (Middleware: vendor)
// ==========================================
Route::middleware(['auth', 'vendor'])->group(function () {
    // Overview / Dashboard
    Route::get('/', [VendorWeb\DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [VendorWeb\DashboardController::class, 'index'])->name('dashboard');

    // Live Chat & Unified Team Inbox
    Route::get('/chat', [VendorWeb\ChatController::class, 'index'])->name('web.chat');

    // Contacts Management
    Route::get('/contacts', [VendorWeb\ContactController::class, 'index'])->name('web.contacts');
    Route::post('/contacts', [VendorWeb\ContactController::class, 'store'])->name('web.contacts.store');
    Route::post('/contacts/{contact}/update', [VendorWeb\ContactController::class, 'update'])->name('web.contacts.update');
    Route::post('/contacts/{contact}/delete', [VendorWeb\ContactController::class, 'destroy'])->name('web.contacts.delete');

    // Contact Groups / Segmentation
    Route::get('/contacts/groups', [VendorWeb\ContactGroupController::class, 'index'])->name('web.contacts.groups');
    Route::post('/contacts/groups', [VendorWeb\ContactGroupController::class, 'store'])->name('web.contacts.groups.store');
    Route::post('/contacts/groups/{group}/update', [VendorWeb\ContactGroupController::class, 'update'])->name('web.contacts.groups.update');
    Route::post('/contacts/groups/{group}/delete', [VendorWeb\ContactGroupController::class, 'destroy'])->name('web.contacts.groups.delete');

    // Contact Custom Fields
    Route::get('/contacts/custom-fields', [VendorWeb\CustomFieldController::class, 'index'])->name('web.contacts.custom-fields');
    Route::post('/contacts/custom-fields', [VendorWeb\CustomFieldController::class, 'store'])->name('web.contacts.custom-fields.store');
    Route::post('/contacts/custom-fields/{field}/update', [VendorWeb\CustomFieldController::class, 'update'])->name('web.contacts.custom-fields.update');
    Route::post('/contacts/custom-fields/{field}/delete', [VendorWeb\CustomFieldController::class, 'destroy'])->name('web.contacts.custom-fields.delete');

    // WhatsApp Approved Templates
    Route::get('/templates', [VendorWeb\TemplateController::class, 'index'])->name('web.templates');
    Route::post('/templates', [VendorWeb\TemplateController::class, 'store'])->name('web.templates.store');
    Route::post('/templates/{template}/delete', [VendorWeb\TemplateController::class, 'destroy'])->name('web.templates.delete');

    // Broadcast Campaigns
    Route::get('/campaigns', [VendorWeb\CampaignController::class, 'index'])->name('web.campaigns');
    Route::post('/campaigns', [VendorWeb\CampaignController::class, 'store'])->name('web.campaigns.store');
    Route::post('/campaigns/{campaign}/action/{action}', [VendorWeb\CampaignController::class, 'action'])->name('web.campaigns.action');
    Route::post('/campaigns/{campaign}/delete', [VendorWeb\CampaignController::class, 'destroy'])->name('web.campaigns.delete');

    // Chatbot Rules
    Route::get('/bot', [VendorWeb\BotRuleController::class, 'index'])->name('web.bot');
    Route::post('/bot/rules', [VendorWeb\BotRuleController::class, 'store'])->name('web.bot.rules.store');
    Route::post('/bot/rules/{rule}/update', [VendorWeb\BotRuleController::class, 'update'])->name('web.bot.rules.update');
    Route::post('/bot/rules/{rule}/delete', [VendorWeb\BotRuleController::class, 'destroy'])->name('web.bot.rules.delete');

    // Visual Bot Flows
    Route::get('/bot/flows', [VendorWeb\BotFlowController::class, 'index'])->name('web.bot.flows');
    Route::post('/bot/flows', [VendorWeb\BotFlowController::class, 'store'])->name('web.bot.flows.store');
    Route::post('/bot/flows/{flow}/update', [VendorWeb\BotFlowController::class, 'update'])->name('web.bot.flows.update');
    Route::post('/bot/flows/{flow}/delete', [VendorWeb\BotFlowController::class, 'destroy'])->name('web.bot.flows.delete');

    // Staff / Agents Management
    Route::get('/agents', [VendorWeb\AgentController::class, 'index'])->name('web.agents');
    Route::post('/agents', [VendorWeb\AgentController::class, 'store'])->name('web.agents.store');
    Route::post('/agents/{agent}/update', [VendorWeb\AgentController::class, 'update'])->name('web.agents.update');
    Route::post('/agents/{agent}/delete', [VendorWeb\AgentController::class, 'destroy'])->name('web.agents.delete');

    // QR Code, Billing, Integrations, and Settings
    Route::get('/qr-code', [VendorWeb\QrCodeController::class, 'index'])->name('web.qr-code');
    Route::get('/my-plan', [VendorWeb\BillingController::class, 'index'])->name('web.my-plan');
    Route::get('/billing', [VendorWeb\BillingController::class, 'index'])->name('web.billing');
    Route::get('/integrations', [VendorWeb\IntegrationController::class, 'index'])->name('web.integrations');
    Route::get('/settings', [VendorWeb\SettingController::class, 'index'])->name('web.settings');
    Route::post('/settings', [VendorWeb\SettingController::class, 'save'])->name('web.settings.save');
});

// ==========================================
// 3. Super Admin Central Console (Middleware: admin)
// ==========================================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Central Console Overview
    Route::get('/', [AdminWeb\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [AdminWeb\DashboardController::class, 'index'])->name('console');

    // User Plans (Auto Recurring & Manual Subscriptions)
    Route::get('/plans/subscriptions', [AdminWeb\PlanController::class, 'index'])->name('plans.subscriptions');
    Route::post('/plans', [AdminWeb\PlanController::class, 'store'])->name('plans.store');
    Route::post('/plans/{plan}/update', [AdminWeb\PlanController::class, 'update'])->name('plans.update');
    Route::post('/plans/{plan}/delete', [AdminWeb\PlanController::class, 'destroy'])->name('plans.delete');

    // Manual / Bank Wire Payment Approvals
    Route::get('/plans/manual', [AdminWeb\ManualPaymentController::class, 'index'])->name('plans.manual');
    Route::post('/manual-subscriptions/{manualRequest}/approve', [AdminWeb\ManualPaymentController::class, 'approve'])->name('manual-subscriptions.approve');
    Route::post('/manual-subscriptions/{manualRequest}/reject', [AdminWeb\ManualPaymentController::class, 'reject'])->name('manual-subscriptions.reject');

    // Vendor Management & Impersonation
    Route::get('/vendors', [AdminWeb\VendorController::class, 'index'])->name('vendors');
    Route::post('/vendors', [AdminWeb\VendorController::class, 'store'])->name('vendors.store');
    Route::post('/vendors/{vendor}/status', [AdminWeb\VendorController::class, 'toggleStatus'])->name('vendors.status');
    Route::post('/vendors/{vendor}/permissions', [AdminWeb\VendorController::class, 'updatePermissions'])->name('vendors.permissions');
    Route::post('/vendors/{vendor}/delete', [AdminWeb\VendorController::class, 'destroy'])->name('vendors.delete');
    Route::post('/vendors/{vendor}/impersonate', [AdminWeb\VendorController::class, 'impersonate'])->name('vendors.impersonate');
    Route::post('/impersonate/leave', [AdminWeb\VendorController::class, 'leaveImpersonation'])->name('impersonate.leave');

    // CMS & Legal Landing Pages
    Route::get('/pages', [AdminWeb\PageController::class, 'index'])->name('pages');
    Route::post('/pages', [AdminWeb\PageController::class, 'store'])->name('pages.store');
    Route::post('/pages/{page}/update', [AdminWeb\PageController::class, 'update'])->name('pages.update');
    Route::post('/pages/{page}/delete', [AdminWeb\PageController::class, 'destroy'])->name('pages.delete');

    // Languages & Localization Engine
    Route::get('/languages', [AdminWeb\LanguageController::class, 'index'])->name('languages');
    Route::post('/languages', [AdminWeb\LanguageController::class, 'store'])->name('languages.store');
    Route::post('/languages/{language}/default', [AdminWeb\LanguageController::class, 'setDefault'])->name('languages.default');
    Route::post('/languages/{language}/delete', [AdminWeb\LanguageController::class, 'destroy'])->name('languages.delete');

    // System Environment & Setup
    Route::get('/setup', [AdminWeb\SetupController::class, 'index'])->name('setup');
    Route::post('/setup', [AdminWeb\SetupController::class, 'save'])->name('setup.save');

    // Meta WhatsApp Cloud API Embedded Signup
    Route::get('/embedded-signup', [AdminWeb\EmbeddedSignupController::class, 'index'])->name('embedded-signup');
    Route::post('/embedded-signup', [AdminWeb\EmbeddedSignupController::class, 'save'])->name('embedded-signup.save');

    // Platform Global Settings Tabs
    Route::get('/settings/{tab?}', [AdminWeb\SettingController::class, 'index'])->name('settings');
    Route::post('/settings/{tab?}', [AdminWeb\SettingController::class, 'save'])->name('settings.save');
});
