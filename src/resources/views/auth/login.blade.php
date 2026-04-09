<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ \App\Models\Setting::appName() }}</title>
    @if(\App\Models\Setting::get('app_favicon'))
        <link rel="icon" href="{{ asset('storage/' . \App\Models\Setting::get('app_favicon')) }}" type="image/x-icon">
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .touch-target { min-height: 48px; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <div class="text-center mb-6 md:mb-8">
            @if(\App\Models\Setting::hasLogo())
                <img src="{{ \App\Models\Setting::logoUrl() }}" alt="{{ \App\Models\Setting::appName() }}" class="h-16 md:h-20 mx-auto mb-2">
            @else
                <div class="text-5xl md:text-6xl mb-2">🕌</div>
            @endif
            <h1 class="text-2xl md:text-3xl font-bold text-green-700">{{ \App\Models\Setting::appName() }}</h1>
            <p class="text-gray-600 mt-2 text-sm md:text-base">Silakan login untuk melanjutkan</p>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('status'))
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg mb-4 text-sm">
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-lg p-6 md:p-8">
            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                        autocomplete="email" inputmode="email"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 text-base focus:ring-green-500 focus:border-green-500 touch-target">
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" name="password" id="password" required
                        autocomplete="current-password"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 text-base focus:ring-green-500 focus:border-green-500 touch-target">
                </div>

                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="w-5 h-5 rounded border-gray-300 text-green-600 focus:ring-green-500">
                        <span class="ml-2 text-sm text-gray-600">Ingat saya</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm text-green-600 hover:text-green-800">Lupa password?</a>
                </div>

                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 active:bg-green-800 text-white py-4 rounded-lg font-semibold text-base touch-target">
                    Login
                </button>
            </form>

            <div class="text-center mt-4">
                <p class="text-sm text-gray-500">Belum terdaftar?
                    <a href="{{ route('register') }}" class="text-green-600 hover:text-green-800 font-semibold">Daftarkan Masjid Anda</a>
                </p>
            </div>

        <div class="flex justify-center gap-4 mt-6 text-sm">
            <a href="{{ route('help.guide') }}" class="text-green-600 hover:text-green-800">📖 Panduan</a>
            <span class="text-gray-300">|</span>
            <a href="{{ route('help.faq') }}" class="text-green-600 hover:text-green-800">❓ FAQ</a>
        </div>

        <p class="text-center text-gray-400 text-xs mt-4">
            &copy; {{ date('Y') }} {{ \App\Models\Setting::orgName() ?: \App\Models\Setting::appName() }}
        </p>
    </div>
</body>
</html>
