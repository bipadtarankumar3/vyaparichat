@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-black text-white tracking-tight">System Settings</h1>
                <span class="px-2 py-0.5 rounded-full bg-amber-500/10 border border-amber-500/30 text-[10px] font-bold text-amber-400 uppercase tracking-wider">
                    {{ ucfirst(str_replace('-', ' ', $tab ?? 'general')) }}
                </span>
            </div>
            <p class="text-xs text-slate-400 mt-1">Configure global application parameters, payment gateways, SMTP mail, and security policies.</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="submit" form="settingsForm" class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs shadow-lg shadow-purple-500/20 transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>Save Changes</span>
            </button>
        </div>
    </div>

    <!-- Horizontal Tabs Navigation -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1 border-b border-slate-800 text-xs font-semibold">
        <a href="{{ route('admin.settings', 'general') }}" class="px-3.5 py-2 rounded-xl transition {{ ($tab ?? 'general') === 'general' ? 'bg-purple-600 text-white font-bold shadow-md shadow-purple-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
            General
        </a>
        <a href="{{ route('admin.settings', 'user') }}" class="px-3.5 py-2 rounded-xl transition {{ ($tab ?? '') === 'user' ? 'bg-purple-600 text-white font-bold shadow-md shadow-purple-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
            User Registration
        </a>
        <a href="{{ route('admin.settings', 'currency') }}" class="px-3.5 py-2 rounded-xl transition {{ ($tab ?? '') === 'currency' ? 'bg-purple-600 text-white font-bold shadow-md shadow-purple-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
            Currency
        </a>
        <a href="{{ route('admin.settings', 'payment') }}" class="px-3.5 py-2 rounded-xl transition {{ ($tab ?? '') === 'payment' ? 'bg-purple-600 text-white font-bold shadow-md shadow-purple-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
            Payments & Gateways
        </a>
        <a href="{{ route('admin.settings', 'subscription-plans') }}" class="px-3.5 py-2 rounded-xl transition {{ ($tab ?? '') === 'subscription-plans' ? 'bg-purple-600 text-white font-bold shadow-md shadow-purple-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
            User Plans
        </a>
        <a href="{{ route('admin.settings', 'email') }}" class="px-3.5 py-2 rounded-xl transition {{ ($tab ?? '') === 'email' ? 'bg-purple-600 text-white font-bold shadow-md shadow-purple-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
            Email (SMTP)
        </a>
        <a href="{{ route('admin.settings', 'social-login') }}" class="px-3.5 py-2 rounded-xl transition {{ ($tab ?? '') === 'social-login' ? 'bg-purple-600 text-white font-bold shadow-md shadow-purple-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
            Logins (OAuth)
        </a>
        <a href="{{ route('admin.settings', 'misc') }}" class="px-3.5 py-2 rounded-xl transition {{ ($tab ?? '') === 'misc' ? 'bg-purple-600 text-white font-bold shadow-md shadow-purple-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
            Appearance
        </a>
    </div>

    <!-- Active Tab Form Body -->
    <form id="settingsForm" method="POST" action="{{ route('admin.settings', $tab) }}" class="space-y-6">
        @csrf

        @if(($tab ?? 'general') === 'general')
        <!-- 1. General Settings -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl space-y-4">
                <h2 class="text-sm font-bold text-white uppercase tracking-wider">Site Access & Maintenance</h2>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Public Registration</label>
                    <div class="flex items-center gap-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="allow_registration" value="1" checked class="text-purple-600 focus:ring-purple-500">
                            <span class="text-xs text-slate-300">Enabled (Open signup)</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="allow_registration" value="0" class="text-purple-600 focus:ring-purple-500">
                            <span class="text-xs text-slate-400">Disabled (Invite only)</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Maintenance Mode</label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="maintenance_mode" value="1" class="rounded bg-slate-950 border-slate-800 text-purple-600 focus:ring-purple-500">
                        <span class="text-xs text-slate-300">Activate Platform Maintenance (Only Super Admins can log in)</span>
                    </label>
                </div>
            </div>

            <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl space-y-4">
                <h2 class="text-sm font-bold text-white uppercase tracking-wider">Contact & Support</h2>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Support Email Address</label>
                    <input type="email" name="support_email" value="support@vyaparichat.com" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Support WhatsApp Helpline</label>
                    <input type="text" name="support_whatsapp" value="+91 99999 88888" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-purple-500">
                </div>
            </div>
        </div>

        @elseif($tab === 'user')
        <!-- 2. User Registration Settings -->
        <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl space-y-4 max-w-2xl">
            <h2 class="text-sm font-bold text-white uppercase tracking-wider">Tenant & User Authentication Rules</h2>

            <div class="space-y-3">
                <label class="flex items-center gap-2.5 cursor-pointer p-3 rounded-xl bg-slate-950 border border-slate-800">
                    <input type="checkbox" name="require_email_verification" value="1" checked class="rounded bg-slate-900 border-slate-700 text-purple-600 focus:ring-purple-500">
                    <div>
                        <div class="text-xs font-semibold text-slate-200">Require Email Verification</div>
                        <div class="text-[10px] text-slate-500">Send an activation link to new business signups before granting access.</div>
                    </div>
                </label>

                <label class="flex items-center gap-2.5 cursor-pointer p-3 rounded-xl bg-slate-950 border border-slate-800">
                    <input type="checkbox" name="require_terms" value="1" checked class="rounded bg-slate-900 border-slate-700 text-purple-600 focus:ring-purple-500">
                    <div>
                        <div class="text-xs font-semibold text-slate-200">Require Terms of Service Acceptance</div>
                        <div class="text-[10px] text-slate-500">Vendors must tick "I agree to Terms & Conditions" during registration.</div>
                    </div>
                </label>

                <label class="flex items-center gap-2.5 cursor-pointer p-3 rounded-xl bg-slate-950 border border-slate-800">
                    <input type="checkbox" name="allow_vendor_delete_account" value="1" class="rounded bg-slate-900 border-slate-700 text-purple-600 focus:ring-purple-500">
                    <div>
                        <div class="text-xs font-semibold text-slate-200">Allow Self-Account Deletion</div>
                        <div class="text-[10px] text-slate-500">Let tenant business owners request permanent GDPR data removal.</div>
                    </div>
                </label>
            </div>
        </div>

        @elseif($tab === 'currency')
        <!-- 3. Currency Settings -->
        <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl space-y-4 max-w-2xl">
            <h2 class="text-sm font-bold text-white uppercase tracking-wider">Global Currency & Formatting</h2>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Currency Code</label>
                    <select name="currency_code" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-purple-500">
                        <option value="INR" selected>INR - Indian Rupee (₹)</option>
                        <option value="USD">USD - US Dollar ($)</option>
                        <option value="EUR">EUR - Euro (€)</option>
                        <option value="GBP">GBP - British Pound (£)</option>
                        <option value="AED">AED - UAE Dirham (د.إ)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Currency Symbol</label>
                    <input type="text" name="currency_symbol" value="₹" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white font-bold focus:outline-none focus:border-purple-500">
                </div>
            </div>
        </div>

        @elseif($tab === 'payment')
        <!-- 4. Payment Gateways -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Razorpay Gateway -->
            <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-sky-400"></span>
                        Razorpay Gateway (UPI / Cards / NetBanking)
                    </h2>
                    <label class="flex items-center gap-1.5 cursor-pointer">
                        <input type="checkbox" name="enable_razorpay" value="1" checked class="rounded bg-slate-950 border-slate-700 text-purple-600 focus:ring-purple-500">
                        <span class="text-xs font-bold text-emerald-400">Enabled</span>
                    </label>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Razorpay Key ID</label>
                    <input type="text" name="razorpay_key_id" value="rzp_live_98129038102" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Razorpay Key Secret</label>
                    <input type="password" name="razorpay_key_secret" value="••••••••••••••••••••" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Webhook Secret</label>
                    <input type="password" name="razorpay_webhook_secret" value="••••••••••••••••••••" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-purple-500">
                </div>
            </div>

            <!-- Stripe Gateway -->
            <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-indigo-400"></span>
                        Stripe Recurring Subscriptions
                    </h2>
                    <label class="flex items-center gap-1.5 cursor-pointer">
                        <input type="checkbox" name="enable_stripe" value="1" checked class="rounded bg-slate-950 border-slate-700 text-purple-600 focus:ring-purple-500">
                        <span class="text-xs font-bold text-emerald-400">Enabled</span>
                    </label>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Stripe Publishable Key</label>
                    <input type="text" name="stripe_publishable_key" value="pk_live_89123081923" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Stripe Secret Key</label>
                    <input type="password" name="stripe_secret_key" value="••••••••••••••••••••" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Stripe Webhook Secret</label>
                    <input type="password" name="stripe_webhook_secret" value="••••••••••••••••••••" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-purple-500">
                </div>
            </div>
        </div>

        @elseif($tab === 'subscription-plans')
        <!-- 5. User Plans Configuration -->
        <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-white uppercase tracking-wider">Subscription Tiers & Quota Limits</h2>
                    <p class="text-xs text-slate-400">Define pricing tiers, message caps, and contact quotas available for businesses.</p>
                </div>
                <a href="{{ route('admin.plans.subscriptions') }}" class="text-xs font-bold text-purple-400 hover:text-purple-300">View Active Subscribers →</a>
            </div>

            <div class="space-y-4">
                @foreach($plans as $plan)
                <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-sm text-white">{{ $plan->title }}</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-purple-500/10 text-purple-300 border border-purple-500/20">
                                ₹{{ number_format($plan->price_monthly) }} / mo
                            </span>
                        </div>
                        <div class="text-xs text-slate-400 mt-1">
                            {{ number_format($plan->max_messages_monthly) }} msgs • {{ number_format($plan->max_contacts) }} contacts • {{ $plan->max_team_members ?? 5 }} team seats
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs border border-slate-700 transition">Edit Quotas</button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        @elseif($tab === 'email')
        <!-- 6. Email (SMTP) Settings -->
        <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl space-y-4 max-w-3xl">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-white uppercase tracking-wider">SMTP Mail Server Settings</h2>
                    <p class="text-xs text-slate-400">Used for transactional notifications, welcome emails, password resets, and invoice receipts.</p>
                </div>
                <button type="button" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-purple-300 font-semibold text-xs border border-slate-700 transition">Send Test Email</button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Mail Host (SMTP Host)</label>
                    <input type="text" name="mail_host" value="smtp.sendgrid.net" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Mail Port</label>
                    <input type="text" name="mail_port" value="587" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Username / API Key</label>
                    <input type="text" name="mail_username" value="apikey" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Password</label>
                    <input type="password" name="mail_password" value="••••••••••••••••••••" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Encryption Protocol</label>
                    <select name="mail_encryption" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-purple-500">
                        <option value="tls" selected>TLS (Recommended)</option>
                        <option value="ssl">SSL</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">From Email Address</label>
                    <input type="email" name="mail_from_address" value="noreply@vyaparichat.com" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-purple-500">
                </div>
            </div>
        </div>

        @elseif($tab === 'social-login')
        <!-- 7. Social Logins (Google & Facebook) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-bold text-white uppercase tracking-wider">Google OAuth 2.0</h2>
                    <span class="text-xs font-bold text-emerald-400">Enabled</span>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Google Client ID</label>
                    <input type="text" name="google_client_id" value="981293810293.apps.googleusercontent.com" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Google Client Secret</label>
                    <input type="password" name="google_client_secret" value="••••••••••••••••••••" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-purple-500">
                </div>
            </div>

            <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-bold text-white uppercase tracking-wider">Facebook Login</h2>
                    <span class="text-xs font-bold text-slate-400">Disabled</span>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Facebook App ID</label>
                    <input type="text" name="fb_app_id" value="" placeholder="Optional Facebook App ID" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Facebook App Secret</label>
                    <input type="password" name="fb_app_secret" value="" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-purple-500">
                </div>
            </div>
        </div>

        @elseif($tab === 'misc')
        <!-- 8. Appearance / Misc -->
        <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl space-y-4 max-w-3xl">
            <h2 class="text-sm font-bold text-white uppercase tracking-wider">Appearance & Custom Code Injections</h2>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Custom CSS Styling</label>
                <textarea rows="4" name="custom_css" placeholder="/* Custom CSS overrides */" class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-xs text-purple-300 font-mono focus:outline-none focus:border-purple-500"></textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Header Scripts (Google Analytics / GTM / Meta Pixel)</label>
                <textarea rows="4" name="header_scripts" placeholder="<!-- Header script tags -->" class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-xs text-slate-300 font-mono focus:outline-none focus:border-purple-500"></textarea>
            </div>
        </div>
        @endif
    </form>
</div>
@endsection
