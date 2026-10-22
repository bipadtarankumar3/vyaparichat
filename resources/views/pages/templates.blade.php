@extends('layouts.app')

@section('content')
<div x-data="vyapariTemplatesApp()" x-init="init()" class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">WhatsApp Templates (Meta HSM)</h1>
            <p class="text-xs text-slate-400 mt-1">Pre-approved Meta Graph API message templates required for 24-hour notification window re-engagement.</p>
        </div>
        <button @click="syncTemplates()" :disabled="isSyncing" class="px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 disabled:opacity-50 text-white font-bold text-xs transition flex items-center gap-2 shadow-lg shadow-brand-500/20">
            <svg class="w-4 h-4" :class="isSyncing ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            <span x-text="isSyncing ? 'Syncing...' : 'Sync with Meta'"></span>
        </button>
    </div>

    <!-- Templates Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <template x-for="tpl in templatesList" :key="tpl.id">
            <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-slate-700 transition space-y-4 shadow flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="font-bold text-white text-base font-mono" x-text="tpl.name"></h3>
                            <span class="text-[10px] text-slate-400 uppercase tracking-wider" x-text="tpl.category"></span>
                        </div>
                        <span :class="tpl.status === 'APPROVED' ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30' : 'bg-amber-500/20 text-amber-400 border-amber-500/30'" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border" x-text="tpl.status"></span>
                    </div>

                    <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 text-xs text-slate-300">
                        <template x-for="comp in tpl.components" :key="comp.type">
                            <div x-show="comp.type === 'BODY'" class="leading-relaxed whitespace-pre-wrap" x-text="comp.text"></div>
                        </template>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-800 flex items-center justify-between text-[11px] text-slate-500">
                    <span x-text="'Language: ' + (tpl.language || 'en_US')"></span>
                    <span class="text-brand-400 font-mono">Meta Cloud v20</span>
                </div>
            </div>
        </template>
        <div x-show="templatesList.length === 0" class="col-span-full p-12 text-center rounded-2xl bg-slate-900 border border-slate-800 text-slate-500">
            No templates found. Click "Sync with Meta" to fetch your approved WhatsApp templates.
        </div>
    </div>
</div>

<script>
function vyapariTemplatesApp() {
    return {
        templatesList: [],
        isSyncing: false,

        init() {
            this.loadTemplates();
        },

        async loadTemplates() {
            try {
                let res = await fetch('/api/whatsapp/templates');
                let json = await res.json();
                if (json.success) {
                    this.templatesList = json.data.data || [];
                }
            } catch (e) {
                console.error(e);
            }
        },

        async syncTemplates() {
            this.isSyncing = true;
            try {
                let res = await fetch('/api/whatsapp/templates/sync', { 
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content }
                });
                let json = await res.json();
                alert(json.message || 'Templates synced successfully.');
                this.loadTemplates();
            } catch (e) {
                alert('Sync failed. Please check Meta Cloud API credentials in Settings.');
            } finally {
                this.isSyncing = false;
            }
        }
    };
}
</script>
@endsection
