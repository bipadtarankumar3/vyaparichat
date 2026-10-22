<!-- Bot Rules Listing Component -->
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="font-bold text-slate-200 text-base">Active Auto-Reply Rules</h2>
        <span class="text-xs text-slate-400">Total rules: <strong class="text-white" x-text="botRules.length"></strong></span>
    </div>

    <div class="space-y-3">
        <template x-for="rule in botRules" :key="rule.id">
            <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 hover:border-slate-700 transition flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow">
                <div class="space-y-1.5">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-white text-sm cursor-pointer hover:text-brand-400 transition" @click="viewDetails(rule)" x-text="rule.name"></span>
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
                    <button @click="viewDetails(rule)" class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-brand-300 font-semibold text-[11px] border border-slate-700 transition">Details</button>
                    <button @click="editRule(rule)" class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-[11px] border border-slate-700 transition">Update</button>
                    <button @click="deleteRule(rule.id)" class="px-2.5 py-1 rounded bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 font-semibold text-[11px] border border-rose-500/30 transition">Delete</button>
                </div>
            </div>
        </template>
        <div x-show="botRules.length === 0" class="p-8 text-center rounded-2xl bg-slate-900 border border-slate-800 text-slate-500 text-xs">
            No bot rules configured yet. Click "Add Bot Rule" to set up automatic customer replies.
        </div>
    </div>
</div>
