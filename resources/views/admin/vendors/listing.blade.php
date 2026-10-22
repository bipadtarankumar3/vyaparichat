<!-- Global Platform Metrics Grid -->
<div class="grid grid-cols-2 lg:grid-cols-6 gap-4">
    <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800">
        <div class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider">Total Vendors</div>
        <div class="text-2xl font-extrabold text-white mt-1">{{ number_format($metrics['total_vendors']) }}</div>
    </div>
    <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800">
        <div class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider">Active Vendors</div>
        <div class="text-2xl font-extrabold text-emerald-400 mt-1">{{ number_format($metrics['active_vendors']) }}</div>
    </div>
    <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800">
        <div class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider">Suspended</div>
        <div class="text-2xl font-extrabold text-rose-400 mt-1">{{ number_format($metrics['suspended_vendors']) }}</div>
    </div>
    <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800">
        <div class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider">All Contacts</div>
        <div class="text-2xl font-extrabold text-blue-400 mt-1">{{ number_format($metrics['total_contacts']) }}</div>
    </div>
    <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800">
        <div class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider">Total Messages</div>
        <div class="text-2xl font-extrabold text-amber-400 mt-1">{{ number_format($metrics['total_messages']) }}</div>
    </div>
    <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800">
        <div class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider">Subscriptions</div>
        <div class="text-2xl font-extrabold text-purple-400 mt-1">{{ number_format($metrics['active_subscriptions']) }}</div>
    </div>
</div>

<!-- Search & Table Card -->
<div class="bg-slate-900 rounded-2xl border border-slate-800 overflow-hidden shadow">
    <div class="p-4 border-b border-slate-800 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" action="{{ route('admin.vendors') }}" class="relative w-full sm:w-80">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search vendor name, email or slug..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-purple-500">
        </form>
        <div class="text-xs text-slate-400">
            Showing <span class="font-bold text-white">{{ $vendors->count() }}</span> of {{ $vendors->total() }} businesses
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-950 text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-800">
                <tr>
                    <th class="px-5 py-3.5">Business Name & Slug</th>
                    <th class="px-5 py-3.5">Owner / Contact</th>
                    <th class="px-5 py-3.5">Plan & WhatsApp</th>
                    <th class="px-5 py-3.5">Custom Section Access</th>
                    <th class="px-5 py-3.5">Status</th>
                    <th class="px-5 py-3.5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
                @forelse($vendors as $vendor)
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="px-5 py-4">
                            <div class="font-bold text-sm text-white cursor-pointer hover:text-purple-400 transition" @click="viewDetails({{ json_encode($vendor) }})">{{ $vendor->name }}</div>
                            <div class="font-mono text-[11px] text-purple-400">{{ $vendor->slug }}</div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="text-slate-200 font-semibold">{{ $vendor->email }}</div>
                            <div class="text-slate-500 text-[11px]">{{ $vendor->phone ?? 'No phone' }}</div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-800 text-slate-300 border border-slate-700">
                                    {{ $vendor->activeSubscription->plan->title ?? 'Free / Starter' }}
                                </span>
                                <span class="inline-flex items-center gap-1 text-[11px] {{ $vendor->whatsapp_status === 'connected' ? 'text-emerald-400' : 'text-slate-500' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $vendor->whatsapp_status === 'connected' ? 'bg-emerald-400' : 'bg-slate-600' }}"></span>
                                    {{ ucfirst($vendor->whatsapp_status) }}
                                </span>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex flex-wrap gap-1">
                                @php
                                    $perms = [
                                        'chat' => 'Live Chat',
                                        'contacts' => 'CRM',
                                        'campaigns' => 'Broadcasts',
                                        'templates' => 'Templates',
                                        'bot' => 'Bot Copilot',
                                        'integrations' => 'API',
                                        'billing' => 'Billing',
                                    ];
                                @endphp
                                @foreach($perms as $key => $label)
                                    @if($vendor->hasSectionAccess($key))
                                        <span class="px-1.5 py-0.5 rounded bg-purple-500/10 text-purple-300 border border-purple-500/20 text-[10px] font-medium">{{ $label }}</span>
                                    @else
                                        <span class="px-1.5 py-0.5 rounded bg-slate-800/60 text-slate-600 border border-slate-800 text-[10px] line-through">{{ $label }}</span>
                                    @endif
                                @endforeach
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <form method="POST" action="{{ route('admin.vendors.status', $vendor->id) }}" class="inline">
                                @csrf
                                <button type="submit" class="px-2 py-0.5 rounded text-[10px] font-bold uppercase transition {{ $vendor->status === 'active' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 hover:bg-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/30 hover:bg-rose-500/20' }}">
                                    {{ $vendor->status }}
                                </button>
                            </form>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <!-- Impersonate / Visit Vendor Console -->
                                <form method="POST" action="{{ route('admin.vendors.impersonate', $vendor->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-purple-600/20 hover:bg-purple-600/40 text-purple-300 border border-purple-500/30 text-xs font-bold transition flex items-center gap-1.5" title="Switch to this vendor's workspace">
                                        <span>⚡ Visit Console</span>
                                    </button>
                                </form>

                                <!-- Details Trigger -->
                                <button type="button" @click="viewDetails({{ json_encode($vendor) }})" class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-brand-300 border border-slate-700 text-xs font-semibold transition" title="View Business 360 Details">
                                    Details
                                </button>

                                <!-- Permissions Modal Trigger -->
                                <button type="button" @click="openPermissionsModal({{ json_encode($vendor) }})" class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 text-xs font-semibold transition" title="Grant/Revoke Extra Section Access">
                                    🛡️ Permissions
                                </button>

                                <!-- Archive / Delete Vendor -->
                                <form method="POST" action="{{ route('admin.vendors.delete', $vendor->id) }}" onsubmit="return confirm('Archive / Suspend this vendor?');" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 border border-rose-500/30 text-xs font-semibold transition" title="Archive Vendor">
                                        ✕
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-slate-500">
                            No vendors found. Click "Create New Vendor" to add your first client business.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t border-slate-800">
        {{ $vendors->links() }}
    </div>
</div>
