@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Top Welcome Banner -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-brand-700 via-emerald-800 to-slate-900 p-6 sm:p-8 border border-brand-500/30 shadow-2xl">
        <div class="relative z-10 max-w-2xl">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-brand-500/20 text-emerald-200 border border-brand-400/30 mb-3">
                🚀 Next-Gen WhatsApp Engine
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                Welcome to <span class="text-brand-300">{{ $vendor->name }}</span>
            </h1>
            <p class="text-sm sm:text-base text-emerald-100/80 mt-2 leading-relaxed">
                Direct Meta Graph API v20 connection with instant webhooks, broadcast dispatching, and AI-powered customer engagement.
            </p>
            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('web.chat') }}" class="px-4 py-2.5 rounded-xl bg-white text-slate-950 font-bold text-sm shadow hover:bg-slate-100 transition flex items-center gap-2">
                    <span>Open Live Inbox</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
                <a href="{{ route('web.campaigns') }}" class="px-4 py-2.5 rounded-xl bg-emerald-900/60 text-white font-semibold text-sm border border-emerald-500/30 hover:bg-emerald-800/60 transition">
                    New Broadcast
                </a>
                <a href="{{ route('web.templates') }}" class="px-4 py-2.5 rounded-xl bg-slate-900/70 text-slate-200 font-semibold text-sm border border-slate-700 hover:bg-slate-800 transition">
                    Sync Templates
                </a>
                <a href="{{ route('web.contacts') }}" class="px-4 py-2.5 rounded-xl bg-slate-900/70 text-slate-200 font-semibold text-sm border border-slate-700 hover:bg-slate-800 transition">
                    Manage Audience
                </a>
            </div>
        </div>
        <!-- Decorative Background Glow -->
        <div class="absolute -right-8 -bottom-12 w-64 h-64 bg-brand-500/10 rounded-full blur-3xl pointer-events-none"></div>
    </div>

    <!-- Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-slate-700 transition">
            <div class="flex items-center justify-between text-slate-400 text-xs font-semibold uppercase tracking-wider">
                <span>Total Contacts</span>
                <span class="p-2 rounded-lg bg-emerald-500/10 text-emerald-400">👥</span>
            </div>
            <div class="text-3xl font-extrabold text-white mt-2">{{ number_format($metrics['total_contacts']) }}</div>
            <div class="text-xs text-slate-400 mt-2 flex items-center gap-1.5">
                <span class="text-emerald-400 font-semibold">Active CRM Audience</span>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-slate-700 transition">
            <div class="flex items-center justify-between text-slate-400 text-xs font-semibold uppercase tracking-wider">
                <span>Messages Sent</span>
                <span class="p-2 rounded-lg bg-blue-500/10 text-blue-400">📤</span>
            </div>
            <div class="text-3xl font-extrabold text-white mt-2">{{ number_format($metrics['total_messages_sent']) }}</div>
            <div class="text-xs text-slate-400 mt-2 flex items-center gap-1.5">
                <span class="text-blue-400 font-semibold">Cloud API Outbound</span>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-slate-700 transition">
            <div class="flex items-center justify-between text-slate-400 text-xs font-semibold uppercase tracking-wider">
                <span>Messages Inbound</span>
                <span class="p-2 rounded-lg bg-purple-500/10 text-purple-400">📥</span>
            </div>
            <div class="text-3xl font-extrabold text-white mt-2">{{ number_format($metrics['total_messages_received']) }}</div>
            <div class="text-xs text-slate-400 mt-2 flex items-center gap-1.5">
                <span class="text-purple-400 font-semibold">Customer Replies</span>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-slate-700 transition">
            <div class="flex items-center justify-between text-slate-400 text-xs font-semibold uppercase tracking-wider">
                <span>Approved Templates</span>
                <span class="p-2 rounded-lg bg-amber-500/10 text-amber-400">📋</span>
            </div>
            <div class="text-3xl font-extrabold text-white mt-2">{{ number_format($metrics['approved_templates']) }}</div>
            <div class="text-xs text-slate-400 mt-2 flex items-center gap-1.5">
                <span class="text-emerald-400 font-semibold">Meta Verified HSM</span>
            </div>
        </div>
    </div>

    <!-- Quick Navigation Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('web.chat') }}" class="group p-5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-brand-500/40 transition flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="p-2 rounded-xl bg-brand-500/10 text-brand-400 text-xl">💬</span>
                    <span class="text-xs font-bold text-brand-400 group-hover:translate-x-1 transition">Open &rarr;</span>
                </div>
                <h3 class="font-bold text-white text-base">WhatsApp Live Chat</h3>
                <p class="text-xs text-slate-400 mt-1">Multi-agent inbox, customer conversations, and AI Bot toggling.</p>
            </div>
        </a>

        <a href="{{ route('web.campaigns') }}" class="group p-5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-blue-500/40 transition flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="p-2 rounded-xl bg-blue-500/10 text-blue-400 text-xl">📢</span>
                    <span class="text-xs font-bold text-blue-400 group-hover:translate-x-1 transition">Launch &rarr;</span>
                </div>
                <h3 class="font-bold text-white text-base">Broadcast Campaigns</h3>
                <p class="text-xs text-slate-400 mt-1">Scheduled or instant bulk campaigns with delivery and read rates.</p>
            </div>
        </a>

        <a href="{{ route('web.bot') }}" class="group p-5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-purple-500/40 transition flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="p-2 rounded-xl bg-purple-500/10 text-purple-400 text-xl">🤖</span>
                    <span class="text-xs font-bold text-purple-400 group-hover:translate-x-1 transition">Configure &rarr;</span>
                </div>
                <h3 class="font-bold text-white text-base">Bot & AI Automations</h3>
                <p class="text-xs text-slate-400 mt-1">Priority auto-responders, keyword routing, and live dry-run simulator.</p>
            </div>
        </a>
    </div>

    <!-- Quick Integration Webhook Status -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800">
            <h3 class="font-bold text-white text-base">Meta Webhook Handshake</h3>
            <p class="text-xs text-slate-400 mt-1">Status callback for real-time messages and delivery ticks.</p>
            <div class="mt-3 p-3 rounded-xl bg-slate-950 font-mono text-xs text-slate-300 break-all border border-slate-800 flex items-center justify-between">
                <span>{{ url('/api/webhook/whatsapp/' . $vendor->uuid) }}</span>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800">
            <h3 class="font-bold text-white text-base">Shopify E-Commerce</h3>
            <p class="text-xs text-slate-400 mt-1">Automatic order confirmation & tracking dispatch.</p>
            <div class="mt-3 p-3 rounded-xl bg-slate-950 font-mono text-xs text-slate-300 break-all border border-slate-800 flex items-center justify-between">
                <span>{{ url('/api/webhook/ecommerce/' . $vendor->uuid . '/shopify/orders-create') }}</span>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800">
            <h3 class="font-bold text-white text-base">WooCommerce Webhook</h3>
            <p class="text-xs text-slate-400 mt-1">Real-time status notifications for WordPress stores.</p>
            <div class="mt-3 p-3 rounded-xl bg-slate-950 font-mono text-xs text-slate-300 break-all border border-slate-800 flex items-center justify-between">
                <span>{{ url('/api/webhook/ecommerce/' . $vendor->uuid . '/woocommerce/orders') }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
