# 📤 Module: Peminjaman Barang (Loans)

## 1. Tujuan Modul
Mencatat peminjaman barang masjid agar tidak hilang tanpa catatan.

## 2. Flow Diagram

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│ Pilih Barang│────▶│ Isi Form    │────▶│ Validasi    │
└─────────────┘     │ Peminjaman  │     │ Stok        │
                    └─────────────┘     └──────┬──────┘
                                               │
                         ┌─────────────────────┤
                         │                     │
                         ▼                     ▼
                    ┌─────────┐          ┌─────────┐
                    │ Sukses  │          │ Gagal   │
                    │ Simpan  │          │ (stok   │
                    └────┬────┘          │ kurang) │
                         │               └─────────┘
                         ▼
              ┌──────────────────┐
              │ Status: Dipinjam │
              └────────┬─────────┘
                       │
                       ▼ (saat dikembalikan)
              ┌──────────────────┐
              │ Form Pengembalian│
              └────────┬─────────┘
                       │
                       ▼
              ┌──────────────────┐
              │ Status: Kembali  │
              └──────────────────┘
```

## 3. Struktur Database

### Table: `loans`
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| item_id | bigint | FK to items |
| borrower_name | varchar(255) | Nama peminjam |
| borrower_phone | varchar(50) | No HP (nullable) |
| quantity | int | Jumlah dipinjam |
| borrowed_at | date | Tanggal pinjam |
| due_at | date | Jatuh tempo (nullable) |
| returned_at | date | Tanggal kembali (nullable) |
| returned_condition | varchar | Kondisi saat kembali |
| notes | text | Catatan (nullable) |
| created_at | timestamp | |
| updated_at | timestamp | |

## 4. Controller & Endpoints

### LoanController

| Method | Endpoint | Action | Permission |
|--------|----------|--------|------------|
| GET | /loans | index | All |
| GET | /loans/create | create | Admin, Operator |
| POST | /loans | store | Admin, Operator |
| GET | /loans/{id} | show | All |
| GET | /loans/{id}/return | returnForm | Admin, Operator |
| POST | /loans/{id}/return | returnItem | Admin, Operator |
| DELETE | /loans/{id} | destroy | Admin |

### Validation Rules

**store:**
```php
'item_id' => 'required|exists:items,id',
'borrower_name' => 'required|string|max:255',
'borrower_phone' => 'nullable|string|max:50',
'quantity' => 'required|integer|min:1',
'borrowed_at' => 'required|date',
'due_at' => 'nullable|date|after_or_equal:borrowed_at',
'notes' => 'nullable|string',
```

**returnItem:**
```php
'returned_at' => 'required|date|after_or_equal:borrowed_at',
'returned_condition' => 'required|in:baik,perlu_perbaikan,rusak',
'notes' => 'nullable|string',
```

## 5. State & Error Handling

### Status
- **Dipinjam**: `returned_at IS NULL`
- **Sudah Kembali**: `returned_at IS NOT NULL`
- **Terlambat**: `returned_at IS NULL AND due_at < NOW()`

### Errors
| Condition | Message |
|-----------|---------|
| quantity > available | "Jumlah melebihi stok tersedia (X unit)" |
| Already returned | "Barang sudah dikembalikan sebelumnya" |

## 6. Business Rules

1. ❌ Tidak boleh meminjam lebih dari stok tersedia
2. ✅ Stok berkurang saat dipinjam (hanya di available_quantity)
3. ✅ Stok kembali saat dikembalikan (otomatis)
4. ⚠️ Warning visual untuk yang melewati jatuh tempo
