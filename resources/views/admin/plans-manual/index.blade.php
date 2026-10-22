@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-black text-white tracking-tight">Manual & Prepaid Subscriptions</h1>
                @if(($pendingCount ?? 0) > 0)
                <span class="px-2 py-0.5 rounded-full bg-rose-500/10 border border-rose-500/30 text-[10px] font-bold text-rose-400 uppercase tracking-wider flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-400 animate-ping"></span>
                    {{ $pendingCount }} Pending Approval
                </span>
                @else
                <span class="px-2 py-0.5 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-[10px] font-bold text-emerald-400 uppercase tracking-wider">
                    Queue Clear
                </span>
                @endif
            </div>
            <p class="text-xs text-slate-400 mt-1">Review, approve, or decline manual bank wire, UPI, and offline enterprise subscription requests.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.plans.subscriptions') }}" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs border border-slate-700 transition flex items-center gap-1.5">
                <span>← Automated Subscriptions</span>
            </a>
        </div>
    </div>

    <!-- Manual / Prepaid Requests Table -->
    <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-white uppercase tracking-wider">Manual Subscription Ledger</h2>
                <p class="text-xs text-slate-400">Complete audit trail of offline payments, bank references and activation timestamps.</p>
            </div>
            <span class="text-xs text-purple-300 font-semibold bg-purple-500/10 px-3 py-1 rounded-full border border-purple-500/20">
                Total Requests: {{ count($requests ?? []) }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/80 text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Vendor / Business</th>
                        <th class="py-3 px-4">Plan Requested</th>
                        <th class="py-3 px-4">Notes / Reference</th>
                        <th class="py-3 px-4">Amount</th>
                        <th class="py-3 px-4">Date</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-sans">
                    @forelse($requests as $req)
                    <tr class="hover:bg-slate-800/40 transition {{ $req->status === 'pending' ? 'bg-amber-500/5' : '' }}">
                        <td class="py-3 px-4">
                            <div class="font-bold text-white">{{ $req->vendor?->name ?? 'Vendor' }}</div>
                            <div class="text-[11px] text-slate-500 font-mono">{{ $req->vendor?->email }}</div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="font-semibold text-purple-300">{{ $req->plan?->title ?? 'Subscription Plan' }}</span>
                        </td>
                        <td class="py-3 px-4 font-mono text-[11px] text-amber-300">
                            {{ $req->notes ?? 'Bank Wire / Offline' }}
                        </td>
                        <td class="py-3 px-4 font-mono font-bold text-white">
                            ₹{{ number_format($req->amount) }}
                        </td>
                        <td class="py-3 px-4 text-slate-400 font-mono">
                            {{ $req->created_at->format('M d, H:i') }}
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $req->status === 'pending' ? 'bg-amber-500/15 text-amber-400 border border-amber-500/30' : ($req->status === 'approved' ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/15 text-rose-400 border border-rose-500/30') }}">
                                {{ $req->status }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right space-x-1">
                            @if($req->status === 'pending')
                                <form method="POST" action="{{ route('admin.manual-subscriptions.approve', $req->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 rounded bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-[11px] transition">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('admin.manual-subscriptions.reject', $req->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2 py-1 rounded bg-rose-600/30 text-rose-300 hover:bg-rose-600/50 text-[11px] transition">Decline</button>
                                </form>
                            @else
                                <span class="text-slate-500 text-[11px]">Reviewed</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-8 text-slate-500">No manual payment requests recorded.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
