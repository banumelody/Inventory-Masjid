# 🕌 PRD — Inventory Barang Masjid (MVP)

## 1. Ringkasan Produk
Aplikasi web sederhana untuk mencatat inventaris barang masjid:

- memudahkan pendataan
- mencegah kehilangan
- membuat laporan inventaris rapi

Produk didesain:
- ringan
- mudah dipakai pengurus
- mudah dipelihara

---

## 2. Tujuan

### 2.1 Masalah Saat Ini
Masjid biasanya:

- inventaris hanya ditulis di buku
- Excel tercecer di banyak laptop
- pergantian pengurus = data hilang
- tidak ada laporan rapi saat diminta donatur

### 2.2 Tujuan Produk
✔ Inventaris tercatat rapi  
✔ Mudah diakses & dicetak  
✔ Transisi pengurus lebih aman  

---

## 3. Scope MVP

### 3.1 Masuk (In-Scope)

#### 1️⃣ Manajemen Barang
Field:

- Nama barang
- Kategori
- Lokasi
- Jumlah
- Satuan (unit/pcs/set)
- Kondisi (baik/perlu perbaikan/rusak)
- Catatan

CRUD lengkap.

#### 2️⃣ Master Data
- CRUD kategori
- CRUD lokasi

Dengan aturan:

- kategori/lokasi tidak boleh dihapus jika masih dipakai item

#### 3️⃣ Laporan Inventaris
- list semua barang
- filter kategori
- filter lokasi
- tampilan siap cetak (print-friendly)

---

### 3.2 Tidak Masuk (Out of Scope — MVP)

❌ login multi-user / roles  
❌ export PDF / Excel  
❌ peminjaman barang  
❌ riwayat mutasi  
❌ foto barang  
❌ notifikasi  
❌ integrasi cloud

Semua bisa jadi roadmap berikutnya.

---

## 4. Persona Pengguna

### 👤 Pengurus Masjid
- tidak selalu teknis
- maunya cepat & sederhana
- sering berganti periode

Tujuan utama:  
➡️ "Saya ingin semua barang tercatat & mudah dicek."

---

## 5. Alur Utama (User Flow)

1️⃣ Masuk halaman inventaris  
2️⃣ Klik “Tambah Barang”  
3️⃣ Isi form  
4️⃣ Simpan  
5️⃣ Barang muncul di daftar  
6️⃣ Pengurus bisa filter / cetak

---

## 6. Requirements Fungsional

### 6.1 Barang
User dapat:

- melihat list barang
- cari berdasarkan nama
- filter kategori / lokasi
- tambah
- edit
- hapus

Validasi:

- nama wajib
- jumlah angka ≥ 0
- kategori wajib
- lokasi wajib

---

### 6.2 Kategori
User dapat:

- lihat daftar
- tambah
- edit
- hapus (hanya jika tidak dipakai item)

---

### 6.3 Lokasi
User dapat:

- lihat daftar
- tambah
- edit
- hapus (hanya jika tidak dipakai item)

---

### 6.4 Laporan Inventaris
- tabel + filter
- tampilan print only (tanpa tombol/menu)

---

## 7. Requirements Teknis

### 7.1 Stack
- **Backend:** Laravel
- **Frontend:** Blade + Tailwind (CDN)
- **Database:** MySQL / MariaDB
- **Runtime:** Docker Compose
- **Web server:** Nginx

---

## 8. Infrastruktur & Deployment

### 8.1 Arsitektur Kontainer

| Service | Fungsi |
|---|---|
| app | PHP-FPM + Laravel |
| web | Nginx reverse proxy |
| db | MariaDB |

### 8.2 Mode Deployment
| Lingkungan | Cara |
|---|---|
| Development | Docker Compose + volume (hot reload) |
| Production | Docker Compose (tanpa bind source), debug off |

---

## 9. Konfigurasi Port via `.env`

📌 **Tidak ada port hard-code di compose.**

File `.env` (level project):

```env
# Web
APP_PORT=8085

# DB (optional, dev only)
DB_HOST_PORT=3308
```

Default jika tidak diset:

- web → 8080
- db → 3307

---

## 10. Environment Laravel (contoh)

`src/.env`:

```env
APP_NAME="Inventory Masjid"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8085

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=inventory_masjid
DB_USERNAME=inventory_user
DB_PASSWORD=inventory_pass
```

---

## 11. Database Schema

### categories
- id (PK)
- name

### locations
- id (PK)
- name

### items
- id
- name
- category_id (FK)
- location_id (FK)
- quantity
- unit
- condition
- note
- timestamps

Constraint:

- foreign key cascade restrict pada kategori & lokasi.

---

## 12. UX Guideline
Prinsip:

