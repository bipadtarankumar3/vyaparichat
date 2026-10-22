@extends('layouts.admin')

@section('content')
<div x-data="adminPlansManager()" class="space-y-6">
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
            <button @click="openAddModal()" class="px-3.5 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs shadow-lg shadow-purple-500/20 transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Create Plan Tier</span>
            </button>
            <a href="{{ route('admin.plans.manual') }}" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs border border-slate-700 transition flex items-center gap-1.5">
                <span>Manual/Prepaid Requests</span>
                <span class="px-1.5 py-0.2 rounded bg-rose-500 text-white font-extrabold text-[10px]">1</span>
            </a>
        </div>
    </div>

    <!-- Structured Modular Views -->
    @include('admin.plans.listing')
    @include('admin.plans.add')
    @include('admin.plans.update')
    @include('admin.plans.details')
</div>

<script>
function adminPlansManager() {
    return {
        showAddModal: false,
        showEditModal: false,
        showDetailsModal: false,
        selectedPlan: null,
        editingPlan: null,

        openAddModal() {
            this.showAddModal = true;
        },

        viewDetails(plan) {
            this.selectedPlan = plan;
            this.showDetailsModal = true;
        },

        openEditModal(plan) {
            this.editingPlan = Object.assign({}, plan);
            this.showEditModal = true;
        }
    };
}
</script>
@endsection
