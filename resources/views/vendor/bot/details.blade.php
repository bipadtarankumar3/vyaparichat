<!-- Bot Rule Details & Dry-Run Simulator Playground -->
<div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 space-y-4 shadow flex flex-col justify-start">
    <div class="flex items-center gap-2.5">
        <span class="p-2.5 rounded-xl bg-brand-500/20 text-brand-400 text-lg">🧪</span>
        <div>
            <h3 class="font-bold text-white text-base">Dry-Run Simulator</h3>
            <p class="text-xs text-slate-400">Test incoming customer query live</p>
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

    <template x-if="selectedRule">
        <div class="mt-4 p-4 rounded-xl bg-slate-950 border border-slate-800 space-y-2 text-xs">
            <span class="text-slate-500 uppercase tracking-wider text-[10px] font-bold">Inspecting Rule</span>
            <p class="font-bold text-white" x-text="selectedRule.name"></p>
            <p class="text-slate-400" x-text="'Trigger Type: ' + selectedRule.trigger_type"></p>
            <p class="text-slate-400" x-text="'Priority: ' + selectedRule.priority"></p>
        </div>
    </template>
</div>
