<!-- Vendor 360 Details Modal Component -->
<div x-show="showDetailsModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
    <div @click.away="showDetailsModal = false" class="w-full max-w-lg bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-2xl space-y-5">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <div>
                <h3 class="font-bold text-white text-base" x-text="selectedVendor ? selectedVendor.name : 'Vendor Details'"></h3>
                <p class="text-xs text-slate-400">Complete tenant profile & quota overview</p>
            </div>
            <button type="button" @click="showDetailsModal = false" class="text-slate-400 hover:text-white">&times;</button>
        </div>

        <template x-if="selectedVendor">
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-3 p-4 rounded-xl bg-slate-950 border border-slate-800 text-xs">
                    <div>
                        <span class="text-slate-500 uppercase tracking-wider text-[10px] font-bold">Slug</span>
                        <p class="font-mono text-purple-400 font-semibold mt-0.5" x-text="selectedVendor.slug"></p>
                    </div>
                    <div>
                        <span class="text-slate-500 uppercase tracking-wider text-[10px] font-bold">Email</span>
                        <p class="text-slate-200 mt-0.5" x-text="selectedVendor.email"></p>
                    </div>
                    <div>
                        <span class="text-slate-500 uppercase tracking-wider text-[10px] font-bold">WhatsApp Status</span>
                        <p class="text-emerald-400 font-bold mt-0.5 uppercase" x-text="selectedVendor.whatsapp_status || 'Disconnected'"></p>
                    </div>
                    <div>
                        <span class="text-slate-500 uppercase tracking-wider text-[10px] font-bold">Account Status</span>
                        <p class="font-bold mt-0.5 uppercase" :class="selectedVendor.status === 'active' ? 'text-emerald-400' : 'text-rose-400'" x-text="selectedVendor.status"></p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-800">
                    <button type="button" @click="showDetailsModal = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-xs font-semibold hover:bg-slate-700">Close</button>
                    <form method="POST" :action="'/admin/vendors/' + selectedVendor.id + '/impersonate'" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs">Visit Vendor Console ⚡</button>
                    </form>
                </div>
            </div>
        </template>
    </div>
</div>
