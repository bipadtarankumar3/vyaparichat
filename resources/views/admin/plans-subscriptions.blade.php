@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-black text-white tracking-tight">Auto Subscriptions</h1>
                <span class="px-2 py-0.5 rounded-full bg-purple-500/10 border border-purple-500/30 text-[10px] font-bold text-purple-300 uppercase tracking-wider">
                    Automated Gateway Recurring
                </span>
            </div>
            <p class="text-xs text-slate-400 mt-1">Manage recurring subscription billing tiers processed via Stripe and Razorpay.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.settings', 'subscription-plans') }}" class="px-3.5 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs shadow-lg shadow-purple-500/20 transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Edit Plan Pricing & Quotas</span>
            </a>
            <a href="{{ route('admin.plans.manual') }}" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs border border-slate-700 transition flex items-center gap-1.5">
                <span>Manual/Prepaid Requests</span>
                <span class="px-1.5 py-0.2 rounded bg-rose-500 text-white font-extrabold text-[10px]">1</span>
            </a>
        </div>
    </div>

    <!-- Active Subscription Plans Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @forelse($plans as $plan)
        <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl relative overflow-hidden flex flex-col justify-between hover:border-purple-500/40 transition">
            <div>
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-white">{{ $plan->title }}</h2>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $plan->is_active ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30' : 'bg-slate-800 text-slate-400' }}">
                        {{ $plan->is_active ? 'Active Tier' : 'Inactive' }}
                    </span>
                </div>
                <div class="mt-4 flex items-baseline gap-1">
                    <span class="text-3xl font-black text-white">₹{{ number_format($plan->price_monthly) }}</span>
                    <span class="text-xs text-slate-400 font-semibold">/ month</span>
                </div>
                <p class="mt-2 text-xs text-slate-400 leading-relaxed">{{ $plan->description ?? 'Full multi-tenant WhatsApp cloud messaging package.' }}</p>

                <div class="mt-6 space-y-2.5 pt-4 border-t border-slate-800/80 text-xs text-slate-300">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Monthly Message Quota:</span>
                        <strong class="text-white">{{ number_format($plan->max_messages_monthly) }}</strong>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Total Contacts Limit:</span>
                        <strong class="text-white">{{ number_format($plan->max_contacts) }}</strong>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Team Agents:</span>
                        <strong class="text-white">{{ $plan->max_team_members ?? 5 }} Seats</strong>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Flow Maker & Bot Automation:</span>
                        <strong class="text-emerald-400">Included</strong>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-800 flex items-center justify-between text-xs">
                <span class="text-slate-500 font-mono">Plan ID: #{{ $plan->id }}</span>
                <a href="{{ route('admin.settings', 'subscription-plans') }}" class="font-bold text-purple-400 hover:text-purple-300 transition">Configure Tier →</a>
            </div>
        </div>
        @empty
        <div class="col-span-3 p-8 text-center text-slate-500 bg-slate-900 rounded-2xl border border-slate-800">
            No subscription tiers configured.
        </div>
        @endforelse
    </div>

    <!-- Active Recurring Subscriptions Table -->
    <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-white uppercase tracking-wider">Active Recurring Subscriptions</h2>
                <p class="text-xs text-slate-400">Real-time status of recurring charges and auto-renewals.</p>
            </div>
            <span class="text-xs text-purple-300 font-semibold bg-purple-500/10 px-3 py-1 rounded-full border border-purple-500/20">
                Total Subscribers: {{ $subscriptions->total() }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/80 text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Business / Tenant</th>
                        <th class="py-3 px-4">Subscribed Plan</th>
                        <th class="py-3 px-4">Amount</th>
                        <th class="py-3 px-4">Cycle</th>
                        <th class="py-3 px-4">Started At</th>
                        <th class="py-3 px-4">Next Renewal</th>
                        <th class="py-3 px-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-sans">
                    @forelse($subscriptions as $sub)
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="py-3 px-4">
                            <div class="font-bold text-white">{{ $sub->vendor?->name ?? 'Acme Retail' }}</div>
                            <div class="text-[11px] text-slate-500 font-mono">{{ $sub->vendor?->email ?? 'tenant@vyaparichat.com' }}</div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="font-semibold text-purple-300">{{ $sub->plan?->title ?? 'Growth / Pro' }}</span>
                        </td>
                        <td class="py-3 px-4 font-mono font-bold text-white">
                            ₹{{ number_format($sub->amount) }}
                        </td>
                        <td class="py-3 px-4 uppercase text-[10px] tracking-wider font-semibold text-slate-400">
                            {{ $sub->billing_cycle ?? 'monthly' }}
                        </td>
                        <td class="py-3 px-4 text-slate-400 font-mono">
                            {{ $sub->starts_at?->format('M d, Y') ?? now()->subDays(15)->format('M d, Y') }}
                        </td>
                        <td class="py-3 px-4 text-emerald-400 font-mono font-semibold">
                            {{ $sub->ends_at?->format('M d, Y') ?? now()->addDays(15)->format('M d, Y') }}
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                {{ $sub->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-8 text-slate-500">No active automated subscriptions.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
