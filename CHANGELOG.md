# Changelog

Semua perubahan penting pada proyek ini akan didokumentasikan di file ini.

Format berdasarkan [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
dan proyek ini mengikuti [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [6.0.0] - 2026-04-10

### 🏢 Codename: Multi-Tenant SaaS

Rilis major yang mentransformasi aplikasi menjadi platform SaaS multi-tenant. Setiap masjid memiliki data terisolasi, dengan superadmin yang dapat mengelola seluruh platform.

### Added

- **Multi-Tenant Architecture**
  - Row-level tenant isolation via MasjidScope global scope
  - BelongsToMasjid trait untuk auto-scoping 13 model
  - SetMasjidContext middleware untuk inject tenant context
  - EnsureMasjidContext middleware untuk guard create/write routes

- **Superadmin Dashboard**
  - Platform-wide statistics (total masjid, users, items)
  - Masjid CRUD management (create, edit, suspend, delete)
  - Tenant context switcher — operate as any masjid
  - Global activity log with masjid filter
  - Cross-masjid user transfer
  - Cross-tenant global report view

- **Masjid Self-Registration**
  - Public registration form for new mosques
  - Auto-create admin user, default categories & locations
  - Masjid status management (active, suspended, pending)

- **Notification System**
  - In-app notifications with real-time polling
  - Overdue loan automatic notifications
  - Mark as read / mark all as read
  - Redirect URL validation (anti open-redirect)

- **Multi-Language Support (i18n)**
  - Indonesian (id) and English (en) language files
  - 200+ translation keys covering all content views
  - Language switcher in sidebar
  - SetLocale middleware

- **Dashboard Widget Customization**
  - 11 toggleable widgets per user
  - Preferences saved per user in settings
  - Widget labels fully translatable

- **REST API (Sanctum)**
  - Token-based authentication (login/logout/me)
  - Read endpoints: items, categories, locations, loans, stats
  - Write endpoints: CRUD for items, categories, locations, loans
  - Tenant-scoped via SetApiMasjidContext middleware

- **User Profile Page**
  - View/edit name and email
  - Change password with current password verification

- **UI Enhancements**
  - Breadcrumb navigation component on 20+ views
  - Custom Tailwind modal confirmation dialog (replaces browser confirm)
  - Double-submit prevention on all forms
  - Export loading indicator with spinner overlay

- **Soft Deletes**
  - Item, Category, Location, User models support soft delete
  - Deleted records preserved for audit trail

### Security

- **SVG XSS Prevention** — removed SVG from allowed upload mimes
- **Open Redirect Prevention** — notification redirect URL validation
- **CSV Injection Prevention** — sanitize formula characters in exports
- **BelongsToMasjid on Notification** — tenant-scoped notifications
- **QR Rate Limiting** — throttle:30,1 on public scan routes
- **Import Hardening** — 1000 row limit + filename sanitization
- **Search Input Validation** — max:255 on all search parameters
- **Backup Route Restriction** — superadmin-only access

### Performance

- Eager loading for item.category in ScanLogController (N+1 fix)
- Pagination on categories and locations index (was loading all)
- Database indexes verified on all tenant-scoped tables
- Image upload error handling with fallback storage

### Technical

- 184 tests, 535 assertions — all passing
- 20 test files covering Unit, Feature, E2E, Security, Multi-tenant
- Migration for masjids table, pivot tables, soft deletes
- DummySeeder with 3 sample masjids and full data

---

## [5.0.0] - 2026-01-19

### 🚀 Codename: QR Label & Scan

Rilis major dengan fitur QR Code lengkap dan sistem pengaturan aplikasi via admin UI.

### Added
- **QR Code Label Printing**
  - Cetak label QR individual per barang
  - Cetak label QR massal (bulk print)
  - Pilihan ukuran label: Kecil (60×35mm) dan Sedang (80×45mm)
  - Preview sebelum cetak
  
- **Audit Scan System**
  - Scan QR dengan pilihan tujuan (audit, pengecekan, maintenance, lainnya)
  - Catatan scan untuk dokumentasi
  - Riwayat scan per barang
  
- **Scan Logs**
  - Dashboard scan logs untuk admin
  - Filter berdasarkan tanggal, tujuan, user
  - Export scan logs ke CSV
  - Statistik scan di dashboard
  
- **Application Settings**
  - Pengaturan nama aplikasi via admin UI
  - Upload logo dan favicon
  - Informasi organisasi (nama, alamat, telepon, email, WhatsApp)
  - Halaman About dengan visi & misi
  - Footer customization
  - Semua pengaturan tersimpan di database (tidak perlu edit .env)
  
- **Version Information**
  - Tampilan versi aplikasi di halaman Settings
  - Riwayat versi dan fitur
  - Informasi sistem (Laravel version, PHP version, environment)

### Changed
- Sidebar scroll styling diperbaiki untuk desktop view
- Layout public menggunakan dynamic branding
- Email templates menggunakan dynamic app name
- Print templates menggunakan dynamic branding

### Technical
- Menambahkan `purpose` column ke tabel `scan_logs`
- Menambahkan tabel `settings` untuk konfigurasi aplikasi
- Setting model dengan caching untuk performa
- Config file `version.php` untuk version management

---

## [4.0.0] - 2025-12-01

### 🎨 Codename: Responsive UI

### Added
- Mobile-first responsive design
- Touch-friendly interface dengan target area 44×44px
- Sidebar navigation untuk desktop
- Bottom navigation untuk mobile
- PWA-ready manifest

### Changed
- Semua halaman dioptimasi untuk mobile
- Form input lebih besar untuk touch
- Tabel dengan horizontal scroll di mobile

---

## [3.0.0] - 2025-10-01

### 📊 Codename: Data Management

### Added
- Import data dari Excel/CSV
- Export data ke Excel/CSV
- Activity logging untuk audit trail
- Backup database manual
- Restore dari backup

### Changed
- Improved error handling
- Better validation messages

---

## [2.0.0] - 2025-08-01

### 📦 Codename: Stock & Maintenance

### Added
- Mutasi stok (masuk, keluar, adjustment)
- Riwayat mutasi per barang
- Maintenance/perawatan barang
- Jadwal maintenance
- Laporan inventaris
- Dashboard statistik

### Changed
- Improved loan management
- Better category organization

---

## [1.0.0] - 2025-06-01

### 🕌 Codename: Initial Release

### Added
- Manajemen inventaris barang
- CRUD barang dengan foto
- Kategori barang
- Lokasi penyimpanan
- Peminjaman barang
- Pengembalian barang
- User management dengan role (admin, operator, viewer)
- QR Code generation untuk setiap barang
- Basic scan QR untuk lihat detail barang
- Panduan pengguna
- FAQ

---

## Version Naming Convention

- **Major** (X.0.0): Perubahan besar, fitur utama baru, breaking changes
- **Minor** (0.X.0): Fitur baru yang backward compatible
- **Patch** (0.0.X): Bug fixes dan perbaikan kecil

## Links

- [Repository](https://github.com/banumelody/Inventory-Masjid)
- [Documentation](./docs/README.md)
- [Issue Tracker](https://github.com/banumelody/Inventory-Masjid/issues)
