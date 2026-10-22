@extends('layouts.admin')

@section('content')
<div class="space-y-6">
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
            <button type="button" class="px-3.5 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs shadow-lg shadow-purple-500/20 transition flex items-center gap-1.5">
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
                <p class="text-xs text-slate-400">Pages automatically linked to the public homepage and footer.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-400">Total: 4 Pages</span>
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
                    <!-- Page 1: Terms of Service -->
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="py-3 px-4">
                            <div class="font-bold text-white">Terms of Service & User Agreement</div>
                            <div class="text-[11px] text-slate-500">Legal contract between platform & vendors</div>
                        </td>
                        <td class="py-3 px-4 font-mono text-[11px] text-purple-300">
                            /page/terms-of-service
                        </td>
                        <td class="py-3 px-4 text-slate-400 font-medium">
                            Legal / Policy
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                Published
                            </span>
                        </td>
                        <td class="py-3 px-4 text-slate-400 font-mono">
                            Oct 20, 2026
                        </td>
                        <td class="py-3 px-4 text-right space-x-1">
                            <button class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-[11px] border border-slate-700 transition">Edit</button>
                            <button class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-purple-300 font-semibold text-[11px] border border-slate-700 transition">Preview ↗</button>
                        </td>
                    </tr>

                    <!-- Page 2: Privacy Policy -->
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="py-3 px-4">
                            <div class="font-bold text-white">Privacy Policy & GDPR Compliance</div>
                            <div class="text-[11px] text-slate-500">Customer data handling, Meta Graph API disclosures</div>
                        </td>
                        <td class="py-3 px-4 font-mono text-[11px] text-purple-300">
                            /page/privacy-policy
                        </td>
                        <td class="py-3 px-4 text-slate-400 font-medium">
                            Legal / Policy
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                Published
                            </span>
                        </td>
                        <td class="py-3 px-4 text-slate-400 font-mono">
                            Oct 19, 2026
                        </td>
                        <td class="py-3 px-4 text-right space-x-1">
                            <button class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-[11px] border border-slate-700 transition">Edit</button>
                            <button class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-purple-300 font-semibold text-[11px] border border-slate-700 transition">Preview ↗</button>
                        </td>
                    </tr>

                    <!-- Page 3: About Us -->
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="py-3 px-4">
                            <div class="font-bold text-white">About VyapariChat Multi-Vendor Cloud</div>
                            <div class="text-[11px] text-slate-500">Company mission, leadership, infrastructure</div>
                        </td>
                        <td class="py-3 px-4 font-mono text-[11px] text-purple-300">
                            /page/about-us
                        </td>
                        <td class="py-3 px-4 text-slate-400 font-medium">
                            Company
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                Published
                            </span>
                        </td>
                        <td class="py-3 px-4 text-slate-400 font-mono">
                            Oct 15, 2026
                        </td>
                        <td class="py-3 px-4 text-right space-x-1">
                            <button class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-[11px] border border-slate-700 transition">Edit</button>
                            <button class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-purple-300 font-semibold text-[11px] border border-slate-700 transition">Preview ↗</button>
                        </td>
                    </tr>

                    <!-- Page 4: WhatsApp Cloud Pricing FAQs -->
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="py-3 px-4">
                            <div class="font-bold text-white">Meta WhatsApp Cloud API FAQs</div>
                            <div class="text-[11px] text-slate-500">Tier quotas, conversation charges breakdown</div>
                        </td>
                        <td class="py-3 px-4 font-mono text-[11px] text-purple-300">
                            /page/meta-api-pricing-faq
                        </td>
                        <td class="py-3 px-4 text-slate-400 font-medium">
                            Documentation
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-800 text-slate-400 border border-slate-700">
                                Draft
                            </span>
                        </td>
                        <td class="py-3 px-4 text-slate-400 font-mono">
                            Oct 22, 2026
                        </td>
                        <td class="py-3 px-4 text-right space-x-1">
                            <button class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-[11px] border border-slate-700 transition">Edit</button>
                            <button class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-purple-300 font-semibold text-[11px] border border-slate-700 transition">Preview ↗</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
