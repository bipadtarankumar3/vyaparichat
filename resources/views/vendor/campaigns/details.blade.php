<!-- Campaign Details & Real-Time Analytics Modal -->
<div x-show="showDetailsModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div @click.away="showDetailsModal = false" class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-lg p-6 space-y-5 shadow-2xl">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <div>
                <h3 class="font-bold text-lg text-white" x-text="selectedCampaign ? selectedCampaign.title : 'Campaign Analytics'"></h3>
                <p class="text-xs text-slate-400">WhatsApp Broadcast throughput & delivery metrics breakdown</p>
            </div>
            <button type="button" @click="showDetailsModal = false" class="text-slate-400 hover:text-white">&times;</button>
        </div>

        <template x-if="selectedCampaign">
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-3 p-4 rounded-xl bg-slate-950 border border-slate-800 text-xs">
                    <div>
                        <span class="text-slate-500 uppercase tracking-wider text-[10px] font-bold">Template Used</span>
                        <p class="text-white font-mono font-semibold mt-0.5" x-text="selectedCampaign.template ? selectedCampaign.template.name : 'Custom Direct'"></p>
                    </div>
                    <div>
                        <span class="text-slate-500 uppercase tracking-wider text-[10px] font-bold">Current Status</span>
                        <div class="mt-0.5">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-brand-500/20 text-brand-400 border border-brand-500/30" x-text="selectedCampaign.status"></span>
                        </div>
                    </div>
                    <div>
                        <span class="text-slate-500 uppercase tracking-wider text-[10px] font-bold">Audience Audience</span>
                        <p class="text-slate-300 mt-0.5" x-text="selectedCampaign.contact_group ? selectedCampaign.contact_group.title : 'All Opted-in Contacts'"></p>
                    </div>
                    <div>
                        <span class="text-slate-500 uppercase tracking-wider text-[10px] font-bold">Created At</span>
                        <p class="text-slate-400 mt-0.5" x-text="new Date(selectedCampaign.created_at).toLocaleString()"></p>
                    </div>
                </div>

                <!-- 4 Detailed Metric Counters -->
                <div class="grid grid-cols-4 gap-3 text-center">
                    <div class="p-3 rounded-xl bg-slate-950 border border-slate-800">
                        <span class="text-[10px] font-bold text-slate-500 uppercase">Target</span>
                        <p class="text-lg font-black text-white mt-1" x-text="selectedCampaign.total_contacts"></p>
                    </div>
                    <div class="p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20">
                        <span class="text-[10px] font-bold text-emerald-400 uppercase">Sent</span>
                        <p class="text-lg font-black text-emerald-400 mt-1" x-text="selectedCampaign.sent_count"></p>
                    </div>
                    <div class="p-3 rounded-xl bg-blue-500/10 border border-blue-500/20">
                        <span class="text-[10px] font-bold text-blue-400 uppercase">Delivered</span>
                        <p class="text-lg font-black text-blue-400 mt-1" x-text="selectedCampaign.delivered_count"></p>
                    </div>
                    <div class="p-3 rounded-xl bg-purple-500/10 border border-purple-500/20">
                        <span class="text-[10px] font-bold text-purple-400 uppercase">Read</span>
                        <p class="text-lg font-black text-purple-400 mt-1" x-text="selectedCampaign.read_count"></p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-800">
                    <button type="button" @click="showDetailsModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold">Close</button>
                    <button type="button" @click="editCampaign(selectedCampaign); showDetailsModal = false;" class="px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white text-xs font-bold">Update Campaign</button>
                </div>
            </div>
        </template>
    </div>
</div>
