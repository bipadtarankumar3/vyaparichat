@extends('layouts.app')

@section('content')
<div x-data="vyapariBotManager()" x-init="init()" class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Bot Automations & AI Copilot</h1>
            <p class="text-xs text-slate-400 mt-1">Configure auto-responders, priority triggers, and dry-run test incoming customer queries in real time.</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="openAddModal()" class="px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs transition flex items-center gap-1.5 shadow-lg shadow-brand-500/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Add Bot Rule</span>
            </button>
            <a href="{{ route('web.bot.flows') }}" class="px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-700 hover:bg-slate-800 text-slate-300 font-semibold text-xs transition">
                ⚡ Visual Flow Maker
            </a>
        </div>
    </div>

    <!-- Structured Modular Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left 2 Cols: Listing -->
        <div class="lg:col-span-2">
            @include('vendor.bot.listing')
        </div>

        <!-- Right Col: Details & Simulator -->
        <div>
            @include('vendor.bot.details')
        </div>
    </div>

    <!-- Add and Update Modals -->
    @include('vendor.bot.add')
    @include('vendor.bot.update')
</div>

<script>
function vyapariBotManager() {
    return {
        botRules: [],
        showAddModal: false,
        showEditModal: false,
        selectedRule: null,
        editingId: null,
        simTestMessage: '',
        simResult: null,

        addForm: {
            name: '',
            keywords: '',
            trigger_type: 'keyword',
            priority: 1,
            reply_text: ''
        },

        editForm: {
            name: '',
            keywords: '',
            trigger_type: 'keyword',
            priority: 1,
            reply_text: ''
        },

        init() {
            this.loadRules();
        },

        async loadRules() {
            try {
                let res = await fetch('/api/bot/replies');
                let json = await res.json();
                if (json.success) {
                    this.botRules = json.data || [];
                }
            } catch (e) {
                console.error(e);
            }
        },

        openAddModal() {
            this.addForm = {
                name: '',
                keywords: '',
                trigger_type: 'keyword',
                priority: 1,
                reply_text: ''
            };
            this.showAddModal = true;
        },

        viewDetails(rule) {
            this.selectedRule = rule;
        },

        editRule(rule) {
            this.editingId = rule.id;
            this.editForm = {
                name: rule.name,
                keywords: Array.isArray(rule.trigger_keywords) ? rule.trigger_keywords.join(', ') : '',
                trigger_type: rule.trigger_type || 'keyword',
                priority: rule.priority || 1,
                reply_text: rule.reply_payload?.text || ''
            };
            this.showEditModal = true;
        },

        async saveNewRule() {
            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            let kwArr = this.addForm.keywords.split(',').map(s => s.trim()).filter(Boolean);

            try {
                let res = await fetch('{{ route("web.bot.rules.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({
                        name: this.addForm.name,
                        trigger_type: this.addForm.trigger_type,
                        trigger_keywords: kwArr,
                        reply_payload: { text: this.addForm.reply_text },
                        priority: this.addForm.priority,
                        status: 'active'
                    })
                });

                this.showAddModal = false;
                this.loadRules();
            } catch (e) {
                console.error(e);
            }
        },

        async updateExistingRule() {
            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            let kwArr = this.editForm.keywords.split(',').map(s => s.trim()).filter(Boolean);

            try {
                let res = await fetch(`/bot/rules/${this.editingId}/update`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({
                        name: this.editForm.name,
                        trigger_type: this.editForm.trigger_type,
                        trigger_keywords: kwArr,
                        reply_payload: { text: this.editForm.reply_text },
                        priority: this.editForm.priority,
                        status: 'active'
                    })
                });

                this.showEditModal = false;
                this.loadRules();
            } catch (e) {
                console.error(e);
            }
        },

        async deleteRule(id) {
            if (!confirm('Are you sure you want to delete this bot rule?')) return;
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

        async runSimulation() {
            if (!this.simTestMessage) return;
            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            try {
                let res = await fetch('/api/bot/test-match', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({ message: this.simTestMessage })
                });
                let json = await res.json();
                if (json.success && json.matched) {
                    this.simResult = {
                        matched_keyword: json.rule.trigger_keywords?.[0] || 'Trigger match',
                        reply_text: json.rule.reply_payload?.text || 'No response text configured'
                    };
                } else {
                    this.simResult = {
                        matched_keyword: 'None',
                        reply_text: 'No bot rule matched this message. Default fallback will trigger.'
                    };
                }
            } catch (e) {
                console.error(e);
            }
        }
    };
}
</script>
@endsection
