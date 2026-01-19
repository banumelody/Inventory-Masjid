# 🕌 Inventory Masjid - Panduan Deployment Production

> **Versi:** 5.0.0 (QR Label & Scan)  
> **Terakhir diperbarui:** Januari 2026

---

## 📋 Daftar Isi

1. [Prasyarat](#prasyarat)
2. [Metode Deployment](#metode-deployment)
3. [Deployment dengan Docker (Rekomendasi)](#deployment-dengan-docker-rekomendasi)
4. [Deployment Manual (VPS/Shared Hosting)](#deployment-manual-vpsshared-hosting)
5. [Konfigurasi Post-Deployment](#konfigurasi-post-deployment)
6. [SSL/HTTPS Setup](#sslhttps-setup)
7. [Maintenance & Update](#maintenance--update)
8. [Backup & Restore](#backup--restore)
9. [Troubleshooting](#troubleshooting)
10. [Security Checklist](#security-checklist)

---

## Prasyarat

### Minimum Server Requirements
- **CPU:** 1 Core
- **RAM:** 1 GB (2 GB recommended)
- **Storage:** 10 GB
- **OS:** Ubuntu 20.04+ / Debian 11+ / CentOS 8+

### Software Requirements

#### Untuk Docker Deployment:
- Docker 20.10+
- Docker Compose 2.0+

#### Untuk Manual Deployment:
- PHP 8.1+ dengan extensions:
  - BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML, GD/Imagick
- Composer 2.x
- MySQL 8.0+ / MariaDB 10.6+
- Nginx atau Apache
- Git

---

## Metode Deployment

| Metode | Kelebihan | Cocok Untuk |
|--------|-----------|-------------|
| **Docker** | Mudah, konsisten, isolated | VPS, Cloud Server |
| **Manual** | Kontrol penuh, shared hosting | Shared Hosting, VPS |

---

## Deployment dengan Docker (Rekomendasi)

### Langkah 1: Clone Repository

```bash
# Clone repository
git clone https://github.com/banumelody/Inventory-Masjid.git
cd inventory-masjid
```

### Langkah 2: Konfigurasi Environment

```bash
# Copy template environment
cp src/.env.example src/.env

# Edit konfigurasi
nano src/.env
```

**Ubah nilai berikut di `src/.env`:**

```env
# Application
APP_NAME="Inventory Masjid Al-Ikhlas"  # Nama masjid Anda
APP_ENV=production
APP_DEBUG=false
APP_URL=https://inventory.masjid-anda.com

# Database (sesuaikan dengan .env docker)
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=inventory_masjid
DB_USERNAME=inventory_user
DB_PASSWORD=GantiDenganPasswordKuat123!

# Session & Cache
SESSION_DRIVER=database
CACHE_DRIVER=file
```

### Langkah 3: Konfigurasi Docker Environment

```bash
# Buat file .env untuk docker-compose
cat > .env << 'EOF'
# Database Configuration
DB_DATABASE=inventory_masjid
DB_USERNAME=inventory_user
DB_PASSWORD=GantiDenganPasswordKuat123!
DB_ROOT_PASSWORD=RootPasswordKuat456!

# Port (default 80, ubah jika konflik)
APP_PORT=80
EOF
```

### Langkah 4: Generate App Key

```bash
# Generate application key
docker compose -f docker-compose.prod.yml run --rm app php artisan key:generate
```

### Langkah 5: Jalankan Aplikasi

```bash
# Build dan jalankan containers
docker compose -f docker-compose.prod.yml up -d

# Tunggu database siap (±30 detik)
sleep 30

# Cek status
docker compose -f docker-compose.prod.yml ps
```

### Langkah 6: Inisialisasi Database

```bash
# Jalankan migrasi
docker compose -f docker-compose.prod.yml exec app php artisan migrate --force

# Seed data awal (roles, admin, settings)
docker compose -f docker-compose.prod.yml exec app php artisan db:seed --force

# Buat symbolic link untuk storage
docker compose -f docker-compose.prod.yml exec app php artisan storage:link
```

### Langkah 7: Optimasi Production

```bash
# Cache konfigurasi untuk performa
docker compose -f docker-compose.prod.yml exec app php artisan config:cache
docker compose -f docker-compose.prod.yml exec app php artisan route:cache
docker compose -f docker-compose.prod.yml exec app php artisan view:cache

# Set permission
docker compose -f docker-compose.prod.yml exec app chown -R www-data:www-data storage bootstrap/cache
docker compose -f docker-compose.prod.yml exec app chmod -R 775 storage bootstrap/cache
```

### Langkah 8: Verifikasi

```bash
# Test akses
curl -I http://localhost

# Atau buka di browser
# http://ip-server-anda
```

---

## Deployment Manual (VPS/Shared Hosting)

### Langkah 1: Install Dependencies

```bash
# Ubuntu/Debian
sudo apt update
sudo apt install -y php8.1-fpm php8.1-cli php8.1-mysql php8.1-mbstring \
    php8.1-xml php8.1-curl php8.1-gd php8.1-zip php8.1-bcmath \
    nginx mariadb-server git unzip

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### Langkah 2: Setup Database

```bash
# Login ke MySQL
sudo mysql -u root

# Buat database dan user
CREATE DATABASE inventory_masjid CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'inventory_user'@'localhost' IDENTIFIED BY 'PasswordKuat123!';
GRANT ALL PRIVILEGES ON inventory_masjid.* TO 'inventory_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Langkah 3: Clone & Setup Aplikasi

```bash
# Clone ke direktori web
cd /var/www
git clone https://github.com/banumelody/Inventory-Masjid.git
cd inventory-masjid/src

# Install dependencies
composer install --optimize-autoloader --no-dev

# Setup environment
cp .env.example .env
nano .env  # Edit sesuai konfigurasi
```

### Langkah 4: Konfigurasi `.env`

```env
APP_NAME="Inventory Masjid"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://inventory.masjid-anda.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventory_masjid
DB_USERNAME=inventory_user
DB_PASSWORD=PasswordKuat123!

SESSION_DRIVER=database
CACHE_DRIVER=file
```

### Langkah 5: Inisialisasi Aplikasi

```bash
# Generate key
php artisan key:generate

# Migrasi database
php artisan migrate --force

# Seed data
php artisan db:seed --force

# Storage link
php artisan storage:link

# Optimasi
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
sudo chown -R www-data:www-data /var/www/inventory-masjid
sudo chmod -R 755 /var/www/inventory-masjid
sudo chmod -R 775 /var/www/inventory-masjid/src/storage
sudo chmod -R 775 /var/www/inventory-masjid/src/bootstrap/cache
```

### Langkah 6: Konfigurasi Nginx

```bash
sudo nano /etc/nginx/sites-available/inventory-masjid
```

```nginx
server {
    listen 80;
    server_name inventory.masjid-anda.com;
    root /var/www/inventory-masjid/src/public;

    index index.php;
    charset utf-8;

    # Gzip compression
    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Upload limit
    client_max_body_size 20M;
}
```

```bash
# Aktifkan site
sudo ln -s /etc/nginx/sites-available/inventory-masjid /etc/nginx/sites-enabled/

# Test & restart
sudo nginx -t
sudo systemctl restart nginx
```

---

## Konfigurasi Post-Deployment

### Login Pertama

1. Buka aplikasi di browser: `http://ip-server` atau `https://domain-anda.com`
2. Login dengan akun default:
   - **Email:** `admin@masjid.local`
   - **Password:** `admin123`

### ⚠️ PENTING: Segera Lakukan

1. **Ganti Password Admin**
   - Klik nama user di sidebar → Edit profil → Ganti password

2. **Kustomisasi Aplikasi** (Menu: Pengaturan)
   - Upload logo masjid
   - Ubah nama aplikasi
   - Isi informasi organisasi (nama masjid, alamat, telepon)
   - Isi visi & misi (opsional)

3. **Tambah User** (Menu: Pengguna)
   - Buat akun untuk operator/pengurus lain
   - Jangan share akun admin

---

## SSL/HTTPS Setup

### Opsi 1: Let's Encrypt dengan Certbot

```bash
# Install certbot
sudo apt install certbot python3-certbot-nginx

# Generate SSL certificate
sudo certbot --nginx -d inventory.masjid-anda.com

# Auto-renewal (sudah otomatis via cron)
sudo certbot renew --dry-run
```

### Opsi 2: Cloudflare (Gratis)

1. Daftarkan domain di Cloudflare
2. Ubah nameserver ke Cloudflare
3. Aktifkan SSL mode "Full" atau "Full (Strict)"
4. Aktifkan "Always Use HTTPS"

### Update APP_URL

Setelah SSL aktif, update `src/.env`:
```env
APP_URL=https://inventory.masjid-anda.com
```

Lalu clear cache:
```bash
# Docker
docker compose -f docker-compose.prod.yml exec app php artisan config:cache

# Manual
php artisan config:cache
```

---

## Maintenance & Update

### Update Aplikasi

```bash
# Docker
cd /path/to/inventory-masjid
git pull origin main
docker compose -f docker-compose.prod.yml exec app composer install --no-dev
docker compose -f docker-compose.prod.yml exec app php artisan migrate --force
docker compose -f docker-compose.prod.yml exec app php artisan optimize:clear
docker compose -f docker-compose.prod.yml exec app php artisan config:cache
docker compose -f docker-compose.prod.yml exec app php artisan route:cache
docker compose -f docker-compose.prod.yml exec app php artisan view:cache

# Manual
cd /var/www/inventory-masjid/src
git pull origin main
composer install --no-dev
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Restart Services

```bash
# Docker
docker compose -f docker-compose.prod.yml restart

# Manual
sudo systemctl restart php8.1-fpm
sudo systemctl restart nginx
```

### Lihat Logs

```bash
# Docker
docker compose -f docker-compose.prod.yml logs -f app
docker compose -f docker-compose.prod.yml logs -f --tail=100

# Manual
tail -f /var/www/inventory-masjid/src/storage/logs/laravel.log
```

---

## Backup & Restore

### Backup Database

```bash
# Docker
docker compose -f docker-compose.prod.yml exec db mariadb-dump -u inventory_user -p inventory_masjid > backup_$(date +%Y%m%d_%H%M%S).sql

# Manual
mysqldump -u inventory_user -p inventory_masjid > backup_$(date +%Y%m%d_%H%M%S).sql
```

### Backup Files (Upload)

```bash
# Backup folder storage
tar -czvf storage_backup_$(date +%Y%m%d).tar.gz src/storage/app/public/
```

### Restore Database

```bash
# Docker
docker compose -f docker-compose.prod.yml exec -T db mariadb -u inventory_user -p inventory_masjid < backup_file.sql

# Manual
mysql -u inventory_user -p inventory_masjid < backup_file.sql
```

### Automated Backup (Cron)

```bash
# Edit crontab
crontab -e

# Tambahkan (backup setiap hari jam 2 pagi)
0 2 * * * cd /path/to/inventory-masjid && docker compose -f docker-compose.prod.yml exec -T db mariadb-dump -u inventory_user -pPasswordAnda inventory_masjid > /path/to/backups/backup_$(date +\%Y\%m\%d).sql
```

---

## Troubleshooting

### Error 500 / Blank Page

```bash
# Cek log error
tail -50 src/storage/logs/laravel.log

# Fix permission
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Clear cache
php artisan optimize:clear
```

### Database Connection Error

```bash
# Cek status database
# Docker
docker compose -f docker-compose.prod.yml ps
docker compose -f docker-compose.prod.yml logs db

# Manual
sudo systemctl status mariadb
```

### Upload Gagal

```bash
# Cek permission storage
ls -la src/storage/app/public/

# Cek upload limit di php.ini
php -i | grep upload_max_filesize
php -i | grep post_max_size

# Cek nginx limit
# Pastikan ada: client_max_body_size 20M;
```

### Session/Login Bermasalah

```bash
# Clear session
php artisan session:clear

# Atau hapus manual
rm -rf src/storage/framework/sessions/*

# Rebuild cache
php artisan config:cache
```

---

## Security Checklist

### Wajib ✅

- [ ] `APP_DEBUG=false` di production
- [ ] `APP_ENV=production`
- [ ] Password database kuat (min. 16 karakter, kombinasi huruf/angka/simbol)
- [ ] Password admin sudah diganti dari default
- [ ] HTTPS/SSL aktif
- [ ] Port database tidak exposed ke public (hanya localhost)
- [ ] File `.env` tidak bisa diakses dari web

### Recommended 🔒

- [ ] Firewall aktif (UFW/iptables)
- [ ] Fail2ban untuk proteksi brute force
- [ ] Regular backup otomatis
- [ ] Update OS dan packages secara berkala
- [ ] Monitoring server (uptime, disk space)

### Cek Keamanan

```bash
# Pastikan .env tidak bisa diakses
curl -I https://domain-anda.com/.env
# Harus return 403 atau 404

# Pastikan storage tidak bisa diakses langsung
curl -I https://domain-anda.com/storage/
# Harus return 403 atau redirect
```

---

## Bantuan

Jika mengalami kendala:

1. Cek dokumentasi: `docs/README.md`
2. Cek FAQ di aplikasi
3. Buka issue di GitHub repository
4. Hubungi maintainer

---

**Inventory Masjid v5.0.0** - Open Source Mosque Inventory Management System
