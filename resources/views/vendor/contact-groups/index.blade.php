@extends('layouts.app')

@section('content')
<div x-data="{ showModal: false, isEditing: false, editId: null, title: '', description: '' }" class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
                <span>📁</span>
                <span>Audience Contact Groups</span>
            </h1>
            <p class="text-xs text-slate-400 mt-1">Organize customers into broadcast target segments (e.g. VIP Customers, Leads, Wholesale).</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="isEditing = false; title = ''; description = ''; showModal = true" class="px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs transition flex items-center gap-1.5 shadow-lg shadow-brand-500/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Create Group</span>
            </button>
            <a href="{{ route('web.contacts') }}" class="px-4 py-2 rounded-xl bg-slate-900 border border-slate-700 hover:bg-slate-800 text-slate-300 font-semibold text-xs transition">
                ← All Contacts
            </a>
        </div>
    </div>

    <!-- Groups Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        @forelse($groups as $group)
            <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-slate-700 transition space-y-3 shadow flex flex-col justify-between">
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-white text-base">{{ $group->title }}</h3>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-brand-500/20 text-brand-400 border border-brand-500/30">
                            {{ $group->contacts_count ?? 0 }} Contacts
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed">{{ $group->description ?: 'No description provided.' }}</p>
                </div>

                <div class="pt-3 border-t border-slate-800 flex items-center justify-between text-xs text-slate-500">
                    <div class="flex items-center gap-2">
                        <button @click="isEditing = true; editId = {{ $group->id }}; title = '{{ addslashes($group->title) }}'; description = '{{ addslashes($group->description ?? '') }}'; showModal = true" class="text-slate-400 hover:text-white font-semibold">Edit</button>
                        <span>•</span>
                        <form method="POST" action="{{ route('web.contacts.groups.delete', $group->id) }}" onsubmit="return confirm('Delete this group?');" class="inline">
                            @csrf
                            <button type="submit" class="text-rose-400 hover:text-rose-300 font-semibold">Delete</button>
                        </form>
                    </div>
                    <a href="{{ route('web.contacts') }}" class="text-brand-400 hover:underline font-semibold">View Audience &rarr;</a>
                </div>
            </div>
        @empty
            <div class="col-span-full p-12 text-center rounded-2xl bg-slate-900 border border-slate-800 text-slate-500 text-xs">
                No contact groups created yet. Click "Create Group" to start organizing segments.
            </div>
        @endforelse
    </div>

    <!-- Create / Edit Group Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="showModal = false" class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-md p-6 space-y-4 shadow-2xl">
            <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                <h3 class="font-bold text-base text-white" x-text="isEditing ? 'Edit Contact Group' : 'Create Contact Group'"></h3>
                <button @click="showModal = false" class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <form :action="isEditing ? `/contacts/groups/${editId}/update` : '{{ route('web.contacts.groups.store') }}'" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Group Title *</label>
                    <input type="text" name="title" x-model="title" required placeholder="e.g. VIP Customers, Leads" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Description</label>
                    <textarea name="description" x-model="description" rows="3" placeholder="Targeting criteria or details..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-brand-500"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-800">
                    <button type="button" @click="showModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white text-xs font-bold shadow-lg shadow-brand-500/20" x-text="isEditing ? 'Update Group' : 'Create Group'"></button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
