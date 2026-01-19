<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ \App\Models\Setting::appName() }}</title>
    @if(\App\Models\Setting::get('app_favicon'))
    <link rel="icon" href="{{ asset('storage/' . \App\Models\Setting::get('app_favicon')) }}" type="image/x-icon">
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .gradient-bg {
            background: linear-gradient(180deg, #f8fafc 0%, #e2e8f0 100%);
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .float-animation {
            animation: float 3s ease-in-out infinite;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body class="min-h-screen gradient-bg">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="py-4 px-6 bg-white shadow-sm">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    @if(\App\Models\Setting::hasLogo())
                        <img src="{{ \App\Models\Setting::logoUrl() }}" alt="{{ \App\Models\Setting::appName() }}" class="h-10 w-10 object-contain">
                    @else
                        <div class="bg-yellow-400 p-2 rounded-lg">
                            <i class="fas fa-mosque text-emerald-800 text-2xl"></i>
                        </div>
                    @endif
                    <span class="text-emerald-800 font-bold text-xl">{{ \App\Models\Setting::appName() }}</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('help.guide') }}" class="text-emerald-700 hover:text-emerald-900 font-medium hidden sm:inline">
                        <i class="fas fa-book mr-1"></i>Panduan
                    </a>
                    <a href="{{ route('help.faq') }}" class="text-emerald-700 hover:text-emerald-900 font-medium hidden sm:inline">
                        <i class="fas fa-question-circle mr-1"></i>FAQ
                    </a>
                    <a href="{{ route('login') }}" 
                       class="bg-yellow-400 text-emerald-900 px-6 py-2.5 rounded-full font-bold hover:bg-yellow-500 transition-all shadow-md hover:shadow-lg">
                        <i class="fas fa-sign-in-alt mr-2"></i>Masuk
                    </a>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <main class="flex-1 flex items-center justify-center px-6 py-12">
            <div class="max-w-4xl mx-auto text-center">
                <div class="float-animation mb-8">
                    @if(\App\Models\Setting::hasLogo())
                        <div class="inline-block bg-white p-6 rounded-full shadow-2xl">
                            <img src="{{ \App\Models\Setting::logoUrl() }}" alt="{{ \App\Models\Setting::appName() }}" class="h-24 w-24 md:h-32 md:w-32 object-contain">
                        </div>
                    @else
                        <div class="inline-block bg-yellow-400 p-8 rounded-full shadow-2xl">
                            <i class="fas fa-mosque text-7xl md:text-8xl text-emerald-800"></i>
                        </div>
                    @endif
                </div>
                
                <h1 class="text-4xl md:text-6xl font-extrabold mb-6 leading-tight">
                    <span class="text-gray-800">{{ \App\Models\Setting::appName() }}</span>
                </h1>
                
                @if(\App\Models\Setting::orgName())
                <p class="text-2xl md:text-3xl text-yellow-600 font-semibold mb-4">
                    {{ \App\Models\Setting::orgName() }}
                </p>
                @endif
                
                <p class="text-xl md:text-2xl text-gray-600 mb-10 max-w-2xl mx-auto leading-relaxed">
                    @if(\App\Models\Setting::get('about_description'))
                        {{ Str::limit(\App\Models\Setting::get('about_description'), 150) }}
                    @else
                        Kelola inventaris masjid dengan mudah, cepat, dan terorganisir. 
                        Pencatatan barang, peminjaman, dan pelaporan dalam satu sistem.
                    @endif
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center mb-16">
                    <a href="{{ route('login') }}" 
                       class="bg-yellow-400 text-emerald-900 px-10 py-4 rounded-xl font-bold text-lg hover:bg-yellow-500 transition-all shadow-xl hover:shadow-2xl transform hover:-translate-y-1">
                        <i class="fas fa-sign-in-alt mr-2"></i>Masuk ke Sistem
                    </a>
                    <a href="{{ route('help.guide') }}" 
                       class="bg-white text-emerald-800 px-10 py-4 rounded-xl font-bold text-lg hover:bg-gray-50 transition-all shadow-xl hover:shadow-2xl transform hover:-translate-y-1 border-2 border-emerald-200">
                        <i class="fas fa-book-open mr-2"></i>Lihat Panduan
                    </a>
                </div>

                <!-- Features -->
                <div class="grid md:grid-cols-3 gap-6 mt-12">
                    <div class="bg-white rounded-2xl p-6 shadow-xl card-hover transition-all duration-300">
                        <div class="bg-emerald-500 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                            <i class="fas fa-boxes text-3xl text-white"></i>
                        </div>
                        <h3 class="font-bold text-xl mb-2 text-emerald-800">Manajemen Barang</h3>
                        <p class="text-gray-600">Catat dan kelola semua barang inventaris masjid dengan mudah</p>
                    </div>
                    
                    <div class="bg-white rounded-2xl p-6 shadow-xl card-hover transition-all duration-300">
                        <div class="bg-blue-500 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                            <i class="fas fa-hand-holding text-3xl text-white"></i>
                        </div>
                        <h3 class="font-bold text-xl mb-2 text-blue-800">Peminjaman</h3>
                        <p class="text-gray-600">Lacak peminjaman dan pengembalian barang dengan sistem yang teratur</p>
                    </div>
                    
                    <div class="bg-white rounded-2xl p-6 shadow-xl card-hover transition-all duration-300">
                        <div class="bg-purple-500 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                            <i class="fas fa-chart-bar text-3xl text-white"></i>
                        </div>
                        <h3 class="font-bold text-xl mb-2 text-purple-800">Laporan</h3>
                        <p class="text-gray-600">Generate laporan lengkap untuk keperluan dokumentasi</p>
                    </div>
                </div>

                <!-- Additional Info -->
                <div class="mt-16 bg-emerald-700 rounded-2xl p-8 shadow-xl">
                    <h2 class="text-2xl font-bold text-white mb-6">✨ Fitur Unggulan</h2>
                    <div class="grid md:grid-cols-2 gap-4 text-left">
                        <div class="flex items-center space-x-3 bg-emerald-600 rounded-lg p-4">
                            <i class="fas fa-check-circle text-yellow-400 text-xl"></i>
                            <span class="text-white font-medium">Multi-user dengan role berbeda</span>
                        </div>
                        <div class="flex items-center space-x-3 bg-emerald-600 rounded-lg p-4">
                            <i class="fas fa-check-circle text-yellow-400 text-xl"></i>
                            <span class="text-white font-medium">QR Code untuk setiap barang</span>
                        </div>
                        <div class="flex items-center space-x-3 bg-emerald-600 rounded-lg p-4">
                            <i class="fas fa-check-circle text-yellow-400 text-xl"></i>
                            <span class="text-white font-medium">Export ke Excel & PDF</span>
                        </div>
                        <div class="flex items-center space-x-3 bg-emerald-600 rounded-lg p-4">
                            <i class="fas fa-check-circle text-yellow-400 text-xl"></i>
                            <span class="text-white font-medium">Responsive mobile-friendly</span>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="py-6 px-6 text-center bg-emerald-800">
            <p class="text-white">
                @if(\App\Models\Setting::get('footer_text'))
                    {{ \App\Models\Setting::get('footer_text') }} · 
                @endif
                &copy; {{ date('Y') }} {{ \App\Models\Setting::appName() }}.
                @if(\App\Models\Setting::get('show_powered_by', '1') === '1')
                    Dibuat dengan <i class="fas fa-heart text-red-400"></i> menggunakan <a href="https://github.com" target="_blank" class="underline hover:text-yellow-400">Inventory Masjid</a>
                @endif
            </p>
        </footer>
    </div>
</body>
</html>
