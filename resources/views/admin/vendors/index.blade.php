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

    <!-- Structured Modular Views -->
    @include('admin.vendors.listing')
    @include('admin.vendors.add')
    @include('admin.vendors.update')
    @include('admin.vendors.details')
</div>

<script>
function adminVendorsManager() {
    return {
        showCreateModal: false,
        showPermissionsModal: false,
        showDetailsModal: false,
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

        viewDetails(vendor) {
            this.selectedVendor = vendor;
            this.showDetailsModal = true;
        },

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
