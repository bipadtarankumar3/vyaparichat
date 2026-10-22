@extends('layouts.admin')

@section('content')
<div x-data="{
    showModal: false,
    isEditing: false,
    formAction: '{{ route('admin.pages.store') }}',
    formTitle: 'Create New Page',
    submitText: 'Create Page',
    pageData: {
        id: null,
        title: '',
        slug: '',
        type: 'Legal / Policy',
        status: 'published',
        content: ''
    },
    openCreate() {
        this.isEditing = false;
        this.formAction = '{{ route('admin.pages.store') }}';
        this.formTitle = 'Create New Page';
        this.submitText = 'Create Page';
        this.pageData = { id: null, title: '', slug: '', type: 'Legal / Policy', status: 'published', content: '' };
        this.showModal = true;
    },
    openEdit(p) {
        this.isEditing = true;
        this.formAction = '/admin/pages/' + p.id + '/update';
        this.formTitle = 'Edit Page: ' + p.title;
        this.submitText = 'Save Changes';
        this.pageData = {
            id: p.id,
            title: p.title,
            slug: p.slug,
            type: p.type,
            status: p.status,
            content: p.content || ''
        };
        this.showModal = true;
    }
}" class="space-y-6">

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs space-y-1">
            @foreach($errors->all() as $error)
                <p>• {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-black text-white tracking-tight">CMS & Landing Pages</h1>
                <span class="px-2 py-0.5 rounded-full bg-orange-500/10 border border-orange-500/30 text-[10px] font-bold text-orange-400 uppercase tracking-wider">
                    Content Management
                </span>
            </div>
            <p class="text-xs text-slate-400 mt-1">Manage public landing pages, legal policies, terms, and custom CMS documents.</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="openCreate()" type="button" class="px-3.5 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs shadow-lg shadow-purple-500/20 transition flex items-center gap-1.5 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Create New Page</span>
            </button>
        </div>
    </div>

    <!-- Pages List -->
    <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-white uppercase tracking-wider">Published & Draft Pages</h2>
                <p class="text-xs text-slate-400">Pages automatically linked to the public homepage, footer, and documentation.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-400">Total: {{ count($pages) }} Pages</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/80 text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Title</th>
                        <th class="py-3 px-4">URL Slug</th>
                        <th class="py-3 px-4">Type</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Last Updated</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-sans">
                    @forelse($pages as $page)
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="py-3 px-4">
                                <div class="font-bold text-white">{{ $page->title }}</div>
                                <div class="text-[11px] text-slate-500 line-clamp-1">{{ Str::limit(strip_tags($page->content ?? 'No content defined yet'), 60) }}</div>
                            </td>
                            <td class="py-3 px-4 font-mono text-[11px] text-purple-300">
                                /page/{{ $page->slug }}
                            </td>
                            <td class="py-3 px-4 text-slate-400 font-medium">
                                {{ $page->type }}
                            </td>
                            <td class="py-3 px-4">
                                @if($page->status === 'published')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                        Published
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-800 text-slate-400 border border-slate-700">
                                        Draft
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-400 font-mono">
                                {{ $page->updated_at?->format('M d, Y') ?? 'Recently' }}
                            </td>
                            <td class="py-3 px-4 text-right space-x-1">
                                <button @click='openEdit(@json($page))' class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-[11px] border border-slate-700 transition cursor-pointer">Edit</button>
                                <a href="/page/{{ $page->slug }}" target="_blank" class="inline-block px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-purple-300 font-semibold text-[11px] border border-slate-700 transition">Preview ↗</a>
                                <form action="{{ route('admin.pages.delete', $page) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this CMS page?');">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 rounded bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 font-semibold text-[11px] border border-rose-500/30 transition cursor-pointer">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-slate-500">
                                No CMS pages found. Click "Create New Page" to add one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create / Edit Modal -->
    <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="showModal = false" class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-xl p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <h3 class="text-base font-bold text-white" x-text="formTitle"></h3>
                <button @click="showModal = false" class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <form :action="formAction" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-300 font-medium mb-1">Page Title *</label>
                    <input type="text" name="title" x-model="pageData.title" required class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-purple-500" placeholder="e.g. Terms of Service & User Agreement">
                </div>

                <div x-show="!isEditing">
                    <label class="block text-slate-300 font-medium mb-1">URL Slug (Optional)</label>
                    <input type="text" name="slug" x-model="pageData.slug" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-purple-500" placeholder="e.g. terms-of-service (auto-generated if empty)">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-300 font-medium mb-1">Page Type *</label>
                        <select name="type" x-model="pageData.type" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-purple-500">
                            <option value="Legal / Policy">Legal / Policy</option>
                            <option value="Company">Company</option>
                            <option value="Documentation">Documentation</option>
                            <option value="Landing Page">Landing Page</option>
                            <option value="FAQ">FAQ</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-300 font-medium mb-1">Publish Status *</label>
                        <select name="status" x-model="pageData.status" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-purple-500">
                            <option value="published">Published</option>
                            <option value="draft">Draft</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-slate-300 font-medium mb-1">Page Content (HTML / Text)</label>
                    <textarea name="content" x-model="pageData.content" rows="6" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white font-mono text-[11px] focus:outline-none focus:border-purple-500" placeholder="Enter HTML or markdown body for this page..."></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-800">
                    <button type="button" @click="showModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold cursor-pointer">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold cursor-pointer" x-text="submitText"></button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
