🕌 Inventory Barang Masjid v5.0.0 - QR Label & Scan System

## 🏷️ Release Overview

**VERSION:** 5.0.0  
**CODENAME:** QR Label & Scan  
**RELEASE DATE:** 2026-01-19

This is a major release featuring a complete QR Code labeling and scanning system, along with advanced admin settings and application configuration capabilities.

---

## ✨ Major Features

### QR Code Label Printing

- **Generate QR codes** untuk setiap barang secara otomatis
- **Cetak label QR individual** dengan size options:
  - Small (60×35mm) - untuk barang kecil
  - Medium (80×45mm) - untuk barang standar
- **Cetak massal (Bulk Print)** untuk multiple barang sekaligus
- **Live Preview** sebelum cetak untuk memastikan hasil
- Compatible dengan printer thermal maupun inkjet

### Audit Scan System

- **Scan QR codes** dengan kamera smartphone/webcam
- **Pilihan purpose/tujuan scan:**
  - Audit (audit reguler)
  - Pengecekan (general check)
  - Maintenance (pemeliharaan)
  - Lainnya (custom purpose)
- **Catatan scan** untuk dokumentasi lengkap
- **Riwayat scan per barang** - track semua aktivitas
- **Manual input fallback** jika kamera gagal
- Real-time feedback saat scan berhasil

### Scan Logs Dashboard

- **Admin dashboard** untuk monitoring semua scan activities
- **Advanced filtering:**
  - Filter by tanggal (date range)
  - Filter by purpose (tujuan scan)
  - Filter by user (siapa yang scan)
  - Filter by item (barang mana yang di-scan)
- **Export to CSV** untuk analisis lebih lanjut
- **Scan statistics** di dashboard overview
- **Search functionality** untuk pencarian cepat

### Application Settings (Admin UI)

- **Dynamic app name** - ubah nama aplikasi tanpa restart
- **Logo & Favicon upload** - branding aplikasi
- **Organization information:**
  - Nama organisasi (masjid)
  - Alamat lengkap
  - Nomor telepon
  - Email kontak
  - Link WhatsApp
- **About page** dengan visi, misi, dan deskripsi
- **Footer customization** - sesuaikan footer di setiap halaman
- **Database-based configuration** - semua setting tersimpan di DB (tidak perlu edit .env)

### Version Information System

- **Display app version** di Settings page
- **Version history** - informasi perubahan tiap versi
- **Features changelog** - list fitur yang ditambahkan
- **System information:**
  - Laravel version
  - PHP version
  - Environment (development/production)
  - Database information

---

## 🎨 UI/UX Improvements

- Sidebar scroll styling improvement untuk desktop view
- Dynamic branding di semua halaman (konsisten dengan app name & logo)
- Email templates update dengan dynamic app name
- Print templates dengan dynamic branding
- Enhanced admin control panel
- Improved responsive design

---

## 🔧 Technical Changes

### Database Changes

- **New column** `purpose` di tabel `scan_logs` untuk tracking tujuan scan
- **New table** `settings` untuk menyimpan konfigurasi aplikasi
- Migration files untuk database updates
- Seeder untuk initial configuration

### Code Changes

- **Setting Model** dengan caching untuk performa optimal
- **Version Config** file (`config/version.php`) untuk centralized version management
- Refactored layout templates untuk dynamic branding
- Enhanced admin controllers

### Dependencies & Requirements

- Laravel 10
- PHP 8.1+
- MariaDB 10.11
- Docker & Docker Compose
- Nginx
- DomPDF (untuk PDF export)

---

## 📦 Installation & Setup

### Quick Start with Docker

```bash
# Clone repository
git clone https://github.com/banumelody/Inventory-Masjid.git
cd Inventory-Masjid

# Setup environment
cp .env.example .env

# Build and run containers
docker-compose up -d --build

# Install dependencies and setup database
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan db:seed
docker-compose exec app php artisan storage:link

# Access application
# Open http://localhost:8085 in your browser
```

### Default Login

- **Email:** admin@masjid.local
- **Password:** password

### Port Configuration

Edit `.env` file:

```env
APP_PORT=8085        # Web application port
DB_HOST_PORT=3308    # Database port
```

---

## 📋 Complete Feature List (All Versions)

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

- ✅ Responsive layout (Desktop, Tablet, Mobile)
- ✅ Hamburger menu untuk layar kecil
- ✅ Card view di mobile
- ✅ Touch-friendly interface
- ✅ Form draft dengan localStorage
- ✅ Lazy load gambar
- ✅ Pagination

### V5 - QR Code Label & Scan 🏷️ [LATEST]

- ✅ Generate & print QR labels
- ✅ Bulk print QR codes
- ✅ Scan QR dengan audit trail
- ✅ Admin settings UI
- ✅ Version information system
- ✅ Export scan logs ke CSV
- ✅ Manual input fallback

---

## 🧪 Testing

```bash
# Run all tests
docker-compose exec app php artisan test

# Run unit tests only
docker-compose exec app php artisan test --filter=Unit

# Run feature tests only
docker-compose exec app php artisan test --filter=Feature

# Run with coverage
docker-compose exec app php artisan test --coverage
```

---

## 📚 Documentation

Technical documentation tersedia di `/docs`:

- [Overview](https://github.com/banumelody/Inventory-Masjid/blob/main/docs/README.md)
- [Loans Module](https://github.com/banumelody/Inventory-Masjid/blob/main/docs/module-loans.md)
- [Stock Movements Module](https://github.com/banumelody/Inventory-Masjid/blob/main/docs/module-stock-movements.md)
- [Users Module](https://github.com/banumelody/Inventory-Masjid/blob/main/docs/module-users.md)
- [Backup Module](https://github.com/banumelody/Inventory-Masjid/blob/main/docs/module-backup.md)
- [Export Module](https://github.com/banumelody/Inventory-Masjid/blob/main/docs/module-export.md)
- [QR Code Module](https://github.com/banumelody/Inventory-Masjid/blob/main/docs/module-qrcode.md) ✨ NEW

---

## 🤝 Contributing

Kami menerima pull requests untuk improvements dan bug fixes. Mohon baca kontribusi guidelines sebelum submit PR.

---

## 📄 License

Project ini menggunakan lisensi MIT. Lihat [LICENSE](https://github.com/banumelody/Inventory-Masjid/blob/main/LICENSE) file untuk detail.

---

## 🙏 Acknowledgments

Terima kasih kepada semua yang telah berkontribusi pada project ini, khususnya:

- Tim development yang telah mengimplementasikan fitur
- Community yang memberikan feedback
- Pengguna yang telah menggunakan aplikasi ini

---

**Happy Inventory Management! 🎉**

Untuk pertanyaan atau issues, silakan buka [GitHub Issues](https://github.com/banumelody/Inventory-Masjid/issues).
