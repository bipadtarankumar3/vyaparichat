@extends('layouts.app')

@section('content')
<div x-data="{ showModal: false, isEditing: false, editId: null, name: '', key: '', type: 'text', isRequired: false }" class="space-y-6 max-w-4xl">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
                <span>➕</span>
                <span>Custom Input Fields (Add Input)</span>
            </h1>
            <p class="text-xs text-slate-400 mt-1">Define custom data fields for customer profiles (e.g. City, Order Number, Birthday, Membership ID).</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="isEditing = false; name = ''; key = ''; type = 'text'; isRequired = false; showModal = true" class="px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs transition flex items-center gap-1.5 shadow-lg shadow-brand-500/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Add Custom Field</span>
            </button>
            <a href="{{ route('web.contacts') }}" class="px-4 py-2 rounded-xl bg-slate-900 border border-slate-700 hover:bg-slate-800 text-slate-300 font-semibold text-xs transition">
                ← All Contacts
            </a>
        </div>
    </div>

    <!-- Custom Fields Table -->
    <div class="bg-slate-900 rounded-2xl border border-slate-800 overflow-hidden shadow">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950 text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-800">
                    <tr>
                        <th class="px-5 py-3.5">Input Field Name</th>
                        <th class="px-5 py-3.5">Key / Variable</th>
                        <th class="px-5 py-3.5">Input Type</th>
                        <th class="px-5 py-3.5">Required</th>
                        <th class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($fields as $field)
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="px-5 py-4 font-bold text-white">{{ $field->name }}</td>
                            <td class="px-5 py-4 font-mono text-brand-400 text-[11px]">&#123;&#123; {{ $field->key }} &#125;&#125;</td>
                            <td class="px-5 py-4 text-slate-300 uppercase text-[10px]">{{ $field->type ?? 'text' }}</td>
                            <td class="px-5 py-4">
                                <span class="{{ $field->is_required ? 'text-amber-400 font-bold' : 'text-slate-500' }}">
                                    {{ $field->is_required ? 'Yes' : 'No' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right space-x-1">
                                <button @click="isEditing = true; editId = {{ $field->id }}; name = '{{ addslashes($field->name) }}'; key = '{{ addslashes($field->key) }}'; type = '{{ $field->type }}'; isRequired = {{ $field->is_required ? 'true' : 'false' }}; showModal = true" class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-[11px] border border-slate-700 transition">Edit</button>
                                <form method="POST" action="{{ route('web.contacts.custom-fields.delete', $field->id) }}" onsubmit="return confirm('Delete this field?');" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 rounded bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 font-semibold text-[11px] border border-rose-500/30 transition">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-slate-500">
                                No custom input fields configured yet. Standard fields (Name, Phone, Email, Groups) are active by default.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create / Edit Field Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="showModal = false" class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-md p-6 space-y-4 shadow-2xl">
            <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                <h3 class="font-bold text-base text-white" x-text="isEditing ? 'Edit Custom Field' : 'Add Custom Field'"></h3>
                <button @click="showModal = false" class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <form :action="isEditing ? `/contacts/custom-fields/${editId}/update` : '{{ route('web.contacts.custom-fields.store') }}'" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Field Label Name *</label>
                    <input type="text" name="name" x-model="name" required placeholder="e.g. City, Order Number" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-brand-500">
                </div>

                <div x-show="!isEditing">
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Variable Key *</label>
                    <input type="text" name="key" x-model="key" :required="!isEditing" placeholder="e.g. city, order_no" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Input Field Type</label>
                    <select name="type" x-model="type" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-brand-500">
                        <option value="text">Text Input</option>
                        <option value="number">Numeric</option>
                        <option value="date">Date Picker</option>
                        <option value="select">Dropdown Select</option>
                        <option value="boolean">Yes/No Boolean</option>
                    </select>
                </div>

                <div class="pt-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_required" value="1" x-model="isRequired" class="rounded bg-slate-950 border-slate-800 text-brand-500 focus:ring-brand-500">
                        <span class="text-xs text-slate-300">Mandatory / Required field for audience</span>
                    </label>
                </div>

                <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-800">
                    <button type="button" @click="showModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white text-xs font-bold shadow-lg shadow-brand-500/20" x-text="isEditing ? 'Update Field' : 'Save Custom Field'"></button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
