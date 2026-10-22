@extends('layouts.admin')

@section('content')
<div x-data="{
    showAddModal: false,
    langName: '',
    langCode: '',
    direction: 'ltr',
    openAdd() {
        this.langName = '';
        this.langCode = '';
        this.direction = 'ltr';
        this.showAddModal = true;
    }
}" class="space-y-6">

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs font-semibold flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs space-y-1">
            @foreach($errors->all() as $error)
                <p>• {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-black text-white tracking-tight">Languages & Localization</h1>
                <span class="px-2 py-0.5 rounded-full bg-cyan-500/10 border border-cyan-500/30 text-[10px] font-bold text-cyan-400 uppercase tracking-wider">
                    Multi-Language Engine
                </span>
            </div>
            <p class="text-xs text-slate-400 mt-1">Configure system languages, translation strings, RTL writing modes, and default locales.</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="openAdd()" type="button" class="px-3.5 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs shadow-lg shadow-purple-500/20 transition flex items-center gap-1.5 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Add Language</span>
            </button>
        </div>
    </div>

    <!-- Languages List Table -->
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
                                        <div class="font-bold text-white">{{ $language->name }}</div>
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

    <!-- Add Language Modal -->
    <div x-show="showAddModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="showAddModal = false" class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-md p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <h3 class="text-base font-bold text-white">Add System Language</h3>
                <button @click="showAddModal = false" class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <form action="{{ route('admin.languages.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-300 font-medium mb-1">Language Name *</label>
                    <input type="text" name="name" x-model="langName" required class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-purple-500" placeholder="e.g. French / Français">
                </div>

                <div>
                    <label class="block text-slate-300 font-medium mb-1">Language Code (ISO 639-1) *</label>
                    <input type="text" name="code" x-model="langCode" required maxlength="10" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white font-mono focus:outline-none focus:border-purple-500" placeholder="e.g. fr">
                </div>

                <div>
                    <label class="block text-slate-300 font-medium mb-1">Text Direction *</label>
                    <select name="direction" x-model="direction" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-purple-500">
                        <option value="ltr">LTR (Left to Right - e.g. English, French, Spanish)</option>
                        <option value="rtl">RTL (Right to Left - e.g. Arabic, Hebrew, Urdu)</option>
                    </select>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-800">
                    <button type="button" @click="showAddModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold cursor-pointer">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold cursor-pointer">Add Language</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
