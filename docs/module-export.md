# 📥 Module: Export

## 1. Tujuan Modul
Memudahkan distribusi dan pencetakan laporan inventaris.

## 2. Supported Formats

### CSV (Excel Compatible)
- UTF-8 with BOM for Excel compatibility
- Comma separated
- Quoted strings

### PDF
- Generated using DomPDF
- Print-ready layout
- Includes summary

## 3. Controller & Endpoints

### ExportController

| Method | Endpoint | Action | Permission |
|--------|----------|--------|------------|
| GET | /export | index | All |
| GET | /export/excel | excel | All |
| GET | /export/pdf | pdf | All |

## 4. Filter Support

Both exports support filtering by:
- Category (category_id)
- Location (location_id)

Example:
```
GET /export/excel?category_id=1&location_id=2
```

## 5. CSV Format

### Headers
```
No,Nama Barang,Kategori,Lokasi,Jumlah,Satuan,Kondisi,Catatan
```

### Sample Row
```
1,"Sajadah Merah","Peralatan Ibadah","Ruang Utama",50,"pcs","Baik","Warna merah maroon"
```

## 6. PDF Layout

```
┌────────────────────────────────────────────┐
│        🕌 LAPORAN INVENTARIS MASJID        │
│         Tanggal: DD/MM/YYYY                │
│    Kategori: X | Lokasi: Y                 │
├────────────────────────────────────────────┤
│ No │ Nama │ Kategori │ Lokasi │ Qty │ ... │
├────────────────────────────────────────────┤
│  1 │ ...  │   ...    │  ...   │ ... │ ... │
├────────────────────────────────────────────┤
│ Total Barang: X                            │
│ Kondisi Baik: X | Perlu Perbaikan: X | ... │
├────────────────────────────────────────────┤
│                          Mengetahui,       │
│                          ___________       │
│                          Pengurus Masjid   │
└────────────────────────────────────────────┘
```

## 7. Dependencies

- **DomPDF**: `barryvdh/laravel-dompdf`

## 8. File Naming

- Excel: `inventaris_YYYY-MM-DD.csv`
- PDF: `inventaris_YYYY-MM-DD.pdf`
