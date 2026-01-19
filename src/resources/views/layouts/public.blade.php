<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', \App\Models\Setting::appName())</title>
    @if(\App\Models\Setting::get('app_favicon'))
        <link rel="icon" href="{{ asset('storage/' . \App\Models\Setting::get('app_favicon')) }}" type="image/x-icon">
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .touch-target { min-height: 44px; min-width: 44px; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen text-sm md:text-base">
    <!-- Top Navigation -->
    <nav class="bg-green-700 text-white shadow-lg sticky top-0 z-50">
        <div class="max-w-5xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <a href="{{ route('home') }}" class="text-xl font-bold flex items-center gap-2">
                    @if(\App\Models\Setting::hasLogo())
                        <img src="{{ \App\Models\Setting::logoUrl() }}" alt="{{ \App\Models\Setting::appName() }}" class="h-8">
                    @else
                        <span class="text-2xl">🕌</span>
                    @endif
                    <span>{{ \App\Models\Setting::appName() }}</span>
                </a>
                
                <div class="flex items-center gap-4">
                    <a href="{{ route('help.guide') }}" class="hover:text-green-200 {{ request()->routeIs('help.guide') ? 'text-green-200 font-semibold' : '' }}">
                        📖 Panduan
                    </a>
                    <a href="{{ route('help.faq') }}" class="hover:text-green-200 {{ request()->routeIs('help.faq') ? 'text-green-200 font-semibold' : '' }}">
                        ❓ FAQ
                    </a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="bg-green-600 hover:bg-green-500 px-4 py-2 rounded-lg">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="bg-white text-green-700 hover:bg-green-50 px-4 py-2 rounded-lg font-medium">
                            Login
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-5xl mx-auto px-4 py-8">
        @yield('content')
    </main>

    <footer class="max-w-5xl mx-auto px-4 py-6 text-center text-gray-500 text-sm border-t border-gray-200">
        <p>&copy; {{ date('Y') }} {{ \App\Models\Setting::orgName() ?: \App\Models\Setting::appName() }}</p>
    </footer>
</body>
</html>
