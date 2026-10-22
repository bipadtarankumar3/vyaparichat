<!-- Create Page Modal Component -->
<div x-show="showAddModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div @click.away="showAddModal = false" class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-xl p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <h3 class="text-base font-bold text-white">Create New CMS Page</h3>
            <button type="button" @click="showAddModal = false" class="text-slate-400 hover:text-white">&times;</button>
        </div>

        <form action="{{ route('admin.pages.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block text-slate-300 font-medium mb-1">Page Title *</label>
                <input type="text" name="title" required class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-purple-500" placeholder="e.g. Terms of Service & User Agreement">
            </div>

            <div>
                <label class="block text-slate-300 font-medium mb-1">URL Slug (Optional)</label>
                <input type="text" name="slug" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-purple-500" placeholder="e.g. terms-of-service (auto-generated if empty)">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-slate-300 font-medium mb-1">Page Type *</label>
                    <select name="type" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-purple-500">
                        <option value="Legal / Policy">Legal / Policy</option>
                        <option value="Company">Company</option>
                        <option value="Documentation">Documentation</option>
                        <option value="Landing Page">Landing Page</option>
                        <option value="FAQ">FAQ</option>
                    </select>
                </div>
                <div>
                    <label class="block text-slate-300 font-medium mb-1">Publish Status *</label>
                    <select name="status" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-purple-500">
                        <option value="published">Published</option>
                        <option value="draft">Draft</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-slate-300 font-medium mb-1">Page Content (HTML / Text)</label>
                <textarea name="content" rows="6" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white font-mono text-[11px] focus:outline-none focus:border-purple-500" placeholder="Enter HTML or markdown body for this page..."></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-800">
                <button type="button" @click="showAddModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold cursor-pointer">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold cursor-pointer">Create Page</button>
            </div>
        </form>
    </div>
</div>
