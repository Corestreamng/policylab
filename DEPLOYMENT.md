# Youth Policy Tracker: Deployment, Handover & Operations Guide

## 1. System Overview & Architecture
- **Application**: Youth Policy Tracker (Sub-National Policy Domestication Platform)
- **Client**: Glowing Minds Initiatives (GMI)
- **Engineering**: Corestream Networks and Robotics Limited
- **Stack**: Laravel 11, PHP 8.4 (PHP 8.3+ supported), MySQL 8, Redis, Livewire 3, Alpine.js, Tailwind CSS, Filament v3, Maatwebsite Excel, PDF.js, Chart.js.
- **Security**: Private disk document vault served strictly via signed temporary URLs; strict input sanitization; CSP, HSTS, and frame protection; GA4 cookie consent gate; zero external font/emoji dependencies.

---

## 2. Ubuntu 24.04 LTS Production Server Setup

### 2.1 Server Prerequisites & System Packages
```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y software-properties-common curl git unzip ufw redis-server certbot python3-certbot-nginx

# Add PHP 8.4 repository
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install PHP 8.4 and required extensions
sudo apt install -y php8.4 php8.4-fpm php8.4-mysql php8.4-mbstring php8.4-xml php8.4-bcmath \
    php8.4-curl php8.4-zip php8.4-intl php8.4-gd php8.4-redis php8.4-sqlite3 nginx

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### 2.2 Nginx Server Block Configuration (`/etc/nginx/sites-available/youthpolicytracker.ng`)
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name youthpolicytracker.ng www.youthpolicytracker.ng;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name youthpolicytracker.ng www.youthpolicytracker.ng;
    root /var/www/youthpolicytracker/public;

    index index.php index.html;
    charset utf-8;

    # SSL Configuration (Certbot)
    ssl_certificate /etc/letsencrypt/live/youthpolicytracker.ng/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/youthpolicytracker.ng/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Permissions-Policy "camera=(), microphone=(), geolocation=()" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains; preload" always;

    # Maximum file upload size for Certified True Copy PDFs (25 MB)
    client_max_body_size 30M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    # Deny access to hidden files and private folders
    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Static asset caching (Vite builds)
    location ~* \.(css|js|woff2|woff|svg|png|jpg|ico)$ {
        expires 1y;
        add_header Cache-Control "public, no-transform";
        access_log off;
    }
}
```

Enable site and restart Nginx:
```bash
sudo ln -s /etc/nginx/sites-available/youthpolicytracker.ng /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

## 3. Application Deployment & Environment Setup

```bash
cd /var/www
git clone <repository_url> youthpolicytracker
cd youthpolicytracker

# Set storage and bootstrap permissions
sudo chown -R www-data:www-data /var/www/youthpolicytracker
sudo chmod -R 775 storage bootstrap/cache

# Install dependencies (production optimized)
composer install --no-dev --optimize-autoloader
npm ci
npm run build

# Configure environment
cp .env.example .env
php artisan key:generate
```

### Production `.env` Settings
```env
APP_NAME="Youth Policy Tracker"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://youthpolicytracker.ng

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=youth_policy_tracker
DB_USERNAME=ypt_user
DB_PASSWORD=<secure_database_password>

CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Google Analytics 4 (Only loaded after user cookie consent)
GA_MEASUREMENT_ID=G-XXXXXXXXXX
```

Migrate and seed canonical taxonomy and dataset:
```bash
php artisan migrate --force
php artisan db:seed --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 4. Systemd Daemons: Queue Worker & Scheduler

### 4.1 Queue Worker (`/etc/systemd/system/ypt-worker.service`)
```ini
[Unit]
Description=Youth Policy Tracker Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/youthpolicytracker/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
RestartSec=5s

[Install]
WantedBy=multi-user.target
```
Enable and start:
```bash
sudo systemctl daemon-reload
sudo systemctl enable --now ypt-worker.service
```

### 4.2 Laravel Scheduler Cron (`/etc/cron.d/ypt-scheduler`)
```cron
* * * * * www-data /usr/bin/php /var/www/youthpolicytracker/artisan schedule:run >> /dev/null 2>&1
```

---

## 5. Automated Backups & Disaster Recovery

### Database & Private Document Vault Backup Script (`/usr/local/bin/ypt-backup.sh`)
```bash
#!/usr/bin/env bash
set -e

BACKUP_DIR="/var/backups/youthpolicytracker"
DATE=$(date +"%Y%m%d_%H%M%S")
mkdir -p "$BACKUP_DIR"

# 1. Database dump
mysqldump -u ypt_user -p'<password>' youth_policy_tracker | gzip > "$BACKUP_DIR/db_${DATE}.sql.gz"

# 2. Private documents archive
tar -czf "$BACKUP_DIR/documents_${DATE}.tar.gz" -C /var/www/youthpolicytracker/storage/app private

# 3. Retention: Keep last 14 days
find "$BACKUP_DIR" -type f -mtime +14 -delete
```
Make executable and add to crontab:
```bash
sudo chmod +x /usr/local/bin/ypt-backup.sh
echo "0 2 * * * root /usr/local/bin/ypt-backup.sh" | sudo tee /etc/cron.d/ypt-backup
```

---

## 6. Staff Training & Administrative Handover Outline

### Target Audience: Glowing Minds Initiatives (GMI) Data Officers & Partner Analysts
- **Portal URL**: `https://youthpolicytracker.ng/admin`
- **Default Roles**:
  - `Super Admin`: Full system governance, role allocation, stage taxonomy modification, audit trail inspection.
  - `Data Entry Manager`: State policy milestone updates, document uploads, partner/donor registry management, bulk CSV import.
  - `Viewer`: Read-only access to records and filtered dataset exports.

### Routine Operational Workflows
1. **Updating a State's Policy Status**:
   - Navigate to **Policy Statuses** > Locate state > Click **Edit**.
   - Update **Lifecycle Stage** (e.g. from *Validating* to *Validated*).
   - Enter **Year Commenced** and **Verification Source Note** (e.g., *Official State Gazette Notice Vol. 58*).
   - If stalled, toggle **Mark as Stalled** and supply the technical justification.
   - Click **Save**. An immutable audit log entry and lifecycle history milestone are automatically recorded.

2. **Uploading a Certified True Copy (CTC) Document**:
   - Navigate to **Document Vault** > Click **New Document**.
   - Select State, Publication Year, and Document Type.
   - Upload PDF (strictly validated as PDF; automatically assigned a secure UUID and stored on private storage).
   - Confirm public visibility toggle. The document immediately surfaces in the public CTC Hub with one-click signed downloads.

3. **Performing Bulk CSV Imports**:
   - Navigate to **Bulk Data Import**.
   - Click **Download CSV Template** to review required headers.
   - Upload the completed CSV and click **Validate & Preview**.
   - Review row-level validation notes. If errors occur (e.g., misspelled stage name), correct them in the CSV.
   - Click **Commit Validated Import** to batch-update state records.
