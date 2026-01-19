<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - {{ \App\Models\Setting::appName() }}</title>
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
            <h1 class="text-2xl md:text-3xl font-bold text-green-700">Lupa Password</h1>
            <p class="text-gray-600 mt-2 text-sm md:text-base">Masukkan email untuk reset password</p>
        </div>

        @if(session('status'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4 text-sm">
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
            <form action="{{ route('password.email') }}" method="POST">
                @csrf
                <div class="mb-6">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                        autocomplete="email" inputmode="email"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 text-base focus:ring-green-500 focus:border-green-500 touch-target"
                        placeholder="nama@email.com">
                </div>

                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 active:bg-green-800 text-white py-4 rounded-lg font-semibold text-base touch-target">
                    Kirim Link Reset Password
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-sm text-green-600 hover:text-green-800">
                    &larr; Kembali ke Login
                </a>
            </div>
        </div>

        <p class="text-center text-gray-400 text-xs mt-6">
            &copy; {{ date('Y') }} {{ \App\Models\Setting::orgName() ?: \App\Models\Setting::appName() }}
        </p>
    </div>
</body>
</html>
