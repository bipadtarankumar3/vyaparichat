<!-- Templates Listing Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
    <template x-for="tpl in templatesList" :key="tpl.id">
        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-slate-700 transition space-y-4 shadow flex flex-col justify-between">
            <div class="space-y-3">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="font-bold text-white text-base font-mono cursor-pointer hover:text-brand-400 transition" @click="viewTemplate(tpl)" x-text="tpl.name"></h3>
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
                <div class="flex items-center gap-2">
                    <button @click="viewTemplate(tpl)" class="text-brand-400 hover:text-brand-300 font-semibold">Details</button>
                    <button @click="deleteTemplate(tpl.id)" class="text-rose-400 hover:text-rose-300 font-semibold">Delete</button>
                </div>
            </div>
        </div>
    </template>
    <div x-show="templatesList.length === 0" class="col-span-full p-12 text-center rounded-2xl bg-slate-900 border border-slate-800 text-slate-500">
        No templates found. Click "Create Template" or "Sync with Meta" to fetch your approved WhatsApp templates.
    </div>
</div>
