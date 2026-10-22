@extends('layouts.app')

@section('content')
<div x-data="{ showAddModal: false }" class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
                <span>👔</span>
                <span>Agents & Team Members</span>
            </h1>
            <p class="text-xs text-slate-400 mt-1">Manage staff seats, invite customer support agents, and assign conversation permissions.</p>
        </div>
        <button @click="showAddModal = true" class="px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs transition flex items-center gap-2 shadow-lg shadow-brand-500/20">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Add New Agent</span>
        </button>
    </div>

    <!-- Agent Seats Card -->
    <div class="bg-slate-900 rounded-2xl border border-slate-800 overflow-hidden shadow">
        <div class="p-4 border-b border-slate-800 flex items-center justify-between">
            <div class="text-xs text-slate-400">
                Active Staff Seats: <span class="font-bold text-white">{{ $agents->count() }}</span> agents
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950 text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-800">
                    <tr>
                        <th class="px-5 py-3.5">Agent Name</th>
                        <th class="px-5 py-3.5">Email (Login)</th>
                        <th class="px-5 py-3.5">Assigned Role</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5">Date Added</th>
                        <th class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($agents as $agent)
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="px-5 py-4 font-bold text-white flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center font-bold text-xs text-brand-400">
                                    {{ substr($agent->name, 0, 1) }}
                                </div>
                                <span>{{ $agent->name }}</span>
                            </td>
                            <td class="px-5 py-4 text-slate-300 font-mono">{{ $agent->email }}</td>
                            <td class="px-5 py-4">
                                <form method="POST" action="{{ route('web.agents.update', $agent->id) }}" class="inline-flex items-center gap-1">
                                    @csrf
                                    <select name="role" onchange="this.form.submit()" class="bg-slate-950 border border-slate-800 rounded-lg text-[10px] font-bold uppercase tracking-wider text-purple-300 px-2 py-0.5 focus:outline-none">
                                        <option value="agent" {{ ($agent->pivot->role ?? 'agent') === 'agent' ? 'selected' : '' }}>Agent</option>
                                        <option value="admin" {{ ($agent->pivot->role ?? 'agent') === 'admin' ? 'selected' : '' }}>Admin</option>
                                    </select>
                                </form>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center gap-1.5 text-xs text-emerald-400 font-semibold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                    Active
                                </span>
                            </td>
                            <td class="px-5 py-4 text-slate-500">{{ $agent->created_at->format('M d, Y') }}</td>
                            <td class="px-5 py-4 text-right">
                                @if($agent->id !== Auth::id())
                                    <form method="POST" action="{{ route('web.agents.delete', $agent->id) }}" onsubmit="return confirm('Remove agent from team?');" class="inline">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 rounded bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 font-semibold text-[11px] border border-rose-500/30 transition">Remove</button>
                                    </form>
                                @else
                                    <span class="text-slate-500 text-[11px] italic">Current User</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-slate-500">
                                No agents added yet. Click "Add New Agent" to invite your first support staff member.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ADD AGENT MODAL -->
    <div x-show="showAddModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div @click.away="showAddModal = false" class="w-full max-w-md bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <h3 class="font-bold text-white text-base">Add Team Member / Agent</h3>
                <button @click="showAddModal = false" class="text-slate-400 hover:text-white">&times;</button>
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
</div>
@endsection
