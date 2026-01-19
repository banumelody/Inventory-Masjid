# Changelog

Semua perubahan penting pada proyek ini akan didokumentasikan di file ini.

Format berdasarkan [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
dan proyek ini mengikuti [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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

- [Repository](https://github.com/your-repo/inventory-masjid)
- [Documentation](./docs/README.md)
- [Issue Tracker](https://github.com/your-repo/inventory-masjid/issues)
