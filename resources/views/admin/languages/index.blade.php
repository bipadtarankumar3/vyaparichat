@extends('layouts.admin')

@section('content')
<div x-data="adminLanguagesManager()" class="space-y-6">

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs font-semibold flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            <span>{{ session('error') }}</span>
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
                <h1 class="text-2xl font-black text-white tracking-tight">Languages & Localization</h1>
                <span class="px-2 py-0.5 rounded-full bg-cyan-500/10 border border-cyan-500/30 text-[10px] font-bold text-cyan-400 uppercase tracking-wider">
                    Multi-Language Engine
                </span>
            </div>
            <p class="text-xs text-slate-400 mt-1">Configure system languages, translation strings, RTL writing modes, and default locales.</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="openAddModal()" type="button" class="px-3.5 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs shadow-lg shadow-purple-500/20 transition flex items-center gap-1.5 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Add Language</span>
            </button>
        </div>
    </div>

    <!-- Structured Modular Views -->
    @include('admin.languages.listing')
    @include('admin.languages.add')
    @include('admin.languages.update')
    @include('admin.languages.details')
</div>

<script>
function adminLanguagesManager() {
    return {
        showAddModal: false,
        showEditModal: false,
        showDetailsModal: false,
        selectedLanguage: null,
        editingLanguage: null,

        openAddModal() {
            this.showAddModal = true;
        },

        viewDetails(lang) {
            this.selectedLanguage = lang;
            this.showDetailsModal = true;
        },

        openEditModal(lang) {
            this.editingLanguage = Object.assign({}, lang);
            this.showEditModal = true;
        }
    };
}
</script>
@endsection
