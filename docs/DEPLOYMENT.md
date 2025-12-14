# 🚀 Deployment Guide

Panduan lengkap untuk men-deploy SIAKAD ke production environment.

## 📋 Table of Contents

- [Prerequisites](#prerequisites)
- [Server Setup](#server-setup)
- [Database Setup](#database-setup)
- [Application Deployment](#application-deployment)
- [Performance Optimization](#performance-optimization)
- [Monitoring](#monitoring)
- [Troubleshooting](#troubleshooting)

---

## Prerequisites

### Minimum Server Requirements

| Component | Minimum | Recommended |
|-----------|---------|-------------|
| CPU | 2 cores | 4 cores |
| RAM | 2 GB | 4 GB |
| Storage | 20 GB SSD | 50 GB SSD |
| OS | Ubuntu 22.04 | Ubuntu 24.04 |

### Required Software

```bash
# PHP 8.2 with extensions
php8.2 php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml 
php8.2-bcmath php8.2-curl php8.2-zip php8.2-gd php8.2-redis

# Database
mysql-server-8.0

# Web Server
nginx

# Node.js (for asset building)
nodejs >= 18.x
npm >= 9.x

# Process Manager
supervisor

# Optional (Recommended)
redis-server
```

---

## Server Setup

### 1. Install Dependencies

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.2
sudo add-apt-repository ppa:ondrej/php -y
sudo apt install php8.2 php8.2-fpm php8.2-mysql php8.2-mbstring \
    php8.2-xml php8.2-bcmath php8.2-curl php8.2-zip php8.2-gd php8.2-redis -y

# Install MySQL
sudo apt install mysql-server-8.0 -y

# Install Nginx
sudo apt install nginx -y

# Install Node.js 20
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Supervisor
sudo apt install supervisor -y

# Install Redis (optional but recommended)
sudo apt install redis-server -y
```

### 2. Configure Nginx

```nginx
# /etc/nginx/sites-available/siakad
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com;
    root /var/www/siakad/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Gzip compression
    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml;
}
```

```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/siakad /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 3. SSL Certificate (Let's Encrypt)

```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d yourdomain.com
```

---

## Database Setup

### 1. Create Database and User

```sql
CREATE DATABASE siakad CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'siakad'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON siakad.* TO 'siakad'@'localhost';
FLUSH PRIVILEGES;
```

### 2. Optimize MySQL for Production

```ini
# /etc/mysql/mysql.conf.d/mysqld.cnf
[mysqld]
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
innodb_flush_log_at_trx_commit = 2
innodb_flush_method = O_DIRECT
max_connections = 200
query_cache_type = 0
```

---

## Application Deployment

### 1. Clone and Setup

```bash
# Create directory
sudo mkdir -p /var/www/siakad
sudo chown -R $USER:www-data /var/www/siakad

# Clone repository
git clone https://github.com/yourusername/siakad.git /var/www/siakad
cd /var/www/siakad

# Install dependencies
composer install --optimize-autoloader --no-dev
npm ci && npm run build
```

### 2. Configure Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` untuk production:

```env
APP_NAME=SIAKAD
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=siakad
DB_USERNAME=siakad
DB_PASSWORD=strong_password_here

CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
```

### 3. Run Migrations

```bash
php artisan migrate --force
php artisan db:seed --force  # Only on first deploy
```

### 4. Optimize for Production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### 5. Set Permissions

```bash
sudo chown -R www-data:www-data /var/www/siakad/storage
sudo chown -R www-data:www-data /var/www/siakad/bootstrap/cache
sudo chmod -R 775 /var/www/siakad/storage
sudo chmod -R 775 /var/www/siakad/bootstrap/cache
```

---

## Queue Worker Setup

### Supervisor Configuration

```ini
# /etc/supervisor/conf.d/siakad-worker.conf
[program:siakad-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/siakad/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/siakad/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start siakad-worker:*
```

---

## Performance Optimization

### 1. Enable OPcache

```ini
# /etc/php/8.2/fpm/conf.d/10-opcache.ini
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
opcache.validate_timestamps=0
opcache.save_comments=1
```

### 2. Redis Configuration

```bash
# /etc/redis/redis.conf
maxmemory 512mb
maxmemory-policy allkeys-lru
```

---

## Monitoring

### Health Check Endpoints

| Endpoint | Purpose |
|----------|---------|
| `GET /health` | Basic health status |
| `GET /health/detailed` | DB, Cache, Storage check |
| `GET /up` | Laravel's built-in health |

### Recommended Monitoring Stack

- **APM**: Laravel Telescope (dev) / Sentry (production)
- **Logs**: Papertrail / Logtail
- **Uptime**: UptimeRobot / Pingdom
- **Metrics**: Laravel Pulse / Prometheus

---

## Backup Strategy

### Database Backup Script

```bash
#!/bin/bash
# /usr/local/bin/backup-siakad.sh
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_DIR="/var/backups/siakad"
mkdir -p $BACKUP_DIR

mysqldump -u siakad -p'password' siakad | gzip > "$BACKUP_DIR/db_$TIMESTAMP.sql.gz"

# Keep only last 7 days
find $BACKUP_DIR -type f -mtime +7 -delete
```

### Cron Schedule

```bash
# Daily backup at 2 AM
0 2 * * * /usr/local/bin/backup-siakad.sh
```

---

## Zero-Downtime Deployment

```bash
#!/bin/bash
# deploy.sh
cd /var/www/siakad

# Pull latest changes
git pull origin main

# Install dependencies
composer install --optimize-autoloader --no-dev

# Build assets
npm ci && npm run build

# Run migrations
php artisan migrate --force

# Clear and cache
php artisan optimize:clear
php artisan optimize

# Restart queue workers
sudo supervisorctl restart siakad-worker:*

# Reload PHP-FPM
sudo systemctl reload php8.2-fpm
```

---

## Troubleshooting

### Common Issues

| Issue | Solution |
|-------|----------|
| 500 Error | Check `storage/logs/laravel.log` |
| Permission denied | Run permission commands above |
| Queue not processing | Check Supervisor status |
| Slow queries | Enable query logging, add indexes |

### Useful Commands

```bash
# View logs
tail -f /var/www/siakad/storage/logs/laravel.log

# Check queue status
php artisan queue:monitor redis:default

# Clear all caches
php artisan optimize:clear

# Check failed jobs
php artisan queue:failed
```

---

## Rollback Procedure

```bash
# 1. Revert to previous version
git checkout <previous-commit-hash>

# 2. Reinstall dependencies
composer install --optimize-autoloader --no-dev

# 3. Rollback migrations (if needed)
php artisan migrate:rollback --step=1

# 4. Rebuild caches
php artisan optimize
```
