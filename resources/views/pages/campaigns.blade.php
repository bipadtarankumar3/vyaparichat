@extends('layouts.app')

@section('content')
<div x-data="vyapariCampaignsApp()" x-init="init()" class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Broadcast Campaigns</h1>
            <p class="text-xs text-slate-400 mt-1">High-throughput bulk WhatsApp blasts with chunked queues and live progress metrics.</p>
        </div>
        <button @click="showCreateModal = true" class="px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs transition flex items-center gap-2 shadow-lg shadow-brand-500/20">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Create Broadcast</span>
        </button>
    </div>

    <!-- Campaigns Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <template x-for="camp in campaignsList" :key="camp.id">
            <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-slate-700 transition space-y-4 shadow">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="font-bold text-white text-base" x-text="camp.title"></h3>
                        <p class="text-xs text-slate-400 mt-0.5" x-text="'Template: ' + (camp.template ? camp.template.name : 'N/A')"></p>
                    </div>
                    <span :class="{
                        'bg-emerald-500/20 text-emerald-400 border-emerald-500/30': camp.status === 'completed',
                        'bg-blue-500/20 text-blue-400 border-blue-500/30': camp.status === 'processing',
                        'bg-amber-500/20 text-amber-400 border-amber-500/30': camp.status === 'paused',
                        'bg-slate-800 text-slate-400 border-slate-700': camp.status === 'draft'
                    }" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border" x-text="camp.status"></span>
                </div>

                <!-- Progress Bar -->
                <div class="space-y-1">
                    <div class="flex items-center justify-between text-xs text-slate-400">
                        <span>Delivery Progress</span>
                        <span class="font-bold text-slate-200" x-text="camp.total_contacts > 0 ? Math.round(((camp.sent_count + camp.failed_count) / camp.total_contacts) * 100) + '%' : '0%'"></span>
                    </div>
                    <div class="w-full h-2 rounded-full bg-slate-800 overflow-hidden">
                        <div class="h-full bg-brand-500 rounded-full transition-all" :style="'width: ' + (camp.total_contacts > 0 ? ((camp.sent_count + camp.failed_count) / camp.total_contacts) * 100 : 0) + '%'"></div>
                    </div>
                </div>

                <!-- Stats Row -->
                <div class="grid grid-cols-4 gap-2 pt-3 border-t border-slate-800 text-center text-xs">
                    <div>
                        <div class="text-slate-500 text-[10px]">Total</div>
                        <div class="font-bold text-white" x-text="camp.total_contacts"></div>
                    </div>
                    <div>
                        <div class="text-slate-500 text-[10px]">Sent</div>
                        <div class="font-bold text-emerald-400" x-text="camp.sent_count"></div>
                    </div>
                    <div>
                        <div class="text-slate-500 text-[10px]">Delivered</div>
                        <div class="font-bold text-blue-400" x-text="camp.delivered_count"></div>
                    </div>
                    <div>
                        <div class="text-slate-500 text-[10px]">Read</div>
                        <div class="font-bold text-purple-400" x-text="camp.read_count"></div>
                    </div>
                </div>
            </div>
        </template>
        <div x-show="campaignsList.length === 0" class="col-span-full p-12 text-center rounded-2xl bg-slate-900 border border-slate-800 text-slate-500">
            No broadcast campaigns created yet. Click "Create Broadcast" to launch your first WhatsApp bulk campaign.
        </div>
    </div>
</div>

<script>
function vyapariCampaignsApp() {
    return {
        campaignsList: [],
        showCreateModal: false,

        init() {
            this.loadCampaigns();
        },

        async loadCampaigns() {
            try {
                let res = await fetch('/api/campaigns');
                let json = await res.json();
                if (json.success) {
                    this.campaignsList = json.data.data || [];
                }
            } catch (e) {
                console.error(e);
            }
        }
    };
}
</script>
@endsection
