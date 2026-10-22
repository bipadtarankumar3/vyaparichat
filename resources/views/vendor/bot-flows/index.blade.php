@extends('layouts.app')

@section('content')
<div x-data="vyapariBotFlowsManager()" class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
                <span>⚡</span>
                <span>Chatbot Flow Maker</span>
            </h1>
            <p class="text-xs text-slate-400 mt-1">Design automated multi-step conversation trees, interactive quick-reply buttons, and dynamic WhatsApp flows.</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="openAddModal()" class="px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs transition flex items-center gap-1.5 shadow-lg shadow-brand-500/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Create Flow</span>
            </button>
            <a href="{{ route('web.bot') }}" class="px-4 py-2 rounded-xl bg-slate-900 border border-slate-700 hover:bg-slate-800 text-slate-300 font-semibold text-xs transition">
                ← All Chatbots
            </a>
        </div>
    </div>

    <!-- Structured Modular Views -->
    @include('vendor.bot-flows.listing')
    @include('vendor.bot-flows.add')
    @include('vendor.bot-flows.update')
    @include('vendor.bot-flows.details')
</div>

<script>
function vyapariBotFlowsManager() {
    return {
        showAddModal: false,
        showEditModal: false,
        showDetailsModal: false,
        selectedFlow: null,
        editingFlow: null,

        openAddModal() {
            this.showAddModal = true;
        },

        viewDetails(flow) {
            this.selectedFlow = flow;
            this.showDetailsModal = true;
        },

        openEditModal(flow) {
            this.editingFlow = Object.assign({}, flow);
            this.showEditModal = true;
        }
    };
}
</script>
@endsection
