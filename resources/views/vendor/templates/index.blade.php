@extends('layouts.app')

@section('content')
<div x-data="vyapariTemplatesApp()" x-init="init()" class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">WhatsApp Templates (Meta HSM)</h1>
            <p class="text-xs text-slate-400 mt-1">Pre-approved Meta Graph API message templates required for 24-hour notification window re-engagement.</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="showModal = true" class="px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs transition flex items-center gap-1.5 shadow-lg shadow-brand-500/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Create Template</span>
            </button>
            <button @click="syncTemplates()" :disabled="isSyncing" class="px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 hover:bg-slate-800 disabled:opacity-50 text-slate-300 font-semibold text-xs transition flex items-center gap-2">
                <svg class="w-4 h-4" :class="isSyncing ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span x-text="isSyncing ? 'Syncing...' : 'Sync with Meta'"></span>
            </button>
        </div>
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
                    <button @click="deleteTemplate(tpl.id)" class="text-rose-400 hover:text-rose-300 font-semibold">Delete</button>
                </div>
            </div>
        </template>
        <div x-show="templatesList.length === 0" class="col-span-full p-12 text-center rounded-2xl bg-slate-900 border border-slate-800 text-slate-500">
            No templates found. Click "Create Template" or "Sync with Meta" to fetch your approved WhatsApp templates.
        </div>
    </div>

    <!-- Create Template Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="showModal = false" class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-md p-6 space-y-4 shadow-2xl">
            <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                <h3 class="font-bold text-base text-white">Create New WhatsApp Template</h3>
                <button @click="showModal = false" class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <form action="{{ route('web.templates.store') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Template Identifier Name *</label>
                    <input type="text" name="name" required placeholder="e.g. order_dispatch_update" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-brand-500">
                    <span class="text-[10px] text-slate-500">Only lowercase alphanumeric and underscores allowed.</span>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Category</label>
                        <select name="category" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-brand-500">
                            <option value="MARKETING">Marketing</option>
                            <option value="UTILITY" selected>Utility</option>
                            <option value="AUTHENTICATION">Authentication (OTP)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Language</label>
                        <select name="language" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-brand-500">
                            <option value="en_US" selected>English (en_US)</option>
                            <option value="hi">Hindi (hi)</option>
                            <option value="es">Spanish (es)</option>
                            <option value="ar">Arabic (ar)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Body Text Content *</label>
                    <textarea name="body_text" required rows="4" placeholder="Hello {{1}}, your order #{{2}} has been confirmed and dispatched." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-brand-500"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-800">
                    <button type="button" @click="showModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white text-xs font-bold shadow-lg shadow-brand-500/20">Submit Template</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function vyapariTemplatesApp() {
    return {
        templatesList: [],
        isSyncing: false,
        showModal: false,

        async init() {
            this.loadTemplates();
        },

        async loadTemplates() {
            try {
                let res = await fetch('/api/whatsapp/templates');
                let json = await res.json();
                if (json.success) {
                    this.templatesList = json.data || [];
                }
            } catch (e) {
                console.error(e);
            }
        },

        async syncTemplates() {
            this.isSyncing = true;
            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            try {
                await fetch('/api/whatsapp/templates/sync', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
                });
                await this.loadTemplates();
            } catch (e) {
                console.error(e);
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
