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

## 3. Safe Production Deployment & Rollback Architecture

The production deployment script (`bin/deploy.sh`) safeguards site availability:
1. Engages custom maintenance mode (`public.offline`).
2. Warns instead of performing a destructive `git reset --hard` if the working tree has local modifications.
3. Compiles frontend assets if Node/npm is available; otherwise verifies pre-compiled artifacts in `public/build` from CI/staging.
4. Takes an automated `mysqldump` snapshot before migrations are executed.
5. Notes that database migrations are not rolled back automatically on error (to avoid accidental data loss).
6. Restores code, clears/re-caches framework artifacts, and ensures the site comes out of maintenance mode on the previous release if any step fails.

### 3.1 Executable Deployment Script (`bin/deploy.sh`)

```bash
#!/usr/bin/env bash
set -eo pipefail

APP_DIR="${APP_DIR:-$(pwd)}"
cd "$APP_DIR"

PREV_COMMIT=$(git rev-parse HEAD 2>/dev/null || echo "HEAD")
MAINTENANCE_ACTIVE=0
LATEST_BACKUP_FILE=""

cleanup_on_error() {
    local exit_code=$?
    echo "======================================================================"
    echo "DEPLOYMENT FAILED AT LINE $1 (Exit Code: $exit_code)"
    echo "======================================================================"

    # Warn instead of destructive reset if working tree has local modifications
    if [ -n "$(git status --porcelain 2>/dev/null)" ]; then
        echo "WARNING: Working tree contains local uncommitted modifications."
        echo "Skipping destructive 'git reset --hard' to prevent irreversible data loss."
        echo "Please inspect git status and resolve modified files manually."
    else
        echo "Resetting code to previous commit ($PREV_COMMIT)..."
        git reset --hard "$PREV_COMMIT" 2>/dev/null || true
    fi

    # Clear potentially corrupted runtime caches
    php artisan config:clear 2>/dev/null || true
    php artisan route:clear 2>/dev/null || true
    php artisan view:clear 2>/dev/null || true
    php artisan event:clear 2>/dev/null || true
    php artisan cache:clear 2>/dev/null || true

    # Re-cache previous state
    php artisan config:cache 2>/dev/null || true
    php artisan route:cache 2>/dev/null || true
    php artisan view:cache 2>/dev/null || true
    php artisan event:cache 2>/dev/null || true

    php artisan queue:restart 2>/dev/null || true

    # Bring site out of maintenance mode on previous release
    if [ "$MAINTENANCE_ACTIVE" -eq 1 ]; then
        echo "Deactivating maintenance mode on previous release..."
        php artisan up 2>/dev/null || true
    fi

    echo ""
    echo "CRITICAL NOTICE: Code and framework caches have been restored."
    if [ -n "$LATEST_BACKUP_FILE" ]; then
        echo "NOTE: Database migrations are NOT rolled back automatically."
        echo "A pre-migration database snapshot was created at:"
        echo "  $LATEST_BACKUP_FILE"
        echo "To restore the database to its pre-deployment state, run:"
        echo "  gzip -dc \"$LATEST_BACKUP_FILE\" | mysql -u <username> -p <database_name>"
    fi
    echo "======================================================================"
    exit "$exit_code"
}

trap 'cleanup_on_error $LINENO' ERR

echo ">>> Deploying Youth Policy Tracker..."

# 1. Engage maintenance mode
echo ">>> Engaging maintenance mode..."
php artisan down --render="public.offline" --retry=60 || true
MAINTENANCE_ACTIVE=1

# 2. Update dependencies
echo ">>> Installing production PHP dependencies (composer)..."
composer install --no-dev --optimize-autoloader --no-interaction

# Frontend build: build locally if npm is present; otherwise verify pre-compiled assets from CI/staging
if command -v npm >/dev/null 2>&1; then
    echo ">>> Compiling frontend assets via npm..."
    npm ci
    npm run build
else
    echo ">>> INFO: npm is not installed on this host."
    echo ">>> Verifying pre-compiled build assets from CI/staging in public/build..."
    if [ ! -f "public/build/manifest.json" ]; then
        echo "ERROR: public/build/manifest.json not found! Assets must be pre-built on CI/staging before deploying."
        exit 1
    fi
    echo ">>> Pre-compiled assets verified successfully."
fi

# 3. Storage link
echo ">>> Verifying storage symlink..."
php artisan storage:link || true

# 4. Database snapshot before migration
echo ">>> Taking pre-migration database snapshot..."
BACKUP_DIR="${APP_DIR}/storage/app/backups"
mkdir -p "$BACKUP_DIR"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
LATEST_BACKUP_FILE="${BACKUP_DIR}/db_pre_migrate_${TIMESTAMP}.sql.gz"

DB_CONN=$(php -r "echo config('database.default');" 2>/dev/null || echo "mysql")
if [ "$DB_CONN" = "mysql" ]; then
    DB_HOST=$(php -r "echo config('database.connections.mysql.host');" 2>/dev/null)
    DB_PORT=$(php -r "echo config('database.connections.mysql.port');" 2>/dev/null)
    DB_NAME=$(php -r "echo config('database.connections.mysql.database');" 2>/dev/null)
    DB_USER=$(php -r "echo config('database.connections.mysql.username');" 2>/dev/null)
    DB_PASS=$(php -r "echo config('database.connections.mysql.password');" 2>/dev/null)

    if command -v mysqldump >/dev/null 2>&1 && [ -n "$DB_NAME" ]; then
        MYSQL_PWD="$DB_PASS" mysqldump -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" "$DB_NAME" | gzip > "$LATEST_BACKUP_FILE"
        echo ">>> Database snapshot created: $LATEST_BACKUP_FILE"
    else
        echo ">>> NOTICE: mysqldump binary not in PATH or database name unset. Proceeding without automated dump."
    fi
else
    echo ">>> INFO: Active connection is '$DB_CONN'. mysqldump snapshot skipped."
fi

# 5. Database migrations
echo ">>> Running database migrations..."
php artisan migrate --force

# 6. Optimize framework caches
echo ">>> Optimizing production framework caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear
php artisan cache:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 7. Restart queue workers
echo ">>> Restarting background queue workers..."
php artisan queue:restart

# 8. Deactivate maintenance mode
echo ">>> Deactivating maintenance mode..."
php artisan up
MAINTENANCE_ACTIVE=0

echo ">>> SUCCESS: Youth Policy Tracker deployment completed cleanly."
```

