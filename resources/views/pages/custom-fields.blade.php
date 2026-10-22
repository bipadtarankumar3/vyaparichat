@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-4xl">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
                <span>➕</span>
                <span>Custom Input Fields (Add Input)</span>
            </h1>
            <p class="text-xs text-slate-400 mt-1">Define custom data fields for customer profiles (e.g. City, Order Number, Birthday, Membership ID).</p>
        </div>
        <a href="{{ route('web.contacts') }}" class="px-4 py-2 rounded-xl bg-slate-900 border border-slate-700 hover:bg-slate-800 text-slate-300 font-semibold text-xs transition">
            ← All Contacts
        </a>
    </div>

    <!-- Custom Fields Table -->
    <div class="bg-slate-900 rounded-2xl border border-slate-800 overflow-hidden shadow">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950 text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-800">
                    <tr>
                        <th class="px-5 py-3.5">Input Field Name</th>
                        <th class="px-5 py-3.5">Key / Variable</th>
                        <th class="px-5 py-3.5">Input Type</th>
                        <th class="px-5 py-3.5">Required</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($fields as $field)
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="px-5 py-4 font-bold text-white">{{ $field->name }}</td>
                            <td class="px-5 py-4 font-mono text-brand-400 text-[11px]">&#123;&#123; {{ $field->key }} &#125;&#125;</td>
                            <td class="px-5 py-4 text-slate-300 uppercase text-[10px]">{{ $field->type ?? 'text' }}</td>
                            <td class="px-5 py-4">
                                <span class="{{ $field->is_required ? 'text-amber-400' : 'text-slate-500' }}">
                                    {{ $field->is_required ? 'Yes' : 'No' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-10 text-center text-slate-500">
                                No custom input fields configured yet. Standard fields (Name, Phone, Email, Groups) are active by default.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
