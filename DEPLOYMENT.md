# Production Deployment Guide

Panduan untuk deploy Laravel REST API ke production server.

## 📋 Prerequisites

- PHP >= 8.2
- Composer
- MySQL/PostgreSQL (untuk production)
- Web Server (Apache/Nginx)
- SSL Certificate (untuk HTTPS)

## 🚀 Deployment Steps

### 1. Clone Repository

```bash
git clone <your-repo-url>
cd project-1-angular-backend-laravel
```

### 2. Install Dependencies

```bash
composer install --optimize-autoloader --no-dev
```

### 3. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` file:

```env
APP_NAME="Your API Name"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database Configuration (MySQL)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# CORS Configuration will be in config/cors.php
# Add your Angular production URL there
```

### 4. Database Migration

```bash
php artisan migrate --force
php artisan db:seed --force
```

### 5. Optimize for Production

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize
```

### 6. Set Permissions

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### 7. Configure CORS for Production

Edit `config/cors.php`:

```php
'allowed_origins' => [
    'https://your-angular-app.com',
    'https://www.your-angular-app.com',
],
```

## 🔧 Web Server Configuration

### Apache Configuration

Create `.htaccess` in public folder (already exists):

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

VirtualHost configuration:

```apache
<VirtualHost *:443>
    ServerName your-api.com
    DocumentRoot /path/to/your-app/public

    <Directory /path/to/your-app/public>
        AllowOverride All
        Require all granted
    </Directory>

    SSLEngine on
    SSLCertificateFile /path/to/cert.pem
    SSLCertificateKeyFile /path/to/key.pem
</VirtualHost>
```

### Nginx Configuration

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name your-api.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name your-api.com;
    root /path/to/your-app/public;

    ssl_certificate /path/to/cert.pem;
    ssl_certificate_key /path/to/key.pem;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

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
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## 🔒 Security Checklist

- [ ] Set `APP_DEBUG=false` in production
- [ ] Use HTTPS only
- [ ] Configure proper CORS origins
- [ ] Use strong database passwords
- [ ] Enable rate limiting
- [ ] Keep Laravel and dependencies updated
- [ ] Configure firewall rules
- [ ] Use environment variables for sensitive data
- [ ] Enable CSRF protection
- [ ] Set proper file permissions

## 📊 Database Backup

### Create Backup Script

```bash
#!/bin/bash
BACKUP_DIR="/path/to/backups"
DATE=$(date +%Y%m%d_%H%M%S)
FILENAME="backup_$DATE.sql"

mysqldump -u username -p'password' database_name > "$BACKUP_DIR/$FILENAME"
gzip "$BACKUP_DIR/$FILENAME"

# Keep only last 7 days of backups
find $BACKUP_DIR -name "backup_*.sql.gz" -mtime +7 -delete
```

### Setup Cron Job

```bash
crontab -e
```

Add:
```
0 2 * * * /path/to/backup-script.sh
```

## 🔄 Update Deployment

```bash
# Pull latest changes
git pull origin main

# Update dependencies
composer install --optimize-autoloader --no-dev

# Run migrations
php artisan migrate --force

# Clear and cache
php artisan config:clear
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 📈 Monitoring

### Laravel Logs

```bash
tail -f storage/logs/laravel.log
```

### Check API Health

```bash
curl https://your-api.com/up
```

### Monitor Performance

Install Laravel Telescope for development/staging:

```bash
composer require laravel/telescope
php artisan telescope:install
php artisan migrate
```

## 🔧 Troubleshooting

### Permission Issues

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Clear All Cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Database Connection Issues

Check:
1. Database credentials in `.env`
2. Database server is running
3. Firewall allows database connection
4. MySQL user has proper permissions

### 500 Internal Server Error

1. Check `storage/logs/laravel.log`
2. Ensure `APP_DEBUG=false` in production
3. Check file permissions
4. Clear cache

## 📱 API Versioning

For API versioning, consider:

```php
// routes/api.php
Route::prefix('v1')->group(function () {
    // v1 routes
});

Route::prefix('v2')->group(function () {
    // v2 routes
});
```

## 🔐 Rate Limiting

Laravel includes rate limiting by default. Configure in `app/Http/Kernel.php`:

```php
'api' => [
    'throttle:api',
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
],
```

Adjust in `config/sanctum.php`:

```php
'middleware' => [
    'throttle:60,1', // 60 requests per minute
],
```

## 📊 Performance Optimization

### Enable OPcache

Edit `php.ini`:

```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
```

### Queue Jobs

For heavy operations, use queues:

```bash
php artisan queue:work --daemon
```

Setup supervisor to keep queue worker running:

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/worker.log
```

## 🔄 CI/CD Pipeline

### GitHub Actions Example

`.github/workflows/deploy.yml`:

```yaml
name: Deploy

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          
      - name: Install Dependencies
        run: composer install --optimize-autoloader --no-dev
        
      - name: Run Tests
        run: php artisan test
        
      - name: Deploy to Server
        uses: appleboy/ssh-action@master
        with:
          host: ${{ secrets.HOST }}
          username: ${{ secrets.USERNAME }}
          key: ${{ secrets.SSH_KEY }}
          script: |
            cd /path/to/your-app
            git pull origin main
            composer install --optimize-autoloader --no-dev
            php artisan migrate --force
            php artisan config:cache
            php artisan route:cache
```

## 📞 Support

For production issues:
1. Check Laravel logs
2. Check web server error logs
3. Verify environment configuration
4. Ensure all services are running

---

## ✅ Post-Deployment Checklist

- [ ] Database migrations completed
- [ ] Seeder data loaded
- [ ] All caches cleared and rebuilt
- [ ] File permissions correct
- [ ] SSL certificate installed and working
- [ ] CORS configured for production domain
- [ ] Environment variables set correctly
- [ ] Backup system in place
- [ ] Monitoring tools configured
- [ ] API endpoints tested
- [ ] Documentation updated
- [ ] Angular frontend connected successfully

---

**Production deployment completed! 🚀**
