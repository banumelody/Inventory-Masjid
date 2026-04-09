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
        .nav-transition { transition: all 0.3s ease; }
        
        /* Sidebar Styles */
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.625rem 1rem;
            border-radius: 0.5rem;
            color: #4b5563;
            transition: all 0.15s ease;
            font-size: 0.875rem;
        }
        .sidebar-link:hover {
            background-color: #f0fdf4;
            color: #15803d;
        }
        .sidebar-link.active {
            background-color: #dcfce7;
            color: #15803d;
            font-weight: 500;
        }
        .sidebar-group-title {
            font-size: 0.7rem;
            font-weight: 600;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0 1rem;
            margin-bottom: 0.5rem;
        }
        
        /* Sidebar Scrollbar - Smooth & Modern */
        .sidebar-nav {
            scrollbar-width: thin;
            scrollbar-color: #d1d5db transparent;
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
        }
        .sidebar-nav::-webkit-scrollbar {
            width: 6px;
        }
        .sidebar-nav::-webkit-scrollbar-track {
            background: transparent;
            margin: 4px 0;
        }
        .sidebar-nav::-webkit-scrollbar-thumb {
            background-color: #d1d5db;
            border-radius: 3px;
            transition: background-color 0.2s ease;
        }
        .sidebar-nav::-webkit-scrollbar-thumb:hover {
            background-color: #9ca3af;
        }
        .sidebar-nav:hover::-webkit-scrollbar-thumb {
            background-color: #9ca3af;
        }
        
        /* Sidebar Container */
        .sidebar-container {
            display: flex;
            flex-direction: column;
            height: 100%;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen text-sm md:text-base">
    @auth
    <div class="flex min-h-screen">
        <!-- Sidebar (Desktop) -->
        <aside id="sidebar" class="fixed lg:static inset-y-0 left-0 z-40 w-64 bg-white border-r border-gray-200 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out print:hidden">
            <div class="sidebar-container">
                <!-- Logo -->
                <div class="flex items-center gap-3 px-4 py-4 border-b border-gray-100 flex-shrink-0">
                    @if(\App\Models\Setting::hasLogo())
                        <img src="{{ \App\Models\Setting::logoUrl() }}" alt="{{ \App\Models\Setting::appName() }}" class="h-10 w-10 object-contain">
                    @else
                        <span class="text-3xl">🕌</span>
                    @endif
                    <div class="min-w-0">
                        <h1 class="font-bold text-gray-800 truncate">{{ \App\Models\Setting::appName() }}</h1>
                        @if(\App\Models\Setting::orgName())
                            <p class="text-xs text-gray-500 truncate">{{ \App\Models\Setting::orgName() }}</p>
                        @endif
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-3 py-4 pb-20 space-y-6 overflow-y-auto sidebar-nav">

                @if(auth()->user()->isSuperAdmin())
                <!-- Tenant Switcher -->
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs font-semibold text-gray-400 uppercase mb-2">Konteks Masjid</p>
                    <form action="{{ route('masjids.switch') }}" method="POST" id="tenant-switch-form">
                        @csrf
                        <select name="masjid_id" onchange="document.getElementById('tenant-switch-form').submit()"
                            class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <option value="all" {{ !session('current_masjid_id') ? 'selected' : '' }}>🌐 Semua Masjid</option>
                            @foreach(\App\Models\Masjid::orderBy('name')->get() as $m)
                                <option value="{{ $m->id }}" {{ session('current_masjid_id') == $m->id ? 'selected' : '' }}>🕌 {{ $m->name }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>

                <!-- Superadmin Menu -->
                <div>
                    <p class="sidebar-group-title">Superadmin</p>
                    <div class="space-y-1">
                        <a href="{{ route('masjids.index') }}" class="sidebar-link {{ request()->routeIs('masjids.*') ? 'active' : '' }}">
                            <span class="text-lg">🕌</span>
                            <span>Kelola Masjid</span>
                        </a>
                    </div>
                </div>
                @endif

                <!-- Main Menu -->
                <div>
                    <p class="sidebar-group-title">Menu Utama</p>
                    <div class="space-y-1">
                        <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <span class="text-lg">📊</span>
                            <span>{{ __('ui.dashboard') }}</span>
                        </a>
                        <a href="{{ route('items.index') }}" class="sidebar-link {{ request()->routeIs('items.*') ? 'active' : '' }}">
                            <span class="text-lg">📦</span>
                            <span>{{ __('ui.items') }}</span>
                        </a>
                        <a href="{{ route('loans.index') }}" class="sidebar-link {{ request()->routeIs('loans.*') && !request()->routeIs('loans.scan-return') ? 'active' : '' }}">
                            <span class="text-lg">📤</span>
                            <span>{{ __('ui.loans') }}</span>
                        </a>
                        <a href="{{ route('stock-movements.index') }}" class="sidebar-link {{ request()->routeIs('stock-movements.*') ? 'active' : '' }}">
                            <span class="text-lg">📊</span>
                            <span>{{ __('ui.stock_movements') }}</span>
                        </a>
                        <a href="{{ route('maintenances.index') }}" class="sidebar-link {{ request()->routeIs('maintenances.*') ? 'active' : '' }}">
                            <span class="text-lg">🔧</span>
                            <span>{{ __('ui.maintenances') }}</span>
                        </a>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div>
                    <p class="sidebar-group-title">Aksi Cepat</p>
                    <div class="space-y-1">
                        <a href="{{ route('qrcode.scan') }}" class="sidebar-link {{ request()->routeIs('qrcode.scan') ? 'active' : '' }}">
                            <span class="text-lg">📷</span>
                            <span>Scan QR Barang</span>
                        </a>
                        @if(auth()->user()->canManageLoans())
                        <a href="{{ route('qrcode.audit-scan') }}" class="sidebar-link {{ request()->routeIs('qrcode.audit-scan') ? 'active' : '' }}">
                            <span class="text-lg">📋</span>
                            <span>Audit Scan</span>
                        </a>
                        <a href="{{ route('loans.scan-return') }}" class="sidebar-link {{ request()->routeIs('loans.scan-return') ? 'active' : '' }}">
                            <span class="text-lg">🔄</span>
                            <span>Scan Pengembalian</span>
                        </a>
                        @endif
                    </div>
                </div>

                <!-- Master Data -->
                <div>
                    <p class="sidebar-group-title">Master Data</p>
                    <div class="space-y-1">
                        <a href="{{ route('categories.index') }}" class="sidebar-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                            <span class="text-lg">📁</span>
                            <span>{{ __('ui.categories') }}</span>
                        </a>
                        <a href="{{ route('locations.index') }}" class="sidebar-link {{ request()->routeIs('locations.*') ? 'active' : '' }}">
                            <span class="text-lg">📍</span>
                            <span>{{ __('ui.locations') }}</span>
                        </a>
                    </div>
                </div>

                <!-- Reports -->
                <div>
                    <p class="sidebar-group-title">{{ __('ui.reports') }}</p>
                    <div class="space-y-1">
                        <a href="{{ route('reports.index') }}" class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                            <span class="text-lg">📋</span>
                            <span>{{ __('ui.reports') }}</span>
                        </a>
                        <a href="{{ route('export.index') }}" class="sidebar-link {{ request()->routeIs('export.*') ? 'active' : '' }}">
                            <span class="text-lg">📥</span>
                            <span>{{ __('ui.export') }}</span>
                        </a>
                    </div>
                </div>

                <!-- Admin Only -->
                @if(auth()->user()->isAdmin())
                <div>
                    <p class="sidebar-group-title">Administrasi</p>
                    <div class="space-y-1">
                        <a href="{{ route('imports.index') }}" class="sidebar-link {{ request()->routeIs('imports.*') ? 'active' : '' }}">
                            <span class="text-lg">📤</span>
                            <span>{{ __('ui.imports') }}</span>
                        </a>
                        <a href="{{ route('users.index') }}" class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                            <span class="text-lg">👥</span>
                            <span>{{ __('ui.users') }}</span>
                        </a>
                        @if(auth()->user()->isSuperAdmin())
                        <a href="{{ route('backups.index') }}" class="sidebar-link {{ request()->routeIs('backups.*') ? 'active' : '' }}">
                            <span class="text-lg">💾</span>
                            <span>{{ __('ui.backups') }}</span>
                        </a>
                        @endif
                        <a href="{{ route('activity-logs.index') }}" class="sidebar-link {{ request()->routeIs('activity-logs.*') ? 'active' : '' }}">
                            <span class="text-lg">📜</span>
                            <span>{{ __('ui.activity_logs') }}</span>
                        </a>
                        <a href="{{ route('scan-logs.index') }}" class="sidebar-link {{ request()->routeIs('scan-logs.*') ? 'active' : '' }}">
                            <span class="text-lg">📷</span>
                            <span>{{ __('ui.scan_logs') }}</span>
                        </a>
                        <a href="{{ route('settings.index') }}" class="sidebar-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                            <span class="text-lg">⚙️</span>
                            <span>{{ __('ui.settings') }}</span>
                        </a>
                        <a href="{{ route('feedbacks.index') }}" class="sidebar-link {{ request()->routeIs('feedbacks.index') ? 'active' : '' }}">
                            <span class="text-lg">💬</span>
                            <span>{{ __('ui.feedbacks') }}</span>
                        </a>
                    </div>
                </div>
                @endif

                <!-- Help -->
                <div>
                    <p class="sidebar-group-title">Bantuan</p>
                    <div class="space-y-1">
                        <a href="{{ route('help.guide') }}" class="sidebar-link {{ request()->routeIs('help.guide') ? 'active' : '' }}">
                            <span class="text-lg">📖</span>
                            <span>{{ __('ui.guide') }}</span>
                        </a>
                        <a href="{{ route('notifications.index') }}" class="sidebar-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                            <span class="text-lg">🔔</span>
                            <span>{{ __('ui.notifications') }}</span>
                            <span id="notif-badge-sidebar" class="hidden ml-auto bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center"></span>
                        </a>
                        <a href="{{ route('help.faq') }}" class="sidebar-link {{ request()->routeIs('help.faq') ? 'active' : '' }}">
                            <span class="text-lg">❓</span>
                            <span>FAQ</span>
                        </a>
                        <a href="{{ route('about') }}" class="sidebar-link {{ request()->routeIs('about') ? 'active' : '' }}">
                            <span class="text-lg">ℹ️</span>
                            <span>{{ __('ui.about') }}</span>
                        </a>
                        <div class="flex items-center gap-1 px-3 py-1.5">
                            <span class="text-lg">🌐</span>
                            <form action="{{ route('language.switch', 'id') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-xs px-2 py-1 rounded {{ app()->getLocale() === 'id' ? 'bg-green-100 text-green-800 font-bold' : 'text-gray-500 hover:text-gray-700' }}">ID</button>
                            </form>
                            <form action="{{ route('language.switch', 'en') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-xs px-2 py-1 rounded {{ app()->getLocale() === 'en' ? 'bg-blue-100 text-blue-800 font-bold' : 'text-gray-500 hover:text-gray-700' }}">EN</button>
                            </form>
                        </div>
                    </div>
                </div>
                </nav>
            </div>
            
            <!-- User Info (Floating at bottom) -->
            <div class="absolute bottom-0 left-0 right-0 p-3 border-t border-gray-200 bg-white shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
                <div class="flex items-center gap-3 px-2">
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 flex-1 min-w-0 hover:opacity-80" title="Edit Profil">
                        <div class="w-9 h-9 rounded-full bg-green-100 flex items-center justify-center text-green-700 font-semibold">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-700 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ auth()->user()->role->display_name }}</p>
                        </div>
                    </a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="p-2 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50" title="Logout">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Sidebar Overlay (Mobile) -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden lg:hidden" onclick="toggleSidebar()"></div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-h-screen lg:ml-0">
            <!-- Top Bar (Mobile) -->
            <header class="lg:hidden bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between sticky top-0 z-20 print:hidden">
                <button onclick="toggleSidebar()" class="p-2 rounded-lg hover:bg-gray-100 touch-target">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                    @if(\App\Models\Setting::hasLogo())
                        <img src="{{ \App\Models\Setting::logoUrl() }}" alt="{{ \App\Models\Setting::appName() }}" class="h-8 w-8 object-contain">
                    @else
                        <span class="text-2xl">🕌</span>
                    @endif
                    <span class="font-bold text-gray-800">{{ Str::limit(\App\Models\Setting::appName(), 20) }}</span>
                </a>
                <div class="flex items-center gap-2">
                    <a href="{{ route('notifications.index') }}" class="p-2 rounded-lg hover:bg-gray-100 relative" title="Notifikasi">
                        <span class="text-lg">🔔</span>
                        <span id="notif-badge-mobile" class="hidden absolute -top-0.5 -right-0.5 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center"></span>
                    </a>
                    <div class="w-2"></div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-4 md:p-6 lg:p-8">
                <div class="max-w-7xl mx-auto">
                    @auth
                        @if(auth()->user()->isSuperAdmin() && session('current_masjid_id'))
                            @php $currentMasjid = \App\Models\Masjid::find(session('current_masjid_id')); @endphp
                            @if($currentMasjid)
                            <div class="bg-indigo-50 border border-indigo-200 text-indigo-800 px-4 py-2 rounded-lg mb-4 flex items-center justify-between text-sm">
                                <span>🕌 Melihat data <strong>{{ $currentMasjid->name }}</strong></span>
                                <form action="{{ route('masjids.switch') }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="masjid_id" value="">
                                    <button type="submit" class="text-indigo-600 hover:text-indigo-800 underline text-xs">Kembali ke Global View</button>
                                </form>
                            </div>
                            @endif
                        @endif
                    @endauth
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4 text-sm md:text-base">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm md:text-base" id="error-alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-lg mb-4 text-sm md:text-base">
                            {{ session('warning') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm md:text-base" id="validation-errors">
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>

            <!-- Footer -->
            <footer class="py-4 text-center text-gray-400 text-sm print:hidden">
                <a href="{{ route('feedbacks.create') }}" class="hover:text-green-600">💬 Kirim Feedback</a>
            </footer>
        </div>
    </div>
    @else
    <!-- Guest Layout (Login Page) -->
    <main class="min-h-screen flex items-center justify-center p-4">
        @yield('content')
    </main>
    @endauth

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        // Close sidebar when clicking a link (mobile)
        document.querySelectorAll('#sidebar a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 1024) {
                    toggleSidebar();
                }
            });
        });

        // Auto scroll to errors
        const errorAlert = document.getElementById('error-alert') || document.getElementById('validation-errors');
        if (errorAlert) {
            errorAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        // Form draft save to localStorage
        document.querySelectorAll('form[data-draft]').forEach(form => {
            const key = 'draft_' + form.dataset.draft;
            
            const draft = localStorage.getItem(key);
            if (draft) {
                const data = JSON.parse(draft);
                Object.keys(data).forEach(name => {
                    const input = form.querySelector(`[name="${name}"]`);
                    if (input && input.type !== 'hidden' && input.type !== 'file') {
                        input.value = data[name];
                    }
                });
            }

            form.addEventListener('input', () => {
                const data = {};
                new FormData(form).forEach((value, name) => {
                    if (name !== '_token') data[name] = value;
                });
                localStorage.setItem(key, JSON.stringify(data));
            });

            form.addEventListener('submit', () => {
                localStorage.removeItem(key);
            });
        });
    </script>

    @yield('scripts')

    @auth
    <script>
        function fetchNotifCount() {
            fetch('{{ route("notifications.unreadCount") }}')
                .then(r => r.json())
                .then(data => {
                    const badges = document.querySelectorAll('#notif-badge-mobile, #notif-badge-sidebar');
                    badges.forEach(badge => {
                        if (data.count > 0) {
                            badge.textContent = data.count > 9 ? '9+' : data.count;
                            badge.classList.remove('hidden');
                        } else {
                            badge.classList.add('hidden');
                        }
                    });
                })
                .catch(() => {});
        }
        fetchNotifCount();
        setInterval(fetchNotifCount, 60000);
    </script>
    @endauth
</body>
</html>
