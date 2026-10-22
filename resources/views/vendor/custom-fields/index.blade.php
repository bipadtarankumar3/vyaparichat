@extends('layouts.app')

@section('content')
<div x-data="vyapariCustomFieldsManager()" class="space-y-6 max-w-4xl">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
                <span>➕</span>
                <span>Custom Input Fields (Add Input)</span>
            </h1>
            <p class="text-xs text-slate-400 mt-1">Define custom data fields for customer profiles (e.g. City, Order Number, Birthday, Membership ID).</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="openAddModal()" class="px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs transition flex items-center gap-1.5 shadow-lg shadow-brand-500/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Add Custom Field</span>
            </button>
            <a href="{{ route('web.contacts') }}" class="px-4 py-2 rounded-xl bg-slate-900 border border-slate-700 hover:bg-slate-800 text-slate-300 font-semibold text-xs transition">
                ← All Contacts
            </a>
        </div>
    </div>

    <!-- Structured Modular Views -->
    @include('vendor.custom-fields.listing')
    @include('vendor.custom-fields.add')
    @include('vendor.custom-fields.update')
    @include('vendor.custom-fields.details')
</div>

<script>
function vyapariCustomFieldsManager() {
    return {
        showAddModal: false,
        showEditModal: false,
        showDetailsModal: false,
        selectedField: null,
        editingField: null,

        openAddModal() {
            this.showAddModal = true;
        },

        viewDetails(field) {
            this.selectedField = field;
            this.showDetailsModal = true;
        },

        openEditModal(field) {
            this.editingField = Object.assign({}, field);
            this.showEditModal = true;
        }
    };
}
</script>
@endsection
