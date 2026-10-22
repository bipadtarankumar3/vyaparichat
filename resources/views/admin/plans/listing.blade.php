<!-- Active Subscription Plans Cards Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    @forelse($plans as $plan)
    <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl relative overflow-hidden flex flex-col justify-between hover:border-purple-500/40 transition">
        <div>
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-white cursor-pointer hover:text-purple-400 transition" @click="viewDetails({{ json_encode($plan) }})">{{ $plan->title }}</h2>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $plan->is_active ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30' : 'bg-slate-800 text-slate-400' }}">
                    {{ $plan->is_active ? 'Active Tier' : 'Inactive' }}
                </span>
            </div>
            <div class="mt-4 flex items-baseline gap-1">
                <span class="text-3xl font-black text-white">₹{{ number_format($plan->price_monthly) }}</span>
                <span class="text-xs text-slate-400 font-semibold">/ month</span>
            </div>
            <p class="mt-2 text-xs text-slate-400 leading-relaxed">{{ $plan->description ?? 'Full multi-tenant WhatsApp cloud messaging package.' }}</p>

            <div class="mt-6 space-y-2.5 pt-4 border-t border-slate-800/80 text-xs text-slate-300">
                <div class="flex items-center justify-between">
                    <span class="text-slate-400">Monthly Message Quota:</span>
                    <strong class="text-white">{{ number_format($plan->max_messages_monthly) }}</strong>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-400">Total Contacts Limit:</span>
                    <strong class="text-white">{{ number_format($plan->max_contacts) }}</strong>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-400">Team Agents:</span>
                    <strong class="text-white">{{ $plan->max_team_members ?? $plan->max_staff_members ?? 5 }} Seats</strong>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-400">Flow Maker & Bot Automation:</span>
                    <strong class="text-emerald-400">Included</strong>
                </div>
            </div>
        </div>

        <div class="mt-6 pt-4 border-t border-slate-800 flex items-center justify-between text-xs">
            <span class="text-slate-500 font-mono">Plan ID: #{{ $plan->id }}</span>
            <div class="flex items-center gap-2">
                <button type="button" @click="viewDetails({{ json_encode($plan) }})" class="font-bold text-brand-300 hover:text-white">Details</button>
                <span>•</span>
                <button type="button" @click="openEditModal({{ json_encode($plan) }})" class="font-bold text-purple-400 hover:text-purple-300">Update</button>
                <span>•</span>
                <form method="POST" action="{{ route('admin.plans.delete', $plan->id) }}" onsubmit="return confirm('Delete this plan tier?');" class="inline">
                    @csrf
                    <button type="submit" class="text-rose-400 hover:text-rose-300 font-semibold">Delete</button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-3 p-8 text-center text-slate-500 bg-slate-900 rounded-2xl border border-slate-800">
        No subscription tiers configured. Click "Create Plan Tier" to add pricing packages.
    </div>
    @endforelse
</div>

<!-- Active Recurring Subscriptions Table -->
<div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-sm font-bold text-white uppercase tracking-wider">Active Recurring Subscriptions</h2>
            <p class="text-xs text-slate-400">Real-time status of recurring charges and auto-renewals.</p>
        </div>
        <span class="text-xs text-purple-300 font-semibold bg-purple-500/10 px-3 py-1 rounded-full border border-purple-500/20">
            Total Subscribers: {{ $subscriptions->total() }}
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-300">
            <thead class="bg-slate-950/80 text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-800">
                <tr>
                    <th class="py-3 px-4">Business / Tenant</th>
                    <th class="py-3 px-4">Active Plan Tier</th>
                    <th class="py-3 px-4">Gateway</th>
                    <th class="py-3 px-4">Next Billing Date</th>
                    <th class="py-3 px-4">Amount</th>
                    <th class="py-3 px-4">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
                @forelse($subscriptions as $sub)
                <tr class="hover:bg-slate-800/50">
                    <td class="py-3.5 px-4 font-bold text-white">{{ $sub->vendor->name ?? 'N/A' }}</td>
                    <td class="py-3.5 px-4">
                        <span class="px-2 py-0.5 rounded bg-purple-500/10 text-purple-300 border border-purple-500/30 font-semibold">
                            {{ $sub->plan->title ?? 'Custom Tier' }}
                        </span>
                    </td>
                    <td class="py-3.5 px-4 font-mono uppercase text-[11px] text-slate-400">{{ $sub->payment_gateway ?? 'Manual' }}</td>
                    <td class="py-3.5 px-4">{{ $sub->ends_at ? $sub->ends_at->format('M d, Y') : 'Auto-renewing' }}</td>
                    <td class="py-3.5 px-4 font-bold text-emerald-400">₹{{ number_format($sub->plan->price_monthly ?? 0) }}</td>
                    <td class="py-3.5 px-4">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $sub->status === 'active' ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30' : 'bg-slate-800 text-slate-400' }}">
                            {{ $sub->status }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-8 text-center text-slate-500">
                        No recurring subscribers found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pt-2">
        {{ $subscriptions->links() }}
    </div>
</div>
