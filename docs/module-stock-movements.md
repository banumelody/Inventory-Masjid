# 📊 Module: Mutasi Stok (Stock Movements)

## 1. Tujuan Modul
Mencatat setiap perubahan stok dengan alasan yang jelas untuk audit trail.

## 2. Flow Diagram

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│ Pilih Barang│────▶│ Pilih Jenis │────▶│ Isi Detail  │
└─────────────┘     │ (In/Out)    │     │ Mutasi      │
                    └─────────────┘     └──────┬──────┘
                                               │
                         ┌─────────────────────┤
                         │                     │
                         ▼                     ▼
                    ┌─────────┐          ┌─────────┐
                    │ Type=In │          │Type=Out │
                    │ +qty    │          │ -qty    │
                    └────┬────┘          └────┬────┘
                         │                    │
                         ▼                    ▼
              ┌──────────────────┐  ┌──────────────────┐
              │ items.quantity++ │  │ items.quantity-- │
              └──────────────────┘  └──────────────────┘
                         │                    │
                         └────────┬───────────┘
                                  ▼
                    ┌──────────────────────────┐
                    │ Record in stock_movements│
                    └──────────────────────────┘
```

## 3. Struktur Database

### Table: `stock_movements`
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| item_id | bigint | FK to items |
| type | enum('in','out') | Jenis mutasi |
| quantity | int | Jumlah |
| reason | varchar(255) | Alasan |
| moved_at | date | Tanggal mutasi |
| notes | text | Catatan (nullable) |
| created_at | timestamp | |
| updated_at | timestamp | |

## 4. Controller & Endpoints

### StockMovementController

| Method | Endpoint | Action | Permission |
|--------|----------|--------|------------|
| GET | /stock-movements | index | All |
| GET | /stock-movements/create | create | Admin, Operator |
| POST | /stock-movements | store | Admin, Operator |
| GET | /stock-movements/item/{id} | itemHistory | All |

### Validation Rules

```php
'item_id' => 'required|exists:items,id',
'type' => 'required|in:in,out',
'quantity' => 'required|integer|min:1',
'reason' => 'required|string|max:255',
'moved_at' => 'required|date',
'notes' => 'nullable|string',
```

## 5. State & Error Handling

### Type Labels
- **in**: "Masuk" (green)
- **out**: "Keluar" (red)

### Preset Reasons
- Pembelian
- Donasi Masuk
- Penyesuaian Stok
- Barang Rusak
- Barang Hilang
- Donasi Keluar
- Lainnya

### Errors
| Condition | Message |
|-----------|---------|
| out > current stock | "Jumlah melebihi stok saat ini (X unit)" |

## 6. Business Rules

1. ❌ Tidak boleh mutasi keluar lebih dari stok saat ini
2. ✅ Quantity pada items langsung ter-update (sync)
3. ✅ Setiap mutasi tercatat sebagai history
4. ✅ Bisa filter per barang untuk melihat riwayat

## 7. Integration

Stock movements terintegrasi dengan:
- **Items**: Auto update quantity
- **Dashboard**: Recent movements
- **Item Detail**: Movement history
