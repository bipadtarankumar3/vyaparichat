@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-black text-white tracking-tight">Meta Embedded Signup</h1>
                <span class="px-2 py-0.5 rounded-full bg-blue-500/10 border border-blue-500/30 text-[10px] font-bold text-blue-400 uppercase tracking-wider flex items-center gap-1">
                    <svg class="w-3 h-3 text-blue-400" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    Meta WhatsApp Cloud API
                </span>
            </div>
            <p class="text-xs text-slate-400 mt-1">Configure your Facebook Developer App to let vendors connect their WhatsApp Business Accounts with 1-click popup.</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="submit" form="embeddedForm" class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs shadow-lg shadow-purple-500/20 transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>Save Meta App Credentials</span>
            </button>
        </div>
    </div>

    <!-- Explainer Alert Banner -->
    <div class="p-5 rounded-2xl bg-blue-500/10 border border-blue-500/30 text-blue-200 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-start gap-3">
            <div class="w-9 h-9 rounded-xl bg-blue-500/20 border border-blue-500/40 flex items-center justify-center text-blue-300 text-lg flex-shrink-0">
                🚀
            </div>
            <div>
                <h2 class="font-bold text-sm text-white">How Embedded Signup Works for Vendors</h2>
                <p class="text-xs text-blue-300/80 mt-0.5">When vendors click "Connect WhatsApp" in their dashboard, Meta opens a popup asking them to pick their Business Manager and phone number. Your App ID automatically receives their permanent WABA ID and Phone Number ID without manual copy-pasting.</p>
            </div>
        </div>
        <a href="https://developers.facebook.com/docs/whatsapp/embedded-signup" target="_blank" class="px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow transition flex-shrink-0 flex items-center gap-1">
            <span>Meta Developer Docs ↗</span>
        </a>
    </div>

    <!-- Embedded Signup Settings Form -->
    <form id="embeddedForm" method="POST" action="{{ route('admin.embedded-signup') }}" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @csrf
        <!-- App Credentials -->
        <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl space-y-4">
            <h2 class="text-sm font-bold text-white uppercase tracking-wider">Facebook Developer App</h2>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Meta App ID (Facebook App ID)</label>
                <input type="text" name="facebook_app_id" value="184920491823901" placeholder="e.g. 184920491823901" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-purple-500">
                <span class="text-[10px] text-slate-500">Obtained from Meta App Dashboard > App Settings > Basic.</span>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Meta App Secret</label>
                <input type="password" name="facebook_app_secret" value="••••••••••••••••••••••••" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-purple-500">
                <span class="text-[10px] text-slate-500">Used on the server-side to exchange authorization codes for permanent access tokens.</span>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Embedded Signup Configuration ID (Config ID)</label>
                <input type="text" name="facebook_config_id" value="918239102938102" placeholder="e.g. 918239102938102" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-purple-500">
                <span class="text-[10px] text-slate-500">Created in Meta App Dashboard > WhatsApp > Quickstart > Embedded Signup Features.</span>
            </div>
        </div>

        <!-- Webhook Configuration -->
        <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl space-y-4">
            <h2 class="text-sm font-bold text-white uppercase tracking-wider">Inbound Webhooks & System Token</h2>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Global Callback URL (Webhook URL)</label>
                <div class="flex items-center gap-2">
                    <input type="text" readonly value="{{ url('/api/v1/webhook/whatsapp') }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-emerald-400 font-mono focus:outline-none">
                    <button type="button" onclick="navigator.clipboard.writeText('{{ url('/api/v1/webhook/whatsapp') }}')" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-bold text-slate-300 border border-slate-700 transition">Copy</button>
                </div>
                <span class="text-[10px] text-slate-500">Paste this URL in Meta Dashboard > WhatsApp > Configuration > Callback URL.</span>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Webhook Verify Token</label>
                <div class="flex items-center gap-2">
                    <input type="text" name="webhook_verify_token" value="{{ env('WHATSAPP_WEBHOOK_VERIFY_TOKEN', 'vyaparichat_verify_secret_token_2026') }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-purple-500">
                </div>
                <span class="text-[10px] text-slate-500">Matched during Meta challenge validation handshake.</span>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">System User Permanent Access Token (Optional fallback)</label>
                <input type="password" name="system_user_token" value="EAAG0••••••••••••••••••••" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-purple-500">
                <span class="text-[10px] text-slate-500">Business Manager system user token with whatsapp_business_messaging permissions.</span>
            </div>
        </div>
    </form>
</div>
@endsection
