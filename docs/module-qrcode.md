# 📷 Module: QR Code & Scan

## Overview

Fitur QR Code memungkinkan setiap barang memiliki label QR unik yang bisa dicetak dan ditempel. Saat dipindai, langsung membuka halaman detail barang. Fitur ini mendukung audit inventaris yang cepat dan akurat.

## Fitur Utama

### 1. Generate QR Code
- Setiap barang bisa memiliki QR code unik
- QR berisi URL aman (contoh: `https://domain.com/i/abc123xyz`)
- Key acak 24 karakter (bukan ID) untuk keamanan
- Generate otomatis saat cetak label

### 2. Cetak Label QR
**Single Item:**
- Ukuran kecil (60×35mm) atau sedang (80×45mm)
- Pilih jumlah copy
- Layout: QR code + nama barang + kode + lokasi

**Bulk/Massal:**
- Pilih multiple items sekaligus
- Filter: barang yang belum punya QR
- Print dalam satu halaman A4

### 3. Scan QR Code
**Mode Normal:**
- Scan menggunakan kamera HP/webcam
- Langsung redirect ke halaman detail barang
- Fallback: input kode manual

**Mode Audit:**
- Pilih tujuan scan (Audit, Pengecekan, Maintenance, Lainnya)
- Tambahkan catatan (opsional)
- Tercatat di log untuk keperluan audit

### 4. Scan Logs (Admin)
- Riwayat semua scan tercatat
- Filter: barang, user, tanggal, tujuan
- Export ke CSV untuk audit
- Statistik: total, hari ini, minggu ini

## Database

### items (updated)
```sql
qr_code_key VARCHAR(255) UNIQUE NULLABLE
```

### scan_logs
```sql
id BIGINT PRIMARY KEY
item_id BIGINT FK -> items.id
user_id BIGINT NULLABLE FK -> users.id
scanned_at TIMESTAMP
purpose VARCHAR(255) NULLABLE  -- audit, check, maintenance, other
notes TEXT NULLABLE
ip_address VARCHAR(45) NULLABLE
timestamps
```

## Routes

| Method | URI | Name | Akses |
|--------|-----|------|-------|
| GET | `/scan` | qrcode.scan | All users |
| GET | `/scan/audit` | qrcode.audit-scan | Admin, Operator |
| GET | `/i/{qrKey}` | qrcode.redirect | Public |
| POST | `/i/{qrKey}/audit` | qrcode.scan-with-purpose | Admin, Operator |
| POST | `/items/{item}/qr/generate` | qrcode.generate | Admin, Operator |
| GET | `/items/{item}/qr/preview` | qrcode.preview | Admin, Operator |
| GET | `/items/{item}/qr/print` | qrcode.print | Admin, Operator |
| GET | `/qr/bulk` | qrcode.bulk | Admin, Operator |
| POST | `/qr/bulk/print` | qrcode.bulk.print | Admin, Operator |
| GET | `/scan-logs` | scan-logs.index | Admin |
| GET | `/scan-logs/export` | scan-logs.export | Admin |

## Alur Penggunaan

### Cetak Label Baru
1. Buka halaman detail barang
2. Klik "Generate QR Code" (jika belum ada)
3. Klik "Cetak Label"
4. Pilih ukuran & jumlah
5. Print dan tempel ke barang

### Cetak Label Massal
1. Buka menu Inventaris
2. Klik "🏷️ Cetak Label"
3. Pilih barang yang diinginkan
4. Klik "Siapkan untuk Cetak"
5. Print semua

### Scan untuk Cek Barang
1. Buka menu "📷 Scan QR Barang"
2. Arahkan kamera ke QR
3. Otomatis buka halaman detail

### Scan untuk Audit
1. Buka menu "📋 Audit Scan"
2. Pilih tujuan (Audit Inventaris/Pengecekan/dll)
3. Scan QR
4. Data tercatat untuk laporan

### Lihat Log Scan (Admin)
1. Buka menu Administrasi > Scan Logs
2. Filter berdasarkan kebutuhan
3. Export ke CSV jika perlu

## Permissions

| Action | Admin | Operator | Viewer |
|--------|:-----:|:--------:|:------:|
| Scan QR (normal) | ✅ | ✅ | ✅ |
| Audit Scan | ✅ | ✅ | ❌ |
| Generate QR | ✅ | ✅ | ❌ |
| Cetak Label | ✅ | ✅ | ❌ |
| View Scan Logs | ✅ | ❌ | ❌ |
| Export Scan Logs | ✅ | ❌ | ❌ |

## Tips Cetak

1. **Kertas Stiker** - Gunakan kertas stiker A4 untuk hasil terbaik
2. **Skala 100%** - Jangan gunakan "fit to page", cetak skala 100%
3. **Preview** - Selalu preview sebelum cetak
4. **Test Scan** - Test scan setelah cetak untuk memastikan terbaca

## Keamanan

- QR key acak (bukan sequential ID)
- Role-based access control
- IP address tercatat di log
- Tidak ada data sensitif di QR code

## Troubleshooting

### Kamera tidak aktif
- Pastikan browser diizinkan akses kamera
- Gunakan HTTPS di production
- Fallback: input kode manual

### QR tidak terbaca
- Pastikan pencahayaan cukup
- Jarak optimal 10-20cm
- Hindari pantulan/blur

### Label terlalu kecil/besar
- Pastikan skala print 100%
- Coba ukuran label berbeda
