<!-- Agent Details Modal Component -->
<div x-show="showDetailsModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
    <div @click.away="showDetailsModal = false" class="w-full max-w-md bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <div>
                <h3 class="font-bold text-white text-base">Agent Profile & Permissions</h3>
                <p class="text-xs text-slate-400">Team seat credentials & conversation assignments</p>
            </div>
            <button type="button" @click="showDetailsModal = false" class="text-slate-400 hover:text-white">&times;</button>
        </div>

        <template x-if="selectedAgent">
            <div class="space-y-4">
                <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-950 border border-slate-800">
                    <div class="w-10 h-10 rounded-full bg-brand-500/20 text-brand-400 flex items-center justify-center font-bold text-base">
                        <span x-text="selectedAgent.name ? selectedAgent.name.charAt(0).toUpperCase() : '?'"></span>
                    </div>
                    <div>
                        <h4 class="font-bold text-white text-sm" x-text="selectedAgent.name"></h4>
                        <p class="text-xs text-slate-400 font-mono" x-text="selectedAgent.email"></p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div class="p-3 rounded-xl bg-slate-950 border border-slate-800">
                        <span class="text-slate-500 uppercase tracking-wider text-[10px] font-bold">Assigned Role</span>
                        <p class="text-purple-300 font-bold uppercase mt-1" x-text="selectedRole"></p>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-950 border border-slate-800">
                        <span class="text-slate-500 uppercase tracking-wider text-[10px] font-bold">Seat Status</span>
                        <p class="text-emerald-400 font-bold mt-1">Active</p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-800">
                    <button type="button" @click="showDetailsModal = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-xs font-semibold hover:bg-slate-700">Close</button>
                    <button type="button" @click="openEditModal(selectedAgent, selectedRole); showDetailsModal = false;" class="px-4 py-2 rounded-xl bg-brand-600 text-white text-xs font-bold hover:bg-brand-500">Edit Role</button>
                </div>
            </div>
        </template>
    </div>
</div>
