# Copilot Instructions — Inventory Masjid

## Project Overview

Mosque inventory management system (Laravel 10 + Blade + Tailwind CSS CDN + MariaDB). The Laravel app lives entirely inside `src/`. The repo root contains Docker configs, docs, and PRDs.

All UI text is in **Indonesian (Bahasa Indonesia)**. Locale is `id`, timezone is `Asia/Jakarta`, faker locale is `id_ID`. Keep all user-facing strings, labels, flash messages, and validation messages in Indonesian.

## Build & Run

```bash
# Start dev environment
docker-compose up -d --build

# First-time setup (inside container)
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan db:seed
docker-compose exec app php artisan storage:link

# Access at http://localhost:8085 (admin@masjid.local / password)
```

## Testing

```bash
# All tests
docker-compose exec app php artisan test

# Single test class
docker-compose exec app php artisan test --filter=ItemTest

# Single test method
docker-compose exec app php artisan test --filter=test_item_available_quantity_calculation

# Unit tests only
docker-compose exec app php artisan test --filter=Unit

# Feature tests only
docker-compose exec app php artisan test --filter=Feature
```

Tests use `RefreshDatabase` trait. Unit tests go in `src/tests/Unit/`, feature tests in `src/tests/Feature/`. Feature tests authenticate with `actingAs()` and assert redirects + session state.

## Architecture

### Docker Services

Three containers: `app` (PHP 8.2-FPM), `web` (Nginx), `db` (MariaDB 10.11). The `src/` directory is mounted at `/var/www` in the container. Nginx proxies PHP to FPM on port 9000.

### Role-Based Access Control

Three roles: **admin**, **operator**, **viewer**. Authorization uses a custom `CheckRole` middleware applied as `middleware('role:admin,operator')` on route groups. The `User` model has convenience methods: `isAdmin()`, `isOperator()`, `isViewer()`, `canEditItems()`, `canDeleteItems()`, etc.

Routes are organized in `src/routes/web.php` in nested groups:
- Public (no auth): welcome, help pages, QR scan redirect (`/i/{qrKey}`)
- Guest only: login, password reset
- Auth required: dashboard, items view, reports, exports, scan, feedback
- Auth + `role:admin,operator`: item CRUD, loans, stock, categories, locations, maintenances, QR generation
- Auth + `role:admin`: users, backups, imports, activity logs, scan logs, settings

### Key Domain Models

- **Item** — central model. Has `category_id`, `location_id`, `qr_code_key`. Computed accessors: `available_quantity` (quantity minus active loans), `borrowed_quantity`, `condition_label` (Indonesian). Condition enum: `baik`, `perlu_perbaikan`, `rusak`.
- **Loan** — tracks borrowing. Status derived from `returned_at` and `due_at`: 'Sudah Kembali', 'Terlambat', 'Dipinjam'. Has QR-based return flow via `return_qr_key`.
- **Maintenance** — equipment maintenance lifecycle with before/progress/after photos via `MaintenancePhoto`. Status workflow: `pending` → `in_progress` → `completed`/`cancelled`. Types: `perbaikan`, `perawatan`, `penggantian_part`.
- **Setting** — database-stored app config with cache. Access via static methods: `Setting::get('key')`, `Setting::set('key', 'value')`.
- **ActivityLog** — audit trail. Log actions with `ActivityLog::log()` static helper. Tracks old/new values, IP, user agent.

### QR Code System

Items get a unique `qr_code_key` (random hex via `bin2hex(random_bytes(12))`). Public scan URL: `/i/{qrKey}`. QR codes are generated server-side with `simplesoftwareio/simple-qrcode`. Labels print in two sizes: 60×35mm (small) and 80×45mm (medium). Bulk printing is supported.

## Conventions

### Controllers

Controllers use inline validation via `$this->validate()` (no Form Request classes). Pagination is 10 items per page. File uploads (photos) are stored via `Storage::disk('public')` and resized to max 1200px. Every create/update/delete action logs via `ActivityLog::log()`.

### Models

All models define `$fillable` arrays. Date fields use `$casts`. Computed properties use Laravel accessor convention: `get{Name}Attribute()`. Indonesian labels are returned by accessors like `condition_label`, `type_label`, `status_label`, with matching `_color` accessors for UI badges.

### Views

Two layouts: `layouts/app.blade.php` (authenticated, sidebar nav) and `layouts/public.blade.php` (guest pages). Styling is Tailwind CSS via CDN (no build step). PDF generation uses `barryvdh/laravel-dompdf`.

### Database

Migrations use restrictive foreign keys (`restrictOnDelete`). Status/type fields use string enums in migrations (not PHP enums). Seeders: use `ProductionSeeder` for production (`--class=ProductionSeeder`), `DatabaseSeeder` for development (includes sample items).

### File Structure

All application code is under `src/`. When referencing paths in artisan commands or composer, the working directory is `src/`. There is no `api.php` routes file — all routes are web-based with session auth.
