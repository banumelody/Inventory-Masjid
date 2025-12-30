# 🕌 Inventory Barang Masjid V2

Aplikasi web untuk mencatat inventaris barang masjid dengan fitur lengkap.

## Requirements

- Docker & Docker Compose

## Quick Start

1. Clone/extract project ini

2. Copy environment file:
   ```bash
   cp .env.example .env
   ```

3. Build dan jalankan Docker containers:
   ```bash
   docker-compose up -d --build
   ```

4. Install dependencies & setup database:
   ```bash
   docker-compose exec app composer install
   docker-compose exec app php artisan key:generate
   docker-compose exec app php artisan migrate --force
   docker-compose exec app php artisan db:seed
   docker-compose exec app php artisan storage:link
   ```

5. Akses aplikasi di browser:
   ```
   http://localhost:8085
   ```

6. Login dengan:
   - Email: `admin@masjid.local`
   - Password: `password`

## Konfigurasi Port

Edit file `.env` di root project:

```env
APP_PORT=8085      # Port web (default: 8080)
DB_HOST_PORT=3308  # Port database (default: 3307)
```

## Fitur V2 🚀

### 1. Manajemen Barang
- ✅ CRUD barang dengan foto
- ✅ Filter & pencarian
- ✅ Resize foto otomatis (maks 1200px)

### 2. Peminjaman Barang
- ✅ Catat peminjaman (siapa, kapan, berapa)
- ✅ Tracking jatuh tempo
- ✅ Pengembalian dengan kondisi
- ✅ Warning untuk yang terlambat

### 3. Mutasi Stok
- ✅ Catat masuk/keluar barang
- ✅ Alasan: beli, rusak, hilang, donasi, dll
- ✅ Riwayat mutasi per barang
- ✅ Auto sync dengan stok

### 4. Export Data
- ✅ Export Excel (CSV)
- ✅ Export PDF
- ✅ Filter sebelum export

### 5. User & Role
- ✅ **Admin**: Semua akses
- ✅ **Operator**: CRUD barang & peminjaman
- ✅ **Viewer**: Hanya lihat & export

### 6. Backup Otomatis
- ✅ Backup database harian (02:00)
- ✅ Kompresi .gz
- ✅ Retention 30 hari
- ✅ Download & hapus manual

## Menu Aplikasi

| Menu | Fungsi |
|------|--------|
| Inventaris | Daftar barang + CRUD |
| Peminjaman | Kelola pinjam-meminjam |
| Mutasi | Riwayat perubahan stok |
| Kategori | Master kategori |
| Lokasi | Master lokasi |
| Laporan | Laporan + export |
| Pengguna | Kelola user (Admin) |
| Backup | Kelola backup (Admin) |

## Tech Stack

- Laravel 10
- Blade + Tailwind CSS (CDN)
- MariaDB 10.11
- Docker Compose
- Nginx
- DomPDF

## Scheduler (Backup Otomatis)

Untuk mengaktifkan backup otomatis, tambahkan cron job:

```bash
* * * * * cd /path/to/project/src && php artisan schedule:run >> /dev/null 2>&1
```

Atau di Docker:
```bash
docker-compose exec app php artisan schedule:run
```
