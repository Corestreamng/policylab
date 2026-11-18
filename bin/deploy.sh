#!/usr/bin/env bash
set -eo pipefail

# -----------------------------------------------------------------------------
# Youth Policy Tracker: Production Deployment Script
# Client: Glowing Minds Initiatives (GMI)
# Engineering: Corestream Networks and Robotics Limited
# -----------------------------------------------------------------------------

APP_DIR="${APP_DIR:-$(pwd)}"
cd "$APP_DIR"

PREV_COMMIT=$(git rev-parse HEAD 2>/dev/null || echo "HEAD")
MAINTENANCE_ACTIVE=0
LATEST_BACKUP_FILE=""

cleanup_on_error() {
    local exit_code=$?
    echo ""
    echo "======================================================================"
    echo "DEPLOYMENT FAILED AT LINE $1 (Exit Code: $exit_code)"
    echo "======================================================================"

    # Warn instead of resetting if working tree has local modifications
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

    # Restart queue workers on previous code
    php artisan queue:restart 2>/dev/null || true

    # Bring site out of maintenance mode on the previous release
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
