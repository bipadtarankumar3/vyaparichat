<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950 text-slate-100 antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - {{ config('app.name', 'VyapariChat') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full flex items-center justify-center p-4 bg-slate-950 font-sans selection:bg-brand-500 selection:text-white">

    <div class="w-full max-w-md space-y-8">
        <!-- Logo Header -->
        <div class="text-center space-y-2">
            <div class="inline-flex w-12 h-12 rounded-2xl bg-gradient-to-tr from-brand-600 to-emerald-400 items-center justify-center shadow-xl shadow-brand-500/20 text-white text-2xl font-bold">
                💬
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                Sign in to <span class="text-brand-400">VyapariChat</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-400">
                Official Meta WhatsApp Cloud API SaaS Platform
            </p>
        </div>

        <!-- Login Card -->
        <div class="p-6 sm:p-8 rounded-2xl bg-slate-900 border border-slate-800 shadow-2xl space-y-6">
            @if ($errors->any())
                <div class="p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-xs font-semibold">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email', 'admin@example.com') }}" required autofocus class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:border-brand-500 transition">
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Password</label>
                    <input type="password" id="password" name="password" value="123456" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:border-brand-500 transition">
                </div>

                <div class="flex items-center justify-between text-xs text-slate-400">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded bg-slate-950 border-slate-800 text-brand-500 focus:ring-0">
                        <span>Remember me</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-sm shadow-lg shadow-brand-500/25 transition">
                    Sign In
                </button>
            </form>

            <!-- Quick Demo Credentials Box -->
            <div class="p-3.5 rounded-xl bg-slate-950/70 border border-slate-800/80 text-[11px] text-slate-400 space-y-1">
                <div class="font-bold text-slate-300">Default Demo Accounts:</div>
                <div class="flex items-center justify-between">
                    <span>SuperAdmin:</span>
                    <span class="font-mono text-emerald-400">admin@example.com / 123456</span>
                </div>
                <div class="flex items-center justify-between">
                    <span>Vendor Admin:</span>
                    <span class="font-mono text-emerald-400">support@vyaparichat.com / 123456</span>
                </div>
            </div>
        </div>

        <div class="text-center text-xs text-slate-500">
            Powered by clean Laravel 13 & Meta Graph API v20.0
        </div>
    </div>

</body>
</html>
