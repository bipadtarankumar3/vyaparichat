@extends('layouts.app')

@section('content')
<div x-data="vyapariBotApp()" x-init="init()" class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Bot Automations & AI Copilot</h1>
            <p class="text-xs text-slate-400 mt-1">Configure auto-responders, priority triggers, and dry-run test incoming customer queries in real time.</p>
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
                        </div>
                        <div class="text-right">
                            <span :class="rule.status === 'active' ? 'text-emerald-400' : 'text-slate-500'" class="text-xs font-semibold uppercase tracking-wider" x-text="rule.status"></span>
                        </div>
                    </div>
                </template>
                <div x-show="botRules.length === 0" class="p-8 text-center rounded-2xl bg-slate-900 border border-slate-800 text-slate-500 text-xs">
                    No bot rules configured yet.
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
                <input type="text" x-model="simTestMessage" @keydown.enter="runSimulation()" placeholder="e.g. What is the price?" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-brand-500">
                <button @click="runSimulation()" class="w-full py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs transition shadow-lg shadow-brand-500/20">
                    Run Test Match
                </button>
            </div>

            <!-- Sim Result -->
            <template x-if="simResult">
                <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 text-xs space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400 font-semibold">Match Status:</span>
                        <span :class="simResult.matched ? 'text-emerald-400 font-bold' : 'text-rose-400 font-bold'" x-text="simResult.matched ? 'Trigger Matched' : 'No Match Found'"></span>
                    </div>
                    <template x-if="simResult.matched">
                        <div class="space-y-2">
                            <div>
                                <div class="text-slate-500 text-[10px] uppercase font-bold">Triggered Rule:</div>
                                <div class="text-white font-bold mt-0.5" x-text="simResult.rule.name"></div>
                            </div>
                            <div>
                                <div class="text-slate-500 text-[10px] uppercase font-bold">Bot Output:</div>
                                <div class="text-emerald-300 font-mono p-2.5 rounded-lg bg-slate-900 border border-slate-800 mt-1 whitespace-pre-wrap" x-text="simResult.simulated_reply.text"></div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
function vyapariBotApp() {
    return {
        botRules: [],
        simTestMessage: '',
        simResult: null,

        init() {
            this.loadBotRules();
        },

        async loadBotRules() {
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

        async runSimulation() {
            if (!this.simTestMessage.trim()) return;
            try {
                let res = await fetch('/api/bot-replies/simulate', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    },
                    body: JSON.stringify({ message: this.simTestMessage })
                });
                let json = await res.json();
                if (json.success) {
                    this.simResult = json.data;
                }
            } catch (e) {
                console.error(e);
            }
        }
    };
}
</script>
@endsection