- sederhana
- readable
- sedikit klik
- tombol besar

Menu:

- Inventaris
- Kategori
- Lokasi
- Laporan

---

## 13. Keamanan (MVP)
- DB tidak diexpose pada production
- APP_DEBUG = false di production
- backup manual (sementara)

---

## 14. KPI Sukses

Produk dianggap berhasil jika:

✔ pengurus bisa input barang tanpa tutorial  
✔ inventaris mudah dicetak  
✔ data tidak lagi tercecer

---

## 15. Roadmap Lanjutan

V2:

- peminjaman barang
- upload foto
- mutasi stok
- export excel/pdf
- user & role
- backup otomatis

---

# 🛠️ PRD — Inventory Barang Masjid (V2)

> Versi ini mengembangkan MVP dengan fokus pada transparansi, akuntabilitas, dan keberlanjutan data.

## 1. Tujuan Utama

1️⃣ Mengurangi kehilangan barang karena dipinjam tanpa catatan.  
2️⃣ Memberikan identifikasi barang yang lebih jelas (foto).  
3️⃣ Mencatat perubahan stok secara historis (mutasi).  
4️⃣ Memudahkan distribusi laporan (export).  
5️⃣ Menambah kontrol akses melalui role.  
6️⃣ Menjamin keberlangsungan data (backup otomatis).

---

## 2. Scope V2 (In‑Scope)

### 2.1 Peminjaman Barang

**Masalah**: Barang sering dipinjam, tapi lupa siapa yang membawa dan kapan kembali.

**Fitur**:
- Catat peminjaman:
  - Barang
  - Peminjam (nama + kontak opsional)
  - Jumlah dipinjam
  - Tanggal pinjam
  - Tanggal rencana kembali
  - Catatan
- Pengembalian barang:
  - Tanggal kembali
  - Kondisi saat kembali
- Status: `Dipinjam` / `Sudah kembali`
- Tanda peringatan untuk yang lewat jatuh tempo.

**DB (baru)** — `loans`:
- id
- item_id (FK)
- borrower_name
- borrower_phone (nullable)
- quantity
- borrowed_at (date)
- due_at (date, nullable)
- returned_at (date, nullable)
- returned_condition (nullable)
- notes (nullable)

Rules:
- tidak boleh meminjam lebih dari stok tersedia
- jika barang kembali → stok otomatis bertambah

---

### 2.2 Upload Foto Barang

**Tujuan**: Mempermudah identifikasi barang.

**Fitur**:
- 1 foto per barang (MVP)
- Resize otomatis (maks 1200px)
- Kompresi ringan

**DB** — tambah kolom pada `items`:
- photo_path (nullable)

Storage:
- lokal (public/storage) — cloud opsional nanti

---

### 2.3 Mutasi Stok

**Masalah**: Saat stok berubah, alasan tidak terekam.

**Fitur**:
- Catat perubahan stok manual:
  - jenis mutasi: `Tambah`, `Kurang`
  - jumlah
  - alasan (beli, rusak, hilang, donasi, dll)
  - tanggal
- Riwayat mutasi per barang

**DB (baru)** — `stock_movements`:
- id
- item_id (FK)
- type (in|out)
- quantity
- reason
- moved_at (date)
- notes (nullable)

Rules:
- sinkron dengan field `quantity` pada `items`

---

### 2.4 Export Excel / PDF

**Fitur**:
- Export daftar inventaris:
  - semua data
  - bisa berdasarkan filter
- Format:
  - Excel (.xlsx)
  - PDF (layout sederhana siap cetak)

Catatan: gunakan library umum (ex: maatwebsite/excel, dompdf / snappy) — detail teknis ada di design doc dev.

---

### 2.5 User & Role

**Masalah**: Data sensitif, tidak semua orang boleh mengubah.

**Role (MVP):**
- **Admin** — semua akses
- **Operator** — CRUD barang & peminjaman, tidak bisa hapus item & user
- **Viewer** — hanya lihat dan export

**DB (baru)**
- `users`
- `roles`
- pivot `role_user` (atau field role sederhana pada users — ditentukan saat implementasi)

Auth: login sederhana (email + password), tanpa SSO dulu.

---

### 2.6 Backup Otomatis

**Tujuan**: Data aman jika server rusak / human error.

**Fitur**:
- Backup otomatis database harian
- Kompres (.gz)
- Penyimpanan:
  - minimal: folder backup lokal
  - opsi tambahan: upload ke Google Drive/S3 (non-MVP, roadmap)
- Halaman daftar backup + tombol download

Retention default: simpan 30 hari.

---

## 3. Out of Scope (V2)

❌ approval workflow  
❌ notifikasi WA/email (kecuali indikator visual)  
❌ multi‑tenant penuh  
❌ audit trail tingkat field  
❌ cloud storage khusus (opsional nanti)

