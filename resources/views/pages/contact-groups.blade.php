@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
                <span>📁</span>
                <span>Audience Contact Groups</span>
            </h1>
            <p class="text-xs text-slate-400 mt-1">Organize customers into broadcast target segments (e.g. VIP Customers, Leads, Wholesale).</p>
        </div>
        <div class="flex items-center gap-2">
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
                    <span>Target Segment</span>
                    <a href="{{ route('web.contacts') }}" class="text-brand-400 hover:underline font-semibold">View Audience &rarr;</a>
                </div>
            </div>
        @empty
            <div class="col-span-full p-12 text-center rounded-2xl bg-slate-900 border border-slate-800 text-slate-500 text-xs">
                No contact groups created yet.
            </div>
        @endforelse
    </div>
</div>
@endsection