### 3.2 Restoring Pre-Deployment Database Snapshot
Migrations are intentionally **not rolled back automatically** by `deploy.sh` to prevent data loss on destructive changes. If a deploy fails and database rollback is necessary:
1. Locate the snapshot listed in the deployment failure output under `storage/app/backups/`.
2. Restore via MySQL CLI:
   ```bash
   gzip -dc storage/app/backups/db_pre_migrate_YYYYMMDD_HHMMSS.sql.gz | mysql -u ypt_user -p youth_policy_tracker
   ```

### 3.3 Provisioning the First Super Admin
In production, no default accounts or passwords exist. Provision the first administrative user using the secure interactive command:
```bash
php artisan tracker:make-super-admin
```
Follow prompts for Full Name, Email Address, and Secure Password (minimum 8 characters). Once created, sign in at `https://youthpolicytracker.ng/admin` and complete the mandatory TOTP 2FA enrollment.

### 3.4 Production `.env` Required Security Settings
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
DB_PASSWORD=<secure_password>

# Session Security & Cookie Protection
SESSION_DRIVER=redis
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true

# Reverse Proxy Trust (Nginx / Cloudflare)
TRUSTED_PROXIES=*
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
Measure these exact production metrics under simulated load (using k6, autocannon, or Lighthouse on staging):
1. **Time to First Byte (TTFB)**: Target `< 150ms` on public cached landing routes; `< 300ms` on authenticated Filament requests.
2. **Largest Contentful Paint (LCP)**: Target `< 2.0s` on mobile (Moto G4 4G simulation). Measure on landing page hero map.
3. **Cumulative Layout Shift (CLS)**: Target `0.00` (SVG map dimensions explicitly constrained by `viewBox="0 0 800 650"` and aspect-ratio CSS).
4. **Interaction to Next Paint (INP)**: Target `< 100ms` for state drawer expansion and map hover interactions.
5. **Database Query Efficiency**: Target `< 12 queries` on the landing page (`PolicyTracker`) using eager loading (`with(['policyStatus.stage', 'policyStatus.leadingOrganizations'])`). Confirm zero N+1 query patterns when filtering by zone or lifecycle stage.
6. **Worker Concurrency & Memory**: Monitor PHP-FPM RSS memory: target `< 35MB` per active child worker; monitor Redis cache hit ratio: target `> 90%`; monitor queue job latency: target `< 500ms`.

---

## 5. Systemd Daemons: Queue Worker & Scheduler

### 5.1 Queue Worker (`/etc/systemd/system/ypt-worker.service`)
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

### 5.2 Laravel Scheduler Cron (`/etc/cron.d/ypt-scheduler`)
```cron
* * * * * www-data /usr/bin/php /var/www/youthpolicytracker/artisan schedule:run >> /dev/null 2>&1
```

---

## 6. Automated Backups & Disaster Recovery

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

## 7. Staff Training & Administrative Handover Outline

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
