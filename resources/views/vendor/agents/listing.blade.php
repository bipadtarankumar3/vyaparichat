<!-- Agent Seats Listing Table Component -->
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
                            <span class="cursor-pointer hover:text-brand-400 transition" @click="viewDetails({{ json_encode($agent) }}, '{{ $agent->pivot->role ?? 'agent' }}')">{{ $agent->name }}</span>
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
                        <td class="px-5 py-4 text-right space-x-1">
                            <button type="button" @click="viewDetails({{ json_encode($agent) }}, '{{ $agent->pivot->role ?? 'agent' }}')" class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-brand-300 font-semibold text-[11px] border border-slate-700 transition">Details</button>
                            @if($agent->id !== Auth::id())
                                <button type="button" @click="openEditModal({{ json_encode($agent) }}, '{{ $agent->pivot->role ?? 'agent' }}')" class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-[11px] border border-slate-700 transition">Update</button>
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
