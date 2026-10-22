<!-- Add Language Modal Component -->
<div x-show="showAddModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div @click.away="showAddModal = false" class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-md p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <h3 class="text-base font-bold text-white">Add System Language</h3>
            <button type="button" @click="showAddModal = false" class="text-slate-400 hover:text-white">&times;</button>
        </div>

        <form action="{{ route('admin.languages.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block text-slate-300 font-medium mb-1">Language Name *</label>
                <input type="text" name="name" required class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-purple-500" placeholder="e.g. French / Français">
            </div>

            <div>
                <label class="block text-slate-300 font-medium mb-1">Language Code (ISO 639-1) *</label>
                <input type="text" name="code" required maxlength="10" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white font-mono focus:outline-none focus:border-purple-500" placeholder="e.g. fr">
            </div>

            <div>
                <label class="block text-slate-300 font-medium mb-1">Text Direction *</label>
                <select name="direction" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-purple-500">
                    <option value="ltr">LTR (Left to Right - e.g. English, French, Spanish)</option>
                    <option value="rtl">RTL (Right to Left - e.g. Arabic, Hebrew, Urdu)</option>
                </select>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-800">
                <button type="button" @click="showAddModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold cursor-pointer">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold cursor-pointer">Add Language</button>
            </div>
        </form>
    </div>
</div>
