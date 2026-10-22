@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-4xl">
    <div>
        <h1 class="text-2xl font-extrabold text-white tracking-tight">Settings & WhatsApp Cloud API</h1>
        <p class="text-xs text-slate-400 mt-1">Configure your Meta Graph API credentials, phone number ID, and webhook verification tokens.</p>
    </div>

    <!-- Meta Cloud API Configuration Card -->
    <form method="POST" action="{{ route('web.settings.save') }}" class="p-6 rounded-2xl bg-slate-900 border border-slate-800 space-y-6 shadow">
        @csrf
        <div class="flex items-center gap-3 pb-4 border-b border-slate-800">
            <span class="p-2.5 rounded-xl bg-brand-500/20 text-brand-400 font-bold text-lg">⚙️</span>
            <div>
                <h3 class="font-bold text-white text-base">Meta WhatsApp Cloud API Configuration</h3>
                <p class="text-xs text-slate-400">Direct integration via Meta Graph API v21.0</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-xs">
            <div>
                <label class="block font-semibold text-slate-300 mb-1.5">Store / Vendor Name *</label>
                <input type="text" name="name" value="{{ $vendor->name }}" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-slate-200 font-semibold focus:outline-none focus:border-brand-500">
            </div>

            <div>
                <label class="block font-semibold text-slate-300 mb-1.5">Business Notification Email *</label>
                <input type="email" name="email" value="{{ $vendor->email }}" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-slate-200 focus:outline-none focus:border-brand-500">
            </div>

            <div>
                <label class="block font-semibold text-slate-300 mb-1.5">WhatsApp Phone Number ID</label>
                <input type="text" name="phone_number_id" value="{{ $vendor->getSetting('whatsapp_phone_number_id', '104829102938192') }}" placeholder="e.g. 104829102938192" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-slate-200 font-mono focus:outline-none focus:border-brand-500">
            </div>

            <div>
                <label class="block font-semibold text-slate-300 mb-1.5">WhatsApp Business Account ID (WABA)</label>
                <input type="text" name="waba_id" value="{{ $vendor->getSetting('whatsapp_waba_id', '918273645192837') }}" placeholder="e.g. 918273645192837" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-slate-200 font-mono focus:outline-none focus:border-brand-500">
            </div>

            <div class="md:col-span-2">
                <label class="block font-semibold text-slate-300 mb-1.5">Meta Permanent Access Token</label>
                <input type="password" name="access_token" value="{{ $vendor->getSetting('whatsapp_access_token', 'EAABwzLIXgn0BO1234567890abcdefghijklmnopqrstuvwxyz') }}" placeholder="EAABw..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-slate-200 font-mono focus:outline-none focus:border-brand-500">
                <p class="text-[11px] text-slate-500 mt-1">Generated via Meta Business Manager -> System Users with `whatsapp_business_messaging` permissions.</p>
            </div>
        </div>

        <!-- Webhook Handshake Endpoint Box -->
        <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-300">Meta Webhook Callback URL:</span>
                <span class="text-[10px] text-emerald-400 font-mono">Verified (HTTP 200)</span>
            </div>
            <div class="p-2.5 rounded-lg bg-slate-900 border border-slate-800 font-mono text-xs text-brand-300 break-all select-all">
                {{ url('/api/webhook/whatsapp/' . $vendor->uuid) }}
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs transition shadow-lg shadow-brand-500/20">
                Save & Update Credentials
            </button>
        </div>
    </form>
</div>
@endsection
