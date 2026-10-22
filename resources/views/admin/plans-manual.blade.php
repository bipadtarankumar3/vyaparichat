@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-black text-white tracking-tight">Manual & Prepaid Subscriptions</h1>
                <span class="px-2 py-0.5 rounded-full bg-rose-500/10 border border-rose-500/30 text-[10px] font-bold text-rose-400 uppercase tracking-wider flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-400 animate-ping"></span>
                    1 Pending Approval
                </span>
            </div>
            <p class="text-xs text-slate-400 mt-1">Review, approve, or decline manual bank wire, UPI, and offline enterprise subscription requests.</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" class="px-3.5 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs shadow-lg shadow-purple-500/20 transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Record Manual Payment</span>
            </button>
            <a href="{{ route('admin.plans.subscriptions') }}" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs border border-slate-700 transition flex items-center gap-1.5">
                <span>← Automated Subscriptions</span>
            </a>
        </div>
    </div>

    <!-- Pending Approval Card (Alert banner) -->
    <div class="p-5 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-200 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-start gap-3">
            <div class="w-9 h-9 rounded-xl bg-amber-500/20 border border-amber-500/40 flex items-center justify-center text-amber-300 text-lg flex-shrink-0">
                ⚠️
            </div>
            <div>
                <h2 class="font-bold text-sm text-white">1 Pending Offline Subscription Awaiting Approval</h2>
                <p class="text-xs text-amber-300/80 mt-0.5">Vendor submitted UPI transaction reference #UPI-892348924 for Enterprise Plan (₹4,999). Verify bank statement and approve.</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow transition">
                ✓ Approve & Activate
            </button>
            <button type="button" class="px-3 py-1.5 rounded-lg bg-rose-600/30 hover:bg-rose-600/50 text-rose-300 font-semibold text-xs border border-rose-500/30 transition">
                ✕ Reject
            </button>
        </div>
    </div>

    <!-- Manual / Prepaid Requests Table -->
    <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-white uppercase tracking-wider">Manual Subscription Ledger</h2>
                <p class="text-xs text-slate-400">Complete audit trail of offline payments, bank references and activation timestamps.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-400">Filter:</span>
                <select class="bg-slate-950 border border-slate-800 rounded-lg text-xs text-slate-300 px-2.5 py-1 focus:outline-none focus:border-purple-500">
                    <option>All Requests</option>
                    <option selected>Pending (1)</option>
                    <option>Approved</option>
                    <option>Declined</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/80 text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Vendor / Business</th>
                        <th class="py-3 px-4">Plan Requested</th>
                        <th class="py-3 px-4">Payment Method</th>
                        <th class="py-3 px-4">Reference / UTR</th>
                        <th class="py-3 px-4">Amount</th>
                        <th class="py-3 px-4">Date</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-sans">
                    <!-- Row 1: Pending Request -->
                    <tr class="hover:bg-slate-800/40 transition bg-amber-500/5">
                        <td class="py-3 px-4">
                            <div class="font-bold text-white">Apex Supermarket Ltd</div>
                            <div class="text-[11px] text-slate-500 font-mono">finance@apexretail.in</div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="font-semibold text-purple-300">Enterprise High Volume</span>
                        </td>
                        <td class="py-3 px-4 font-medium text-slate-300">
                            Bank IMPS / UPI
                        </td>
                        <td class="py-3 px-4 font-mono text-[11px] text-amber-300">
                            UPI-892348924
                        </td>
                        <td class="py-3 px-4 font-mono font-bold text-white">
                            ₹4,999
                        </td>
                        <td class="py-3 px-4 text-slate-400 font-mono">
                            Today, 14:20
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-amber-500/15 text-amber-400 border border-amber-500/30">
                                Pending
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right space-x-1">
                            <button class="px-2.5 py-1 rounded bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-[11px] transition">Approve</button>
                            <button class="px-2 py-1 rounded bg-rose-600/30 text-rose-300 hover:bg-rose-600/50 text-[11px] transition">Decline</button>
                        </td>
                    </tr>

                    <!-- Row 2: Approved Request -->
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="py-3 px-4">
                            <div class="font-bold text-white">Zenith Logistics Hub</div>
                            <div class="text-[11px] text-slate-500 font-mono">billing@zenithhub.com</div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="font-semibold text-purple-300">Growth Pro</span>
                        </td>
                        <td class="py-3 px-4 font-medium text-slate-300">
                            NEFT Wire Transfer
                        </td>
                        <td class="py-3 px-4 font-mono text-[11px] text-slate-400">
                            HDFC-0091823910
                        </td>
                        <td class="py-3 px-4 font-mono font-bold text-white">
                            ₹1,999
                        </td>
                        <td class="py-3 px-4 text-slate-400 font-mono">
                            Oct 18, 2026
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                Active / Approved
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right">
                            <span class="text-xs text-slate-500">Approved by Admin</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
