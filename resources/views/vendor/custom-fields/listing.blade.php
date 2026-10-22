<!-- Custom Fields Listing Table Component -->
<div class="bg-slate-900 rounded-2xl border border-slate-800 overflow-hidden shadow">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-950 text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-800">
                <tr>
                    <th class="px-5 py-3.5">Input Field Name</th>
                    <th class="px-5 py-3.5">Key / Variable</th>
                    <th class="px-5 py-3.5">Input Type</th>
                    <th class="px-5 py-3.5">Required</th>
                    <th class="px-5 py-3.5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
                @forelse($fields as $field)
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="px-5 py-4 font-bold text-white cursor-pointer hover:text-brand-400 transition" @click="viewDetails({{ json_encode($field) }})">{{ $field->name }}</td>
                        <td class="px-5 py-4 font-mono text-brand-400 text-[11px]">&#123;&#123; {{ $field->key }} &#125;&#125;</td>
                        <td class="px-5 py-4 text-slate-300 uppercase text-[10px]">{{ $field->type ?? 'text' }}</td>
                        <td class="px-5 py-4">
                            <span class="{{ $field->is_required ? 'text-amber-400 font-bold' : 'text-slate-500' }}">
                                {{ $field->is_required ? 'Yes' : 'No' }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right space-x-1">
                            <button type="button" @click="viewDetails({{ json_encode($field) }})" class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-brand-300 font-semibold text-[11px] border border-slate-700 transition">Details</button>
                            <button type="button" @click="openEditModal({{ json_encode($field) }})" class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-[11px] border border-slate-700 transition">Update</button>
                            <form method="POST" action="{{ route('web.contacts.custom-fields.delete', $field->id) }}" onsubmit="return confirm('Delete this field?');" class="inline">
                                @csrf
                                <button type="submit" class="px-2.5 py-1 rounded bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 font-semibold text-[11px] border border-rose-500/30 transition">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-slate-500">
                            No custom input fields configured yet. Standard fields (Name, Phone, Email, Groups) are active by default.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
