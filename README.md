# 🕌 Inventory Barang Masjid V5

Aplikasi web untuk mencatat inventaris barang masjid dengan fitur lengkap, **tampilan responsif**, dan **QR Code labeling**.

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

## Fitur Lengkap 🚀

### MVP - Manajemen Barang
- ✅ CRUD barang dengan foto
- ✅ Filter & pencarian
- ✅ Master kategori & lokasi
- ✅ Laporan print-friendly

### V2 - Extended Features
- ✅ Peminjaman barang dengan tracking
- ✅ Mutasi stok dengan history
- ✅ Export Excel (CSV) & PDF
- ✅ User & Role management
- ✅ Backup otomatis harian

### V3 - Stability & Usability
- ✅ Dashboard overview
- ✅ Warning peminjaman terlambat
- ✅ Feedback system
- ✅ Technical documentation
- ✅ Unit & Feature tests

### V4 - Responsive UI 📱
- ✅ **Responsive layout** - Desktop, Tablet, Mobile
- ✅ **Hamburger menu** untuk layar kecil
- ✅ **Card view** di mobile (tabel di desktop)
- ✅ **Tombol besar** (touch-friendly)
- ✅ **Simpan & Tambah Lagi** untuk input cepat
- ✅ **Form draft** (localStorage)
- ✅ **Lazy load gambar**
- ✅ **Pagination 10 item** default
- ✅ **Auto scroll ke error**

### V5 - QR Code Label & Scan 🏷️
- ✅ **Generate QR** untuk setiap barang
- ✅ **Cetak label** ukuran kecil/sedang
- ✅ **Cetak massal** multiple barang
- ✅ **Scan QR** dengan kamera HP
- ✅ **Audit Scan** dengan tujuan & catatan
- ✅ **Scan Logs** riwayat scan (admin)
- ✅ **Export scan logs** ke CSV
- ✅ **Manual input** fallback jika kamera gagal

## Menu Aplikasi

| Menu | Fungsi |
|------|--------|
| 📊 Dashboard | Overview & statistik |
| Inventaris | Daftar barang + CRUD |
| Pinjam | Kelola peminjaman |
| Mutasi | Riwayat perubahan stok |
| Kategori | Master kategori |
| Lokasi | Master lokasi |
| Laporan | Laporan + export |
| 📷 Scan QR | Scan QR barang |
| 📋 Audit Scan | Scan untuk audit/pengecekan |
| User | Kelola pengguna (Admin) |
| Backup | Kelola backup (Admin) |
| Scan Logs | Riwayat scan QR (Admin) |
| 💬 Feedback | Kelola feedback (Admin) |

## Role & Permission

| Permission | Admin | Operator | Viewer |
|------------|:-----:|:--------:|:------:|
| View items | ✅ | ✅ | ✅ |
| Create/Edit items | ✅ | ✅ | ❌ |
| Delete items | ✅ | ❌ | ❌ |
| Manage loans | ✅ | ✅ | ❌ |
| Manage stock | ✅ | ✅ | ❌ |
| Export data | ✅ | ✅ | ✅ |
| Manage users | ✅ | ❌ | ❌ |
| Manage backups | ✅ | ❌ | ❌ |
| Scan QR | ✅ | ✅ | ✅ |
| Audit scan | ✅ | ✅ | ❌ |
| Generate/Print QR | ✅ | ✅ | ❌ |
| View scan logs | ✅ | ❌ | ❌ |

## Tech Stack

- Laravel 10
- Blade + Tailwind CSS (CDN)
- MariaDB 10.11
- Docker Compose
- Nginx
- DomPDF

## Testing

```bash
# Run all tests
docker-compose exec app php artisan test

# Run unit tests only
docker-compose exec app php artisan test --filter=Unit

# Run feature tests only
docker-compose exec app php artisan test --filter=Feature
```

## Documentation

Technical documentation tersedia di folder `/docs`:
- [Overview](docs/README.md)
- [Loans Module](docs/module-loans.md)
- [Stock Movements Module](docs/module-stock-movements.md)
- [Users Module](docs/module-users.md)
- [Backup Module](docs/module-backup.md)
- [Export Module](docs/module-export.md)
- [QR Code Module](docs/module-qrcode.md) ✨ **V5**

## Scheduler (Backup Otomatis)

Untuk mengaktifkan backup otomatis, tambahkan cron job:

```bash
* * * * * cd /path/to/project/src && php artisan schedule:run >> /dev/null 2>&1
```
