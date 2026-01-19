@extends(auth()->check() ? 'layouts.app' : 'layouts.public')

@section('title', 'Panduan Pengguna - Inventory Masjid')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">📖 Panduan Pengguna</h1>
        <p class="text-gray-600 mt-1">Dokumentasi lengkap cara menggunakan aplikasi</p>
    </div>

    <!-- Table of Contents -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="font-semibold text-gray-800 mb-4">📑 Daftar Isi</h2>
        <div class="grid md:grid-cols-2 gap-2">
            <a href="#getting-started" class="text-green-600 hover:text-green-800 py-1">1. Memulai</a>
            <a href="#inventory" class="text-green-600 hover:text-green-800 py-1">2. Kelola Inventaris</a>
            <a href="#loans" class="text-green-600 hover:text-green-800 py-1">3. Peminjaman Barang</a>
            <a href="#stock" class="text-green-600 hover:text-green-800 py-1">4. Mutasi Stok</a>
            <a href="#maintenance" class="text-green-600 hover:text-green-800 py-1">5. Maintenance</a>
            <a href="#qrcode" class="text-green-600 hover:text-green-800 py-1">6. QR Code</a>
            <a href="#reports" class="text-green-600 hover:text-green-800 py-1">7. Laporan & Export</a>
            <a href="#admin" class="text-green-600 hover:text-green-800 py-1">8. Administrasi</a>
        </div>
    </div>

    <!-- Content -->
    <div class="space-y-8">
        <!-- 1. Getting Started -->
        <section id="getting-started" class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-green-700 mb-4">1. 🚀 Memulai</h2>
            
            <h3 class="font-semibold text-gray-800 mt-4 mb-2">Login ke Aplikasi</h3>
            <ol class="list-decimal list-inside space-y-2 text-gray-600">
                <li>Buka aplikasi di browser</li>
                <li>Masukkan email dan password yang diberikan admin</li>
                <li>Centang "Ingat saya" jika menggunakan perangkat pribadi</li>
                <li>Klik tombol <strong>Login</strong></li>
            </ol>

            <h3 class="font-semibold text-gray-800 mt-6 mb-2">Mengenal Dashboard</h3>
            <p class="text-gray-600 mb-3">Setelah login, Anda akan melihat Dashboard yang menampilkan:</p>
            <ul class="list-disc list-inside space-y-1 text-gray-600">
                <li><strong>Ringkasan Inventaris</strong> - Total barang, kategori, nilai aset</li>
                <li><strong>Peminjaman Aktif</strong> - Barang yang sedang dipinjam</li>
                <li><strong>Grafik Tren</strong> - Statistik peminjaman per bulan</li>
                <li><strong>Barang Populer</strong> - Barang paling sering dipinjam</li>
                <li><strong>Kondisi Barang</strong> - Distribusi kondisi (baik/rusak/perlu perbaikan)</li>
            </ul>

            <h3 class="font-semibold text-gray-800 mt-6 mb-2">Navigasi Aplikasi</h3>
            <p class="text-gray-600 mb-3">Menu navigasi ada di sidebar kiri (desktop) atau hamburger menu (mobile):</p>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2 pr-4">Menu</th>
                            <th class="text-left py-2">Fungsi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600">
                        <tr class="border-b"><td class="py-2 pr-4">📊 Dashboard</td><td>Ringkasan dan statistik</td></tr>
                        <tr class="border-b"><td class="py-2 pr-4">📦 Inventaris</td><td>Kelola daftar barang</td></tr>
                        <tr class="border-b"><td class="py-2 pr-4">📤 Peminjaman</td><td>Catat pinjam/kembali</td></tr>
                        <tr class="border-b"><td class="py-2 pr-4">📊 Mutasi Stok</td><td>Barang masuk/keluar</td></tr>
                        <tr class="border-b"><td class="py-2 pr-4">🔧 Maintenance</td><td>Perbaikan barang</td></tr>
                        <tr class="border-b"><td class="py-2 pr-4">📷 Scan QR</td><td>Scan QR Code barang</td></tr>
                        <tr class="border-b"><td class="py-2 pr-4">📋 Laporan</td><td>Lihat dan cetak laporan</td></tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- 2. Inventory -->
        <section id="inventory" class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-green-700 mb-4">2. 📦 Kelola Inventaris</h2>
            
            <h3 class="font-semibold text-gray-800 mt-4 mb-2">Menambah Barang Baru</h3>
            <ol class="list-decimal list-inside space-y-2 text-gray-600">
                <li>Klik menu <strong>Inventaris</strong></li>
                <li>Klik tombol <strong>+ Tambah Barang</strong></li>
                <li>Isi formulir:
                    <ul class="list-disc list-inside ml-6 mt-1">
                        <li><strong>Nama Barang</strong> - Nama yang mudah dikenali</li>
                        <li><strong>Kategori</strong> - Pilih atau tambah kategori baru</li>
                        <li><strong>Lokasi</strong> - Dimana barang disimpan</li>
                        <li><strong>Jumlah</strong> - Stok awal</li>
                        <li><strong>Satuan</strong> - pcs, unit, set, dll</li>
                        <li><strong>Kondisi</strong> - Baik/Perlu Perbaikan/Rusak</li>
                        <li><strong>Foto</strong> - Opsional, untuk identifikasi</li>
                    </ul>
                </li>
                <li>Klik <strong>Simpan</strong> atau <strong>Simpan & Tambah Lagi</strong></li>
            </ol>

            <h3 class="font-semibold text-gray-800 mt-6 mb-2">Mengedit Barang</h3>
            <ol class="list-decimal list-inside space-y-2 text-gray-600">
                <li>Di halaman Inventaris, cari barang yang ingin diedit</li>
                <li>Klik tombol <strong>✏️ Edit</strong></li>
                <li>Ubah data yang diperlukan</li>
                <li>Klik <strong>Update</strong></li>
            </ol>

            <h3 class="font-semibold text-gray-800 mt-6 mb-2">Melihat Detail Barang</h3>
            <p class="text-gray-600">Klik nama barang untuk melihat informasi lengkap termasuk:</p>
            <ul class="list-disc list-inside space-y-1 text-gray-600 mt-2">
                <li>Informasi dasar barang</li>
                <li>Foto barang (jika ada)</li>
                <li>Stok tersedia vs sedang dipinjam</li>
                <li>Riwayat mutasi stok</li>
                <li>QR Code untuk scan</li>
            </ul>

            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-4">
                <p class="text-yellow-800 text-sm">
                    <strong>💡 Tips:</strong> Gunakan fitur filter dan pencarian untuk menemukan barang dengan cepat.
                </p>
            </div>
        </section>

        <!-- 3. Loans -->
        <section id="loans" class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-green-700 mb-4">3. 📤 Peminjaman Barang</h2>
            
            <h3 class="font-semibold text-gray-800 mt-4 mb-2">Mencatat Peminjaman</h3>
            <ol class="list-decimal list-inside space-y-2 text-gray-600">
                <li>Buka menu <strong>Peminjaman</strong></li>
                <li>Klik <strong>+ Pinjam Barang</strong></li>
                <li>Pilih barang dari daftar</li>
                <li>Isi informasi peminjam:
                    <ul class="list-disc list-inside ml-6 mt-1">
                        <li>Nama peminjam</li>
                        <li>Nomor telepon (untuk follow-up)</li>
                        <li>Jumlah yang dipinjam</li>
                        <li>Tanggal pinjam</li>
                        <li>Tanggal jatuh tempo (opsional)</li>
                    </ul>
                </li>
                <li>Klik <strong>Simpan</strong></li>
                <li>Cetak QR Code untuk slip peminjaman</li>
            </ol>

            <h3 class="font-semibold text-gray-800 mt-6 mb-2">Mencatat Pengembalian</h3>
            <p class="text-gray-600 mb-2"><strong>Metode 1: Scan QR (Direkomendasikan)</strong></p>
            <ol class="list-decimal list-inside space-y-1 text-gray-600 mb-4">
                <li>Buka menu <strong>🔄 Scan Pengembalian</strong></li>
                <li>Arahkan kamera ke QR Code slip peminjaman</li>
                <li>Pilih <strong>Kembalikan Sekarang</strong> (kondisi baik) atau <strong>Isi Detail</strong></li>
            </ol>
            
            <p class="text-gray-600 mb-2"><strong>Metode 2: Manual</strong></p>
            <ol class="list-decimal list-inside space-y-1 text-gray-600">
                <li>Di halaman Peminjaman, cari peminjaman aktif</li>
                <li>Klik tombol <strong>✅ Kembalikan</strong></li>
                <li>Isi tanggal pengembalian dan kondisi barang</li>
                <li>Klik <strong>Simpan</strong></li>
            </ol>

            <h3 class="font-semibold text-gray-800 mt-6 mb-2">Status Peminjaman</h3>
            <div class="flex flex-wrap gap-2 mt-2">
                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm">🟡 Dipinjam</span>
                <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm">🔴 Terlambat</span>
                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm">🟢 Dikembalikan</span>
            </div>
        </section>

        <!-- 4. Stock Movements -->
        <section id="stock" class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-green-700 mb-4">4. 📊 Mutasi Stok</h2>
            
            <p class="text-gray-600 mb-4">Mutasi stok digunakan untuk mencatat perubahan jumlah barang selain dari peminjaman:</p>

            <h3 class="font-semibold text-gray-800 mt-4 mb-2">Jenis Mutasi</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2 pr-4">Jenis</th>
                            <th class="text-left py-2">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600">
                        <tr class="border-b"><td class="py-2 pr-4 text-green-600 font-medium">Masuk</td><td>Barang baru, donasi, pembelian</td></tr>
                        <tr class="border-b"><td class="py-2 pr-4 text-red-600 font-medium">Keluar</td><td>Barang rusak, hilang, didonasikan</td></tr>
                        <tr class="border-b"><td class="py-2 pr-4 text-blue-600 font-medium">Adjustment</td><td>Koreksi hasil stock opname</td></tr>
                    </tbody>
                </table>
            </div>

            <h3 class="font-semibold text-gray-800 mt-6 mb-2">Mencatat Mutasi</h3>
            <ol class="list-decimal list-inside space-y-2 text-gray-600">
                <li>Buka menu <strong>Mutasi Stok</strong></li>
                <li>Klik <strong>+ Tambah Mutasi</strong></li>
                <li>Pilih barang dan jenis mutasi</li>
                <li>Masukkan jumlah dan keterangan</li>
                <li>Klik <strong>Simpan</strong></li>
            </ol>
        </section>

        <!-- 5. Maintenance -->
        <section id="maintenance" class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-green-700 mb-4">5. 🔧 Maintenance</h2>
            
            <p class="text-gray-600 mb-4">Fitur maintenance untuk mencatat perbaikan dan perawatan barang:</p>

            <h3 class="font-semibold text-gray-800 mt-4 mb-2">Menambah Data Maintenance</h3>
            <ol class="list-decimal list-inside space-y-2 text-gray-600">
                <li>Buka menu <strong>Maintenance</strong></li>
                <li>Klik <strong>+ Tambah Maintenance</strong></li>
                <li>Pilih barang yang di-maintenance</li>
                <li>Pilih tipe: Perbaikan / Perawatan / Penggantian Part</li>
                <li>Isi deskripsi kerusakan/pekerjaan</li>
                <li>Isi info vendor (opsional)</li>
                <li>Masukkan estimasi biaya</li>
                <li>Klik <strong>Simpan</strong></li>
            </ol>

            <h3 class="font-semibold text-gray-800 mt-6 mb-2">Alur Status Maintenance</h3>
            <div class="flex items-center gap-2 flex-wrap text-sm mt-2">
                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded">Menunggu</span>
                <span class="text-gray-400">→</span>
                <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded">Dalam Proses</span>
                <span class="text-gray-400">→</span>
                <span class="px-3 py-1 bg-green-100 text-green-700 rounded">Selesai</span>
            </div>
            <p class="text-gray-500 text-sm mt-2">Klik tombol status di halaman detail untuk update status dengan cepat.</p>
        </section>

        <!-- 6. QR Code -->
        <section id="qrcode" class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-green-700 mb-4">6. 📷 QR Code</h2>
            
            <h3 class="font-semibold text-gray-800 mt-4 mb-2">QR Code Barang</h3>
            <p class="text-gray-600 mb-2">Setiap barang bisa memiliki QR Code untuk identifikasi cepat:</p>
            <ol class="list-decimal list-inside space-y-1 text-gray-600">
                <li>Buka detail barang</li>
                <li>Klik <strong>Generate QR</strong></li>
                <li>Cetak label dan tempel pada barang</li>
                <li>Scan kapan saja untuk lihat info barang</li>
            </ol>

            <h3 class="font-semibold text-gray-800 mt-6 mb-2">QR Code Peminjaman</h3>
            <p class="text-gray-600 mb-2">QR Code peminjaman untuk proses pengembalian cepat:</p>
            <ol class="list-decimal list-inside space-y-1 text-gray-600">
                <li>Setelah mencatat peminjaman, klik <strong>🏷️ Cetak QR</strong></li>
                <li>Berikan slip QR kepada peminjam</li>
                <li>Saat pengembalian, scan QR tersebut</li>
            </ol>

            <h3 class="font-semibold text-gray-800 mt-6 mb-2">Cetak QR Massal</h3>
            <p class="text-gray-600">Untuk mencetak banyak label QR sekaligus:</p>
            <ol class="list-decimal list-inside space-y-1 text-gray-600 mt-2">
                <li>Buka halaman <strong>Inventaris</strong></li>
                <li>Klik <strong>Cetak QR Massal</strong></li>
                <li>Pilih barang-barang yang akan dicetak</li>
                <li>Pilih ukuran label</li>
                <li>Klik <strong>Cetak</strong></li>
            </ol>
        </section>

        <!-- 7. Reports -->
        <section id="reports" class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-green-700 mb-4">7. 📋 Laporan & Export</h2>
            
            <h3 class="font-semibold text-gray-800 mt-4 mb-2">Jenis Laporan</h3>
            <ul class="list-disc list-inside space-y-1 text-gray-600">
                <li><strong>Laporan Inventaris</strong> - Daftar barang dengan filter kategori/lokasi/kondisi</li>
                <li><strong>Laporan Peminjaman</strong> - Riwayat peminjaman per periode</li>
                <li><strong>Dashboard Analytics</strong> - Grafik tren dan statistik</li>
            </ul>

            <h3 class="font-semibold text-gray-800 mt-6 mb-2">Export Data</h3>
            <ol class="list-decimal list-inside space-y-2 text-gray-600">
                <li>Buka menu <strong>Export Data</strong></li>
                <li>Pilih filter data yang diinginkan</li>
                <li>Pilih format:
                    <ul class="list-disc list-inside ml-6 mt-1">
                        <li><strong>Excel</strong> - Untuk pengolahan data lebih lanjut</li>
                        <li><strong>PDF</strong> - Untuk laporan formal/cetak</li>
                    </ul>
                </li>
                <li>Klik tombol export</li>
            </ol>

            <h3 class="font-semibold text-gray-800 mt-6 mb-2">Cetak Laporan</h3>
            <p class="text-gray-600">Di halaman Laporan, klik tombol <strong>🖨️ Cetak</strong> untuk membuka preview cetak.</p>
        </section>

        <!-- 8. Admin -->
        <section id="admin" class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-green-700 mb-4">8. ⚙️ Administrasi</h2>
            <p class="text-gray-500 text-sm mb-4">Menu ini hanya tersedia untuk Administrator</p>
            
            <h3 class="font-semibold text-gray-800 mt-4 mb-2">Kelola Pengguna</h3>
            <p class="text-gray-600 mb-2">Tambah, edit, atau nonaktifkan user:</p>
            <ol class="list-decimal list-inside space-y-1 text-gray-600">
                <li>Buka menu <strong>Pengguna</strong></li>
                <li>Klik <strong>+ Tambah User</strong></li>
                <li>Isi nama, email, password</li>
                <li>Pilih role: Admin / Operator / Viewer</li>
                <li>Klik <strong>Simpan</strong></li>
            </ol>

            <h3 class="font-semibold text-gray-800 mt-6 mb-2">Import Data</h3>
            <p class="text-gray-600 mb-2">Import data barang dari file CSV/Excel:</p>
            <ol class="list-decimal list-inside space-y-1 text-gray-600">
                <li>Buka menu <strong>Import Data</strong></li>
                <li>Download template terlebih dahulu</li>
                <li>Isi data di Excel, save as CSV</li>
                <li>Upload dan mapping kolom</li>
                <li>Review dan import</li>
            </ol>

            <h3 class="font-semibold text-gray-800 mt-6 mb-2">Backup Database</h3>
            <p class="text-gray-600 mb-2">Backup data secara berkala:</p>
            <ol class="list-decimal list-inside space-y-1 text-gray-600">
                <li>Buka menu <strong>Backup</strong></li>
                <li>Klik <strong>Buat Backup</strong></li>
                <li>Download file backup untuk disimpan offline</li>
            </ol>

            <h3 class="font-semibold text-gray-800 mt-6 mb-2">Activity Log</h3>
            <p class="text-gray-600">Monitor semua aktivitas di sistem:</p>
            <ul class="list-disc list-inside space-y-1 text-gray-600 mt-2">
                <li>Siapa melakukan apa dan kapan</li>
                <li>Perubahan data (sebelum/sesudah)</li>
                <li>Login/logout user</li>
                <li>Filter berdasarkan user, aksi, tanggal</li>
            </ul>
        </section>
    </div>

    <!-- Help Links -->
    <div class="mt-8 bg-green-50 rounded-lg p-6">
        <h3 class="font-semibold text-green-800 mb-3">Butuh bantuan?</h3>
        <div class="flex flex-wrap gap-4">
            <a href="{{ route('help.faq') }}" class="inline-flex items-center gap-2 text-green-700 hover:text-green-900">
                ❓ Lihat FAQ
            </a>
            @auth
            <a href="{{ route('feedbacks.create') }}" class="inline-flex items-center gap-2 text-green-700 hover:text-green-900">
                💬 Kirim Feedback
            </a>
            @else
            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-green-700 hover:text-green-900">
                🔐 Login untuk Mulai
            </a>
            @endauth
        </div>
    </div>
</div>
@endsection
