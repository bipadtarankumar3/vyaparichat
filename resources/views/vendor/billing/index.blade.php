@extends('layouts.app')

@section('content')
<div x-data="vyapariBillingApp()" x-init="init()" class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Subscription Plans & Billing</h1>
            <p class="text-xs text-slate-400 mt-1">Manage plan tier, feature gating, staff seats, and payment methods.</p>
        </div>
        @if(isset($currentSubscription) && $currentSubscription)
            <div class="px-4 py-2 rounded-xl bg-brand-500/10 border border-brand-500/30 text-brand-400 text-xs font-semibold flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-brand-400 animate-pulse"></span>
                <span>Active: {{ $currentSubscription->plan->title ?? 'Current Plan' }}</span>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <template x-for="plan in billingPlans" :key="plan.id">
            <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 hover:border-slate-700 transition flex flex-col justify-between space-y-6 shadow">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-white text-lg" x-text="plan.title"></h3>
                        <span x-show="plan.slug === 'pro-enterprise'" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-brand-500/20 text-brand-400 border border-brand-500/30">Popular</span>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed" x-text="plan.description"></p>
                    <div class="text-3xl font-extrabold text-white">
                        ₹<span x-text="plan.price_monthly"></span><span class="text-xs text-slate-400 font-normal"> / month</span>
                    </div>
                    <ul class="text-xs text-slate-300 space-y-2.5 pt-4 border-t border-slate-800">
                        <li class="flex items-center gap-2">
                            <span class="text-brand-400 font-bold">✓</span>
                            <span x-text="plan.max_contacts > 0 ? plan.max_contacts + ' Contacts Limit' : 'Unlimited Contacts'"></span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-brand-400 font-bold">✓</span>
                            <span x-text="plan.max_campaigns_per_month + ' Broadcasts / month'"></span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-brand-400 font-bold">✓</span>
                            <span x-text="plan.max_staff_members + ' Agent seats'"></span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span :class="plan.has_ai_bot ? 'text-brand-400 font-bold' : 'text-slate-600'" x-text="plan.has_ai_bot ? '✓' : '✗'"></span>
                            <span :class="plan.has_ai_bot ? 'text-slate-300' : 'text-slate-500'">AI Copilot & Smart Bot</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span :class="plan.has_api_access ? 'text-brand-400 font-bold' : 'text-slate-600'" x-text="plan.has_api_access ? '✓' : '✗'"></span>
                            <span :class="plan.has_api_access ? 'text-slate-300' : 'text-slate-500'">Public REST API Access</span>
                        </li>
                    </ul>
                </div>

                <button class="w-full py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs transition shadow-lg shadow-brand-500/20">
                    Subscribe Now
                </button>
            </div>
        </template>
    </div>
</div>

<script>
function vyapariBillingApp() {
    return {
        billingPlans: [],

        init() {
            this.loadBillingPlans();
        },

        async loadBillingPlans() {
            try {
                let res = await fetch('/api/billing/plans');
                let json = await res.json();
                if (json.success) {
                    this.billingPlans = json.data || [];
                }
            } catch (e) {
                console.error(e);
            }
        }
    };
}
</script>
@endsection
