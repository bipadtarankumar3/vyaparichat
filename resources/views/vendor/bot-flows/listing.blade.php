<!-- Bot Flows Grid Listing Component -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
    @forelse($flows as $flow)
        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-slate-700 transition space-y-4 shadow flex flex-col justify-between">
            <div class="space-y-2">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="font-bold text-white text-base cursor-pointer hover:text-brand-400 transition" @click="viewDetails({{ json_encode($flow) }})">{{ $flow->name }}</h3>
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
                    <button type="button" @click="viewDetails({{ json_encode($flow) }})" class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-brand-300 font-semibold text-[11px] border border-slate-700 transition">Details</button>
                    <button type="button" @click="openEditModal({{ json_encode($flow) }})" class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-[11px] border border-slate-700 transition">Update</button>
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
