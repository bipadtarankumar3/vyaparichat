<!-- Edit Auto-Reply Rule Modal -->
<div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div @click.away="showEditModal = false" class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-md p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between pb-2 border-b border-slate-800">
            <h3 class="font-bold text-base text-white">Update Bot Rule</h3>
            <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-white">&times;</button>
        </div>

        <form @submit.prevent="updateExistingRule()" class="space-y-3">
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Rule Name *</label>
                <input type="text" x-model="editForm.name" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-brand-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Trigger Keywords (comma separated) *</label>
                <input type="text" x-model="editForm.keywords" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-brand-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Trigger Type</label>
                    <select x-model="editForm.trigger_type" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-brand-500">
                        <option value="keyword">Keyword (Contains)</option>
                        <option value="exact">Exact Match</option>
                        <option value="welcome">Welcome Message</option>
                        <option value="fallback">Fallback Default</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Priority</label>
                    <input type="number" x-model="editForm.priority" min="1" max="100" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-brand-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Bot Auto-Reply Text *</label>
                <textarea x-model="editForm.reply_text" required rows="3" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-brand-500"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-800">
                <button type="button" @click="showEditModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white text-xs font-bold shadow-lg shadow-brand-500/20">Update Bot Rule</button>
            </div>
        </form>
    </div>
</div>
