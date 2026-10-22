@extends('layouts.app')

@section('content')
<div x-data="vyapariBotApp()" x-init="init()" class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Bot Automations & AI Copilot</h1>
            <p class="text-xs text-slate-400 mt-1">Configure auto-responders, priority triggers, and dry-run test incoming customer queries in real time.</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="openCreateModal()" class="px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs transition flex items-center gap-1.5 shadow-lg shadow-brand-500/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Add Bot Rule</span>
            </button>
            <a href="{{ route('web.bot.flows') }}" class="px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-700 hover:bg-slate-800 text-slate-300 font-semibold text-xs transition">
                ⚡ Visual Flow Maker
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left 2 Cols: Bot Rules List -->
        <div class="lg:col-span-2 space-y-4">
            <h2 class="font-bold text-slate-200 text-base">Active Auto-Reply Rules</h2>
            <div class="space-y-3">
                <template x-for="rule in botRules" :key="rule.id">
                    <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 hover:border-slate-700 transition flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow">
                        <div class="space-y-1.5">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-white text-sm" x-text="rule.name"></span>
                                <span class="px-2 py-0.5 rounded bg-brand-500/20 text-brand-400 border border-brand-500/30 text-[10px] uppercase font-bold" x-text="rule.trigger_type"></span>
                                <span class="text-xs text-slate-500" x-text="'Priority: ' + rule.priority"></span>
                            </div>
                            <div class="flex flex-wrap gap-1">
                                <template x-for="kw in rule.trigger_keywords" :key="kw">
                                    <span class="px-2 py-0.5 rounded bg-slate-800 text-slate-300 font-mono text-[10px]" x-text="kw"></span>
                                </template>
                            </div>
                            <div class="text-xs text-slate-400 italic" x-text="'Reply: ' + (rule.reply_payload?.text || '')"></div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span :class="rule.status === 'active' ? 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20' : 'text-slate-500 bg-slate-800 border-slate-700'" class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider border" x-text="rule.status"></span>
                            <button @click="editRule(rule)" class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-[11px] border border-slate-700 transition">Edit</button>
                            <button @click="deleteRule(rule.id)" class="px-2.5 py-1 rounded bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 font-semibold text-[11px] border border-rose-500/30 transition">Delete</button>
                        </div>
                    </div>
                </template>
                <div x-show="botRules.length === 0" class="p-8 text-center rounded-2xl bg-slate-900 border border-slate-800 text-slate-500 text-xs">
                    No bot rules configured yet. Click "Add Bot Rule" to set up automatic customer replies.
                </div>
            </div>
        </div>

        <!-- Right Col: Interactive Simulator Playground -->
        <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 space-y-4 shadow flex flex-col justify-start">
            <div class="flex items-center gap-2.5">
                <span class="p-2.5 rounded-xl bg-brand-500/20 text-brand-400 text-lg">🧪</span>
                <div>
                    <h3 class="font-bold text-white text-base">Dry-Run Simulator</h3>
                    <p class="text-xs text-slate-400">Test incoming customer query</p>
                </div>
            </div>

            <div class="space-y-3">
                <input type="text" x-model="simTestMessage" @keydown.enter="runSimulation()" placeholder="e.g. price, catalog, help" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-brand-500">
                <button @click="runSimulation()" class="w-full py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs transition shadow-lg shadow-brand-500/20">
                    Run Test Match
                </button>
            </div>

            <!-- Sim Result -->
            <template x-if="simResult">
                <div class="mt-4 p-4 rounded-xl border bg-slate-950 space-y-2 border-brand-500/30">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-bold text-brand-400">Match Found</span>
                        <span class="text-slate-400 text-[10px]" x-text="'Trigger: ' + simResult.matched_keyword"></span>
                    </div>
                    <div class="text-xs text-slate-200 bg-slate-900 p-2.5 rounded-lg border border-slate-800" x-text="simResult.reply_text"></div>
                </div>
            </template>
        </div>
    </div>

    <!-- Create / Edit Bot Rule Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="showModal = false" class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-md p-6 space-y-4 shadow-2xl">
            <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                <h3 class="font-bold text-base text-white" x-text="isEditing ? 'Edit Bot Rule' : 'Create Auto-Reply Rule'"></h3>
                <button @click="showModal = false" class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <form @submit.prevent="saveRule()" class="space-y-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Rule Name *</label>
                    <input type="text" x-model="formData.name" required placeholder="e.g. Price Inquiry Bot" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Trigger Keywords (comma separated) *</label>
                    <input type="text" x-model="formData.keywords" required placeholder="price, cost, rate, charges" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Trigger Match Condition</label>
                    <select x-model="formData.trigger_type" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-brand-500">
                        <option value="contains">Message Contains Any Keyword</option>
                        <option value="exact">Exact Match</option>
                        <option value="starts_with">Starts With</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Auto-Response Message *</label>
                    <textarea x-model="formData.reply_text" required rows="3" placeholder="Hello! Our product starts at ₹499. Check our catalog..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-brand-500"></textarea>
                </div>

                <div x-show="isEditing">
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Status</label>
                    <select x-model="formData.status" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-brand-500">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-800">
                    <button type="button" @click="showModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white text-xs font-bold shadow-lg shadow-brand-500/20" x-text="isEditing ? 'Update Rule' : 'Save Bot Rule'"></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function vyapariBotApp() {
    return {
        botRules: [],
        showModal: false,
        isEditing: false,
        editId: null,
        formData: {
            name: '',
            keywords: '',
            reply_text: '',
            trigger_type: 'contains',
            status: 'active'
        },
        simTestMessage: '',
        simResult: null,

        async init() {
            this.loadRules();
        },

        async loadRules() {
            try {
                let res = await fetch('/api/bot-replies');
                let json = await res.json();
                if (json.success) {
                    this.botRules = json.data || [];
                }
            } catch (e) {
                console.error(e);
            }
        },

        openCreateModal() {
            this.isEditing = false;
            this.editId = null;
            this.formData = {
                name: '',
                keywords: '',
                reply_text: '',
                trigger_type: 'contains',
                status: 'active'
            };
            this.showModal = true;
        },

        editRule(rule) {
            this.isEditing = true;
            this.editId = rule.id;
            this.formData = {
                name: rule.name || '',
                keywords: (rule.trigger_keywords || []).join(', '),
                reply_text: rule.reply_payload?.text || '',
                trigger_type: rule.trigger_type || 'contains',
                status: rule.status || 'active'
            };
            this.showModal = true;
        },

        async saveRule() {
            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            let url = this.isEditing ? `/bot/rules/${this.editId}/update` : '/bot/rules';

            try {
                await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify(this.formData)
                });
                this.showModal = false;
                this.loadRules();
            } catch (e) {
                console.error(e);
            }
        },

        async deleteRule(id) {
            if (!confirm('Delete this bot rule?')) return;
            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            try {
                await fetch(`/bot/rules/${id}/delete`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    }
                });
                this.loadRules();
            } catch (e) {
                console.error(e);
            }
        },

        runSimulation() {
            if (!this.simTestMessage.trim()) return;
            let query = this.simTestMessage.toLowerCase();
            let match = this.botRules.find(r => {
                if (r.status !== 'active') return false;
                return (r.trigger_keywords || []).some(k => query.includes(k.toLowerCase()));
            });

            if (match) {
                this.simResult = {
                    matched_keyword: match.trigger_keywords[0],
                    reply_text: match.reply_payload?.text || 'Standard automated reply triggered.'
                };
            } else {
                this.simResult = {
                    matched_keyword: 'None (Default fallback)',
                    reply_text: 'Thank you for reaching out! Our team will get back to you shortly.'
                };
            }
        }
    };
}
</script>
@endsection
