# 👥 Module: User & Role

## 1. Tujuan Modul
Mengontrol akses pengguna berdasarkan role untuk keamanan data.

## 2. Role Definitions

### Admin
- Semua akses
- Kelola user
- Kelola backup
- Hapus item
- Kelola feedback

### Operator
- CRUD barang (tidak bisa hapus)
- Kelola peminjaman
- Kelola mutasi stok
- Export data

### Viewer
- Lihat semua data
- Export data
- Kirim feedback

## 3. Struktur Database

### Table: `roles`
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| name | varchar | Unique key (admin/operator/viewer) |
| display_name | varchar | Nama tampilan |
| timestamps | | |

### Table: `users`
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| name | varchar(255) | Nama user |
| email | varchar(255) | Email (unique) |
| password | varchar | Hashed password |
| role_id | bigint | FK to roles |
| remember_token | varchar | |
| timestamps | | |

## 4. Controller & Endpoints

### AuthController
| Method | Endpoint | Action |
|--------|----------|--------|
| GET | /login | showLogin |
| POST | /login | login |
| POST | /logout | logout |

### UserController (Admin only)
| Method | Endpoint | Action |
|--------|----------|--------|
| GET | /users | index |
| GET | /users/create | create |
| POST | /users | store |
| GET | /users/{id}/edit | edit |
| PUT | /users/{id} | update |
| DELETE | /users/{id} | destroy |

## 5. Middleware

### CheckRole Middleware
```php
// Usage in routes
Route::middleware('role:admin,operator')->group(...)
```

### Permission Methods on User Model
```php
$user->isAdmin()
$user->isOperator()
$user->isViewer()
$user->canManageUsers()
$user->canDeleteItems()
$user->canEditItems()
$user->canManageLoans()
$user->canManageStock()
$user->canManageBackups()
```

## 6. Error Handling

| Condition | Action |
|-----------|--------|
| Unauthorized | 403 Forbidden |
| Wrong credentials | Redirect back with error |
| Delete self | Error: "Tidak dapat menghapus akun sendiri" |
| Delete last admin | Error: "Tidak dapat menghapus admin terakhir" |

## 7. Security Notes

- Passwords hashed with bcrypt
- Session-based authentication
- CSRF protection on all forms
- Remember me functionality
