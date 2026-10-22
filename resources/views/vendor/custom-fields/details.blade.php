<!-- Custom Field Details & Tag Usage Modal -->
<div x-show="showDetailsModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div @click.away="showDetailsModal = false" class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-md p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between pb-2 border-b border-slate-800">
            <div>
                <h3 class="font-bold text-base text-white" x-text="selectedField ? selectedField.name : 'Field Details'"></h3>
                <p class="text-xs text-slate-400">Dynamic WhatsApp template variable info</p>
            </div>
            <button type="button" @click="showDetailsModal = false" class="text-slate-400 hover:text-white">&times;</button>
        </div>

        <template x-if="selectedField">
            <div class="space-y-4">
                <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 space-y-2.5 text-xs">
                    <div>
                        <span class="text-slate-500 uppercase tracking-wider text-[10px] font-bold">Variable Merge Tag</span>
                        <div class="mt-1 flex items-center gap-2">
                            <code class="px-2 py-1 rounded bg-slate-900 text-brand-400 font-mono font-bold" x-text="'{{' + selectedField.key + '}}'"></code>
                            <span class="text-[10px] text-slate-500">Use in templates & bot replies</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-800">
                        <div>
                            <span class="text-slate-500 uppercase tracking-wider text-[10px] font-bold">Data Type</span>
                            <p class="text-slate-200 mt-0.5 uppercase" x-text="selectedField.type"></p>
                        </div>
                        <div>
                            <span class="text-slate-500 uppercase tracking-wider text-[10px] font-bold">Required</span>
                            <p class="text-slate-200 mt-0.5" x-text="selectedField.is_required ? 'Yes' : 'No'"></p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-800">
                    <button type="button" @click="showDetailsModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold">Close</button>
                    <button type="button" @click="openEditModal(selectedField); showDetailsModal = false;" class="px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white text-xs font-bold">Edit Field</button>
                </div>
            </div>
        </template>
    </div>
</div>
