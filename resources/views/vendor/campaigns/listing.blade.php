<!-- Broadcast Campaigns Listing Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
    <template x-for="camp in campaignsList" :key="camp.id">
        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-slate-700 transition space-y-4 shadow flex flex-col justify-between">
            <div class="space-y-3">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="font-bold text-white text-base cursor-pointer hover:text-brand-400 transition" @click="viewDetails(camp)" x-text="camp.title"></h3>
                        <p class="text-xs text-slate-400 mt-0.5" x-text="'Template: ' + (camp.template ? camp.template.name : 'N/A')"></p>
                    </div>
                    <span :class="{
                        'bg-emerald-500/20 text-emerald-400 border-emerald-500/30': camp.status === 'completed',
                        'bg-blue-500/20 text-blue-400 border-blue-500/30': camp.status === 'processing',
                        'bg-amber-500/20 text-amber-400 border-amber-500/30': camp.status === 'paused',
                        'bg-slate-800 text-slate-400 border-slate-700': camp.status === 'draft' || camp.status === 'cancelled'
                    }" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border" x-text="camp.status"></span>
                </div>

                <!-- Progress Bar -->
                <div class="space-y-1">
                    <div class="flex items-center justify-between text-xs text-slate-400">
                        <span>Delivery Progress</span>
                        <span class="font-bold text-slate-200" x-text="camp.total_contacts > 0 ? Math.round(((camp.sent_count + camp.failed_count) / camp.total_contacts) * 100) + '%' : '0%'"></span>
                    </div>
                    <div class="w-full h-2 rounded-full bg-slate-800 overflow-hidden">
                        <div class="h-full bg-brand-500 rounded-full transition-all" :style="'width: ' + (camp.total_contacts > 0 ? ((camp.sent_count + camp.failed_count) / camp.total_contacts) * 100 : 0) + '%'"></div>
                    </div>
                </div>

                <!-- Stats Row -->
                <div class="grid grid-cols-4 gap-2 pt-3 border-t border-slate-800 text-center text-xs">
                    <div>
                        <div class="text-slate-500 text-[10px]">Total</div>
                        <div class="font-bold text-white" x-text="camp.total_contacts"></div>
                    </div>
                    <div>
                        <div class="text-slate-500 text-[10px]">Sent</div>
                        <div class="font-bold text-emerald-400" x-text="camp.sent_count"></div>
                    </div>
                    <div>
                        <div class="text-slate-500 text-[10px]">Delivered</div>
                        <div class="font-bold text-blue-400" x-text="camp.delivered_count"></div>
                    </div>
                    <div>
                        <div class="text-slate-500 text-[10px]">Read</div>
                        <div class="font-bold text-purple-400" x-text="camp.read_count"></div>
                    </div>
                </div>
            </div>

            <!-- Actions Controls -->
            <div class="pt-3 border-t border-slate-800 flex items-center justify-between text-xs">
                <div class="space-x-1">
                    <button @click="viewDetails(camp)" class="px-2.5 py-1 rounded bg-slate-800 text-slate-300 hover:text-white font-semibold text-[11px] border border-slate-700 transition">Details</button>
                    <button x-show="camp.status === 'processing'" @click="campaignAction(camp.id, 'pause')" class="px-2.5 py-1 rounded bg-amber-500/10 text-amber-300 hover:bg-amber-500/20 font-semibold text-[11px] border border-amber-500/30 transition">Pause</button>
                    <button x-show="camp.status === 'paused'" @click="campaignAction(camp.id, 'resume')" class="px-2.5 py-1 rounded bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 font-semibold text-[11px] border border-emerald-500/30 transition">Resume</button>
                    <button x-show="camp.status === 'processing' || camp.status === 'paused'" @click="campaignAction(camp.id, 'cancel')" class="px-2.5 py-1 rounded bg-slate-800 text-slate-400 hover:text-white font-semibold text-[11px] border border-slate-700 transition">Cancel</button>
                </div>
                <button @click="deleteCampaign(camp.id)" class="px-2 py-1 rounded bg-rose-500/10 text-rose-400 hover:bg-rose-500/20 text-[11px] border border-rose-500/30 transition">Delete</button>
            </div>
        </div>
    </template>
    <div x-show="campaignsList.length === 0" class="col-span-full p-12 text-center rounded-2xl bg-slate-900 border border-slate-800 text-slate-500">
        No active broadcast campaigns found. Click "Create Broadcast" to launch your first WhatsApp campaign blast.
    </div>
</div>
