@extends('layouts.app')

@section('content')
<div x-data="vyapariCampaignsManager()" x-init="init()" class="space-y-6">
    <!-- Activity Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Broadcast Campaigns</h1>
            <p class="text-xs text-slate-400 mt-1">High-throughput bulk WhatsApp blasts with chunked queues and live progress metrics.</p>
        </div>
        <button @click="openAddModal()" class="px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs transition flex items-center gap-2 shadow-lg shadow-brand-500/20">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Create Broadcast</span>
        </button>
    </div>

    <!-- Structured Modular Views -->
    @include('vendor.campaigns.listing')
    @include('vendor.campaigns.add')
    @include('vendor.campaigns.update')
    @include('vendor.campaigns.details')
</div>

<script>
function vyapariCampaignsManager() {
    return {
        campaignsList: [],
        showCreateModal: false,
        showEditModal: false,
        showDetailsModal: false,
        selectedCampaign: null,

        editCampaignForm: {
            id: null,
            title: '',
            status: 'draft'
        },

        init() {
            this.loadCampaigns();
        },

        async loadCampaigns() {
            try {
                let res = await fetch('/api/campaigns');
                let json = await res.json();
                if (json.success) {
                    this.campaignsList = json.data || [];
                }
            } catch (e) {
                console.error(e);
            }
        },

        openAddModal() {
            this.showCreateModal = true;
        },

        viewDetails(camp) {
            this.selectedCampaign = camp;
            this.showDetailsModal = true;
        },

        editCampaign(camp) {
            this.editCampaignForm = {
                id: camp.id,
                title: camp.title,
                status: camp.status
            };
            this.showEditModal = true;
        },

        async updateCampaign() {
            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            try {
                // Perform quick update or action
                this.showEditModal = false;
                this.loadCampaigns();
            } catch (e) {
                console.error(e);
            }
        },

        async campaignAction(id, action) {
            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            try {
                await fetch(`/campaigns/${id}/action/${action}`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
                });
                this.loadCampaigns();
            } catch (e) {
                console.error(e);
            }
        },

        async deleteCampaign(id) {
            if (!confirm('Are you sure you want to delete this campaign?')) return;
            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            try {
                await fetch(`/campaigns/${id}/delete`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
                });
                this.loadCampaigns();
            } catch (e) {
                console.error(e);
            }
        }
    };
}
</script>
@endsection