---

## 4. Dampak UI/UX

- Tambah menu: **Peminjaman**, **Mutasi Stok**, **Pengguna**, **Backup**
- Pada halaman barang:
  - badge stok
  - tombol "Pinjam" dan "Mutasi"
  - preview foto kecil

Prinsip tetap: sederhana, tidak membingungkan.

---

## 5. Dampak Teknis

- Perubahan skema database (migration baru)
- Tambah middleware auth + role
- Penambahan storage public untuk foto
- Penjadwalan cron (Laravel scheduler) untuk backup
- Tambahan dependency export

Backward compatible:
- data lama tetap valid
- field baru nullable

---

## 6. Acceptance Criteria (ringkas)

- Barang yang dipinjam tercatat dan bisa dikembalikan.
- Mutasi stok tercatat dan mempengaruhi jumlah.
- Foto muncul pada detail barang.
- Export berjalan dan file valid.
- Role membatasi akses sesuai definisi.
- Backup berjalan otomatis & bisa diunduh.

---

## 7. Risiko & Mitigasi

**Risiko:**
- fitur terlalu banyak → pengurus bingung
- storage membengkak karena foto & backup
- human error saat mutasi

**Mitigasi:**
- UI tetap sederhana, jelaskan dengan label
- limit ukuran foto + rotasi backup
- konfirmasi sebelum mutasi & peminjaman

---

## 8. Next Step

1️⃣ Validasi fitur ke pengguna (pengurus masjid)  
2️⃣ Finalisasi prioritas rilis bertahap  
3️⃣ Buat design teknis per modul  
4️⃣ Implementasi + tes

---

# 🚀 PRD — Inventory Barang Masjid (V3 — Planning & Delivery)

> Fokus utama V3: memastikan produk benar‑benar dipakai, prioritas jelas, desain teknis matang, dan rilis lebih stabil.

---

## 1️⃣ Validasi Fitur ke Pengguna (Pengurus Masjid)

### Tujuan
- Memastikan fitur yang dibuat benar‑benar digunakan.
- Menemukan masalah usability sebelum build lebih jauh.

### Aktivitas
- Observasi langsung penggunaan aplikasi.
- Wawancara singkat (10–15 menit per pengurus).
- Checklist validasi per modul (inventaris, pinjam, mutasi, export, dll).

### Output yang Diharapkan
- Daftar pain point nyata pengguna.
- Usulan perbaikan berbasis data, bukan asumsi.
- Prioritas perbaikan UI/UX kecil.

### Alat Bantu
- Form feedback sederhana (Google Form / internal).
- Screenshots / screen recording saat penggunaan.

---

## 2️⃣ Finalisasi Prioritas Rilis Bertahap

### Tujuan
Mencegah "fitur menumpuk", rilis tetap terkontrol.

### Metode Prioritas
Gunakan kerangka sederhana:

- **Must Have** → langsung mempengaruhi operasional
- **Should Have** → penting tapi bisa menunggu
- **Nice to Have** → hanya jika ada waktu

### Contoh Pembagian (Awal)

**Release 3.0 (Stabilitas + Usability)**
- perbaikan tampilan & alur yang membingungkan
- bug fixing kritikal
- optimasi loading tabel

**Release 3.1 (Improve Existing Features)**
- penyempurnaan peminjaman
- penyempurnaan mutasi stok
- penyempurnaan export

**Release 3.2 (Enhancement Kecil)**
- label, tooltip, dan bantuan pemakaian

> Catatan: daftar pasti ditentukan setelah sesi validasi pengguna.

---

## 3️⃣ Buat Design Teknis per Modul

### Tujuan
Mengurangi risiko refactor besar karena desain terburu‑buru.

### Isi Dokumen Teknis (per modul)
1. **Tujuan Modul** — masalah yang diselesaikan
2. **Flow Diagram** — alur data & proses
3. **Struktur Database** — tabel, relasi, constraint
4. **API / Controller** — endpoint & rule validasi
5. **State & Error Handling** — bagaimana jika gagal
6. **Permission** — siapa boleh apa

### Modul Prioritas untuk Didokumentasikan
- Peminjaman Barang
- Mutasi Stok
- Backup Otomatis
- Role & Permission
- Export

Output berupa dokumen ringkas (bisa Markdown) untuk developer.

---

## 4️⃣ Implementasi + Tes

### Strategi Implementasi
- kerjakan modul kecil dulu
- commit bertahap
- hindari big‑bang release

### Jenis Pengujian

**1. Unit Test**
- validasi stok tidak minus
- validasi peminjaman tidak melebihi stok

