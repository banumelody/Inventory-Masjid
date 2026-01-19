<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', \App\Models\Setting::appName()); ?></title>
    <?php if(\App\Models\Setting::get('app_favicon')): ?>
    <link rel="icon" href="<?php echo e(asset('storage/' . \App\Models\Setting::get('app_favicon'))); ?>" type="image/x-icon">
    <?php endif; ?>
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
    <?php if(auth()->guard()->check()): ?>
    <div class="flex min-h-screen">
        <!-- Sidebar (Desktop) -->
        <aside id="sidebar" class="fixed lg:static inset-y-0 left-0 z-40 w-64 bg-white border-r border-gray-200 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out print:hidden">
            <div class="sidebar-container">
                <!-- Logo -->
                <div class="flex items-center gap-3 px-4 py-4 border-b border-gray-100 flex-shrink-0">
                    <?php if(\App\Models\Setting::hasLogo()): ?>
                        <img src="<?php echo e(\App\Models\Setting::logoUrl()); ?>" alt="<?php echo e(\App\Models\Setting::appName()); ?>" class="h-10 w-10 object-contain">
                    <?php else: ?>
                        <span class="text-3xl">🕌</span>
                    <?php endif; ?>
                    <div class="min-w-0">
                        <h1 class="font-bold text-gray-800 truncate"><?php echo e(\App\Models\Setting::appName()); ?></h1>
                        <?php if(\App\Models\Setting::orgName()): ?>
                            <p class="text-xs text-gray-500 truncate"><?php echo e(\App\Models\Setting::orgName()); ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-3 py-4 space-y-6 overflow-y-auto sidebar-nav">
                <!-- Main Menu -->
                <div>
                    <p class="sidebar-group-title">Menu Utama</p>
                    <div class="space-y-1">
                        <a href="<?php echo e(route('dashboard')); ?>" class="sidebar-link <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
                            <span class="text-lg">📊</span>
                            <span>Dashboard</span>
                        </a>
                        <a href="<?php echo e(route('items.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('items.*') ? 'active' : ''); ?>">
                            <span class="text-lg">📦</span>
                            <span>Inventaris</span>
                        </a>
                        <a href="<?php echo e(route('loans.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('loans.*') && !request()->routeIs('loans.scan-return') ? 'active' : ''); ?>">
                            <span class="text-lg">📤</span>
                            <span>Peminjaman</span>
                        </a>
                        <a href="<?php echo e(route('stock-movements.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('stock-movements.*') ? 'active' : ''); ?>">
                            <span class="text-lg">📊</span>
                            <span>Mutasi Stok</span>
                        </a>
                        <a href="<?php echo e(route('maintenances.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('maintenances.*') ? 'active' : ''); ?>">
                            <span class="text-lg">🔧</span>
                            <span>Maintenance</span>
                        </a>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div>
                    <p class="sidebar-group-title">Aksi Cepat</p>
                    <div class="space-y-1">
                        <a href="<?php echo e(route('qrcode.scan')); ?>" class="sidebar-link <?php echo e(request()->routeIs('qrcode.scan') ? 'active' : ''); ?>">
                            <span class="text-lg">📷</span>
                            <span>Scan QR Barang</span>
                        </a>
                        <?php if(auth()->user()->canManageLoans()): ?>
                        <a href="<?php echo e(route('qrcode.audit-scan')); ?>" class="sidebar-link <?php echo e(request()->routeIs('qrcode.audit-scan') ? 'active' : ''); ?>">
                            <span class="text-lg">📋</span>
                            <span>Audit Scan</span>
                        </a>
                        <a href="<?php echo e(route('loans.scan-return')); ?>" class="sidebar-link <?php echo e(request()->routeIs('loans.scan-return') ? 'active' : ''); ?>">
                            <span class="text-lg">🔄</span>
                            <span>Scan Pengembalian</span>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Master Data -->
                <div>
                    <p class="sidebar-group-title">Master Data</p>
                    <div class="space-y-1">
                        <a href="<?php echo e(route('categories.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('categories.*') ? 'active' : ''); ?>">
                            <span class="text-lg">📁</span>
                            <span>Kategori</span>
                        </a>
                        <a href="<?php echo e(route('locations.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('locations.*') ? 'active' : ''); ?>">
                            <span class="text-lg">📍</span>
                            <span>Lokasi</span>
                        </a>
                    </div>
                </div>

                <!-- Reports -->
                <div>
                    <p class="sidebar-group-title">Laporan</p>
                    <div class="space-y-1">
                        <a href="<?php echo e(route('reports.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('reports.*') ? 'active' : ''); ?>">
                            <span class="text-lg">📋</span>
                            <span>Laporan</span>
                        </a>
                        <a href="<?php echo e(route('export.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('export.*') ? 'active' : ''); ?>">
                            <span class="text-lg">📥</span>
                            <span>Export Data</span>
                        </a>
                    </div>
                </div>

                <!-- Admin Only -->
                <?php if(auth()->user()->isAdmin()): ?>
                <div>
                    <p class="sidebar-group-title">Administrasi</p>
                    <div class="space-y-1">
                        <a href="<?php echo e(route('imports.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('imports.*') ? 'active' : ''); ?>">
                            <span class="text-lg">📤</span>
                            <span>Import Data</span>
                        </a>
                        <a href="<?php echo e(route('users.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('users.*') ? 'active' : ''); ?>">
                            <span class="text-lg">👥</span>
                            <span>Pengguna</span>
                        </a>
                        <a href="<?php echo e(route('backups.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('backups.*') ? 'active' : ''); ?>">
                            <span class="text-lg">💾</span>
                            <span>Backup</span>
                        </a>
                        <a href="<?php echo e(route('activity-logs.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('activity-logs.*') ? 'active' : ''); ?>">
                            <span class="text-lg">📜</span>
                            <span>Activity Log</span>
                        </a>
                        <a href="<?php echo e(route('scan-logs.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('scan-logs.*') ? 'active' : ''); ?>">
                            <span class="text-lg">📷</span>
                            <span>Scan Logs</span>
                        </a>
                        <a href="<?php echo e(route('settings.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('settings.*') ? 'active' : ''); ?>">
                            <span class="text-lg">⚙️</span>
                            <span>Pengaturan</span>
                        </a>
                        <a href="<?php echo e(route('feedbacks.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('feedbacks.index') ? 'active' : ''); ?>">
                            <span class="text-lg">💬</span>
                            <span>Feedback</span>
                        </a>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Help -->
                <div>
                    <p class="sidebar-group-title">Bantuan</p>
                    <div class="space-y-1">
                        <a href="<?php echo e(route('help.guide')); ?>" class="sidebar-link <?php echo e(request()->routeIs('help.guide') ? 'active' : ''); ?>">
                            <span class="text-lg">📖</span>
                            <span>Panduan</span>
                        </a>
                        <a href="<?php echo e(route('help.faq')); ?>" class="sidebar-link <?php echo e(request()->routeIs('help.faq') ? 'active' : ''); ?>">
                            <span class="text-lg">❓</span>
                            <span>FAQ</span>
                        </a>
                        <a href="<?php echo e(route('about')); ?>" class="sidebar-link <?php echo e(request()->routeIs('about') ? 'active' : ''); ?>">
                            <span class="text-lg">ℹ️</span>
                            <span>Tentang</span>
                        </a>
                    </div>
                </div>
                </nav>

                <!-- User Info -->
                <div class="p-3 border-t border-gray-100 bg-white flex-shrink-0">
                    <div class="flex items-center gap-3 px-2">
                        <div class="w-9 h-9 rounded-full bg-green-100 flex items-center justify-center text-green-700 font-semibold">
                            <?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?>

                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-700 truncate"><?php echo e(auth()->user()->name); ?></p>
                            <p class="text-xs text-gray-500"><?php echo e(auth()->user()->role->display_name); ?></p>
                        </div>
                        <form action="<?php echo e(route('logout')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="p-2 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50" title="Logout">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                            </button>
                        </form>
                    </div>
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
                <a href="<?php echo e(route('dashboard')); ?>" class="flex items-center gap-2">
                    <?php if(\App\Models\Setting::hasLogo()): ?>
                        <img src="<?php echo e(\App\Models\Setting::logoUrl()); ?>" alt="<?php echo e(\App\Models\Setting::appName()); ?>" class="h-8 w-8 object-contain">
                    <?php else: ?>
                        <span class="text-2xl">🕌</span>
                    <?php endif; ?>
                    <span class="font-bold text-gray-800"><?php echo e(Str::limit(\App\Models\Setting::appName(), 20)); ?></span>
                </a>
                <div class="w-10"></div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-4 md:p-6 lg:p-8">
                <div class="max-w-7xl mx-auto">
                    <?php if(session('success')): ?>
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4 text-sm md:text-base">
                            <?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?>

                    <?php if(session('error')): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm md:text-base" id="error-alert">
                            <?php echo e(session('error')); ?>

                        </div>
                    <?php endif; ?>

                    <?php if(session('warning')): ?>
                        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-lg mb-4 text-sm md:text-base">
                            <?php echo e(session('warning')); ?>

                        </div>
                    <?php endif; ?>

                    <?php if($errors->any()): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm md:text-base" id="validation-errors">
                            <ul class="list-disc list-inside">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php echo $__env->yieldContent('content'); ?>
                </div>
            </main>

            <!-- Footer -->
            <footer class="py-4 text-center text-gray-400 text-sm print:hidden">
                <a href="<?php echo e(route('feedbacks.create')); ?>" class="hover:text-green-600">💬 Kirim Feedback</a>
            </footer>
        </div>
    </div>
    <?php else: ?>
    <!-- Guest Layout (Login Page) -->
    <main class="min-h-screen flex items-center justify-center p-4">
        <?php echo $__env->yieldContent('content'); ?>
    </main>
    <?php endif; ?>

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

    <?php echo $__env->yieldContent('scripts'); ?>
</body>
</html>
<?php /**PATH /var/www/resources/views/layouts/app.blade.php ENDPATH**/ ?>