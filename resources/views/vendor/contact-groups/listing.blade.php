<!-- Groups Grid Listing Component -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-5">
    @forelse($groups as $group)
        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-slate-700 transition space-y-3 shadow flex flex-col justify-between">
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-white text-base cursor-pointer hover:text-brand-400 transition" @click="viewDetails({{ json_encode($group) }})">{{ $group->title }}</h3>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-brand-500/20 text-brand-400 border border-brand-500/30">
                        {{ $group->contacts_count ?? 0 }} Contacts
                    </span>
                </div>
                <p class="text-xs text-slate-400 leading-relaxed">{{ $group->description ?: 'No description provided.' }}</p>
            </div>

            <div class="pt-3 border-t border-slate-800 flex items-center justify-between text-xs text-slate-500">
                <div class="flex items-center gap-2">
                    <button type="button" @click="viewDetails({{ json_encode($group) }})" class="text-brand-400 hover:text-brand-300 font-semibold">Details</button>
                    <span>•</span>
                    <button type="button" @click="openEditModal({{ json_encode($group) }})" class="text-slate-400 hover:text-white font-semibold">Update</button>
                    <span>•</span>
                    <form method="POST" action="{{ route('web.contacts.groups.delete', $group->id) }}" onsubmit="return confirm('Delete this group?');" class="inline">
                        @csrf
                        <button type="submit" class="text-rose-400 hover:text-rose-300 font-semibold">Delete</button>
                    </form>
                </div>
                <a href="{{ route('web.contacts') }}?group_id={{ $group->id }}" class="text-brand-400 hover:underline font-semibold">View Contacts &rarr;</a>
            </div>
        </div>
    @empty
        <div class="col-span-full p-12 text-center rounded-2xl bg-slate-900 border border-slate-800 text-slate-500 text-xs">
            No contact groups created yet. Click "Create Group" to start organizing segments.
        </div>
    @endforelse
</div>
