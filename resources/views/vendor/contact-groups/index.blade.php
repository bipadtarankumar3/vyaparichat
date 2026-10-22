@extends('layouts.app')

@section('content')
<div x-data="vyapariContactGroupsManager()" class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
                <span>📁</span>
                <span>Audience Contact Groups</span>
            </h1>
            <p class="text-xs text-slate-400 mt-1">Organize customers into broadcast target segments (e.g. VIP Customers, Leads, Wholesale).</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="openAddModal()" class="px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs transition flex items-center gap-1.5 shadow-lg shadow-brand-500/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Create Group</span>
            </button>
            <a href="{{ route('web.contacts') }}" class="px-4 py-2 rounded-xl bg-slate-900 border border-slate-700 hover:bg-slate-800 text-slate-300 font-semibold text-xs transition">
                ← All Contacts
            </a>
        </div>
    </div>

    <!-- Structured Modular Views -->
    @include('vendor.contact-groups.listing')
    @include('vendor.contact-groups.add')
    @include('vendor.contact-groups.update')
    @include('vendor.contact-groups.details')
</div>

<script>
function vyapariContactGroupsManager() {
    return {
        showAddModal: false,
        showEditModal: false,
        showDetailsModal: false,
        selectedGroup: null,
        editingGroup: null,

        openAddModal() {
            this.showAddModal = true;
        },

        viewDetails(group) {
            this.selectedGroup = group;
            this.showDetailsModal = true;
        },

        openEditModal(group) {
            this.editingGroup = Object.assign({}, group);
            this.showEditModal = true;
        }
    };
}
</script>
@endsection
