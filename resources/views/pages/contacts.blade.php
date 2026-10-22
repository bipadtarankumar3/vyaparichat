@extends('layouts.app')

@section('content')
<div x-data="vyapariContactsApp()" x-init="init()" class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Audience CRM & Contacts</h1>
            <p class="text-xs text-slate-400 mt-1">Manage contacts, custom groups, opt-outs, and bulk Excel imports/exports.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="/api/contacts/dummy-template" class="px-3.5 py-2 rounded-xl bg-slate-900 text-slate-300 border border-slate-700 text-xs font-semibold hover:bg-slate-800 transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Excel Template</span>
            </a>
            <a href="/api/contacts/export" class="px-3.5 py-2 rounded-xl bg-brand-600/20 text-brand-300 border border-brand-500/30 text-xs font-semibold hover:bg-brand-600/30 transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Export (.xlsx)</span>
            </a>
            <button @click="showAddModal = true" class="px-3.5 py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-lg shadow-brand-500/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Add Contact</span>
            </button>
        </div>
    </div>

    <!-- Filter & Table Card -->
    <div class="bg-slate-900 rounded-2xl border border-slate-800 overflow-hidden shadow">
        <div class="p-4 border-b border-slate-800 flex flex-wrap items-center justify-between gap-3">
            <div class="relative w-full sm:w-80">
                <input type="text" x-model="contactSearch" @input.debounce.300ms="loadContacts()" placeholder="Search by name, phone or email..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-brand-500">
            </div>
            <div class="text-xs text-slate-400">
                Showing <span class="font-bold text-white" x-text="contactsList.length"></span> contacts
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950 text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-800">
                    <tr>
                        <th class="px-5 py-3.5">Name</th>
                        <th class="px-5 py-3.5">Phone</th>
                        <th class="px-5 py-3.5">Email</th>
                        <th class="px-5 py-3.5">Groups</th>
                        <th class="px-5 py-3.5">Opt-out</th>
                        <th class="px-5 py-3.5">Date Added</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    <template x-for="c in contactsList" :key="c.id">
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="px-5 py-3.5 font-bold text-slate-200" x-text="c.first_name ? c.first_name + ' ' + (c.last_name || '') : '-'"></td>
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
                        </tr>
                    </template>
                    <tr x-show="contactsList.length === 0">
                        <td colspan="6" class="px-5 py-10 text-center text-slate-500">
                            No contacts found. Click "Add Contact" or download the template to import.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function vyapariContactsApp() {
    return {
        contactSearch: '',
        contactsList: [],
        showAddModal: false,

        init() {
            this.loadContacts();
        },

        async loadContacts() {
            try {
                let res = await fetch(`/api/contacts?search=${encodeURIComponent(this.contactSearch)}`);
                let json = await res.json();
                if (json.success) {
                    this.contactsList = json.data.data || [];
                }
            } catch (e) {
                console.error(e);
            }
        },

        formatDate(dateStr) {
            if (!dateStr) return '';
            let d = new Date(dateStr);
            return d.toLocaleDateString();
        }
    };
}
</script>
@endsection
