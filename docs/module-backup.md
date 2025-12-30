# 💾 Module: Backup

## 1. Tujuan Modul
Menjamin keberlangsungan data dengan backup otomatis dan manual.

## 2. Flow Diagram

```
┌──────────────────────┐
│ Automatic (Scheduler)│
│ Daily at 02:00       │
└──────────┬───────────┘
           │
           ▼
┌──────────────────────┐     ┌──────────────────────┐
│   Manual Trigger     │────▶│     mysqldump        │
│   (Admin clicks)     │     │     + gzip           │
└──────────────────────┘     └──────────┬───────────┘
                                        │
                                        ▼
                             ┌──────────────────────┐
                             │ storage/app/backups/ │
                             │ backup_YYYY-MM-DD_   │
                             │ HHmmss.sql.gz        │
                             └──────────┬───────────┘
                                        │
                                        ▼
                             ┌──────────────────────┐
                             │ Record in backups    │
                             │ table                │
                             └──────────────────────┘
```

## 3. Struktur Database

### Table: `backups`
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| filename | varchar | Nama file |
| path | varchar | Path relatif |
| size | bigint | Ukuran dalam bytes |
| created_at | timestamp | |
| updated_at | timestamp | |

## 4. Controller & Endpoints

### BackupController (Admin only)

| Method | Endpoint | Action |
|--------|----------|--------|
| GET | /backups | index |
| POST | /backups | create |
| GET | /backups/{id}/download | download |
| DELETE | /backups/{id} | destroy |

## 5. Artisan Command

### backup:database
```bash
php artisan backup:database
```

Creates a compressed database backup.

## 6. Scheduler Setup

### Console/Kernel.php
```php
protected function schedule(Schedule $schedule): void
{
    $schedule->command('backup:database')->dailyAt('02:00');
}
```

### Cron Setup (Production)
```bash
* * * * * cd /path/to/project/src && php artisan schedule:run >> /dev/null 2>&1
```

## 7. Retention Policy

- Default: 30 days
- Old backups automatically deleted
- Cleanup runs after each new backup

## 8. Storage

### Path Structure
```
storage/
└── app/
    └── backups/
        ├── backup_2024-01-01_020000.sql.gz
        ├── backup_2024-01-02_020000.sql.gz
        └── ...
```

## 9. Security Notes

- Backups stored outside public directory
- Only admin can access
- Direct download via authenticated route
