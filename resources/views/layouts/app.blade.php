<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950 text-slate-100 antialiased" x-data="{ currentTab: 'overview', mobileMenuOpen: false }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'VyapariChat') }} - WhatsApp Cloud API SaaS</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN Fallback + Vite -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                        },
                        wa: {
                            light: '#25D366',
                            dark: '#075E54',
                            deep: '#128C7E',
                            chat: '#0b141a',
                            bubble: '#005c4b',
                            incoming: '#202c33',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js Collapse + Core -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }
    </style>
</head>
<body class="h-full flex flex-col overflow-hidden bg-slate-950 font-sans selection:bg-brand-500 selection:text-white">

    <!-- Super Admin Impersonation / Console Switch Banner -->
    @if(session()->has('impersonated_vendor_id') || (Auth::check() && Auth::user()->is_super_admin))
        <div class="bg-gradient-to-r from-purple-950 via-indigo-950 to-slate-900 border-b border-purple-500/40 px-4 py-2 flex items-center justify-between text-xs text-purple-200 z-50 shadow-md">
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded-full bg-purple-500/30 text-purple-300 font-bold uppercase tracking-wider text-[10px] border border-purple-500/40">
                    ⚡ Super Admin Session
                </span>
                <span>You are currently managing <strong>{{ $vendor->name }}</strong> workspace.</span>
            </div>
            <div class="flex items-center gap-2">
                @if(session()->has('impersonated_vendor_id'))
                    <form method="POST" action="{{ route('admin.impersonate.leave') }}">
                        @csrf
                        <button type="submit" class="px-3 py-1 rounded-lg bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs transition flex items-center gap-1 shadow-md shadow-purple-500/20">
                            <span>← Return to Admin Dashboard</span>
                        </button>
                    </form>
                @else
                    <a href="{{ route('admin.vendors') }}" class="px-3 py-1 rounded-lg bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs transition flex items-center gap-1 shadow-md shadow-purple-500/20">
                        <span>← Admin Central Console</span>
                    </a>
                @endif
            </div>
        </div>
    @endif

    <!-- Top Navigation Bar -->
    <header class="h-16 flex-shrink-0 border-b border-slate-800 bg-slate-900/80 backdrop-blur px-4 sm:px-6 flex items-center justify-between z-30">
        <div class="flex items-center gap-3">
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-slate-400 hover:text-white p-1.5 rounded-lg hover:bg-slate-800">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-brand-600 to-emerald-400 flex items-center justify-center shadow-lg shadow-brand-500/20 text-white font-bold text-lg">
                    💬
                </div>
                <div>
                    <span class="font-extrabold text-lg tracking-tight bg-gradient-to-r from-white via-slate-100 to-slate-400 bg-clip-text text-transparent">Vyapari<span class="text-brand-500">Chat</span></span>
                    <span class="ml-1.5 text-[10px] uppercase font-bold tracking-wider px-1.5 py-0.5 rounded bg-brand-500/10 text-brand-400 border border-brand-500/20">V2.0</span>
                </div>
            </div>
        </div>

        <!-- Vendor & Status Pill -->
        <div class="flex items-center gap-4">
            @if(Auth::check() && Auth::user()->is_super_admin)
                <a href="{{ route('admin.vendors') }}" class="hidden sm:flex items-center gap-1.5 px-3 py-1 rounded-full bg-purple-500/10 border border-purple-500/30 text-xs font-bold text-purple-300 hover:bg-purple-500/20 transition">
                    <span>🛡️ Admin Console</span>
                </a>
            @endif

            <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-800/80 border border-slate-700/60 text-xs">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span class="text-slate-300 font-medium">{{ $vendor->name ?? 'Default Vendor' }}</span>
                <span class="text-slate-500">•</span>
                <span class="text-emerald-400 font-medium">Meta Cloud API Connected</span>
            </div>

            <!-- Profile Dropdown Menu & Logout -->
            <div class="flex items-center gap-3 pl-3 border-l border-slate-800">
                <a href="{{ route('web.settings') }}" class="w-8 h-8 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center font-bold text-xs text-brand-400 hover:border-brand-500 transition" title="Settings">
                    {{ substr($vendor->name ?? 'V', 0, 1) }}
                </a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-xs text-slate-400 hover:text-rose-400 transition flex items-center gap-1 px-2 py-1 rounded hover:bg-slate-800" title="Sign out">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span class="hidden md:inline">Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Workspace Container -->
    <div class="flex-1 flex overflow-hidden">

        <!-- Sidebar Navigation -->
        <aside :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'" class="fixed md:static inset-y-16 left-0 w-64 bg-slate-900 border-r border-slate-800 flex flex-col justify-between z-20 transition-transform duration-200 ease-in-out">
            <nav class="p-3 space-y-1 overflow-y-auto" x-data="{
                contactsOpen: {{ in_array($activeNav ?? '', ['contacts', 'contacts_groups', 'contacts_fields']) ? 'true' : 'false' }},
                chatbotOpen: {{ in_array($activeNav ?? '', ['automations', 'bot_flows']) ? 'true' : 'false' }},
                setupOpen: {{ in_array($activeNav ?? '', ['settings', 'integrations']) ? 'true' : 'false' }}
            }">
                <!-- 1. Dashboard -->
                <a href="{{ route('dashboard') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl border text-sm font-semibold transition {{ ($activeNav ?? '') === 'overview' ? 'bg-brand-600/15 text-brand-400 border-brand-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60 border-transparent' }}">
                    <svg class="w-5 h-5 text-sky-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    <span>Dashboard</span>
                </a>

                <!-- 2. Live Chat (with badge 21) -->
                @if($vendor->hasSectionAccess('chat'))
                <a href="{{ route('web.chat') }}" class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl border text-sm font-semibold transition {{ ($activeNav ?? '') === 'chat' ? 'bg-brand-600/15 text-brand-400 border-brand-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60 border-transparent' }}">
                    <div class="flex items-center gap-2.5">
                        <span class="px-1.5 py-0.5 rounded-md bg-emerald-500 text-slate-950 font-extrabold text-[11px] leading-none">{{ $unreadChatCount ?? 21 }}</span>
                        <svg class="w-5 h-5 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        <span>Live Chat</span>
                    </div>
                </a>
                @endif

                <!-- 3. Templates -->
                @if($vendor->hasSectionAccess('templates'))
                <a href="{{ route('web.templates') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl border text-sm font-semibold transition {{ ($activeNav ?? '') === 'templates' ? 'bg-brand-600/15 text-brand-400 border-brand-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60 border-transparent' }}">
                    <svg class="w-5 h-5 text-teal-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <span>Templates</span>
                </a>
                @endif

                <!-- 4. Campaigns -->
                @if($vendor->hasSectionAccess('campaigns'))
                <a href="{{ route('web.campaigns') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl border text-sm font-semibold transition {{ ($activeNav ?? '') === 'campaigns' ? 'bg-brand-600/15 text-brand-400 border-brand-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60 border-transparent' }}">
                    <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    <span>Campaigns</span>
                </a>
                @endif

                <!-- 5. Contacts (Expandable) -->
                @if($vendor->hasSectionAccess('contacts'))
                <div class="space-y-1">
                    <button @click="contactsOpen = !contactsOpen" class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-300 hover:text-white hover:bg-slate-800/60 transition">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span>Contacts</span>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="contactsOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="contactsOpen" x-collapse class="pl-9 space-y-1">
                        <a href="{{ route('web.contacts') }}" class="block py-1.5 px-3 rounded-lg text-xs font-medium {{ ($activeNav ?? '') === 'contacts' ? 'text-brand-400 font-bold bg-brand-500/10' : 'text-slate-400 hover:text-white' }}">
                            All Contacts
                        </a>
                        <a href="{{ route('web.contacts.groups') }}" class="block py-1.5 px-3 rounded-lg text-xs font-medium {{ ($activeNav ?? '') === 'contacts_groups' ? 'text-brand-400 font-bold bg-brand-500/10' : 'text-slate-400 hover:text-white' }}">
                            — Contact Groups
                        </a>
                        <a href="{{ route('web.contacts.custom-fields') }}" class="block py-1.5 px-3 rounded-lg text-xs font-medium {{ ($activeNav ?? '') === 'contacts_fields' ? 'text-brand-400 font-bold bg-brand-500/10' : 'text-slate-400 hover:text-white' }}">
                            — Add Input
                        </a>
                    </div>
                </div>
                @endif

                <!-- 6. Chatbot (Expandable) -->
                @if($vendor->hasSectionAccess('bot'))
                <div class="space-y-1">
                    <button @click="chatbotOpen = !chatbotOpen" class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-300 hover:text-white hover:bg-slate-800/60 transition">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                            <span>Chatbot</span>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="chatbotOpen ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                    <div x-show="chatbotOpen" x-collapse class="pl-9 space-y-1">
                        <a href="{{ route('web.bot') }}" class="block py-1.5 px-3 rounded-lg text-xs font-medium {{ ($activeNav ?? '') === 'automations' ? 'text-brand-400 font-bold bg-brand-500/10' : 'text-slate-400 hover:text-white' }}">
                            All Chatbots
                        </a>
                        <a href="{{ route('web.bot.flows') }}" class="block py-1.5 px-3 rounded-lg text-xs font-medium {{ ($activeNav ?? '') === 'bot_flows' ? 'text-brand-400 font-bold bg-brand-500/10' : 'text-slate-400 hover:text-white' }}">
                            — Flow Maker
                        </a>
                    </div>
                </div>
                @endif

                <!-- 7. Agents -->
                <a href="{{ route('web.agents') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl border text-sm font-semibold transition {{ ($activeNav ?? '') === 'agents' ? 'bg-brand-600/15 text-brand-400 border-brand-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60 border-transparent' }}">
                    <svg class="w-5 h-5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span>Agents</span>
                </a>

                <!-- 8. QR Code -->
                <a href="{{ route('web.qr-code') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl border text-sm font-semibold transition {{ ($activeNav ?? '') === 'qr_code' ? 'bg-brand-600/15 text-brand-400 border-brand-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60 border-transparent' }}">
                    <svg class="w-5 h-5 text-fuchsia-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    <span>QR Code</span>
                </a>

                <!-- 9. My Plan -->
                @if($vendor->hasSectionAccess('billing'))
                <a href="{{ route('web.my-plan') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl border text-sm font-semibold transition {{ ($activeNav ?? '') === 'billing' ? 'bg-brand-600/15 text-brand-400 border-brand-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60 border-transparent' }}">
                    <svg class="w-5 h-5 text-purple-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    <span>My Plan</span>
                </a>
                @endif

                <!-- 10. Setup (Expandable) -->
                <div class="space-y-1">
                    <button @click="setupOpen = !setupOpen" class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-300 hover:text-white hover:bg-slate-800/60 transition">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>Setup</span>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="setupOpen ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                    <div x-show="setupOpen" x-collapse class="pl-9 space-y-1">
                        <a href="{{ route('web.settings') }}" class="block py-1.5 px-3 rounded-lg text-xs font-medium {{ ($activeNav ?? '') === 'settings' ? 'text-brand-400 font-bold bg-brand-500/10' : 'text-slate-400 hover:text-white' }}">
                            Basic
                        </a>
                        <a href="{{ route('web.settings') }}" class="block py-1.5 px-3 rounded-lg text-xs font-medium text-slate-400 hover:text-white">
                            WhatsApp Setup
                        </a>
                        <a href="{{ route('web.bot') }}" class="block py-1.5 px-3 rounded-lg text-xs font-medium text-slate-400 hover:text-white">
                            Chatbot Settings
                        </a>
                        <a href="{{ route('web.integrations') }}" class="block py-1.5 px-3 rounded-lg text-xs font-medium {{ ($activeNav ?? '') === 'integrations' ? 'text-brand-400 font-bold bg-brand-500/10' : 'text-slate-400 hover:text-white' }}">
                            API Integration
                        </a>
                    </div>
                </div>
            </nav>

            <div class="p-3 border-t border-slate-800 space-y-2">
                @if(Auth::check() && Auth::user()->is_super_admin)
                    <a href="{{ route('admin.vendors') }}" class="w-full flex items-center justify-between p-2 rounded-xl bg-purple-950/60 border border-purple-800/40 text-xs text-purple-300 hover:bg-purple-900/60 transition">
                        <span class="font-bold flex items-center gap-1.5">
                            <span>🛡️</span>
                            <span>Admin Console</span>
                        </span>
                        <span class="text-[10px]">&rarr;</span>
                    </a>
                @endif

                <div class="p-3 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-slate-400 flex items-center justify-between">
                    <div>
                        <div class="font-semibold text-slate-200">Server Health</div>
                        <div class="text-emerald-400 flex items-center gap-1.5 mt-0.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                            Operational
                        </div>
                    </div>
                    <span class="text-[10px] text-slate-500 font-mono">v2.0-L13</span>
                </div>
            </div>
        </aside>

        <!-- Dynamic Content Body -->
        <main class="flex-1 overflow-y-auto bg-slate-950 p-4 sm:p-6 lg:p-8">
            @yield('content')
        </main>
    </div>

</body>
</html>
