@extends('layouts.admin')

@section('content')
<div x-data="adminVendorsManager()" class="space-y-6">
    <!-- Top Title & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Vendors & Client Businesses</h1>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">Manage vendor tenants, grant custom section permissions, and visit any vendor console directly.</p>
        </div>
        <button @click="showCreateModal = true" class="px-4 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs transition flex items-center gap-2 shadow-lg shadow-purple-500/20">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Create New Vendor</span>
        </button>
    </div>

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

    <!-- Search & Filter Card -->
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
                                <div class="font-bold text-sm text-white">{{ $vendor->name }}</div>
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

                                    <!-- Permissions Modal Trigger -->
                                    <button @click="openPermissionsModal({{ json_encode($vendor) }})" class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 text-xs font-semibold transition" title="Grant/Revoke Extra Section Access">
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

    <!-- MODAL 1: CREATE NEW VENDOR -->
    <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div @click.away="showCreateModal = false" class="w-full max-w-xl bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-2xl space-y-5 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <div class="flex items-center gap-2.5">
                    <span class="p-2 rounded-xl bg-purple-500/20 text-purple-400 font-bold">🏢</span>
                    <div>
                        <h3 class="font-bold text-white text-base">Create New Vendor Tenant</h3>
                        <p class="text-xs text-slate-400">Set up business profile, assign owner login, and grant initial module access.</p>
                    </div>
                </div>
                <button @click="showCreateModal = false" class="text-slate-400 hover:text-white p-1">&times;</button>
            </div>

            <form method="POST" action="{{ route('admin.vendors.store') }}" class="space-y-4 text-xs">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Business Name *</label>
                        <input type="text" name="name" required placeholder="e.g. Reliance Retail" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-slate-200 focus:outline-none focus:border-purple-500">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Slug (URL identifier)</label>
                        <input type="text" name="slug" placeholder="e.g. reliance-retail" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-slate-200 focus:outline-none focus:border-purple-500">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Owner Full Name *</label>
                        <input type="text" name="owner_name" required placeholder="e.g. Mukesh K" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-slate-200 focus:outline-none focus:border-purple-500">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Owner Email (Login ID) *</label>
                        <input type="email" name="email" required placeholder="owner@store.com" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-slate-200 focus:outline-none focus:border-purple-500">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Initial Password *</label>
                        <input type="password" name="owner_password" required minlength="6" placeholder="At least 6 characters" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-slate-200 focus:outline-none focus:border-purple-500">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Owner Phone</label>
                        <input type="text" name="phone" placeholder="+919876543210" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-slate-200 focus:outline-none focus:border-purple-500">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block font-semibold text-slate-300 mb-1">Subscription Plan</label>
                        <select name="plan_id" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-slate-200 focus:outline-none focus:border-purple-500">
                            <option value="">No Plan (Free Trial)</option>
                            @foreach($plans as $plan)
                                <option value="{{ $plan->id }}">{{ $plan->title }} (₹{{ $plan->price_monthly }}/mo)</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Extra Module Permissions -->
                <div class="pt-3 border-t border-slate-800 space-y-2">
                    <label class="block font-bold text-white text-xs">Custom Section / Module Access</label>
                    <p class="text-[11px] text-slate-400">Select which sections this vendor can see and use in their portal.</p>
                    
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 pt-2">
                        <label class="flex items-center gap-2 p-2 rounded-lg bg-slate-950 border border-slate-800 cursor-pointer hover:border-purple-500/50">
                            <input type="checkbox" name="permissions[]" value="chat" checked class="rounded text-purple-600 focus:ring-0">
                            <span class="text-slate-300">Live Chat</span>
                        </label>
                        <label class="flex items-center gap-2 p-2 rounded-lg bg-slate-950 border border-slate-800 cursor-pointer hover:border-purple-500/50">
                            <input type="checkbox" name="permissions[]" value="contacts" checked class="rounded text-purple-600 focus:ring-0">
                            <span class="text-slate-300">Audience CRM</span>
                        </label>
                        <label class="flex items-center gap-2 p-2 rounded-lg bg-slate-950 border border-slate-800 cursor-pointer hover:border-purple-500/50">
                            <input type="checkbox" name="permissions[]" value="campaigns" checked class="rounded text-purple-600 focus:ring-0">
                            <span class="text-slate-300">Broadcasts</span>
                        </label>
                        <label class="flex items-center gap-2 p-2 rounded-lg bg-slate-950 border border-slate-800 cursor-pointer hover:border-purple-500/50">
                            <input type="checkbox" name="permissions[]" value="templates" checked class="rounded text-purple-600 focus:ring-0">
                            <span class="text-slate-300">Templates</span>
                        </label>
                        <label class="flex items-center gap-2 p-2 rounded-lg bg-slate-950 border border-slate-800 cursor-pointer hover:border-purple-500/50">
                            <input type="checkbox" name="permissions[]" value="bot" checked class="rounded text-purple-600 focus:ring-0">
                            <span class="text-slate-300">Bot & AI Copilot</span>
                        </label>
                        <label class="flex items-center gap-2 p-2 rounded-lg bg-slate-950 border border-slate-800 cursor-pointer hover:border-purple-500/50">
                            <input type="checkbox" name="permissions[]" value="integrations" checked class="rounded text-purple-600 focus:ring-0">
                            <span class="text-slate-300">E-Commerce API</span>
                        </label>
                        <label class="flex items-center gap-2 p-2 rounded-lg bg-slate-950 border border-slate-800 cursor-pointer hover:border-purple-500/50">
                            <input type="checkbox" name="permissions[]" value="billing" checked class="rounded text-purple-600 focus:ring-0">
                            <span class="text-slate-300">Plans & Billing</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                    <button type="button" @click="showCreateModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold transition">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold transition shadow-lg shadow-purple-500/20">Create Vendor</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: EDIT EXTRA ACCESS / PERMISSIONS -->
    <div x-show="showPermissionsModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div @click.away="showPermissionsModal = false" class="w-full max-w-lg bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-2xl space-y-5">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <div class="flex items-center gap-2.5">
                    <span class="p-2 rounded-xl bg-purple-500/20 text-purple-400 font-bold">🛡️</span>
                    <div>
                        <h3 class="font-bold text-white text-base">Grant Extra Section Access</h3>
                        <p class="text-xs text-slate-400" x-text="'Customize permissions for: ' + selectedVendor?.name"></p>
                    </div>
                </div>
                <button @click="showPermissionsModal = false" class="text-slate-400 hover:text-white p-1">&times;</button>
            </div>

            <form :action="'/admin/vendors/' + selectedVendor?.id + '/permissions'" method="POST" class="space-y-4 text-xs">
                @csrf
                <div class="space-y-2">
                    <template x-for="perm in permissionOptions" :key="perm.key">
                        <label class="flex items-center justify-between p-3 rounded-xl bg-slate-950 border border-slate-800 cursor-pointer hover:border-purple-500/50">
                            <div class="space-y-0.5">
                                <span class="font-bold text-white text-sm" x-text="perm.name"></span>
                                <p class="text-[11px] text-slate-400" x-text="perm.description"></p>
                            </div>
                            <input type="checkbox" name="permissions[]" :value="perm.key" :checked="isPermissionGranted(perm.key)" class="rounded text-purple-600 focus:ring-0 w-4 h-4">
                        </label>
                    </template>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                    <button type="button" @click="showPermissionsModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold transition">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold transition shadow-lg shadow-purple-500/20">Save Permissions</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function adminVendorsManager() {
    return {
        showCreateModal: false,
        showPermissionsModal: false,
        selectedVendor: null,

        permissionOptions: [
            { key: 'chat', name: 'WhatsApp Live Chat', description: 'Real-time multi-agent conversation timeline and chat inbox.' },
            { key: 'contacts', name: 'Audience CRM', description: 'Directory, Excel contacts import/export, and customer segments.' },
            { key: 'campaigns', name: 'Broadcast Campaigns', description: 'High-throughput bulk blasts and progress analytics.' },
            { key: 'templates', name: 'WhatsApp Meta Templates', description: 'Meta HSM template management and direct synchronization.' },
            { key: 'bot', name: 'Bot Automations & AI Copilot', description: 'Auto-responders, priority triggers, and dry-run tester.' },
            { key: 'integrations', name: 'E-Commerce & REST API', description: 'Shopify/WooCommerce webhooks and developer API credentials.' },
            { key: 'billing', name: 'Plans & Billing', description: 'Subscription upgrades, quota limits, and billing status.' }
        ],

        openPermissionsModal(vendor) {
            this.selectedVendor = vendor;
            this.showPermissionsModal = true;
        },

        isPermissionGranted(key) {
            if (!this.selectedVendor) return true;
            if (!this.selectedVendor.custom_permissions) return true;
            return this.selectedVendor.custom_permissions[key] !== false;
        }
    };
}
</script>
@endsection
