<!-- Add Agent Modal Component -->
<div x-show="showAddModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
    <div @click.away="showAddModal = false" class="w-full max-w-md bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <h3 class="font-bold text-white text-base">Add Team Member / Agent</h3>
            <button type="button" @click="showAddModal = false" class="text-slate-400 hover:text-white">&times;</button>
        </div>

        <form method="POST" action="{{ route('web.agents.store') }}" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-semibold text-slate-300 mb-1">Agent Full Name *</label>
                <input type="text" name="name" required placeholder="e.g. Rahul Sharma" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-slate-200 focus:outline-none focus:border-brand-500">
            </div>

            <div>
                <label class="block font-semibold text-slate-300 mb-1">Agent Email (Login ID) *</label>
                <input type="email" name="email" required placeholder="rahul@store.com" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-slate-200 focus:outline-none focus:border-brand-500">
            </div>

            <div>
                <label class="block font-semibold text-slate-300 mb-1">Temporary Password *</label>
                <input type="password" name="password" required minlength="6" placeholder="At least 6 characters" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-slate-200 focus:outline-none focus:border-brand-500">
            </div>

            <div>
                <label class="block font-semibold text-slate-300 mb-1">Staff Role *</label>
                <select name="role" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-slate-200 focus:outline-none focus:border-brand-500">
                    <option value="agent">Support Agent (Live Chat & Contacts)</option>
                    <option value="admin">Vendor Manager (Full Workspace Access)</option>
                </select>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                <button type="button" @click="showAddModal = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700 transition">Cancel</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold transition">Save Agent</button>
            </div>
        </form>
    </div>
</div>
