@extends('layouts.app')

@section('content')
<div x-data="{ showModal: false, isEditing: false, editId: null, name: '', triggerKeyword: '', description: '', status: 'active' }" class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
                <span>⚡</span>
                <span>Chatbot Flow Maker</span>
            </h1>
            <p class="text-xs text-slate-400 mt-1">Design automated multi-step conversation trees, interactive quick-reply buttons, and dynamic WhatsApp flows.</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="isEditing = false; name = ''; triggerKeyword = ''; description = ''; status = 'active'; showModal = true" class="px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs transition flex items-center gap-1.5 shadow-lg shadow-brand-500/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Create Flow</span>
            </button>
            <a href="{{ route('web.bot') }}" class="px-4 py-2 rounded-xl bg-slate-900 border border-slate-700 hover:bg-slate-800 text-slate-300 font-semibold text-xs transition">
                ← All Chatbots
            </a>
        </div>
    </div>

    <!-- Flows List Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($flows as $flow)
            <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-slate-700 transition space-y-4 shadow flex flex-col justify-between">
                <div class="space-y-2">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="font-bold text-white text-base">{{ $flow->name }}</h3>
                            <span class="font-mono text-purple-400 text-xs">Trigger: #{{ $flow->trigger_keyword }}</span>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $flow->status === 'active' ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30' : 'bg-slate-800 text-slate-400' }}">
                            {{ $flow->status }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed">{{ $flow->description ?: 'Multi-step automated WhatsApp interactive flow.' }}</p>
                </div>

                <div class="pt-3 border-t border-slate-800 flex items-center justify-between text-xs">
                    <div class="space-x-1">
                        <button @click="isEditing = true; editId = {{ $flow->id }}; name = '{{ addslashes($flow->name) }}'; triggerKeyword = '{{ addslashes($flow->trigger_keyword) }}'; description = '{{ addslashes($flow->description ?? '') }}'; status = '{{ $flow->status }}'; showModal = true" class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-[11px] border border-slate-700 transition">Edit</button>
                        <form method="POST" action="{{ route('web.bot.flows.delete', $flow->id) }}" onsubmit="return confirm('Delete this flow?');" class="inline">
                            @csrf
                            <button type="submit" class="px-2.5 py-1 rounded bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 font-semibold text-[11px] border border-rose-500/30 transition">Delete</button>
                        </form>
                    </div>
                    <span class="text-purple-400 font-semibold text-[11px]">Visual Builder ↗</span>
                </div>
            </div>
        @empty
            <div class="col-span-full p-12 text-center rounded-2xl bg-slate-900 border border-slate-800 text-slate-500 text-xs">
                No custom conversation flows configured yet. Click "Create Flow" to design your interactive WhatsApp chatbot.
            </div>
        @endforelse
    </div>

    <!-- Create / Edit Flow Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="showModal = false" class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-md p-6 space-y-4 shadow-2xl">
            <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                <h3 class="font-bold text-base text-white" x-text="isEditing ? 'Edit Flow' : 'Create Chatbot Flow'"></h3>
                <button @click="showModal = false" class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <form :action="isEditing ? `/bot/flows/${editId}/update` : '{{ route('web.bot.flows.store') }}'" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Flow Name *</label>
                    <input type="text" name="name" x-model="name" required placeholder="e.g. Lead Qualification Flow" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Trigger Keyword *</label>
                    <input type="text" name="trigger_keyword" x-model="triggerKeyword" required placeholder="e.g. demo, order, quote" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Description</label>
                    <textarea name="description" x-model="description" rows="3" placeholder="Summary of this conversation flow..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-brand-500"></textarea>
                </div>

                <div x-show="isEditing">
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Status</label>
                    <select name="status" x-model="status" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-brand-500">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-800">
                    <button type="button" @click="showModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white text-xs font-bold shadow-lg shadow-brand-500/20" x-text="isEditing ? 'Update Flow' : 'Create Flow'"></button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
