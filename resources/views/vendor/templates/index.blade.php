@extends('layouts.app')

@section('content')
<div x-data="vyapariTemplatesManager()" x-init="init()" class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">WhatsApp Templates (Meta HSM)</h1>
            <p class="text-xs text-slate-400 mt-1">Pre-approved Meta Graph API message templates required for 24-hour notification window re-engagement.</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="openAddModal()" class="px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs transition flex items-center gap-1.5 shadow-lg shadow-brand-500/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Create Template</span>
            </button>
            <button @click="syncTemplates()" :disabled="isSyncing" class="px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 hover:bg-slate-800 disabled:opacity-50 text-slate-300 font-semibold text-xs transition flex items-center gap-2">
                <svg class="w-4 h-4" :class="isSyncing ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span x-text="isSyncing ? 'Syncing...' : 'Sync with Meta'"></span>
            </button>
        </div>
    </div>

    <!-- Structured Modular Views -->
    @include('vendor.templates.listing')
    @include('vendor.templates.add')
    @include('vendor.templates.update')
    @include('vendor.templates.details')
</div>

<script>
function vyapariTemplatesManager() {
    return {
        templatesList: [],
        showAddModal: false,
        showEditModal: false,
        showDetailsModal: false,
        selectedTemplate: null,
        isSyncing: false,

        editTemplateForm: {
            id: null,
            name: '',
            category: 'UTILITY',
            language: 'en_US',
            body_text: ''
        },

        init() {
            this.loadTemplates();
        },

        async loadTemplates() {
            try {
                let res = await fetch('/api/templates');
                let json = await res.json();
                if (json.success) {
                    this.templatesList = json.data || [];
                }
            } catch (e) {
                console.error(e);
            }
        },

        openAddModal() {
            this.showAddModal = true;
        },

        viewTemplate(tpl) {
            this.selectedTemplate = tpl;
            this.showDetailsModal = true;
        },

        editTemplate(tpl) {
            let bodyComp = (tpl.components || []).find(c => c.type === 'BODY');
            this.editTemplateForm = {
                id: tpl.id,
                name: tpl.name + '_copy',
                category: tpl.category || 'UTILITY',
                language: tpl.language || 'en_US',
                body_text: bodyComp ? bodyComp.text : ''
            };
            this.showEditModal = true;
        },

        async syncTemplates() {
            this.isSyncing = true;
            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            try {
                let res = await fetch('/api/templates/sync', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
                });
                let json = await res.json();
                if (json.success) {
                    alert('Meta Templates synchronized successfully!');
                    this.loadTemplates();
                } else {
                    alert(json.message || 'Meta Sync failed.');
                }
            } catch (e) {
                alert('Meta Sync request failed.');
            } finally {
                this.isSyncing = false;
            }
        },

        async deleteTemplate(id) {
            if (!confirm('Are you sure you want to delete this template?')) return;
            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            try {
                await fetch(`/templates/${id}/delete`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
                });
                this.loadTemplates();
            } catch (e) {
                console.error(e);
            }
        }
    };
}
</script>
@endsection
