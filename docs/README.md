# 📚 Technical Documentation - Inventory Masjid

## Daftar Modul

1. [Peminjaman Barang](./module-loans.md)
2. [Mutasi Stok](./module-stock-movements.md)
3. [User & Role](./module-users.md)
4. [Backup](./module-backup.md)
5. [Export](./module-export.md)

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
│ timestamps                      │
└───────────┬─────────────────────┘
            │
    ┌───────┴───────┐
    │               │
    ▼               ▼
┌─────────────┐  ┌─────────────────┐
│   loans     │  │ stock_movements │
├─────────────┤  ├─────────────────┤
│ id          │  │ id              │
│ item_id(FK) │  │ item_id (FK)    │
│ borrower_*  │  │ type (in/out)   │
│ quantity    │  │ quantity        │
│ borrowed_at │  │ reason          │
│ due_at      │  │ moved_at        │
│ returned_at │  │ notes           │
│ timestamps  │  │ timestamps      │
└─────────────┘  └─────────────────┘
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
