<!-- Pages Table Listing Component -->
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
                            <div class="font-bold text-white cursor-pointer hover:text-purple-400 transition" @click="viewDetails({{ json_encode($page) }})">{{ $page->title }}</div>
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
                            <button type="button" @click="viewDetails({{ json_encode($page) }})" class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-brand-300 font-semibold text-[11px] border border-slate-700 transition cursor-pointer">Details</button>
                            <button type="button" @click="openEdit({{ json_encode($page) }})" class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-[11px] border border-slate-700 transition cursor-pointer">Update</button>
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
