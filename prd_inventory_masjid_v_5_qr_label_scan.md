# 🏷️ PRD — Inventory Barang Masjid (V5 — QR Code Label & Scan)

> Fokus versi ini: setiap aset memiliki label QR yang bisa **dicetak, ditempel, dan dipindai** sehingga pengecekan inventaris menjadi lebih cepat dan rapi.

---

## 1️⃣ Latar Belakang

Pada versi sebelumnya, data inventaris sudah tersimpan dengan baik.
Namun saat berada di lapangan, pengurus masih perlu:

- membuka daftar
- mencari aset secara manual
- memastikan data sesuai

Ini memakan waktu dan rawan salah.

Dengan QR code:

➡️ cukup scan — langsung masuk ke halaman detail aset.

---

## 2️⃣ Tujuan Utama

✔ mempercepat identifikasi barang  
✔ meminimalkan kesalahan input/pengecekan  
✔ memudahkan audit inventaris  
✔ mendukung pengelolaan aset jangka panjang

---

## 3️⃣ Ruang Lingkup (In‑Scope)

### 3.1 Generate QR untuk Setiap Aset

Pada halaman detail / daftar aset:

- tombol **Generate QR**
- QR berisi URL aman, contoh:

```
https://app.example.com/i/abc123
```

> Catatan: kode tidak menampilkan data sensitif — hanya identifier aman.

QR ditampilkan:
- di halaman preview
- siap untuk dicetak

---

### 3.2 Cetak Label QR

Fitur:
- template sederhana label (ukuran kecil & sedang)
- menampilkan:
  - QR code
  - nama aset singkat
  - kode aset
- opsi cetak massal untuk beberapa aset sekaligus

Output: halaman siap print (CSS print‑friendly).

---

### 3.3 Scan QR dari HP

Menggunakan kamera HP (web browser):

- buka halaman **Scan QR**
- arahkan kamera ke QR
- otomatis redirect ke halaman detail aset

Fallback: manual input kode jika kamera gagal.

---

### 3.4 Log Scan (Opsional, MVP V5 Lite)

Mencatat:
- siapa scan (jika login)
- kapan scan
- tujuan (audit / pengecekan)

Tujuan: mendukung audit di masa depan.

> Boleh diaktifkan sebagai opsi — bukan wajib.

---

## 4️⃣ Out of Scope (V5)

❌ barcode 1D  
❌ NFC tag  
❌ sistem audit penuh  
❌ mode offline penuh  
❌ cetak otomatis ke printer khusus

---

## 5️⃣ Perubahan Database

### items (tambahan)
- qr_code_key (unique, string)

Digunakan untuk membentuk URL aman.

### scan_logs (opsional)
- id
- item_id
- user_id (nullable)
- scanned_at (timestamp)
- notes (nullable)

---

## 6️⃣ Alur Penggunaan

1️⃣ admin menambahkan / memilih aset  
2️⃣ generate QR  
3️⃣ cetak & tempel ke aset  
4️⃣ di lapangan — scan → langsung ke detail  
5️⃣ (opsional) tercatat di log

---

## 7️⃣ Pertimbangan Keamanan

- QR **tidak** menyimpan data sensitif
- gunakan key acak (bukan auto‑increment id)
- proteksi role tetap berlaku saat membuka halaman detail
- batasi percobaan brute force key

---

## 8️⃣ Requirements Teknis

- library QR generator (server side)
- halaman scan berbasis WebRTC camera
- CSS print‑friendly
- kompatibel Android & iOS browser

Testing di:
- Chrome Android
- Edge/Chrome desktop

---

## 9️⃣ Acceptance Criteria

- setiap aset memiliki QR unik
- QR bisa dicetak dan ditempel
- scan dari HP langsung membuka detail aset
- tidak ada data sensitif di QR
- performa tetap ringan

---

## 🔜 Next Step

1️⃣ review kebutuhan pengurus  
2️⃣ finalize desain teknis  
3️⃣ implementasi bertahap  
4️⃣ uji lapangan (uji scan di berbagai kondisi)

---

