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

## 3. Safe Zero-Downtime Deployment & Automated Rollback

The production deployment script (`bin/deploy.sh`) ensures zero maintenance downtime residue: if any command fails (composer install, npm build, migration, or cache warm), an immediate automated rollback to the previous commit is triggered, caches are restored, and the application is brought out of maintenance mode on the previous release.

### 3.1 Executable Deployment Script (`bin/deploy.sh`)

```bash
#!/usr/bin/env bash
set -eo pipefail

APP_DIR="${APP_DIR:-/var/www/youthpolicytracker}"
cd "$APP_DIR"

PREV_COMMIT=$(git rev-parse HEAD 2>/dev/null || echo "HEAD")
MAINTENANCE_ACTIVE=0

cleanup_on_error() {
    local exit_code=$?
    echo "DEPLOYMENT FAILED AT LINE $1 (Exit Code: $exit_code). Initiating rollback..."

    # Reset working tree to previous release
    git reset --hard "$PREV_COMMIT" 2>/dev/null || true

    # Clear and re-cache previous state
    php artisan config:clear 2>/dev/null || true
    php artisan route:clear 2>/dev/null || true
    php artisan view:clear 2>/dev/null || true
    php artisan event:clear 2>/dev/null || true
    php artisan cache:clear 2>/dev/null || true

    php artisan config:cache 2>/dev/null || true
    php artisan route:cache 2>/dev/null || true
    php artisan view:cache 2>/dev/null || true
    php artisan event:cache 2>/dev/null || true

    php artisan queue:restart 2>/dev/null || true

    # Bring site out of maintenance mode on previous release
    if [ "$MAINTENANCE_ACTIVE" -eq 1 ]; then
        php artisan up 2>/dev/null || true
    fi

    echo "CRITICAL: System rolled back to previous commit ($PREV_COMMIT). Site is online."
    exit "$exit_code"
}

trap 'cleanup_on_error $LINENO' ERR

echo ">>> Deploying Youth Policy Tracker..."

# 1. Engage maintenance mode
php artisan down --render="public.offline" --retry=60 || true
MAINTENANCE_ACTIVE=1

# 2. Update dependencies
composer install --no-dev --optimize-autoloader --no-interaction
npm ci
npm run build

# 3. Storage link
php artisan storage:link || true

# 4. Database migrations
php artisan migrate --force

# 5. Clear and re-cache production artifacts
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear
php artisan cache:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 6. Restart queue workers
php artisan queue:restart

# 7. Take site out of maintenance mode only after all steps succeed
php artisan up
MAINTENANCE_ACTIVE=0

echo ">>> SUCCESS: Deployment finished cleanly."
```

### 3.2 Automated Git Deployment Command
On the production server, simply run:
```bash
./bin/deploy.sh
```

---

## 4. Production & Staging Hardening Checklist

Do not measure performance from local development servers (`artisan serve` or local Vite HMR). Before launch, execute this verification on the staging VPS:

### 4.1 Nginx Web Server Hardening
- [ ] **HTTP/2 & TLS 1.3**: Confirm SSL cipher suite uses modern TLS 1.3 / TLS 1.2 with OCSP stapling (`ssl_stapling on; ssl_stapling_verify on;`).
- [ ] **Gzip / Brotli Compression**: Enabled for `text/plain`, `text/css`, `text/xml`, `text/javascript`, `application/javascript`, `application/json`, `application/xml`, and `image/svg+xml`. Ensure SVG map (`public/maps/states.svg`) is delivered compressed (< 12 KB transfer size over wire).
- [ ] **Static Asset Cache Headers**: Verify Vite fingerprint assets (`/build/assets/*`) return `Cache-Control: public, max-age=31536000, immutable`.
- [ ] **Dynamic Document Headers**: Verify private document streams return `Cache-Control: private, no-cache, no-store, must-revalidate`.
- [ ] **Client Max Body Size**: Set to `client_max_body_size 30M;` to support PDF uploads up to 25 MB without HTTP 413 errors.
- [ ] **Security Headers**: Verify presence of `X-Frame-Options: SAMEORIGIN`, `X-Content-Type-Options: nosniff`, `Referrer-Policy: strict-origin-when-cross-origin`, and `Permissions-Policy`.

### 4.2 PHP-FPM & OPcache Optimization
- [ ] **OPcache Production Mode** (`/etc/php/8.4/fpm/conf.d/10-opcache.ini`):
  ```ini
  opcache.enable=1
  opcache.memory_consumption=256
  opcache.interned_strings_buffer=64
  opcache.max_accelerated_files=30000
  opcache.validate_timestamps=0
  opcache.revalidate_freq=0
  opcache.save_comments=1
  ```
  *(Note: `opcache.save_comments=1` is required for PHP 8 attributes used by Livewire and Filament).*
- [ ] **PHP-FPM Process Pool** (`/etc/php/8.4/fpm/pool.d/www.conf`):
  ```ini
  pm = dynamic
  pm.max_children = 50
  pm.start_servers = 10
  pm.min_spare_servers = 5
  pm.max_spare_servers = 20
  pm.max_requests = 1000
  ```
  *(Prevents memory leaks across prolonged worker lifetimes).*

### 4.3 Redis Configuration
- [ ] **Memory Limit & Eviction Policy** (`/etc/redis/redis.conf`):
  ```ini
  maxmemory 512mb
  maxmemory-policy allkeys-lru
  ```
- [ ] Verify separate Redis database prefixes or indices for Cache, Session, and Queue (`CACHE_PREFIX=ypt_cache`).
- [ ] Confirm persistence via RDB (`save 900 1`) or AOF for job queue resilience.

### 4.4 What to Measure on Staging & Production
Measure these exact production metrics under simulated load (using k6, autocannon, or Lighthouse):
1. **Time to First Byte (TTFB)**:
   - Target: `< 150ms` for cached landing page responses; `< 300ms` for authenticated Filament pages.
2. **Largest Contentful Paint (LCP)**:
   - Target: `< 2.0s` on mobile (Moto G4 4G simulation). Measure on landing page hero map.
3. **Cumulative Layout Shift (CLS)**:
   - Target: `0.00` (SVG map dimensions explicitly constrained by `viewBox="0 0 800 650"` and aspect-ratio CSS).
4. **First Input Delay / Interaction to Next Paint (INP)**:
   - Target: `< 100ms` for state drawer expansion and map hover interactions.
5. **Database Query Efficiency**:
   - Target: `< 12 queries` on the landing page (`PolicyTracker`) using eager loading (`with(['policyStatus.stage', 'policyStatus.leadingOrganizations'])`).
   - Confirm zero N+1 query patterns when filtering by zone or lifecycle stage.
6. **Worker Concurrency & Memory**:
   - Monitor PHP-FPM RSS memory: target `< 35MB` per active child worker.
   - Monitor Redis cache hit ratio: target `> 90%` (`redis-cli info stats | grep keyspace`).
   - Monitor queue job execution latency: target `< 500ms` between job dispatch and execution.

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
