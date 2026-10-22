<!-- Page Details & HTML Content Preview Modal Component -->
<div x-show="showDetailsModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div @click.away="showDetailsModal = false" class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-xl p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <div>
                <h3 class="text-base font-bold text-white" x-text="selectedPage ? selectedPage.title : 'Page Details'"></h3>
                <p class="text-xs text-slate-400 font-mono" x-text="selectedPage ? '/page/' + selectedPage.slug : ''"></p>
            </div>
            <button type="button" @click="showDetailsModal = false" class="text-slate-400 hover:text-white">&times;</button>
        </div>

        <template x-if="selectedPage">
            <div class="space-y-4 text-xs">
                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-950 border border-slate-800">
                    <span class="text-slate-400">Category Type: <strong class="text-white" x-text="selectedPage.type"></strong></span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase" :class="selectedPage.status === 'published' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-slate-800 text-slate-400'" x-text="selectedPage.status"></span>
                </div>

                <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 max-h-60 overflow-y-auto text-slate-300 leading-relaxed text-xs">
                    <div x-html="selectedPage.content || '<em>No HTML body supplied for this page yet.</em>'"></div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-800">
                    <button type="button" @click="showDetailsModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold">Close</button>
                    <a :href="'/page/' + selectedPage.slug" target="_blank" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-purple-300 font-semibold border border-slate-700">Open Public Page ↗</a>
                    <button type="button" @click="openEdit(selectedPage); showDetailsModal = false;" class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold">Edit Page</button>
                </div>
            </div>
        </template>
    </div>
</div>
