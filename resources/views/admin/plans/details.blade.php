<!-- Plan Tier Details Modal Component -->
<div x-show="showDetailsModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div @click.away="showDetailsModal = false" class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-lg p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <div>
                <h3 class="font-bold text-base text-white" x-text="selectedPlan ? selectedPlan.title : 'Plan Details'"></h3>
                <p class="text-xs text-slate-400">Complete subscription quota and tier limits</p>
            </div>
            <button type="button" @click="showDetailsModal = false" class="text-slate-400 hover:text-white">&times;</button>
        </div>

        <template x-if="selectedPlan">
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-3 p-4 rounded-xl bg-slate-950 border border-slate-800 text-xs">
                    <div>
                        <span class="text-slate-500 uppercase tracking-wider text-[10px] font-bold">Monthly Price</span>
                        <p class="text-emerald-400 font-black text-base mt-0.5" x-text="'₹' + Number(selectedPlan.price_monthly).toLocaleString()"></p>
                    </div>
                    <div>
                        <span class="text-slate-500 uppercase tracking-wider text-[10px] font-bold">Active Status</span>
                        <div class="mt-0.5">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase" :class="selectedPlan.is_active ? 'bg-emerald-500/20 text-emerald-400' : 'bg-slate-800 text-slate-400'" x-text="selectedPlan.is_active ? 'Active' : 'Inactive'"></span>
                        </div>
                    </div>
                    <div>
                        <span class="text-slate-500 uppercase tracking-wider text-[10px] font-bold">Monthly Messages</span>
                        <p class="text-white font-bold mt-0.5" x-text="Number(selectedPlan.max_messages_monthly).toLocaleString()"></p>
                    </div>
                    <div>
                        <span class="text-slate-500 uppercase tracking-wider text-[10px] font-bold">Max Contacts</span>
                        <p class="text-white font-bold mt-0.5" x-text="Number(selectedPlan.max_contacts).toLocaleString()"></p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-800">
                    <button type="button" @click="showDetailsModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold">Close</button>
                    <button type="button" @click="openEditModal(selectedPlan); showDetailsModal = false;" class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-xs font-bold">Edit Tier</button>
                </div>
            </div>
        </template>
    </div>
</div>