**2. Feature / Integration Test**
- alur pinjam → kembali
- alur mutasi → stok sinkron
- export tidak korup

**3. UAT (User Acceptance Test)**
- pengurus mencoba langsung
- checklist lulus/gagal

### Kriteria Lulus Rilis
- tidak ada bug kritikal
- semua modul utama bisa dipakai dari awal sampai akhir
- pengurus bisa mengoperasikan tanpa panduan panjang

---

## Ringkasan Target V3

- ✔ produk benar‑benar divalidasi pengguna
- ✔ roadmap lebih realistis & bertahap
- ✔ desain teknis tiap modul jelas sebelum coding
- ✔ rilis lebih stabil & minim bug

---

# 📱 PRD — Inventory Barang Masjid (V4 — Responsive UI)

> Fokus versi ini: membuat UI **responsif dan konsisten** di semua ukuran layar (mobile, tablet, desktop) — tanpa memaksa pola mobile‑first dan tanpa mengubah alur kerja yang sudah familiar.

---

## 1️⃣ Latar Belakang

Aplikasi mulai dipakai oleh:
- pengurus yang memakai HP,
- sebagian masih memakai laptop/PC,
- beberapa memakai tablet.

Masalah yang muncul:
- tabel pecah saat layar kecil
- perlu scroll horizontal
- tombol dan teks terlalu kecil di HP
- tampilan tidak efisien saat di desktop besar

Target V4: **satu kode, tampilan adaptif** sesuai device.

---

## 2️⃣ Tujuan Utama

✔ tampilan nyaman digunakan di berbagai ukuran layar  
✔ informasi tetap jelas tanpa kehilangan konteks  
✔ interaksi mudah di HP, tetap efektif di desktop  
✔ performa tetap ringan

---

## 3️⃣ Scope (In‑Scope)

### 3.1 Responsive Layout

**Prinsip:**
- fluid layout + breakpoints
- konten beradaptasi, alur tetap sama

**Aturan tampilan:**
- **Desktop (≥ 1024px)** — tabel lengkap + aksi jelas
- **Tablet (768–1023px)** — beberapa kolom disederhanakan
- **Mobile (≤ 767px)** — card list sederhana dengan aksi utama di dalam card

---

### 3.2 Navigasi yang Adaptif

- Desktop → top navigation (tetap)
- Tablet/Mobile → hamburger / collapsible menu

Catatan: bottom navigation TIDAK digunakan kecuali terbukti diperlukan dari hasil uji pengguna.

---

### 3.3 Form yang Nyaman di Semua Device

- susunan field satu kolom saat layar kecil
- label jelas + helper text singkat
- tombol besar & mudah dijangkau
- auto scroll ke error jika validasi gagal

Tambahan:
- opsi **Simpan & Tambah Lagi** untuk input berulang.

---

### 3.4 Optimasi Performa untuk Layar Kecil

- pagination default 10 item
- lazy‑load gambar
- kompresi foto otomatis
- hindari script berat yang tidak perlu

---

### 3.5 Aksesibilitas Ringan

- kontras warna mencukupi
- ukuran font minimum 14px
- area klik tombol memadai

---

## 4️⃣ Out of Scope (V4)

❌ aplikasi native  
❌ PWA full offline  
❌ perubahan arsitektur besar  
❌ redesign total visual/brand

Fokus: **membuat UI yang ada jadi responsif & usable.**

---

## 5️⃣ Dampak ke Modul

### Inventaris
- tabel → card di mobile
- shortcut aksi: detail, pinjam, mutasi

### Peminjaman
- timeline card di layar kecil

### Mutasi Stok
- ikon & label lebih jelas

### Export
- pindahkan ke overflow menu pada layar kecil

---

## 6️⃣ Requirements Teknis

- Tailwind responsive utilities (sm/md/lg/xl)
- komponen reusable
- testing pada:
  - 360×640 (HP kecil)
  - 412×915 (HP menengah)
  - 768×1024 (tablet)
  - 1366×768+ (desktop)

---

## 7️⃣ Acceptance Criteria

- tidak ada horizontal scroll di layar kecil
- elemen tidak tumpang tindih
- aksi utama mudah diakses
- informasi utama (nama, stok, lokasi) selalu terlihat
- performa tetap stabil

---

## 8️⃣ Risiko & Mitigasi

**Risiko:**
- beberapa informasi tersembunyi di mobile
- kompleksitas CSS meningkat

**Mitigasi:**
- tombol "Lihat detail" jelas
- dokumentasi kelas & komponen

---

## 9️⃣ Next Step

1️⃣ buat wireframe responsif  
2️⃣ review dengan 2–3 pengguna  
3️⃣ implementasi bertahap per halaman  
4️⃣ regression test desktop & mobile

---

