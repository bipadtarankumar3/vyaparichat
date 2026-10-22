<!-- Contacts Listing Table Component -->
<div class="bg-slate-900 rounded-2xl border border-slate-800 overflow-hidden shadow">
    <div class="p-4 border-b border-slate-800 flex flex-wrap items-center justify-between gap-3">
        <div class="relative w-full sm:w-80">
            <input type="text" x-model="contactSearch" @input.debounce.300ms="loadContacts()" placeholder="Search by name, phone or email..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-brand-500">
        </div>
        <div class="flex items-center gap-3">
            <span class="text-xs text-slate-400">
                Total Contacts: <strong class="text-white" x-text="contactsList.length"></strong>
            </span>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-950 text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-800">
                <tr>
                    <th class="px-5 py-3.5">Name</th>
                    <th class="px-5 py-3.5">Phone</th>
                    <th class="px-5 py-3.5">Email</th>
                    <th class="px-5 py-3.5">Group</th>
                    <th class="px-5 py-3.5">Opt-out</th>
                    <th class="px-5 py-3.5">Date Added</th>
                    <th class="px-5 py-3.5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
                <template x-for="c in contactsList" :key="c.id">
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="px-5 py-3.5 font-bold text-slate-200">
                            <button type="button" @click="viewDetails(c)" class="hover:text-brand-400 text-left transition" x-text="c.first_name ? c.first_name + ' ' + (c.last_name || '') : '-'"></button>
                        </td>
                        <td class="px-5 py-3.5 font-mono text-emerald-400 font-semibold" x-text="'+' + c.phone_number"></td>
                        <td class="px-5 py-3.5 text-slate-400" x-text="c.email || '-'"></td>
                        <td class="px-5 py-3.5">
                            <template x-for="g in c.groups" :key="g.id">
                                <span class="inline-block px-2 py-0.5 rounded bg-slate-800 text-slate-300 border border-slate-700 mr-1 text-[10px]" x-text="g.title"></span>
                            </template>
                            <span x-show="!c.groups || c.groups.length === 0" class="text-slate-600 text-[11px]">-</span>
                        </td>
                        <td class="px-5 py-3.5">
                            <span :class="c.is_opted_out ? 'text-rose-400 bg-rose-500/10 px-2 py-0.5 rounded border border-rose-500/20' : 'text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded border border-emerald-500/20'" class="text-[10px] font-bold" x-text="c.is_opted_out ? 'Opted Out' : 'Active'"></span>
                        </td>
                        <td class="px-5 py-3.5 text-slate-500" x-text="formatDate(c.created_at)"></td>
                        <td class="px-5 py-3.5 text-right space-x-1">
                            <button @click="viewDetails(c)" class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-brand-300 font-semibold text-[11px] border border-slate-700 transition">Details</button>
                            <button @click="editContact(c)" class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-[11px] border border-slate-700 transition">Update</button>
                            <button @click="deleteContact(c.id)" class="px-2.5 py-1 rounded bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 font-semibold text-[11px] border border-rose-500/30 transition">Delete</button>
                        </td>
                    </tr>
                </template>
                <tr x-show="contactsList.length === 0">
                    <td colspan="7" class="px-5 py-10 text-center text-slate-500">
                        No contacts found. Click "Add Contact" or download the template to import.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
