<!-- Edit Language / Translations Modal Component -->
<div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div @click.away="showEditModal = false" class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-md p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <h3 class="text-base font-bold text-white">Update Language Settings</h3>
            <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-white">&times;</button>
        </div>

        <template x-if="editingLanguage">
            <div class="space-y-4 text-xs">
                <div>
                    <label class="block text-slate-300 font-medium mb-1">Language Name</label>
                    <input type="text" :value="editingLanguage.name" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-purple-500">
                </div>

                <div>
                    <label class="block text-slate-300 font-medium mb-1">Language Code</label>
                    <input type="text" :value="editingLanguage.code" disabled class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-slate-400 font-mono cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-slate-300 font-medium mb-1">Direction</label>
                    <input type="text" :value="editingLanguage.direction ? editingLanguage.direction.toUpperCase() : 'LTR'" disabled class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-slate-400 cursor-not-allowed">
                </div>

                <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 space-y-1">
                    <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">Translation Strings File</span>
                    <p class="font-mono text-[11px] text-purple-300" x-text="'resources/lang/' + editingLanguage.code + '.json'"></p>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-800">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold cursor-pointer">Close</button>
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold cursor-pointer">Save Locale</button>
                </div>
            </div>
        </template>
    </div>
</div>
