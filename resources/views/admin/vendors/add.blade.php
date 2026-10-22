<!-- Create Vendor Tenant Modal Component -->
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
            <button type="button" @click="showCreateModal = false" class="text-slate-400 hover:text-white p-1">&times;</button>
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
