# Release Notes v6.0.0 — Multi-Tenant SaaS

**Tanggal Rilis:** 10 April 2026
**Codename:** Multi-Tenant SaaS

## 🎯 Ringkasan

Versi 6.0.0 adalah rilis major yang mentransformasi Inventory Masjid dari aplikasi single-tenant menjadi platform SaaS multi-tenant. Setiap masjid memiliki data yang sepenuhnya terisolasi, sementara superadmin dapat mengelola seluruh platform.

## 🏢 Multi-Tenant Architecture

Semua data (items, loans, categories, locations, stock movements, maintenances, activity logs, scan logs, feedbacks, notifications, settings, backups) kini ter-scope per masjid. Isolasi menggunakan row-level tenancy:

- **MasjidScope** — Global scope otomatis filter data berdasarkan masjid aktif
- **BelongsToMasjid** — Trait yang diterapkan pada 13 model untuk auto-scoping
- **SetMasjidContext** — Middleware yang inject tenant context dari user session
- **EnsureMasjidContext** — Guard middleware untuk route create/write

## 👑 Superadmin Features

- **Platform Dashboard** — Statistik total masjid, users, items, loans
- **Masjid Management** — CRUD, suspend, delete masjid dengan cascade
- **Tenant Switcher** — Operasikan aplikasi sebagai masjid tertentu
- **Global Activity Log** — Audit trail lintas masjid dengan filter
- **User Transfer** — Pindahkan user antar masjid
- **Global Reports** — Laporan cross-tenant

## 🕌 Masjid Self-Registration

Masjid baru dapat mendaftar sendiri melalui halaman registrasi publik. Otomatis membuat admin user, kategori default, dan lokasi default.

## 🔔 Notification System

- Notifikasi in-app dengan real-time polling
- Otomatis notifikasi untuk peminjaman terlambat
- Mark as read / mark all as read
- Validasi URL redirect (anti open-redirect)

## 🌐 Multi-Language (i18n)

- Bahasa Indonesia dan English
- 200+ translation keys mencakup seluruh content views
- Language switcher di sidebar
- Dashboard, forms, tables, buttons, empty states semua translatable

## 📊 Dashboard Widgets

- 11 widget yang bisa ditampilkan/disembunyikan per user
- Statistik, grafik tren, kondisi barang, peminjaman terlambat, scan QR terbaru, aksi cepat

## 🔌 REST API

Token-based authentication via Laravel Sanctum:
- **Auth:** POST /api/login, POST /api/logout, GET /api/me
- **Read:** GET /api/items, /api/categories, /api/locations, /api/loans, /api/stats
- **Write:** POST/PUT/DELETE untuk items, categories, locations, loans
- Semua endpoint tenant-scoped

## 🛡️ Security Improvements

| Fix | Deskripsi |
|-----|-----------|
| SVG XSS | Hapus SVG dari allowed upload mimes |
| Open Redirect | Validasi URL redirect di notifikasi |
| CSV Injection | Sanitasi karakter formula di export |
| Tenant Isolation | BelongsToMasjid pada Notification model |
| Rate Limiting | Throttle 30 req/menit pada scan QR publik |
| Import Hardening | Limit 1000 baris + sanitasi filename |
| Search Validation | Max 255 karakter pada semua search input |
| Backup Access | Restrict backup routes ke superadmin only |

## 🎨 UI Enhancements

- **Breadcrumb navigation** pada 20+ halaman
- **Custom modal dialog** mengganti browser confirm()
- **Double-submit prevention** pada semua form
- **Export loading indicator** dengan spinner overlay
- **Soft deletes** pada Item, Category, Location, User

## 📈 Performance

- Eager loading fix (N+1 query) di ScanLogController
- Pagination pada categories & locations index
- Image upload error handling dengan fallback storage

## ✅ Testing

| Kategori | Tests | Assertions |
|----------|-------|------------|
| Unit (Item, Loan, StockMovement, Scope) | 22 | ~60 |
| Feature/E2E | 162 | ~475 |
| **Total** | **184** | **535** |

Kategori test meliputi:
- Unit tests (models, scopes, accessors)
- Auth flow tests
- CRUD tests (items, loans, categories, users)
- Role-based access control tests
- Multi-tenant isolation tests
- Cross-tenant security tests
- Superadmin feature tests
- API tests (Sanctum auth + endpoints)
- Notification tests
- Registration & profile tests
- Language/i18n tests
- Dashboard widget tests

## 🔄 Upgrade dari v5.0.0

```bash
# Pull latest code
git pull origin master

# Run migrations (inside container)
docker compose exec app php artisan migrate --force

# Clear caches
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:clear
docker compose exec app php artisan view:clear

# Seed production data (if fresh install)
docker compose exec app php artisan db:seed --class=ProductionSeeder

# For development/testing
docker compose exec app php artisan db:seed --class=DummySeeder
```

## 👥 Credentials (Development)

| Role | Email | Password |
|------|-------|----------|
| Superadmin | superadmin@inventaris.id | superadmin123 |
| Admin (Jakarta) | admin@jakartapus.masjid.local | password |
| Admin (Bandung) | admin@bandung.masjid.local | password |
| Admin (Surabaya) | admin@surabaya.masjid.local | password |

## 📋 Daftar Lengkap Perubahan

Lihat [CHANGELOG.md](CHANGELOG.md) untuk detail perubahan.
