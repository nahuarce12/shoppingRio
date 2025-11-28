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
    echo "Creating storage symlink..."
    php artisan storage:link --force || true
    # Si artisan falla, crear symlink manualmente
    if [ ! -L public/storage ]; then
      ln -s ../storage/app/public public/storage 2>/dev/null || true
    fi
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

# Ejecutar seeders si admin no existe O si RUN_SEEDERS=true
if [ -f artisan ]; then
  # Verificar si ya existe un administrador en BD
  if php artisan check:admin-exists > /dev/null 2>&1; then
    echo "Admin already exists, skipping seeders"
  else
    echo "No admin found, running seeders..."
    php artisan db:seed --force || echo "Seeders fallaron (continuando)..."
  fi
  
  # También correr si forzamos con RUN_SEEDERS=true
  if [ "${RUN_SEEDERS:-false}" = "true" ]; then
    echo "RUN_SEEDERS=true, forcing seeders..."
    php artisan db:seed --force || echo "Seeders fallaron (continuando)..."
  fi
fi

# Ejecutar el comando principal (supervisord)
exec "$@"