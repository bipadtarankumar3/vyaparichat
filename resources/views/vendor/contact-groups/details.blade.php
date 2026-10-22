<!-- Contact Group Details Modal Component -->
<div x-show="showDetailsModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div @click.away="showDetailsModal = false" class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-md p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between pb-2 border-b border-slate-800">
            <div>
                <h3 class="font-bold text-base text-white" x-text="selectedGroup ? selectedGroup.title : 'Group Details'"></h3>
                <p class="text-xs text-slate-400">Audience segment breakdown & targeting</p>
            </div>
            <button type="button" @click="showDetailsModal = false" class="text-slate-400 hover:text-white">&times;</button>
        </div>

        <template x-if="selectedGroup">
            <div class="space-y-4">
                <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 space-y-2 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 uppercase tracking-wider text-[10px] font-bold">Group ID</span>
                        <span class="font-mono text-slate-300" x-text="'#' + selectedGroup.id"></span>
                    </div>
                    <div>
                        <span class="text-slate-500 uppercase tracking-wider text-[10px] font-bold">Description</span>
                        <p class="text-slate-200 mt-1" x-text="selectedGroup.description || 'None'"></p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-800">
                    <button type="button" @click="showDetailsModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold">Close</button>
                    <a :href="'{{ route('web.contacts') }}?group_id=' + selectedGroup.id" class="px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white text-xs font-bold">Open Contacts</a>
                </div>
            </div>
        </template>
    </div>
</div>
