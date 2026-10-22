@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
                <span>⚡</span>
                <span>Chatbot Flow Maker</span>
            </h1>
            <p class="text-xs text-slate-400 mt-1">Design automated multi-step conversation trees, interactive quick-reply buttons, and dynamic WhatsApp flows.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('web.bot') }}" class="px-4 py-2 rounded-xl bg-slate-900 border border-slate-700 hover:bg-slate-800 text-slate-300 font-semibold text-xs transition">
                ← All Chatbots
            </a>
        </div>
    </div>

    <!-- Visual Interactive Flow Canvas Mockup -->
    <div class="p-8 rounded-2xl bg-slate-900 border border-slate-800 space-y-8 min-h-[400px] flex flex-col justify-center items-center shadow relative overflow-hidden">
        <div class="flex flex-col items-center gap-4 text-center z-10 max-w-lg">
            <div class="w-16 h-16 rounded-2xl bg-purple-500/20 text-purple-400 flex items-center justify-center text-3xl shadow-xl shadow-purple-500/20">
                🧠
            </div>
            <div>
                <h3 class="font-bold text-white text-lg">Visual WhatsApp Flow Builder</h3>
                <p class="text-xs text-slate-400 mt-1">Connect welcome triggers, intent branchings, catalog responses, and agent handover steps visually.</p>
            </div>
            <div class="flex items-center gap-3 pt-2">
                <a href="{{ route('web.bot') }}" class="px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs transition shadow-lg shadow-brand-500/20">
                    Manage Keyword Triggers & Bot Rules
                </a>
            </div>
        </div>

        <!-- Decorative Node Graphic Background -->
        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#8b5cf6_1px,transparent_1px)] [background-size:16px_16px] pointer-events-none"></div>
    </div>
</div>
@endsection
