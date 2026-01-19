# 🕌 Inventory Masjid - Deployment Guide

## Prerequisites
- Docker & Docker Compose
- Domain name (optional, for HTTPS)

## Quick Start (Production)

### 1. Clone & Setup
```bash
git clone <repository-url> inventory-masjid
cd inventory-masjid
```

### 2. Configure Environment
```bash
# Copy environment template
cp src/.env.example src/.env

# Edit configuration
nano src/.env
```

**Required changes in `.env`:**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
DB_PASSWORD=your_strong_password_here
```

### 3. Generate App Key
```bash
docker-compose -f docker-compose.prod.yml run --rm app php artisan key:generate
```

### 4. Start Services
```bash
# Create environment file for docker-compose
cat > .env << EOF
DB_DATABASE=inventory_masjid
DB_USERNAME=inventory_user
DB_PASSWORD=your_strong_password_here
DB_ROOT_PASSWORD=your_root_password_here
APP_PORT=80
EOF

# Start containers
docker-compose -f docker-compose.prod.yml up -d
```

### 5. Initialize Database
```bash
# Run migrations
docker-compose -f docker-compose.prod.yml exec app php artisan migrate --force

# Seed production data (roles + admin user + master data)
docker-compose -f docker-compose.prod.yml exec app php artisan db:seed --class=ProductionSeeder --force
```

### 6. Optimize for Production
```bash
docker-compose -f docker-compose.prod.yml exec app php artisan config:cache
docker-compose -f docker-compose.prod.yml exec app php artisan route:cache
docker-compose -f docker-compose.prod.yml exec app php artisan view:cache
docker-compose -f docker-compose.prod.yml exec app php artisan storage:link
```

### 7. Set Permissions
```bash
docker-compose -f docker-compose.prod.yml exec app chown -R www-data:www-data storage bootstrap/cache
docker-compose -f docker-compose.prod.yml exec app chmod -R 775 storage bootstrap/cache
```

## Default Login
- **Email:** `admin@masjid.local`
- **Password:** `admin123`

⚠️ **PENTING:** Segera ganti password setelah login pertama!

## Maintenance Commands

### Backup Database
```bash
docker-compose -f docker-compose.prod.yml exec db mysqldump -u inventory_user -p inventory_masjid > backup_$(date +%Y%m%d).sql
```

### View Logs
```bash
# Application logs
docker-compose -f docker-compose.prod.yml logs -f app

# All services
docker-compose -f docker-compose.prod.yml logs -f
```

### Update Application
```bash
# Pull latest code
git pull origin main

# Rebuild if needed
docker-compose -f docker-compose.prod.yml build

# Run migrations
docker-compose -f docker-compose.prod.yml exec app php artisan migrate --force

# Clear & rebuild cache
docker-compose -f docker-compose.prod.yml exec app php artisan optimize:clear
docker-compose -f docker-compose.prod.yml exec app php artisan config:cache
docker-compose -f docker-compose.prod.yml exec app php artisan route:cache
docker-compose -f docker-compose.prod.yml exec app php artisan view:cache

# Restart
docker-compose -f docker-compose.prod.yml restart
```

### Stop Services
```bash
docker-compose -f docker-compose.prod.yml down
```

## HTTPS Setup (Recommended)

For production, use a reverse proxy like Nginx or Traefik with Let's Encrypt SSL.

Example Nginx config (on host):
```nginx
server {
    listen 80;
    server_name inventory.masjid.id;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name inventory.masjid.id;

    ssl_certificate /etc/letsencrypt/live/inventory.masjid.id/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/inventory.masjid.id/privkey.pem;

    location / {
        proxy_pass http://127.0.0.1:8080;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

## Troubleshooting

### Permission Issues
```bash
docker-compose -f docker-compose.prod.yml exec app chmod -R 775 storage bootstrap/cache
```

### Database Connection Failed
Check if database container is running:
```bash
docker-compose -f docker-compose.prod.yml ps
docker-compose -f docker-compose.prod.yml logs db
```

### Clear All Cache
```bash
docker-compose -f docker-compose.prod.yml exec app php artisan optimize:clear
```

## Security Checklist

- [ ] APP_DEBUG=false
- [ ] APP_ENV=production
- [ ] Strong database passwords
- [ ] Changed default admin password
- [ ] HTTPS enabled
- [ ] Database port not exposed
- [ ] Regular backups configured
