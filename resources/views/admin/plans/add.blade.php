<!-- Create Plan Tier Modal Component -->
<div x-show="showAddModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div @click.away="showAddModal = false" class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-lg p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <h3 class="font-bold text-base text-white">Create New Plan Tier</h3>
            <button type="button" @click="showAddModal = false" class="text-slate-400 hover:text-white">&times;</button>
        </div>

        <form method="POST" action="{{ route('admin.plans.store') }}" class="space-y-4 text-xs">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div class="col-span-2">
                    <label class="block font-semibold text-slate-300 mb-1">Plan Title *</label>
                    <input type="text" name="title" required placeholder="e.g. Pro Growth Plan" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-slate-200 focus:outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-300 mb-1">Monthly Price (INR) *</label>
                    <input type="number" name="price_monthly" value="1499" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-slate-200 focus:outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-300 mb-1">Max Contacts Limit *</label>
                    <input type="number" name="max_contacts" value="5000" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-slate-200 focus:outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-300 mb-1">Monthly Messages *</label>
                    <input type="number" name="max_messages_monthly" value="25000" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-slate-200 focus:outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-300 mb-1">Team Staff Seats</label>
                    <input type="number" name="max_team_members" value="5" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-slate-200 focus:outline-none focus:border-purple-500">
                </div>
                <div class="col-span-2">
                    <label class="block font-semibold text-slate-300 mb-1">Description</label>
                    <textarea name="description" rows="2" placeholder="Plan features summary..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-slate-200 focus:outline-none focus:border-purple-500"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                <button type="button" @click="showAddModal = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700 transition">Cancel</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold transition">Save Plan Tier</button>
            </div>
        </form>
    </div>
</div>
