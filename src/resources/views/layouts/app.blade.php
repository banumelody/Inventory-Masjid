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
                <a href="{{ route('dashboard') }}" class="text-xl font-bold">🕌 Inventory Masjid</a>
                @auth
                <div class="flex items-center space-x-4 lg:space-x-6">
                    <a href="{{ route('dashboard') }}" class="hover:text-green-200 {{ request()->routeIs('dashboard') ? 'text-green-200 font-semibold' : '' }}" title="Dashboard">📊</a>
                    <a href="{{ route('items.index') }}" class="hover:text-green-200 {{ request()->routeIs('items.*') ? 'text-green-200 font-semibold' : '' }}">Inventaris</a>
                    <a href="{{ route('loans.index') }}" class="hover:text-green-200 {{ request()->routeIs('loans.*') ? 'text-green-200 font-semibold' : '' }}">Pinjam</a>
                    <a href="{{ route('stock-movements.index') }}" class="hover:text-green-200 {{ request()->routeIs('stock-movements.*') ? 'text-green-200 font-semibold' : '' }}">Mutasi</a>
                    <a href="{{ route('categories.index') }}" class="hover:text-green-200 {{ request()->routeIs('categories.*') ? 'text-green-200 font-semibold' : '' }}">Kategori</a>
                    <a href="{{ route('locations.index') }}" class="hover:text-green-200 {{ request()->routeIs('locations.*') ? 'text-green-200 font-semibold' : '' }}">Lokasi</a>
                    <a href="{{ route('reports.index') }}" class="hover:text-green-200 {{ request()->routeIs('reports.*') || request()->routeIs('export.*') ? 'text-green-200 font-semibold' : '' }}">Laporan</a>
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('users.index') }}" class="hover:text-green-200 {{ request()->routeIs('users.*') ? 'text-green-200 font-semibold' : '' }}">User</a>
                    <a href="{{ route('backups.index') }}" class="hover:text-green-200 {{ request()->routeIs('backups.*') ? 'text-green-200 font-semibold' : '' }}">Backup</a>
                    <a href="{{ route('feedbacks.index') }}" class="hover:text-green-200 {{ request()->routeIs('feedbacks.index') ? 'text-green-200 font-semibold' : '' }}">💬</a>
                    @endif
                    <div class="border-l border-green-500 pl-4 flex items-center space-x-3">
                        <span class="text-sm hidden lg:inline">{{ auth()->user()->name }}</span>
                        <span class="text-xs px-2 py-0.5 rounded bg-green-600">{{ auth()->user()->role->display_name }}</span>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-sm hover:text-green-200">Logout</button>
                        </form>
                    </div>
                </div>
                @endauth
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

    <footer class="max-w-7xl mx-auto px-4 py-4 text-center text-gray-500 text-sm print:hidden">
        <a href="{{ route('feedbacks.create') }}" class="hover:text-green-600">💬 Kirim Feedback</a>
    </footer>
</body>
</html>
