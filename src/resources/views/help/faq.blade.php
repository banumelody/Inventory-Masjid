@extends(auth()->check() ? 'layouts.app' : 'layouts.public')

@section('title', 'FAQ - Inventory Masjid')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">❓ Frequently Asked Questions</h1>
        <p class="text-gray-600 mt-1">Pertanyaan yang sering ditanyakan</p>
    </div>

    <div class="space-y-4">
        <!-- Umum -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-green-700">📋 Umum</h2>
            </div>
            <div class="divide-y divide-gray-100">
                <details class="group">
                    <summary class="px-6 py-4 cursor-pointer hover:bg-gray-50 flex justify-between items-center">
                        <span class="font-medium text-gray-800">Apa itu Inventory Masjid?</span>
                        <span class="text-gray-400 group-open:rotate-180 transition-transform">▼</span>
                    </summary>
                    <div class="px-6 pb-4 text-gray-600">
                        <p>Inventory Masjid adalah aplikasi untuk mencatat dan mengelola inventaris barang-barang milik masjid. Aplikasi ini membantu pengurus masjid untuk:</p>
                        <ul class="list-disc list-inside mt-2 space-y-1">
                            <li>Mencatat semua barang inventaris</li>
                            <li>Melacak peminjaman barang oleh jamaah</li>
                            <li>Mengelola mutasi stok (masuk/keluar)</li>
                            <li>Mencatat maintenance/perbaikan barang</li>
                            <li>Membuat laporan inventaris</li>
                        </ul>
                    </div>
                </details>

                <details class="group">
                    <summary class="px-6 py-4 cursor-pointer hover:bg-gray-50 flex justify-between items-center">
                        <span class="font-medium text-gray-800">Siapa saja yang bisa menggunakan aplikasi ini?</span>
                        <span class="text-gray-400 group-open:rotate-180 transition-transform">▼</span>
                    </summary>
                    <div class="px-6 pb-4 text-gray-600">
                        <p>Aplikasi ini memiliki 3 level pengguna:</p>
                        <ul class="list-disc list-inside mt-2 space-y-1">
                            <li><strong>Administrator</strong> - Akses penuh ke semua fitur, termasuk kelola user</li>
                            <li><strong>Operator</strong> - Bisa mengelola inventaris, peminjaman, dan mutasi stok</li>
                            <li><strong>Viewer</strong> - Hanya bisa melihat data, tidak bisa mengubah</li>
                        </ul>
                    </div>
                </details>

                <details class="group">
                    <summary class="px-6 py-4 cursor-pointer hover:bg-gray-50 flex justify-between items-center">
                        <span class="font-medium text-gray-800">Apakah data saya aman?</span>
                        <span class="text-gray-400 group-open:rotate-180 transition-transform">▼</span>
                    </summary>
                    <div class="px-6 pb-4 text-gray-600">
                        <p>Ya, keamanan data dijaga dengan:</p>
                        <ul class="list-disc list-inside mt-2 space-y-1">
                            <li>Login dengan email dan password terenkripsi</li>
                            <li>Activity log mencatat semua perubahan data</li>
                            <li>Fitur backup database untuk admin</li>
                            <li>Pembatasan akses berdasarkan role</li>
                        </ul>
                    </div>
                </details>
            </div>
        </div>

        <!-- Inventaris -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-green-700">📦 Inventaris</h2>
            </div>
            <div class="divide-y divide-gray-100">
                <details class="group">
                    <summary class="px-6 py-4 cursor-pointer hover:bg-gray-50 flex justify-between items-center">
                        <span class="font-medium text-gray-800">Bagaimana cara menambah barang baru?</span>
                        <span class="text-gray-400 group-open:rotate-180 transition-transform">▼</span>
                    </summary>
                    <div class="px-6 pb-4 text-gray-600">
                        <ol class="list-decimal list-inside space-y-1">
                            <li>Buka menu <strong>Inventaris</strong></li>
                            <li>Klik tombol <strong>+ Tambah Barang</strong></li>
                            <li>Isi data barang (nama, kategori, lokasi, jumlah, dll)</li>
                            <li>Upload foto barang (opsional)</li>
                            <li>Klik <strong>Simpan</strong></li>
                        </ol>
                    </div>
                </details>

                <details class="group">
                    <summary class="px-6 py-4 cursor-pointer hover:bg-gray-50 flex justify-between items-center">
                        <span class="font-medium text-gray-800">Apa fungsi QR Code pada barang?</span>
                        <span class="text-gray-400 group-open:rotate-180 transition-transform">▼</span>
                    </summary>
                    <div class="px-6 pb-4 text-gray-600">
                        <p>QR Code mempermudah akses informasi barang:</p>
                        <ul class="list-disc list-inside mt-2 space-y-1">
                            <li>Cetak dan tempel QR Code pada barang</li>
                            <li>Scan dengan HP untuk langsung melihat detail barang</li>
                            <li>Cocok untuk inventarisasi dan audit</li>
                        </ul>
                        <p class="mt-2">Untuk generate QR Code: buka detail barang → klik <strong>Generate QR</strong></p>
                    </div>
                </details>

                <details class="group">
                    <summary class="px-6 py-4 cursor-pointer hover:bg-gray-50 flex justify-between items-center">
                        <span class="font-medium text-gray-800">Bagaimana cara import data barang dari Excel?</span>
                        <span class="text-gray-400 group-open:rotate-180 transition-transform">▼</span>
                    </summary>
                    <div class="px-6 pb-4 text-gray-600">
                        <ol class="list-decimal list-inside space-y-1">
                            <li>Buka menu <strong>Import Data</strong> (khusus Admin)</li>
                            <li>Download template CSV terlebih dahulu</li>
                            <li>Isi data di Excel sesuai format template</li>
                            <li>Save as CSV</li>
                            <li>Upload file dan mapping kolom</li>
                            <li>Klik <strong>Import</strong></li>
                        </ol>
                    </div>
                </details>
            </div>
        </div>

        <!-- Peminjaman -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-green-700">📤 Peminjaman</h2>
            </div>
            <div class="divide-y divide-gray-100">
                <details class="group">
                    <summary class="px-6 py-4 cursor-pointer hover:bg-gray-50 flex justify-between items-center">
                        <span class="font-medium text-gray-800">Bagaimana cara mencatat peminjaman?</span>
                        <span class="text-gray-400 group-open:rotate-180 transition-transform">▼</span>
                    </summary>
                    <div class="px-6 pb-4 text-gray-600">
                        <ol class="list-decimal list-inside space-y-1">
                            <li>Buka menu <strong>Peminjaman</strong></li>
                            <li>Klik tombol <strong>+ Pinjam Barang</strong></li>
                            <li>Pilih barang yang dipinjam</li>
                            <li>Isi nama peminjam dan nomor telepon</li>
                            <li>Tentukan jumlah dan tanggal jatuh tempo</li>
                            <li>Klik <strong>Simpan</strong></li>
                        </ol>
                    </div>
                </details>

                <details class="group">
                    <summary class="px-6 py-4 cursor-pointer hover:bg-gray-50 flex justify-between items-center">
                        <span class="font-medium text-gray-800">Bagaimana cara mencatat pengembalian?</span>
                        <span class="text-gray-400 group-open:rotate-180 transition-transform">▼</span>
                    </summary>
                    <div class="px-6 pb-4 text-gray-600">
                        <p><strong>Cara 1: Manual</strong></p>
                        <ol class="list-decimal list-inside space-y-1">
                            <li>Buka menu <strong>Peminjaman</strong></li>
                            <li>Cari peminjaman yang akan dikembalikan</li>
                            <li>Klik tombol <strong>✅ Kembalikan</strong></li>
                            <li>Isi kondisi barang saat dikembalikan</li>
                        </ol>
                        <p class="mt-3"><strong>Cara 2: Scan QR (lebih cepat)</strong></p>
                        <ol class="list-decimal list-inside space-y-1">
                            <li>Buka menu <strong>Scan Pengembalian</strong></li>
                            <li>Scan QR Code slip peminjaman</li>
                            <li>Klik <strong>Kembalikan Sekarang</strong></li>
                        </ol>
                    </div>
                </details>

                <details class="group">
                    <summary class="px-6 py-4 cursor-pointer hover:bg-gray-50 flex justify-between items-center">
                        <span class="font-medium text-gray-800">Apa itu QR Code peminjaman?</span>
                        <span class="text-gray-400 group-open:rotate-180 transition-transform">▼</span>
                    </summary>
                    <div class="px-6 pb-4 text-gray-600">
                        <p>Setiap peminjaman memiliki QR Code unik untuk mempercepat proses pengembalian:</p>
                        <ul class="list-disc list-inside mt-2 space-y-1">
                            <li>Cetak QR dan berikan ke peminjam</li>
                            <li>Saat mengembalikan, cukup scan QR tersebut</li>
                            <li>Tidak perlu cari manual di daftar peminjaman</li>
                        </ul>
                    </div>
                </details>
            </div>
        </div>

        <!-- Maintenance -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-green-700">🔧 Maintenance</h2>
            </div>
            <div class="divide-y divide-gray-100">
                <details class="group">
                    <summary class="px-6 py-4 cursor-pointer hover:bg-gray-50 flex justify-between items-center">
                        <span class="font-medium text-gray-800">Kapan menggunakan fitur Maintenance?</span>
                        <span class="text-gray-400 group-open:rotate-180 transition-transform">▼</span>
                    </summary>
                    <div class="px-6 pb-4 text-gray-600">
                        <p>Gunakan fitur Maintenance untuk mencatat:</p>
                        <ul class="list-disc list-inside mt-2 space-y-1">
                            <li><strong>Perbaikan</strong> - Barang rusak yang diperbaiki</li>
                            <li><strong>Perawatan</strong> - Perawatan berkala (service AC, dll)</li>
                            <li><strong>Penggantian Part</strong> - Ganti komponen/suku cadang</li>
                        </ul>
                        <p class="mt-2">Fitur ini membantu tracking biaya maintenance dan riwayat perbaikan.</p>
                    </div>
                </details>

                <details class="group">
                    <summary class="px-6 py-4 cursor-pointer hover:bg-gray-50 flex justify-between items-center">
                        <span class="font-medium text-gray-800">Bagaimana alur status maintenance?</span>
                        <span class="text-gray-400 group-open:rotate-180 transition-transform">▼</span>
                    </summary>
                    <div class="px-6 pb-4 text-gray-600">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-sm">Menunggu</span>
                            <span>→</span>
                            <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-sm">Dalam Proses</span>
                            <span>→</span>
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-sm">Selesai</span>
                        </div>
                        <p class="mt-3">Atau bisa dibatalkan kapan saja menjadi status <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-sm">Dibatalkan</span></p>
                    </div>
                </details>
            </div>
        </div>

        <!-- Laporan -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-green-700">📊 Laporan</h2>
            </div>
            <div class="divide-y divide-gray-100">
                <details class="group">
                    <summary class="px-6 py-4 cursor-pointer hover:bg-gray-50 flex justify-between items-center">
                        <span class="font-medium text-gray-800">Laporan apa saja yang tersedia?</span>
                        <span class="text-gray-400 group-open:rotate-180 transition-transform">▼</span>
                    </summary>
                    <div class="px-6 pb-4 text-gray-600">
                        <ul class="list-disc list-inside space-y-1">
                            <li><strong>Laporan Inventaris</strong> - Daftar semua barang dengan filter</li>
                            <li><strong>Laporan Peminjaman</strong> - Riwayat peminjaman</li>
                            <li><strong>Laporan Mutasi Stok</strong> - Keluar masuk barang</li>
                            <li><strong>Dashboard Analytics</strong> - Grafik dan statistik</li>
                        </ul>
                    </div>
                </details>

                <details class="group">
                    <summary class="px-6 py-4 cursor-pointer hover:bg-gray-50 flex justify-between items-center">
                        <span class="font-medium text-gray-800">Bagaimana cara export data?</span>
                        <span class="text-gray-400 group-open:rotate-180 transition-transform">▼</span>
                    </summary>
                    <div class="px-6 pb-4 text-gray-600">
                        <ol class="list-decimal list-inside space-y-1">
                            <li>Buka menu <strong>Export Data</strong></li>
                            <li>Pilih filter (kategori, lokasi, kondisi)</li>
                            <li>Pilih format: <strong>Excel</strong> atau <strong>PDF</strong></li>
                            <li>Klik tombol export</li>
                        </ol>
                    </div>
                </details>
            </div>
        </div>

        <!-- Akun & Keamanan -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-green-700">🔐 Akun & Keamanan</h2>
            </div>
            <div class="divide-y divide-gray-100">
                <details class="group">
                    <summary class="px-6 py-4 cursor-pointer hover:bg-gray-50 flex justify-between items-center">
                        <span class="font-medium text-gray-800">Saya lupa password, bagaimana cara reset?</span>
                        <span class="text-gray-400 group-open:rotate-180 transition-transform">▼</span>
                    </summary>
                    <div class="px-6 pb-4 text-gray-600">
                        <ol class="list-decimal list-inside space-y-1">
                            <li>Di halaman login, klik <strong>Lupa password?</strong></li>
                            <li>Masukkan email Anda</li>
                            <li>Cek inbox email untuk link reset</li>
                            <li>Klik link dan buat password baru</li>
                        </ol>
                        <p class="mt-2 text-sm text-gray-500">Link reset berlaku selama 60 menit.</p>
                    </div>
                </details>

                <details class="group">
                    <summary class="px-6 py-4 cursor-pointer hover:bg-gray-50 flex justify-between items-center">
                        <span class="font-medium text-gray-800">Bagaimana cara menambah user baru?</span>
                        <span class="text-gray-400 group-open:rotate-180 transition-transform">▼</span>
                    </summary>
                    <div class="px-6 pb-4 text-gray-600">
                        <p>Hanya <strong>Administrator</strong> yang bisa menambah user:</p>
                        <ol class="list-decimal list-inside space-y-1 mt-2">
                            <li>Buka menu <strong>Pengguna</strong></li>
                            <li>Klik <strong>+ Tambah User</strong></li>
                            <li>Isi nama, email, password</li>
                            <li>Pilih role (Admin/Operator/Viewer)</li>
                            <li>Klik <strong>Simpan</strong></li>
                        </ol>
                    </div>
                </details>
            </div>
        </div>
    </div>

    <!-- Help Links -->
    <div class="mt-8 bg-green-50 rounded-lg p-6">
        <h3 class="font-semibold text-green-800 mb-3">Butuh bantuan lebih lanjut?</h3>
        <div class="flex flex-wrap gap-4">
            <a href="{{ route('help.guide') }}" class="inline-flex items-center gap-2 text-green-700 hover:text-green-900">
                📖 Baca Panduan Lengkap
            </a>
            @auth
            <a href="{{ route('feedbacks.create') }}" class="inline-flex items-center gap-2 text-green-700 hover:text-green-900">
                💬 Kirim Feedback
            </a>
            @else
            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-green-700 hover:text-green-900">
                🔐 Login untuk Kirim Feedback
            </a>
            @endauth
        </div>
    </div>
</div>
@endsection
