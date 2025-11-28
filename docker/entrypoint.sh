#!/usr/bin/env sh
set -e

# Ir al directorio de la app
cd /var/www/html || exit 1

# Generar APP_KEY si no estÃƒÂ¡ definida (recomendado setearla en Render en vez de generarla)
if [ -z "${APP_KEY:-}" ]; then
  echo "APP_KEY not set  generando key temporal..."
  if [ -f artisan ]; then
    php artisan key:generate --force || true
    echo "APP_KEY generado (es mejor definir APP_KEY en Render Dashboard)."
  fi
fi

# Ejecutar package discovery (ahora artisan existe). No fallar el contenedor si algo sale mal.
if [ -f artisan ]; then
  echo "Ejecutando php artisan package:discover --ansi"
  php artisan package:discover --ansi || echo "package:discover fallÃƒÂ³ (continuando)..."
fi

# Crear el enlace de storage/public si hace falta
if [ -f artisan ]; then
  if [ ! -L public/storage ]; then
    php artisan storage:link || true
  fi
fi

# Corregir permisos (por si Render monta volÃƒÂºmenes)
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache || true

# Ejecutar migraciones SIEMPRE para asegurar schema actualizado
if [ -f artisan ]; then
  echo "Running migrations..."
  php artisan migrate --force --no-interaction 2>&1 || echo "Migrations warning (continuando)..."
fi

# Ejecutar seeders si RUN_SEEDERS=true (solo la primera vez)
if [ "${RUN_SEEDERS:-false}" = "true" ] && [ -f artisan ]; then
  echo "Running seeders..."
  php artisan db:seed --force || echo "Seeders fallaron (continuando)..."
fi

# Ejecutar el comando principal (supervisord)
exec "$@"