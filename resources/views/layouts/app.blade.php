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

    <!-- Alpine.js -->
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
            <nav class="p-3 space-y-1 overflow-y-auto">
                <a href="{{ route('dashboard') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl border text-sm font-semibold transition {{ ($activeNav ?? '') === 'overview' ? 'bg-brand-600/15 text-brand-400 border-brand-500/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 border-transparent' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('web.chat') }}" class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl border text-sm font-semibold transition {{ ($activeNav ?? '') === 'chat' ? 'bg-brand-600/15 text-brand-400 border-brand-500/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 border-transparent' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        <span>Live Chat</span>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-brand-500/20 text-brand-400 border border-brand-500/30">Live</span>
                </a>

                <a href="{{ route('web.contacts') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl border text-sm font-semibold transition {{ ($activeNav ?? '') === 'contacts' ? 'bg-brand-600/15 text-brand-400 border-brand-500/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 border-transparent' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span>Audience & CRM</span>
                </a>

                <a href="{{ route('web.campaigns') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl border text-sm font-semibold transition {{ ($activeNav ?? '') === 'campaigns' ? 'bg-brand-600/15 text-brand-400 border-brand-500/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 border-transparent' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                    <span>Broadcast Campaigns</span>
                </a>

                <a href="{{ route('web.templates') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl border text-sm font-semibold transition {{ ($activeNav ?? '') === 'templates' ? 'bg-brand-600/15 text-brand-400 border-brand-500/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 border-transparent' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>WhatsApp Templates</span>
                </a>

                <a href="{{ route('web.bot') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl border text-sm font-semibold transition {{ ($activeNav ?? '') === 'automations' ? 'bg-brand-600/15 text-brand-400 border-brand-500/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 border-transparent' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span>Bot & AI Copilot</span>
                </a>

                <a href="{{ route('web.integrations') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl border text-sm font-semibold transition {{ ($activeNav ?? '') === 'integrations' ? 'bg-brand-600/15 text-brand-400 border-brand-500/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 border-transparent' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                    <span>E-Commerce & API</span>
                </a>

                <a href="{{ route('web.billing') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl border text-sm font-semibold transition {{ ($activeNav ?? '') === 'billing' ? 'bg-brand-600/15 text-brand-400 border-brand-500/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 border-transparent' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    <span>Plans & Billing</span>
                </a>

                <a href="{{ route('web.settings') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl border text-sm font-semibold transition {{ ($activeNav ?? '') === 'settings' ? 'bg-brand-600/15 text-brand-400 border-brand-500/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 border-transparent' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Settings & API</span>
                </a>
            </nav>

            <div class="p-3 border-t border-slate-800">
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
