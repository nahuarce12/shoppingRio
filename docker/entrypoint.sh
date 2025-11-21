#!/usr/bin/env sh
set -e

# Go to application directory
cd /var/www/html

# If APP_KEY is not set, generate one (recommended to set APP_KEY in Render environment)
if [ -z "${APP_KEY:-}" ]; then
  echo "APP_KEY not set — generating application key..."
  if [ -f artisan ]; then
    php artisan key:generate --force
    echo "Generated APP_KEY (consider copying it to your Render environment variables for future deployments)."
  fi
fi

# Ensure storage symlink exists (for public storage uploads)
if [ -f artisan ]; then
  if [ ! -L public/storage ]; then
    php artisan storage:link || true
  fi
fi

# Fix permissions (in case of mounted volumes or Render disk)
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache || true

# Run optional migrations if RUN_MIGRATIONS=true
if [ "${RUN_MIGRATIONS:-false}" = "true" ] && [ -f artisan ]; then
  echo "Running migrations..."
  php artisan migrate --force || true
fi

# Execute the CMD
exec "$@"