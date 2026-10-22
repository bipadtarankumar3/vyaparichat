<!-- Language Details Modal Component -->
<div x-show="showDetailsModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div @click.away="showDetailsModal = false" class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-md p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <div>
                <h3 class="text-base font-bold text-white" x-text="selectedLanguage ? selectedLanguage.name : 'Language Details'"></h3>
                <p class="text-xs text-slate-400 font-mono" x-text="selectedLanguage ? selectedLanguage.code : ''"></p>
            </div>
            <button type="button" @click="showDetailsModal = false" class="text-slate-400 hover:text-white">&times;</button>
        </div>

        <template x-if="selectedLanguage">
            <div class="space-y-4 text-xs">
                <div class="grid grid-cols-2 gap-3 p-4 rounded-xl bg-slate-950 border border-slate-800">
                    <div>
                        <span class="text-slate-500 uppercase tracking-wider text-[10px] font-bold">Writing Mode</span>
                        <p class="text-white font-bold mt-0.5" x-text="selectedLanguage.direction ? selectedLanguage.direction.toUpperCase() : 'LTR'"></p>
                    </div>
                    <div>
                        <span class="text-slate-500 uppercase tracking-wider text-[10px] font-bold">System Role</span>
                        <div class="mt-0.5">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase" :class="selectedLanguage.is_default ? 'bg-purple-500/20 text-purple-300' : 'bg-slate-800 text-slate-400'" x-text="selectedLanguage.is_default ? 'Default Master' : 'Active Locale'"></span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-800">
                    <button type="button" @click="showDetailsModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold cursor-pointer">Close</button>
                    <button type="button" @click="openEditModal(selectedLanguage); showDetailsModal = false;" class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold cursor-pointer">Edit Locale</button>
                </div>
            </div>
        </template>
    </div>
</div>
