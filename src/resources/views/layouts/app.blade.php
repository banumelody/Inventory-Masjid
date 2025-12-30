<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Inventory Masjid')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-green-700 text-white shadow-lg print:hidden">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <a href="{{ route('items.index') }}" class="text-xl font-bold">🕌 Inventory Masjid</a>
                <div class="flex space-x-6">
                    <a href="{{ route('items.index') }}" class="hover:text-green-200 {{ request()->routeIs('items.*') ? 'text-green-200 font-semibold' : '' }}">Inventaris</a>
                    <a href="{{ route('categories.index') }}" class="hover:text-green-200 {{ request()->routeIs('categories.*') ? 'text-green-200 font-semibold' : '' }}">Kategori</a>
                    <a href="{{ route('locations.index') }}" class="hover:text-green-200 {{ request()->routeIs('locations.*') ? 'text-green-200 font-semibold' : '' }}">Lokasi</a>
                    <a href="{{ route('reports.index') }}" class="hover:text-green-200 {{ request()->routeIs('reports.*') ? 'text-green-200 font-semibold' : '' }}">Laporan</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 py-8">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
