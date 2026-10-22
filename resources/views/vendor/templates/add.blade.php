<!-- Create WhatsApp Template Modal -->
<div x-show="showAddModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div @click.away="showAddModal = false" class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-md p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between pb-2 border-b border-slate-800">
            <h3 class="font-bold text-base text-white">Create New WhatsApp Template</h3>
            <button type="button" @click="showAddModal = false" class="text-slate-400 hover:text-white">&times;</button>
        </div>

        <form action="{{ route('web.templates.store') }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Template Identifier Name *</label>
                <input type="text" name="name" required placeholder="e.g. order_dispatch_update" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-brand-500">
                <span class="text-[10px] text-slate-500">Only lowercase alphanumeric and underscores allowed.</span>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Category</label>
                    <select name="category" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-brand-500">
                        <option value="MARKETING">Marketing</option>
                        <option value="UTILITY" selected>Utility</option>
                        <option value="AUTHENTICATION">Authentication (OTP)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Language</label>
                    <select name="language" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-brand-500">
                        <option value="en_US" selected>English (en_US)</option>
                        <option value="hi">Hindi (hi)</option>
                        <option value="es">Spanish (es)</option>
                        <option value="ar">Arabic (ar)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Body Text Content *</label>
                <textarea name="body_text" required rows="4" placeholder="Hello {{1}}, your order #{{2}} has been confirmed and dispatched." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-brand-500"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-800">
                <button type="button" @click="showAddModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white text-xs font-bold shadow-lg shadow-brand-500/20">Submit Template</button>
            </div>
        </form>
    </div>
</div>
