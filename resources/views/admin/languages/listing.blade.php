<!-- Installed Languages Table Listing Component -->
<div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-sm font-bold text-white uppercase tracking-wider">Installed System Languages</h2>
            <p class="text-xs text-slate-400">Translations are loaded dynamically across vendor consoles and chat windows.</p>
        </div>
        <span class="text-xs text-cyan-400 font-semibold bg-cyan-500/10 px-3 py-1 rounded-full border border-cyan-500/20">
            {{ count($languages) }} Languages Active
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-300">
            <thead class="bg-slate-950/80 text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-800">
                <tr>
                    <th class="py-3 px-4">Language</th>
                    <th class="py-3 px-4">Code</th>
                    <th class="py-3 px-4">Direction</th>
                    <th class="py-3 px-4">Translation Progress</th>
                    <th class="py-3 px-4">Role</th>
                    <th class="py-3 px-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60 font-sans">
                @forelse($languages as $language)
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-2.5">
                                <span class="w-6 h-6 rounded-full bg-slate-800 flex items-center justify-center font-bold text-[10px] text-purple-300 uppercase">
                                    {{ substr($language->code, 0, 2) }}
                                </span>
                                <div>
                                    <div class="font-bold text-white cursor-pointer hover:text-purple-400 transition" @click="viewDetails({{ json_encode($language) }})">{{ $language->name }}</div>
                                    <div class="text-[11px] text-slate-500 font-mono">{{ $language->code }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-4 font-mono font-bold text-purple-300">{{ $language->code }}</td>
                        <td class="py-3 px-4 font-medium {{ $language->direction === 'rtl' ? 'text-amber-400' : 'text-slate-400' }}">
                            {{ strtoupper($language->direction) }} ({{ $language->direction === 'rtl' ? 'Right to Left' : 'Left to Right' }})
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-2">
                                <div class="w-24 bg-slate-800 h-2 rounded-full overflow-hidden">
                                    <div class="bg-emerald-500 h-full rounded-full" style="width: {{ $language->progress ?? 100 }}%"></div>
                                </div>
                                <span class="font-mono text-emerald-400 font-bold text-[11px]">{{ $language->progress ?? 100 }}%</span>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            @if($language->is_default)
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-purple-500/15 text-purple-300 border border-purple-500/30">
                                    Default Master
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-800 text-slate-300 border border-slate-700">
                                    Active
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right space-x-1">
                            <button type="button" @click="viewDetails({{ json_encode($language) }})" class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-brand-300 font-semibold text-[11px] border border-slate-700 transition cursor-pointer">Details</button>
                            <button type="button" @click="openEditModal({{ json_encode($language) }})" class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-[11px] border border-slate-700 transition cursor-pointer">Update</button>
                            @if(!$language->is_default)
                                <form action="{{ route('admin.languages.default', $language) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-purple-300 font-semibold text-[11px] border border-slate-700 transition cursor-pointer">Set Default</button>
                                </form>
                                <form action="{{ route('admin.languages.delete', $language) }}" method="POST" class="inline" onsubmit="return confirm('Delete this language locale?');">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 rounded bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 font-semibold text-[11px] border border-rose-500/30 transition cursor-pointer">Delete</button>
                                </form>
                            @else
                                <span class="px-2.5 py-1 text-[11px] text-slate-500 italic">Master Locale</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-8 text-slate-500">
                            No languages configured. Click "Add Language" to install one.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
