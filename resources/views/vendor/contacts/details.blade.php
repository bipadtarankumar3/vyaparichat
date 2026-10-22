<!-- Contact Details Modal / Drawer Component -->
<div x-show="showDetailsModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div @click.away="showDetailsModal = false" class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-lg p-6 space-y-5 shadow-2xl">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <div>
                <h3 class="font-bold text-lg text-white" x-text="selectedContact ? (selectedContact.first_name + ' ' + (selectedContact.last_name || '')) : 'Contact Details'"></h3>
                <p class="text-xs text-slate-400">Complete contact record & WhatsApp message profile</p>
            </div>
            <button type="button" @click="showDetailsModal = false" class="text-slate-400 hover:text-white">&times;</button>
        </div>

        <template x-if="selectedContact">
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-3 p-4 rounded-xl bg-slate-950 border border-slate-800">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">Phone (WhatsApp)</span>
                        <p class="font-mono text-emerald-400 font-semibold text-sm mt-0.5" x-text="'+' + selectedContact.phone_number"></p>
                    </div>
                    <div>
                        <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">Email</span>
                        <p class="text-slate-200 text-xs mt-0.5" x-text="selectedContact.email || 'None'"></p>
                    </div>
                    <div>
                        <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">Marketing Status</span>
                        <div class="mt-1">
                            <span :class="selectedContact.is_opted_out ? 'text-rose-400 bg-rose-500/10 border-rose-500/20' : 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20'" class="px-2 py-0.5 rounded text-[10px] font-bold border" x-text="selectedContact.is_opted_out ? 'Opted Out' : 'Subscribed / Opted In'"></span>
                        </div>
                    </div>
                    <div>
                        <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">Created On</span>
                        <p class="text-slate-400 text-xs mt-0.5" x-text="formatDate(selectedContact.created_at)"></p>
                    </div>
                </div>

                <div>
                    <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">Contact Groups</span>
                    <div class="flex flex-wrap gap-1.5 mt-1.5">
                        <template x-for="g in (selectedContact.groups || [])" :key="g.id">
                            <span class="px-2.5 py-1 rounded-lg bg-slate-800 text-slate-300 border border-slate-700 text-xs font-medium" x-text="g.title"></span>
                        </template>
                        <span x-show="!selectedContact.groups || selectedContact.groups.length === 0" class="text-slate-500 text-xs italic">Not assigned to any group</span>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-800">
                    <button type="button" @click="editContact(selectedContact); showDetailsModal = false;" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700">Edit Contact</button>
                    <a :href="'/chat?phone=' + selectedContact.phone_number" class="px-3.5 py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white text-xs font-bold flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        <span>Open in Live Chat</span>
                    </a>
                </div>
            </div>
        </template>
    </div>
</div>
