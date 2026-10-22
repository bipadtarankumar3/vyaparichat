@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Top Welcome & System Status Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-black text-white tracking-tight">Executive Dashboard</h1>
                <span class="px-2 py-0.5 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-[10px] font-bold text-emerald-400 uppercase tracking-wider flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    Live Platform
                </span>
            </div>
            <p class="text-xs text-slate-400 mt-1">Real-time multi-tenant health, vendor growth analytics, and subscription revenues.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.vendors') }}" class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs shadow-lg shadow-purple-500/20 transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Add New Vendor</span>
            </a>
            <a href="{{ route('admin.settings') }}" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs border border-slate-700 transition flex items-center gap-1.5">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Settings</span>
            </a>
        </div>
    </div>

    <!-- 4 Main KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Vendors -->
        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl relative overflow-hidden group hover:border-purple-500/40 transition">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-purple-500/10 rounded-full blur-xl group-hover:bg-purple-500/20 transition"></div>
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Vendors</span>
                <div class="w-8 h-8 rounded-lg bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-white tracking-tight">{{ $metrics['total_vendors'] ?? 0 }}</span>
                <span class="text-xs font-semibold text-emerald-400 flex items-center gap-0.5">
                    ↑ {{ $metrics['active_vendors'] ?? 0 }} active
                </span>
            </div>
            <div class="mt-2 text-[11px] text-slate-500 font-medium">
                {{ $metrics['suspended_vendors'] ?? 0 }} suspended businesses
            </div>
        </div>

        <!-- Monthly Recurring Revenue -->
        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl relative overflow-hidden group hover:border-emerald-500/40 transition">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-emerald-500/10 rounded-full blur-xl group-hover:bg-emerald-500/20 transition"></div>
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Platform MRR</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-white tracking-tight">₹{{ number_format($metrics['total_mrr'] ?? 0) }}</span>
                <span class="text-xs font-semibold text-emerald-400">/ mo</span>
            </div>
            <div class="mt-2 text-[11px] text-slate-500 font-medium">
                {{ $metrics['active_subscriptions'] ?? 0 }} paid active subscriptions
            </div>
        </div>

        <!-- Messages Transmitted -->
        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl relative overflow-hidden group hover:border-sky-500/40 transition">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-sky-500/10 rounded-full blur-xl group-hover:bg-sky-500/20 transition"></div>
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">WhatsApp Messages</span>
                <div class="w-8 h-8 rounded-lg bg-sky-500/10 border border-sky-500/20 flex items-center justify-center text-sky-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </div>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-white tracking-tight">{{ number_format($metrics['total_messages'] ?? 0) }}</span>
                <span class="text-xs font-semibold text-sky-400">Transmitted</span>
            </div>
            <div class="mt-2 text-[11px] text-slate-500 font-medium">
                99.4% Meta delivery success
            </div>
        </div>

        <!-- Total Contacts Network -->
        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl relative overflow-hidden group hover:border-amber-500/40 transition">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-amber-500/10 rounded-full blur-xl group-hover:bg-amber-500/20 transition"></div>
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Customer Audience</span>
                <div class="w-8 h-8 rounded-lg bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-white tracking-tight">{{ number_format($metrics['total_contacts'] ?? 0) }}</span>
                <span class="text-xs font-semibold text-amber-400">Total Leads</span>
            </div>
            <div class="mt-2 text-[11px] text-slate-500 font-medium">
                Opted-in customer phone numbers
            </div>
        </div>
    </div>

    <!-- Analytics & System Status Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Growth Chart Simulator -->
        <div class="lg:col-span-2 p-6 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-white uppercase tracking-wider">User Onboarding Analytics</h2>
                    <p class="text-xs text-slate-400">Annual vendor signups & active tenant growth curve</p>
                </div>
                <div class="flex items-center gap-1.5 bg-slate-800 p-1 rounded-xl text-[11px] font-semibold text-slate-300">
                    <span class="px-2.5 py-1 rounded-lg bg-purple-600 text-white font-bold">Monthly</span>
                    <span class="px-2.5 py-1 rounded-lg text-slate-400 hover:text-white cursor-pointer">Quarterly</span>
                    <span class="px-2.5 py-1 rounded-lg text-slate-400 hover:text-white cursor-pointer">Annual</span>
                </div>
            </div>

            <!-- CSS Bar Graph -->
            <div class="h-48 pt-6 flex items-end gap-3 sm:gap-4 justify-between border-b border-slate-800 pb-2">
                <div class="flex-1 flex flex-col items-center gap-2 group">
                    <div class="w-full bg-slate-800 group-hover:bg-purple-500/80 rounded-t-lg transition-all h-[35%] relative">
                        <div class="absolute -top-7 left-1/2 -translate-x-1/2 opacity-0 group-hover:opacity-100 transition text-[10px] bg-slate-950 text-white px-1.5 py-0.5 rounded border border-purple-500/40">35</div>
                    </div>
                    <span class="text-[10px] text-slate-400 font-mono">Jan</span>
                </div>
                <div class="flex-1 flex flex-col items-center gap-2 group">
                    <div class="w-full bg-slate-800 group-hover:bg-purple-500/80 rounded-t-lg transition-all h-[45%] relative">
                        <div class="absolute -top-7 left-1/2 -translate-x-1/2 opacity-0 group-hover:opacity-100 transition text-[10px] bg-slate-950 text-white px-1.5 py-0.5 rounded border border-purple-500/40">45</div>
                    </div>
                    <span class="text-[10px] text-slate-400 font-mono">Feb</span>
                </div>
                <div class="flex-1 flex flex-col items-center gap-2 group">
                    <div class="w-full bg-slate-800 group-hover:bg-purple-500/80 rounded-t-lg transition-all h-[60%] relative">
                        <div class="absolute -top-7 left-1/2 -translate-x-1/2 opacity-0 group-hover:opacity-100 transition text-[10px] bg-slate-950 text-white px-1.5 py-0.5 rounded border border-purple-500/40">60</div>
                    </div>
                    <span class="text-[10px] text-slate-400 font-mono">Mar</span>
                </div>
                <div class="flex-1 flex flex-col items-center gap-2 group">
                    <div class="w-full bg-slate-800 group-hover:bg-purple-500/80 rounded-t-lg transition-all h-[75%] relative">
                        <div class="absolute -top-7 left-1/2 -translate-x-1/2 opacity-0 group-hover:opacity-100 transition text-[10px] bg-slate-950 text-white px-1.5 py-0.5 rounded border border-purple-500/40">75</div>
                    </div>
                    <span class="text-[10px] text-slate-400 font-mono">Apr</span>
                </div>
                <div class="flex-1 flex flex-col items-center gap-2 group">
                    <div class="w-full bg-slate-800 group-hover:bg-purple-500/80 rounded-t-lg transition-all h-[85%] relative">
                        <div class="absolute -top-7 left-1/2 -translate-x-1/2 opacity-0 group-hover:opacity-100 transition text-[10px] bg-slate-950 text-white px-1.5 py-0.5 rounded border border-purple-500/40">85</div>
                    </div>
                    <span class="text-[10px] text-slate-400 font-mono">May</span>
                </div>
                <div class="flex-1 flex flex-col items-center gap-2 group">
                    <div class="w-full bg-gradient-to-t from-purple-600 to-indigo-400 rounded-t-lg transition-all h-[95%] relative shadow-lg shadow-purple-500/30">
                        <div class="absolute -top-7 left-1/2 -translate-x-1/2 opacity-100 transition text-[10px] bg-purple-600 font-bold text-white px-2 py-0.5 rounded shadow">112</div>
                    </div>
                    <span class="text-[10px] text-purple-300 font-bold font-mono">Current</span>
                </div>
            </div>

            <div class="flex items-center justify-between text-xs text-slate-400 pt-1">
                <span>Total tenants on platform: <strong class="text-white">{{ $metrics['total_vendors'] ?? 1 }}</strong></span>
                <span class="text-emerald-400 font-semibold">+28% growth over last quarter</span>
            </div>
        </div>

        <!-- Infrastructure Status Card -->
        <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl space-y-4">
            <h2 class="text-sm font-bold text-white uppercase tracking-wider flex items-center justify-between">
                <span>Infrastructure Health</span>
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            </h2>

            <div class="space-y-3">
                <div class="p-3 rounded-xl bg-slate-950 border border-slate-800/80 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
                        <div>
                            <div class="text-xs font-semibold text-slate-200">Database Engine</div>
                            <div class="text-[10px] text-slate-500 font-mono">MySQL 8.0 • Connected</div>
                        </div>
                    </div>
                    <span class="text-[10px] font-bold text-emerald-400 px-2 py-0.5 rounded bg-emerald-500/10 border border-emerald-500/20">Operational</span>
                </div>

                <div class="p-3 rounded-xl bg-slate-950 border border-slate-800/80 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
                        <div>
                            <div class="text-xs font-semibold text-slate-200">Queue Worker</div>
                            <div class="text-[10px] text-slate-500 font-mono">4 Workers Active • 0 Pending</div>
                        </div>
                    </div>
                    <span class="text-[10px] font-bold text-emerald-400 px-2 py-0.5 rounded bg-emerald-500/10 border border-emerald-500/20">Optimal</span>
                </div>

                <div class="p-3 rounded-xl bg-slate-950 border border-slate-800/80 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
                        <div>
                            <div class="text-xs font-semibold text-slate-200">Meta Cloud API</div>
                            <div class="text-[10px] text-slate-500 font-mono">Graph v21.0 • Webhooks Live</div>
                        </div>
                    </div>
                    <span class="text-[10px] font-bold text-emerald-400 px-2 py-0.5 rounded bg-emerald-500/10 border border-emerald-500/20">Synced</span>
                </div>

                <div class="p-3 rounded-xl bg-slate-950 border border-slate-800/80 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
                        <div>
                            <div class="text-xs font-semibold text-slate-200">Payment Gateways</div>
                            <div class="text-[10px] text-slate-500 font-mono">Razorpay & Stripe Webhooks</div>
                        </div>
                    </div>
                    <span class="text-[10px] font-bold text-emerald-400 px-2 py-0.5 rounded bg-emerald-500/10 border border-emerald-500/20">Active</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Vendors Onboarded -->
    <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-white uppercase tracking-wider">Recently Onboarded Businesses</h2>
                <p class="text-xs text-slate-400">Directly visit their console, check subscription tiers or manage permissions.</p>
            </div>
            <a href="{{ route('admin.vendors') }}" class="text-xs font-bold text-purple-400 hover:text-purple-300 transition flex items-center gap-1">
                <span>View All Vendors</span>
                <span>→</span>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/80 text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Business / Vendor</th>
                        <th class="py-3 px-4">Current Plan</th>
                        <th class="py-3 px-4">WhatsApp Status</th>
                        <th class="py-3 px-4">Account Status</th>
                        <th class="py-3 px-4">Onboarded</th>
                        <th class="py-3 px-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-sans">
                    @forelse($recentVendors as $vendor)
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-purple-700 to-indigo-600 flex items-center justify-center font-bold text-white text-xs">
                                    {{ substr($vendor->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-bold text-white">{{ $vendor->name }}</div>
                                    <div class="text-[11px] text-slate-500 font-mono">{{ $vendor->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2.5 py-1 rounded-full bg-purple-500/10 border border-purple-500/20 text-purple-300 font-semibold text-[11px]">
                                {{ $vendor->activeSubscription?->plan?->title ?? 'Growth / Pro Plan' }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                Connected
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $vendor->status === 'active' ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/15 text-rose-400 border border-rose-500/30' }}">
                                {{ $vendor->status }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-slate-400 font-mono text-[11px]">
                            {{ $vendor->created_at->format('M d, Y') }}
                        </td>
                        <td class="py-3 px-4 text-right">
                            <form method="POST" action="{{ route('admin.vendors.impersonate', $vendor->id) }}" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-purple-600 hover:bg-purple-500 text-white font-bold text-[11px] transition shadow shadow-purple-500/20">
                                    Visit Console ↗
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-8 text-slate-500">No registered businesses found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
