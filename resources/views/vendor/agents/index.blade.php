@extends('layouts.app')

@section('content')
<div x-data="vyapariAgentsManager()" class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
                <span>👔</span>
                <span>Agents & Team Members</span>
            </h1>
            <p class="text-xs text-slate-400 mt-1">Manage staff seats, invite customer support agents, and assign conversation permissions.</p>
        </div>
        <button @click="showAddModal = true" class="px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs transition flex items-center gap-2 shadow-lg shadow-brand-500/20">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Add New Agent</span>
        </button>
    </div>

    <!-- Structured Modular Views -->
    @include('vendor.agents.listing')
    @include('vendor.agents.add')
    @include('vendor.agents.update')
    @include('vendor.agents.details')
</div>

<script>
function vyapariAgentsManager() {
    return {
        showAddModal: false,
        showEditModal: false,
        showDetailsModal: false,
        selectedAgent: null,
        selectedRole: 'agent',
        editingAgent: null,
        editingRole: 'agent',

        viewDetails(agent, role) {
            this.selectedAgent = agent;
            this.selectedRole = role || 'agent';
            this.showDetailsModal = true;
        },

        openEditModal(agent, role) {
            this.editingAgent = agent;
            this.editingRole = role || 'agent';
            this.showEditModal = true;
        }
    };
}
</script>
@endsection
