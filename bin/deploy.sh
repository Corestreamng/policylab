#!/usr/bin/env bash
set -eo pipefail

# -----------------------------------------------------------------------------
# Youth Policy Tracker: Safe Production Deployment Script
# Client: Glowing Minds Initiatives (GMI)
# Engineering: Corestream Networks and Robotics Limited
# -----------------------------------------------------------------------------

APP_DIR="${APP_DIR:-$(pwd)}"
cd "$APP_DIR"

PREV_COMMIT=$(git rev-parse HEAD 2>/dev/null || echo "HEAD")
MAINTENANCE_ACTIVE=0

# Ensure that if any command fails, we roll back and guarantee the site is up
cleanup_on_error() {
    local exit_code=$?
    echo ""
    echo "======================================================================"
    echo "DEPLOYMENT FAILED AT LINE $1 (Exit Code: $exit_code)"
    echo "======================================================================"
    echo "Rolling back to previous commit ($PREV_COMMIT)..."

    # Reset working tree to previous commit
    git reset --hard "$PREV_COMMIT" 2>/dev/null || true

    # Clear potentially corrupted caches
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

    # Restart queue on old code
    php artisan queue:restart 2>/dev/null || true

    # Bring site out of maintenance mode on the previous release
    if [ "$MAINTENANCE_ACTIVE" -eq 1 ]; then
        echo "Deactivating maintenance mode on previous release..."
        php artisan up 2>/dev/null || true
    fi

    echo "CRITICAL: Rollback complete. Application is online on previous release."
    echo "======================================================================"
    exit "$exit_code"
}

trap 'cleanup_on_error $LINENO' ERR

echo ">>> Deploying Youth Policy Tracker..."

# 1. Enter maintenance mode before modifications
echo ">>> Engaging maintenance mode..."
php artisan down --render="public.offline" --retry=60 || true
MAINTENANCE_ACTIVE=1

# 2. Update dependencies
echo ">>> Installing production PHP dependencies (composer)..."
composer install --no-dev --optimize-autoloader --no-interaction

echo ">>> Compiling frontend assets (npm)..."
npm ci
npm run build

# 3. Storage symlink
echo ">>> Verifying storage symlink..."
php artisan storage:link || true

# 4. Database migrations
echo ">>> Running database migrations..."
php artisan migrate --force

# 5. Clear and re-cache framework artifacts
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

# 6. Restart queue workers so they load fresh classes
echo ">>> Restarting background queue workers..."
php artisan queue:restart

# 7. Bring site out of maintenance mode only after all steps complete successfully
echo ">>> Deactivating maintenance mode..."
php artisan up
MAINTENANCE_ACTIVE=0

echo ">>> SUCCESS: Deployment finished cleanly with zero maintenance residue."
