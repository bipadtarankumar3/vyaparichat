<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950 text-slate-100 antialiased" x-data="{ mobileMenuOpen: false }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'VyapariChat') }} - Super Admin Central Console</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                        },
                        admin: {
                            50: '#f5f3ff',
                            500: '#8b5cf6',
                            600: '#7c3aed',
                            700: '#6d28d9',
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

    <!-- Alpine.js Collapse & Core -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 9999px; }
    </style>
</head>
<body class="h-full flex flex-col overflow-hidden bg-slate-950 font-sans selection:bg-purple-500 selection:text-white">

    <!-- Top Admin Header -->
    <header class="h-16 flex-shrink-0 border-b border-slate-800 bg-slate-900/90 backdrop-blur px-4 sm:px-6 flex items-center justify-between z-30">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-purple-600 to-indigo-500 flex items-center justify-center shadow-lg shadow-purple-500/20 text-white font-bold text-lg">
                🛡️
            </div>
            <div>
                <span class="font-extrabold text-lg tracking-tight bg-gradient-to-r from-white via-slate-100 to-slate-400 bg-clip-text text-transparent">Vyapari<span class="text-purple-400">Admin</span></span>
                <span class="ml-1.5 text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded bg-purple-500/20 text-purple-300 border border-purple-500/30">Central Console</span>
            </div>
        </div>

        <!-- Admin User Profile & Actions -->
        <div class="flex items-center gap-4">
            <div class="hidden sm:flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-[11px] font-semibold text-emerald-400">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>System Operational</span>
            </div>

            <div class="flex items-center gap-3 pl-3 border-l border-slate-800">
                <div class="text-right hidden sm:block">
                    <div class="text-xs font-bold text-white">{{ Auth::user()->name ?? 'Super Administrator' }}</div>
                    <div class="text-[10px] text-purple-400 font-semibold">Master Admin</div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-xs text-slate-400 hover:text-rose-400 transition flex items-center gap-1 p-2 rounded-lg hover:bg-slate-800" title="Logout">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Workspace Container -->
    <div class="flex-1 flex overflow-hidden">
        <!-- Sidebar Navigation -->
        <aside class="w-64 bg-slate-900 border-r border-slate-800 flex flex-col justify-between z-20 overflow-y-auto">
            <nav class="p-3 space-y-1" x-data="{
                plansOpen: {{ in_array($activeNav ?? '', ['plans_auto', 'plans_manual']) ? 'true' : 'false' }},
                settingsOpen: {{ str_starts_with($activeNav ?? '', 'settings_') ? 'true' : 'false' }}
            }">
                <!-- 1. Dashboard -->
                <a href="{{ route('admin.dashboard') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl border text-sm font-semibold transition {{ ($activeNav ?? '') === 'dashboard' ? 'bg-purple-600/15 text-purple-300 border-purple-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60 border-transparent' }}">
                    <svg class="w-5 h-5 text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    <span>Dashboard</span>
                </a>

                <!-- 2. User Plans (Expandable Accordion) -->
                <div>
                    <button @click="plansOpen = !plansOpen" type="button" class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl border text-sm font-semibold transition {{ in_array($activeNav ?? '', ['plans_auto', 'plans_manual']) ? 'bg-purple-600/15 text-purple-300 border-purple-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60 border-transparent' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-fuchsia-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            <span>User Plans</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-1.5 py-0.2 rounded-full bg-rose-500/30 text-rose-300 font-bold text-[10px] border border-rose-500/40">1</span>
                            <svg :class="plansOpen ? 'rotate-180' : ''" class="w-4 h-4 text-slate-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </button>
                    <div x-show="plansOpen" x-collapse x-cloak class="pl-7 pr-2 py-1 space-y-1">
                        <a href="{{ route('admin.plans.subscriptions') }}" class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium transition {{ ($activeNav ?? '') === 'plans_auto' ? 'bg-purple-500/20 text-purple-200 font-semibold' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            <span>Auto Subscriptions</span>
                        </a>
                        <a href="{{ route('admin.plans.manual') }}" class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium transition {{ ($activeNav ?? '') === 'plans_manual' ? 'bg-purple-500/20 text-purple-200 font-semibold' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            <span>Manual/Prepaid</span>
                            <span class="px-1.5 py-0.2 rounded bg-rose-500 text-white font-extrabold text-[10px]">1</span>
                        </a>
                    </div>
                </div>

                <!-- 3. Users / Vendors -->
                <a href="{{ route('admin.vendors') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl border text-sm font-semibold transition {{ ($activeNav ?? '') === 'vendors' ? 'bg-purple-600/15 text-purple-300 border-purple-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60 border-transparent' }}">
                    <svg class="w-5 h-5 text-teal-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>Users / Vendors</span>
                </a>

                <!-- 4. Pages -->
                <a href="{{ route('admin.pages') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl border text-sm font-semibold transition {{ ($activeNav ?? '') === 'pages' ? 'bg-purple-600/15 text-purple-300 border-purple-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60 border-transparent' }}">
                    <svg class="w-5 h-5 text-orange-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                    <span>Pages</span>
                </a>

                <!-- 5. Languages -->
                <a href="{{ route('admin.languages') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl border text-sm font-semibold transition {{ ($activeNav ?? '') === 'languages' ? 'bg-purple-600/15 text-purple-300 border-purple-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60 border-transparent' }}">
                    <svg class="w-5 h-5 text-cyan-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>
                    <span>Languages</span>
                </a>

                <!-- 6. Setup -->
                <a href="{{ route('admin.setup') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl border text-sm font-semibold transition {{ ($activeNav ?? '') === 'setup' ? 'bg-purple-600/15 text-purple-300 border-purple-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60 border-transparent' }}">
                    <svg class="w-5 h-5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Setup</span>
                </a>

                <!-- 7. Embedded Signup -->
                <a href="{{ route('admin.embedded-signup') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl border text-sm font-semibold transition {{ ($activeNav ?? '') === 'embedded_signup' ? 'bg-purple-600/15 text-purple-300 border-purple-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60 border-transparent' }}">
                    <svg class="w-5 h-5 text-blue-400 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    <span>Embedded Signup</span>
                </a>

                <!-- 8. Settings (Expandable Accordion) -->
                <div>
                    <button @click="settingsOpen = !settingsOpen" type="button" class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl border text-sm font-semibold transition {{ str_starts_with($activeNav ?? '', 'settings_') ? 'bg-purple-600/15 text-purple-300 border-purple-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60 border-transparent' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                            <span>Settings</span>
                        </div>
                        <svg :class="settingsOpen ? 'rotate-180' : ''" class="w-4 h-4 text-slate-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="settingsOpen" x-collapse x-cloak class="pl-7 pr-2 py-1 space-y-1">
                        <a href="{{ route('admin.settings', 'general') }}" class="block px-3 py-1.5 rounded-lg text-xs font-medium transition {{ ($activeNav ?? '') === 'settings_general' ? 'bg-purple-500/20 text-purple-200 font-semibold' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            General
                        </a>
                        <a href="{{ route('admin.settings', 'user') }}" class="block px-3 py-1.5 rounded-lg text-xs font-medium transition {{ ($activeNav ?? '') === 'settings_user' ? 'bg-purple-500/20 text-purple-200 font-semibold' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            User
                        </a>
                        <a href="{{ route('admin.settings', 'currency') }}" class="block px-3 py-1.5 rounded-lg text-xs font-medium transition {{ ($activeNav ?? '') === 'settings_currency' ? 'bg-purple-500/20 text-purple-200 font-semibold' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            Currency
                        </a>
                        <a href="{{ route('admin.settings', 'payment') }}" class="block px-3 py-1.5 rounded-lg text-xs font-medium transition {{ ($activeNav ?? '') === 'settings_payment' ? 'bg-purple-500/20 text-purple-200 font-semibold' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            Payments
                        </a>
                        <a href="{{ route('admin.settings', 'subscription-plans') }}" class="block px-3 py-1.5 rounded-lg text-xs font-medium transition {{ ($activeNav ?? '') === 'settings_subscription_plans' ? 'bg-purple-500/20 text-purple-200 font-semibold' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            User Plans
                        </a>
                        <a href="{{ route('admin.settings', 'email') }}" class="block px-3 py-1.5 rounded-lg text-xs font-medium transition {{ ($activeNav ?? '') === 'settings_email' ? 'bg-purple-500/20 text-purple-200 font-semibold' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            Email
                        </a>
                        <a href="{{ route('admin.settings', 'social-login') }}" class="block px-3 py-1.5 rounded-lg text-xs font-medium transition {{ ($activeNav ?? '') === 'settings_social_login' ? 'bg-purple-500/20 text-purple-200 font-semibold' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            Logins
                        </a>
                        <a href="{{ route('admin.settings', 'misc') }}" class="block px-3 py-1.5 rounded-lg text-xs font-medium transition {{ ($activeNav ?? '') === 'settings_misc' ? 'bg-purple-500/20 text-purple-200 font-semibold' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                            Appearance
                        </a>
                    </div>
                </div>
            </nav>

            <div class="p-4 border-t border-slate-800 text-xs text-slate-500 font-mono">
                Multi-Tenant Architecture v2.0
            </div>
        </aside>

        <!-- Dynamic Body -->
        <main class="flex-1 overflow-y-auto bg-slate-950 p-6 sm:p-8">
            <!-- Flash Message Alerts -->
            @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-xs flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span>✓</span>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span>⚠️</span>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

</body>
</html>
