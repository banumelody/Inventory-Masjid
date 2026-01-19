# 📚 Technical Documentation - Inventory Masjid

## Daftar Modul

1. [Peminjaman Barang](./module-loans.md)
2. [Mutasi Stok](./module-stock-movements.md)
3. [User & Role](./module-users.md)
4. [Backup](./module-backup.md)
5. [Export](./module-export.md)
6. [QR Code & Scan](./module-qrcode.md) ✨ **V5**

---

## Arsitektur Aplikasi

### Stack
- **Backend**: Laravel 10 (PHP 8.2)
- **Frontend**: Blade + Tailwind CSS (CDN)
- **Database**: MariaDB 10.11
- **Web Server**: Nginx
- **Container**: Docker Compose

### Struktur Database

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│  categories │     │  locations  │     │    roles    │
├─────────────┤     ├─────────────┤     ├─────────────┤
│ id          │     │ id          │     │ id          │
│ name        │     │ name        │     │ name        │
│ timestamps  │     │ timestamps  │     │ display_name│
└──────┬──────┘     └──────┬──────┘     └──────┬──────┘
       │                   │                   │
       │                   │                   │
       ▼                   ▼                   ▼
┌─────────────────────────────────┐     ┌─────────────┐
│            items                │     │    users    │
├─────────────────────────────────┤     ├─────────────┤
│ id                              │     │ id          │
│ name                            │     │ name        │
│ category_id (FK)                │     │ email       │
│ location_id (FK)                │     │ password    │
│ quantity                        │     │ role_id (FK)│
│ unit                            │     │ timestamps  │
│ condition                       │     └─────────────┘
│ note                            │
│ photo_path                      │
│ qr_code_key ✨                  │
│ timestamps                      │
└───────────┬─────────────────────┘
            │
    ┌───────┼───────┐
    │       │       │
    ▼       ▼       ▼
┌─────────┐ ┌───────────────┐ ┌───────────┐
│  loans  │ │stock_movements│ │ scan_logs │
├─────────┤ ├───────────────┤ ├───────────┤
│ id      │ │ id            │ │ id        │
│ item_id │ │ item_id (FK)  │ │ item_id   │
│borrower │ │ type (in/out) │ │ user_id   │
│quantity │ │ quantity      │ │ purpose ✨│
│borrowed │ │ reason        │ │ notes     │
│ due_at  │ │ moved_at      │ │scanned_at │
│returned │ │ notes         │ │ ip_address│
│timestamp│ │ timestamps    │ │ timestamps│
└─────────┘ └───────────────┘ └───────────┘
```

### Flow Aplikasi

```
Login → Dashboard → [Inventaris|Peminjaman|Mutasi|Laporan|...]
                          ↓
                    CRUD Operations
                          ↓
                    Database Update
```

---

## Role & Permission

| Permission | Admin | Operator | Viewer |
|------------|:-----:|:--------:|:------:|
| View items | ✅ | ✅ | ✅ |
| Create/Edit items | ✅ | ✅ | ❌ |
| Delete items | ✅ | ❌ | ❌ |
| Manage loans | ✅ | ✅ | ❌ |
| Manage stock | ✅ | ✅ | ❌ |
| Export data | ✅ | ✅ | ✅ |
| Manage users | ✅ | ❌ | ❌ |
| Manage backups | ✅ | ❌ | ❌ |
| QR Code scan | ✅ | ✅ | ✅ |
| QR Audit scan | ✅ | ✅ | ❌ |
| Generate/Print QR | ✅ | ✅ | ❌ |
| View scan logs | ✅ | ❌ | ❌ |

---

## API Endpoints

### Items
- `GET /items` - List items
- `GET /items/{id}` - Show item
- `POST /items` - Create item
- `PUT /items/{id}` - Update item
- `DELETE /items/{id}` - Delete item

### Loans
- `GET /loans` - List loans
- `POST /loans` - Create loan
- `POST /loans/{id}/return` - Return loan

### Stock Movements
- `GET /stock-movements` - List movements
- `POST /stock-movements` - Create movement

### QR Code (V5)
- `GET /scan` - Scan page
- `GET /scan/audit` - Audit scan page
- `GET /i/{key}` - Public redirect from QR scan
- `POST /i/{key}/audit` - Scan with purpose
- `POST /items/{id}/qr/generate` - Generate QR
- `GET /items/{id}/qr/print` - Print label
- `GET /qr/bulk` - Bulk print form
- `GET /scan-logs` - Scan logs (admin)
- `GET /scan-logs/export` - Export scan logs

### Export
- `GET /export/excel` - Export CSV
- `GET /export/pdf` - Export PDF

---

## Error Handling

### Validation Errors
- Returned as session flash with error messages
- Displayed in red alert box

### Business Logic Errors
- Stock insufficient: "Jumlah melebihi stok tersedia"
- Delete protected: "Tidak dapat dihapus karena masih digunakan"

---

## Testing

### Unit Tests
```bash
docker-compose exec app php artisan test --filter=Unit
```

### Feature Tests
```bash
docker-compose exec app php artisan test --filter=Feature
```

### All Tests
```bash
docker-compose exec app php artisan test
```
