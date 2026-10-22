<!-- Template Details & Smartphone Preview Modal -->
<div x-show="showDetailsModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div @click.away="showDetailsModal = false" class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-lg p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <div>
                <h3 class="font-bold text-base text-white font-mono" x-text="selectedTemplate ? selectedTemplate.name : 'Template Details'"></h3>
                <p class="text-xs text-slate-400">Meta WhatsApp Business API Pre-Approved HSM Specification</p>
            </div>
            <button type="button" @click="showDetailsModal = false" class="text-slate-400 hover:text-white">&times;</button>
        </div>

        <template x-if="selectedTemplate">
            <div class="space-y-4">
                <div class="flex items-center justify-between text-xs p-3 rounded-xl bg-slate-950 border border-slate-800">
                    <span class="text-slate-400">Category: <strong class="text-white" x-text="selectedTemplate.category"></strong></span>
                    <span class="text-slate-400">Language: <strong class="text-white" x-text="selectedTemplate.language || 'en_US'"></strong></span>
                    <span :class="selectedTemplate.status === 'APPROVED' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-amber-500/20 text-amber-400'" class="px-2 py-0.5 rounded text-[10px] font-bold" x-text="selectedTemplate.status"></span>
                </div>

                <!-- Smartphone Message Bubble Preview -->
                <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 flex justify-center">
                    <div class="w-full max-w-sm rounded-2xl bg-[#0b141a] p-4 border border-emerald-900/30 relative">
                        <div class="text-[11px] text-emerald-400 font-semibold mb-1 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.299.144.347.491 1.199.534 1.286.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.179.18-.077.355.101.174.449.741.964 1.2 1.348 1.2 1.503.805 1.719.718.217-.087.925-.436 1.17-.587.246-.151.419-.13.578-.051.159.079.925.436 1.084.515.159.079.266.118.304.188.038.07.038.405-.106.81z"/></svg>
                            <span>WhatsApp Official HSM</span>
                        </div>
                        <template x-for="comp in selectedTemplate.components" :key="comp.type">
                            <div x-show="comp.type === 'BODY'" class="text-xs text-slate-100 whitespace-pre-wrap leading-relaxed" x-text="comp.text"></div>
                        </template>
                        <div class="text-right text-[10px] text-slate-500 mt-2">Just now ✓✓</div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-800">
                    <button type="button" @click="showDetailsModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold">Close</button>
                    <a :href="'/campaigns?template_id=' + selectedTemplate.id" class="px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white text-xs font-bold flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                        <span>Launch Broadcast</span>
                    </a>
                </div>
            </div>
        </template>
    </div>
</div>
