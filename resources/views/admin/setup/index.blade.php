@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-black text-white tracking-tight">System Core Setup</h1>
                <span class="px-2 py-0.5 rounded-full bg-slate-800 border border-slate-700 text-[10px] font-bold text-slate-300 uppercase tracking-wider">
                    Infrastructure & Branding
                </span>
            </div>
            <p class="text-xs text-slate-400 mt-1">Configure global application identity, asset logos, timezone, cron worker health, and realtime engines.</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="submit" form="setupForm" class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs shadow-lg shadow-purple-500/20 transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>Save Setup Settings</span>
            </button>
        </div>
    </div>

    <!-- System Requirements & Cron Checklist -->
    <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl space-y-3">
        <h2 class="text-xs font-bold uppercase tracking-wider text-purple-400">Background Services Health</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-between">
                <div>
                    <div class="text-xs font-bold text-white">Cron Daemon</div>
                    <div class="text-[10px] text-slate-500 font-mono">* * * * * php artisan schedule:run</div>
                </div>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/20">Operational</span>
            </div>

            <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-between">
                <div>
                    <div class="text-xs font-bold text-white">Campaign Queue Worker</div>
                    <div class="text-[10px] text-slate-500 font-mono">php artisan queue:work --daemon</div>
                </div>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/20">Active</span>
            </div>

            <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-between">
                <div>
                    <div class="text-xs font-bold text-white">Pusher Realtime Chat</div>
                    <div class="text-[10px] text-slate-500 font-mono">WebSockets Port 6001 / Cloud</div>
                </div>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/20">Configured</span>
            </div>
        </div>
    </div>

    <!-- Branding & Setup Form -->
    <form id="setupForm" method="POST" action="{{ route('admin.setup') }}" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @csrf
        <!-- Application Identity -->
        <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl space-y-4">
            <h2 class="text-sm font-bold text-white uppercase tracking-wider">Application Identity</h2>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Application Name</label>
                <input type="text" name="app_name" value="{{ config('app.name', 'VyapariChat') }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-purple-500">
                <span class="text-[10px] text-slate-500">Displayed on browser title, transactional emails, and invoices.</span>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Timezone</label>
                <select name="timezone" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-purple-500">
                    <option value="Asia/Kolkata" selected>Asia/Kolkata (IST +05:30)</option>
                    <option value="UTC">UTC (GMT +00:00)</option>
                    <option value="America/New_York">America/New_York (EST -05:00)</option>
                    <option value="Asia/Dubai">Asia/Dubai (GST +04:00)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Public Footer Copyright Text</label>
                <input type="text" name="footer_text" value="© 2026 VyapariChat Multi-Vendor Cloud. All rights reserved." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-purple-500">
            </div>
        </div>

        <!-- Logos & Visual Assets -->
        <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl space-y-4">
            <h2 class="text-sm font-bold text-white uppercase tracking-wider">Brand Asset Logos</h2>

            <div class="space-y-3">
                <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-white">Full Brand Logo</div>
                        <div class="text-[10px] text-slate-500">Navbar header brand (Suggested: 240x60 transparent PNG)</div>
                    </div>
                    <button type="button" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-purple-300 font-semibold text-xs border border-slate-700 transition">Upload File</button>
                </div>

                <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-white">Collapsed Sidebar Logo / Favicon</div>
                        <div class="text-[10px] text-slate-500">Square favicon / mobile app icon (64x64 PNG or ICO)</div>
                    </div>
                    <button type="button" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-purple-300 font-semibold text-xs border border-slate-700 transition">Upload File</button>
                </div>

                <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-white">Login Page Background Cover</div>
                        <div class="text-[10px] text-slate-500">Hero gradient or corporate backdrop for auth screens</div>
                    </div>
                    <button type="button" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-purple-300 font-semibold text-xs border border-slate-700 transition">Upload File</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
