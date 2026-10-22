<!-- Edit Extra Access / Permissions Modal Component -->
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
            <button type="button" @click="showPermissionsModal = false" class="text-slate-400 hover:text-white p-1">&times;</button>
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
