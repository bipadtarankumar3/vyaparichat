@extends('layouts.admin')

@section('content')
<div class="space-y-6">
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
            <button type="button" class="px-3.5 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs shadow-lg shadow-purple-500/20 transition flex items-center gap-1.5">
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
                5 Languages Active
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
                    <!-- English (Default) -->
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-2.5">
                                <span class="text-lg">🇬🇧</span>
                                <div>
                                    <div class="font-bold text-white">English (US / UK)</div>
                                    <div class="text-[11px] text-slate-500">English</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-4 font-mono font-bold text-purple-300">en</td>
                        <td class="py-3 px-4 font-medium text-slate-400">LTR (Left to Right)</td>
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-2">
                                <div class="w-24 bg-slate-800 h-2 rounded-full overflow-hidden">
                                    <div class="bg-emerald-500 h-full w-[100%] rounded-full"></div>
                                </div>
                                <span class="font-mono text-emerald-400 font-bold text-[11px]">100%</span>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-purple-500/15 text-purple-300 border border-purple-500/30">
                                Default Master
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right space-x-1">
                            <button class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-[11px] border border-slate-700 transition">Strings (1,420)</button>
                        </td>
                    </tr>

                    <!-- Hindi -->
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-2.5">
                                <span class="text-lg">🇮🇳</span>
                                <div>
                                    <div class="font-bold text-white">Hindi</div>
                                    <div class="text-[11px] text-slate-500">हिन्दी</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-4 font-mono font-bold text-purple-300">hi</td>
                        <td class="py-3 px-4 font-medium text-slate-400">LTR (Left to Right)</td>
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-2">
                                <div class="w-24 bg-slate-800 h-2 rounded-full overflow-hidden">
                                    <div class="bg-emerald-500 h-full w-[94%] rounded-full"></div>
                                </div>
                                <span class="font-mono text-emerald-400 font-bold text-[11px]">94%</span>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-800 text-slate-300 border border-slate-700">
                                Active
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right space-x-1">
                            <button class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-[11px] border border-slate-700 transition">Strings</button>
                            <button class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-purple-300 font-semibold text-[11px] border border-slate-700 transition">Set Default</button>
                        </td>
                    </tr>

                    <!-- Arabic -->
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-2.5">
                                <span class="text-lg">🇦🇪</span>
                                <div>
                                    <div class="font-bold text-white">Arabic</div>
                                    <div class="text-[11px] text-slate-500">العربية</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-4 font-mono font-bold text-purple-300">ar</td>
                        <td class="py-3 px-4 font-medium text-amber-400">RTL (Right to Left)</td>
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-2">
                                <div class="w-24 bg-slate-800 h-2 rounded-full overflow-hidden">
                                    <div class="bg-amber-500 h-full w-[88%] rounded-full"></div>
                                </div>
                                <span class="font-mono text-amber-400 font-bold text-[11px]">88%</span>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-800 text-slate-300 border border-slate-700">
                                Active
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right space-x-1">
                            <button class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-[11px] border border-slate-700 transition">Strings</button>
                            <button class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-purple-300 font-semibold text-[11px] border border-slate-700 transition">Set Default</button>
                        </td>
                    </tr>

                    <!-- Spanish -->
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-2.5">
                                <span class="text-lg">🇪🇸</span>
                                <div>
                                    <div class="font-bold text-white">Spanish</div>
                                    <div class="text-[11px] text-slate-500">Español</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-4 font-mono font-bold text-purple-300">es</td>
                        <td class="py-3 px-4 font-medium text-slate-400">LTR (Left to Right)</td>
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-2">
                                <div class="w-24 bg-slate-800 h-2 rounded-full overflow-hidden">
                                    <div class="bg-emerald-500 h-full w-[91%] rounded-full"></div>
                                </div>
                                <span class="font-mono text-emerald-400 font-bold text-[11px]">91%</span>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-800 text-slate-300 border border-slate-700">
                                Active
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right space-x-1">
                            <button class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-[11px] border border-slate-700 transition">Strings</button>
                            <button class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-purple-300 font-semibold text-[11px] border border-slate-700 transition">Set Default</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
