@extends('layouts.app')

@section('content')
<div x-data="vyapariContactsManager()" x-init="init()" class="space-y-5">
    <!-- Activity Action Header -->
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
            <button @click="openAddModal()" class="px-3.5 py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-lg shadow-brand-500/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Add Contact</span>
            </button>
        </div>
    </div>

    <!-- Structured Modular Views -->
    @include('vendor.contacts.listing')
    @include('vendor.contacts.add')
    @include('vendor.contacts.update')
    @include('vendor.contacts.details')
</div>

<script>
function vyapariContactsManager() {
    return {
        contactSearch: '',
        contactsList: [],
        showAddModal: false,
        showEditModal: false,
        showDetailsModal: false,
        selectedContact: null,
        editingId: null,

        addForm: {
            first_name: '',
            last_name: '',
            phone: '',
            email: '',
            contact_group_id: '',
            lead_status: 'new'
        },

        editForm: {
            first_name: '',
            last_name: '',
            phone: '',
            email: '',
            contact_group_id: '',
            lead_status: 'new'
        },

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

        openAddModal() {
            this.addForm = {
                first_name: '',
                last_name: '',
                phone: '',
                email: '',
                contact_group_id: '',
                lead_status: 'new'
            };
            this.showAddModal = true;
        },

        async saveNewContact() {
            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            try {
                let res = await fetch('{{ route("web.contacts.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify(this.addForm)
                });
                this.showAddModal = false;
                this.loadContacts();
            } catch (e) {
                console.error(e);
            }
        },

        editContact(c) {
            this.editingId = c.id;
            this.editForm = {
                first_name: c.first_name || '',
                last_name: c.last_name || '',
                phone: c.phone_number || '',
                email: c.email || '',
                contact_group_id: (c.groups && c.groups[0]) ? c.groups[0].id : '',
                lead_status: c.notes || 'new'
            };
            this.showEditModal = true;
        },

        async updateExistingContact() {
            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            try {
                let res = await fetch(`/contacts/${this.editingId}/update`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify(this.editForm)
                });
                this.showEditModal = false;
                this.loadContacts();
            } catch (e) {
                console.error(e);
            }
        },

        viewDetails(c) {
            this.selectedContact = c;
            this.showDetailsModal = true;
        },

        async deleteContact(id) {
            if (!confirm('Are you sure you want to delete this contact?')) return;
            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            try {
                await fetch(`/contacts/${id}/delete`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    }
                });
                this.loadContacts();
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
