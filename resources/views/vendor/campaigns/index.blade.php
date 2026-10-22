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
            <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-slate-700 transition space-y-4 shadow flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="font-bold text-white text-base" x-text="camp.title"></h3>
                            <p class="text-xs text-slate-400 mt-0.5" x-text="'Template: ' + (camp.template ? camp.template.name : 'N/A')"></p>
                        </div>
                        <span :class="{
                            'bg-emerald-500/20 text-emerald-400 border-emerald-500/30': camp.status === 'completed',
                            'bg-blue-500/20 text-blue-400 border-blue-500/30': camp.status === 'processing',
                            'bg-amber-500/20 text-amber-400 border-amber-500/30': camp.status === 'paused',
                            'bg-slate-800 text-slate-400 border-slate-700': camp.status === 'draft' || camp.status === 'cancelled'
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

                <!-- Actions Controls -->
                <div class="pt-3 border-t border-slate-800 flex items-center justify-between text-xs">
                    <div class="space-x-1">
                        <button x-show="camp.status === 'processing'" @click="campaignAction(camp.id, 'pause')" class="px-2.5 py-1 rounded bg-amber-500/10 text-amber-300 hover:bg-amber-500/20 font-semibold text-[11px] border border-amber-500/30 transition">Pause</button>
                        <button x-show="camp.status === 'paused'" @click="campaignAction(camp.id, 'resume')" class="px-2.5 py-1 rounded bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 font-semibold text-[11px] border border-emerald-500/30 transition">Resume</button>
                        <button x-show="camp.status === 'processing' || camp.status === 'paused'" @click="campaignAction(camp.id, 'cancel')" class="px-2.5 py-1 rounded bg-slate-800 text-slate-400 hover:text-white font-semibold text-[11px] border border-slate-700 transition">Cancel</button>
                    </div>
                    <button @click="deleteCampaign(camp.id)" class="px-2 py-1 rounded bg-rose-500/10 text-rose-400 hover:bg-rose-500/20 text-[11px] border border-rose-500/30 transition">Delete</button>
                </div>
            </div>
        </template>
        <div x-show="campaignsList.length === 0" class="col-span-full p-12 text-center rounded-2xl bg-slate-900 border border-slate-800 text-slate-500">
            No active broadcast campaigns found. Click "Create Broadcast" to launch your first WhatsApp campaign blast.
        </div>
    </div>

    <!-- Create Broadcast Modal -->
    <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="showCreateModal = false" class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-md p-6 space-y-4 shadow-2xl">
            <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                <h3 class="font-bold text-base text-white">Create New Broadcast Campaign</h3>
                <button @click="showCreateModal = false" class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <form action="{{ route('web.campaigns.store') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Campaign Title *</label>
                    <input type="text" name="title" required placeholder="e.g. Diwali Mega Sale 2026" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Select Meta Approved Template *</label>
                    <select name="whatsapp_template_id" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-brand-500">
                        @foreach($templates as $tpl)
                            <option value="{{ $tpl->id }}">{{ $tpl->name }} ({{ $tpl->language }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Target Audience Group</label>
                    <select name="contact_group_id" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-brand-500">
                        <option value="">All Opted-in Contacts</option>
                        @foreach($groups as $grp)
                            <option value="{{ $grp->id }}">{{ $grp->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-800">
                    <button type="button" @click="showCreateModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white text-xs font-bold shadow-lg shadow-brand-500/20">Launch Broadcast 🚀</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function vyapariCampaignsApp() {
    return {
        campaignsList: [],
        showCreateModal: false,

        async init() {
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
