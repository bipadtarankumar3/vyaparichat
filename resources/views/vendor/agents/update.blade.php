<!-- Update Agent Role Modal Component -->
<div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
    <div @click.away="showEditModal = false" class="w-full max-w-md bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <h3 class="font-bold text-white text-base">Update Agent Access & Role</h3>
            <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-white">&times;</button>
        </div>

        <template x-if="editingAgent">
            <form :action="'/agents/' + editingAgent.id + '/update'" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-300 mb-1">Agent Name</label>
                    <input type="text" :value="editingAgent.name" disabled class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-slate-400 cursor-not-allowed">
                </div>

                <div>
                    <label class="block font-semibold text-slate-300 mb-1">Agent Email (Login ID)</label>
                    <input type="email" :value="editingAgent.email" disabled class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-slate-400 cursor-not-allowed">
                </div>

                <div>
                    <label class="block font-semibold text-slate-300 mb-1">Staff Role *</label>
                    <select name="role" x-model="editingRole" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-slate-200 focus:outline-none focus:border-brand-500">
                        <option value="agent">Support Agent (Live Chat & Contacts)</option>
                        <option value="admin">Vendor Manager (Full Workspace Access)</option>
                    </select>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700 transition">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold transition">Update Role</button>
                </div>
            </form>
        </template>
    </div>
</div>
