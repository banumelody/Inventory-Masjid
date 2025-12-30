# 🕌 Inventory Barang Masjid

Aplikasi web sederhana untuk mencatat inventaris barang masjid.

## Requirements

- Docker & Docker Compose

## Quick Start

1. Clone/extract project ini

2. Copy environment file (jika belum ada):
   ```bash
   cp .env.example .env
   ```

3. Build dan jalankan Docker containers:
   ```bash
   docker-compose up -d --build
   ```

4. Install dependencies & setup database:
   ```bash
   docker-compose exec app composer install --no-scripts
   docker-compose exec app composer dump-autoload --no-scripts
   docker-compose exec app php artisan key:generate
   docker-compose exec app php artisan migrate --force
   docker-compose exec app php artisan db:seed
   ```

5. Akses aplikasi di browser:
   ```
   http://localhost:8085
   ```

## Konfigurasi Port

Edit file `.env` di root project:

```env
APP_PORT=8085      # Port web (default: 8080)
DB_HOST_PORT=3308  # Port database (default: 3307)
```

## Fitur

### Manajemen Barang
- ✅ Tambah, Edit, Hapus barang
- ✅ Field: Nama, Kategori, Lokasi, Jumlah, Satuan, Kondisi, Catatan
- ✅ Filter berdasarkan kategori & lokasi
- ✅ Pencarian berdasarkan nama

### Master Data
- ✅ CRUD Kategori
- ✅ CRUD Lokasi
- ✅ Proteksi hapus jika masih digunakan

### Laporan
- ✅ Laporan inventaris lengkap
- ✅ Filter kategori & lokasi
- ✅ Tampilan print-friendly

## Struktur Database

- **categories**: id, name, timestamps
- **locations**: id, name, timestamps
- **items**: id, name, category_id (FK), location_id (FK), quantity, unit, condition, note, timestamps

## Tech Stack

- Laravel 10
- Blade + Tailwind CSS (CDN)
- MariaDB 10.11
- Docker Compose
- Nginx

## Menu Aplikasi

1. **Inventaris** - Daftar semua barang dengan fitur CRUD
2. **Kategori** - Kelola kategori barang
3. **Lokasi** - Kelola lokasi penyimpanan
4. **Laporan** - Laporan inventaris dengan filter & cetak
