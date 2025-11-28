# Guía de Deployment - ShoppingRio en Render

Documentación completa sobre cómo deployear el proyecto ShoppingRio en Render con Railway como base de datos.

## 📋 Tabla de Contenidos

1. [Resumen de Cambios](#resumen-de-cambios)
2. [Requisitos Previos](#requisitos-previos)
3. [Configuración de Railway (Base de Datos)](#configuración-de-railway-base-de-datos)
4. [Configuración de Render (Aplicación)](#configuración-de-render-aplicación)
5. [Variables de Entorno](#variables-de-entorno)
6. [Troubleshooting](#troubleshooting)
7. [Comandos Útiles](#comandos-útiles)

---

## Resumen de Cambios

Se realizaron los siguientes cambios al proyecto para que funcione correctamente en Render:

### 1. **Dockerfile - Optimización del Build**

-   ✅ Reordenamiento: npm build se ejecuta DESPUÉS de copiar `vite.config.js` y `resources/`
-   ✅ Diagnóstico mejorado con logs verbosos durante el build de Vite
-   ✅ Asegurada la generación de `public/build/` con assets compilados

**Archivo:** `dockerfile`

```dockerfile
# Build frontend assets after app files are present so Vite can find vite.config.js
RUN if [ -f package.json ]; then \
  echo "Installing npm dependencies..." && \
  npm ci && \
  echo "Building frontend assets..." && \
  npm run build && \
  echo "Vite build completed. Checking public/build:" && \
  ls -la public/build/ || echo "public/build not found!"; \
  fi
```

### 2. **package.json - Scripts Faltantes**

-   ✅ Agregado script `prod` como alias de `build` (usado por Dockerfile como fallback)

**Cambio:**

```json
"scripts": {
  "dev": "vite",
  "build": "vite build",
  "prod": "vite build"
}
```

### 3. **docker/supervisord.conf - Corrección de Logging**

-   ✅ `nodaemon=true` (requerido en Docker)
-   ✅ `user=root` (evita warning de privilegios)
-   ✅ `logfile=/dev/null` con `logfile_maxbytes=0` (evita error "Invalid seek")
-   ✅ Configurado logging sin limits en stdout/stderr

### 4. **docker/nginx.conf - Configuración Completa**

-   ✅ Reemplazado con configuración nginx.conf **completa** (no solo server block)
-   ✅ Location especial para `/build/` assets de Vite
-   ✅ GZIP habilitado para compresión de assets
-   ✅ Copiado a `/etc/nginx/nginx.conf` en lugar de `conf.d/`

**Location para assets Vite:**

```nginx
location /build/ {
    alias /var/www/html/public/build/;
    expires 1y;
    access_log off;
    add_header Cache-Control "public, immutable";
}
```

### 5. **docker/entrypoint.sh - Finales de Línea & Seeders**

-   ✅ Convertido a LF (Unix line endings)
-   ✅ Agregado soporte para ejecutar seeders con `RUN_SEEDERS=true`

**Nuevo:**

```bash
# Ejecutar seeders si RUN_SEEDERS=true
if [ "${RUN_SEEDERS:-false}" = "true" ] && [ -f artisan ]; then
  echo "Running seeders..."
  php artisan db:seed --force || echo "Seeders fallaron (continuando)..."
fi
```

### 6. **database/migrations/2025_11_12_231234_rename_database_attributes_to_english.php - Idempotencia**

-   ✅ Reescrito para ser "seguro" en bases de datos frescas
-   ✅ Verifica existencia de columnas antes de renombrar/crear
-   ✅ Compatible con sistemas que ya tienen columnas en inglés

### 7. **bootstrap/app.php - Trust Proxies**

-   ✅ Agregado `$middleware->trustProxies(at: '*')` para confiar en reverse proxies
-   ✅ Necesario para que Render (que usa reverse proxy) maneje URLs correctamente

### 8. **app/Providers/AppServiceProvider.php - HTTPS Forzado**

-   ✅ Agregado `URL::forceScheme('https')` en producción
-   ✅ Previene errores de "mixed content" (assets HTTP en página HTTPS)

**Código:**

```php
if (config('app.env') === 'production' || request()->header('X-Forwarded-Proto') === 'https') {
    URL::forceScheme('https');
}
```

### 9. **.gitattributes - Line Endings**

-   ✅ Forzado LF para `*.sh` y archivos `docker/*.conf`
-   ✅ Previene problemas de CRLF → LF en Windows

---

## Requisitos Previos

-   Cuenta en [Render.com](https://render.com)
-   Cuenta en [Railway.app](https://railway.app)
-   Proyecto en GitHub (este repositorio)
-   Variables de entorno correctas

---

## Configuración de Railway (Base de Datos)

### Paso 1: Crear Base de Datos MySQL

1. Accede a [Railway Dashboard](https://railway.app/dashboard)
2. Click en **New Project** → **Provision Database** → **MySQL**
3. Espera a que se cree el servicio (1-2 minutos)

### Paso 2: Obtener Credenciales

1. En el servicio MySQL, ve a **Variables**
2. Copia los siguientes valores:
    - `MYSQLHOST` (ej: `ballast.proxy.rlwy.net`)
    - `MYSQLPORT` (usualmente `3306`)
    - `MYSQLDATABASE` (será `railway`)
    - `MYSQLUSER` (usualmente `root`)
    - `MYSQLPASSWORD` (contraseña generada)
    - `MYSQL_PUBLIC_URL` (para conexiones externas)

**⚠️ Nota:** Railway por defecto no cambia el nombre de la base de datos de `railway`. Úsalo tal cual.

### Paso 3: Verificar Conectividad (Opcional)

```bash
# Desde tu máquina local, prueba conexión:
mysql -h ballast.proxy.rlwy.net -u root -p -D railway
```

---

## Configuración de Render (Aplicación)

### Paso 1: Conectar GitHub

1. Accede a [Render Dashboard](https://dashboard.render.com)
2. Click en **New** → **Web Service**
3. Selecciona **Build and deploy from Git repository**
4. Conecta tu cuenta de GitHub
5. Selecciona el repositorio `shoppingRio`

### Paso 2: Configurar Servicio

-   **Name:** `shoppingrio` (o tu preferencia)
-   **Branch:** `feature/deployFixes` (o la rama que uses)
-   **Runtime:** `Docker`
-   **Build Command:** (dejar en blanco - Render detecta el Dockerfile)
-   **Start Command:** (dejar en blanco - Dockerfile define ENTRYPOINT)

### Paso 3: Configurar Variables de Entorno

En Render → Settings → Environment, agrega las siguientes variables:

```env
# Laravel Configuration
APP_ENV=production
APP_DEBUG=false
APP_NAME=ShoppingRio
APP_URL=https://shoppingrio.onrender.com
LOG_CHANNEL=stderr

# Database Connection (Railway)
DB_CONNECTION=mysql
DB_HOST=ballast.proxy.rlwy.net
DB_PORT=3306
DB_DATABASE=railway
DB_USERNAME=root
DB_PASSWORD=<TU_CONTRASEÑA_DE_RAILWAY>

# Laravel Encryption
APP_KEY=base64:<TU_APP_KEY>

# Execution Flags (solo en primer deploy)
RUN_MIGRATIONS=true
RUN_SEEDERS=true

# Port (usado por Render)
PORT=80
```

**Nota:** Reemplaza:

-   `ballast.proxy.rlwy.net` con tu MYSQLHOST de Railway
-   `<TU_CONTRASEÑA_DE_RAILWAY>` con tu contraseña
-   `<TU_APP_KEY>` con una key generada (ver más abajo)

### Paso 4: Generar APP_KEY

En tu máquina local:

```bash
php artisan key:generate --show
```

Copia el resultado (incluye `base64:`) y pégalo en la variable `APP_KEY` en Render.

### Paso 5: Deploy

1. Click en **Create Web Service** en Render
2. Espera a que se compile la imagen Docker y se despliegue (5-10 minutos)
3. Revisa los logs para ver si hay errores

---

## Variables de Entorno

### Tabla Completa de Variables

| Variable         | Valor                              | Descripción                               |
| ---------------- | ---------------------------------- | ----------------------------------------- |
| `APP_ENV`        | `production`                       | Ambiente de ejecución                     |
| `APP_DEBUG`      | `false`                            | Debug deshabilitado en producción         |
| `APP_NAME`       | `ShoppingRio`                      | Nombre de la aplicación                   |
| `APP_URL`        | `https://shoppingrio.onrender.com` | URL base (con HTTPS)                      |
| `APP_KEY`        | `base64:...`                       | Clave de encriptación (generada)          |
| `LOG_CHANNEL`    | `stderr`                           | Logs a stderr para Docker                 |
| `DB_CONNECTION`  | `mysql`                            | Tipo de BD                                |
| `DB_HOST`        | Railway HOST                       | Host de Railway                           |
| `DB_PORT`        | `3306`                             | Puerto MySQL                              |
| `DB_DATABASE`    | `railway`                          | Nombre de BD (Railway)                    |
| `DB_USERNAME`    | `root`                             | Usuario MySQL                             |
| `DB_PASSWORD`    | Railway PASSWORD                   | Contraseña de Railway                     |
| `RUN_MIGRATIONS` | `true`                             | Ejecutar migraciones al iniciar           |
| `RUN_SEEDERS`    | `true`                             | Ejecutar seeders al iniciar (solo 1ª vez) |
| `PORT`           | `80`                               | Puerto HTTP                               |

### Importante: RUN_SEEDERS

⚠️ **Usar `RUN_SEEDERS=true` solo en el primer deploy**

-   En el primer deploy: `RUN_SEEDERS=true` → Crea datos de prueba
-   Después del primer deploy exitoso: Cambia a `RUN_SEEDERS=false`
-   Si lo dejas en `true`, cada redeploy borrará y recreará datos

**Credenciales de prueba creadas por seeders:**

| Usuario       | Email                   | Contraseña  |
| ------------- | ----------------------- | ----------- |
| Administrador | `admin@shoppingrio.com` | `Admin123!` |
| Dueño Local 1 | generado                | `password`  |
| Cliente 1     | generado                | `password`  |

---

## Troubleshooting

### Error: "Could not resolve entry module index.html"

**Causa:** Vite intenta buscar `index.html` pero está en un proyecto Laravel.

**Solución:** ✅ Ya incluida en vite.config.js con `laravel-vite-plugin`

Verifica que exista:

```bash
npm install laravel-vite-plugin
```

### Error: "exec format error" en entrypoint.sh

**Causa:** Finales de línea Windows (CRLF) en archivo shell.

**Solución:** ✅ Ya corregido con `.gitattributes` y conversión a LF

Verifica:

```bash
file docker/entrypoint.sh
# Debe mostrar: POSIX shell script text executable, ASCII text
```

### Error: "server directive is not allowed here"

**Causa:** nginx.conf incorrecto o copiado a ubicación incorrecta.

**Solución:** ✅ Ya corregido: Se copia a `/etc/nginx/nginx.conf` como configuración completa

### Error: "Connection refused" en BD

**Causa:** Variables de entorno DB no configuradas correctamente.

**Solución:**

1. Verifica que `DB_HOST` sea el endpoint público de Railway
2. Usa `MYSQL_PUBLIC_URL` de Railway
3. Confirma que las credenciales sean exactas
4. Revisa que Railway MySQL esté activo

### Error: "mixed content" - Assets no cargan

**Causa:** Página HTTPS pero assets URL HTTP.

**Solución:** ✅ Ya corregido con:

-   `URL::forceScheme('https')` en AppServiceProvider
-   `$middleware->trustProxies(at: '*')` en bootstrap/app.php
-   `APP_URL=https://...` en variables de entorno

### No hay datos en la página

**Causa:** Seeders no ejecutados o `RUN_SEEDERS=false`.

**Solución:**

1. Agrega `RUN_SEEDERS=true` en Render
2. Haz redeploy
3. Después que complete, cambia a `RUN_SEEDERS=false`

---

## Comandos Útiles

### Locales (desarrollo)

```bash
# Compilar assets
npm run build

# Generar APP_KEY
php artisan key:generate --show

# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders
php artisan db:seed

# Ver logs
tail -f storage/logs/laravel.log
```

### En Render (a través de Shell)

```bash
# Ver logs en tiempo real
tail -f /var/www/html/storage/logs/laravel.log

# Listar archivos build
ls -la /var/www/html/public/build/assets/

# Verificar BD
php artisan tinker
>>> DB::connection('mysql')->getPdo()->query('SELECT 1')

# Ejecutar comando manual
php artisan migrate:fresh --seed
```

### Git & Deploy

```bash
# Ver cambios antes de push
git status
git diff

# Commit y push
git add .
git commit -m "Fix: descripción del cambio"
git push origin feature/deployFixes

# En Render, verá cambios automáticamente si está habilitado "Auto-Deploy"
```

---

## Resumen del Flujo Completo

```
1. Preparar Localmente
   ↓
2. Crear BD en Railway → Obtener credenciales
   ↓
3. Conectar GitHub a Render
   ↓
4. Configurar Variables de Entorno en Render
   ↓
5. Primer Deploy (RUN_MIGRATIONS=true, RUN_SEEDERS=true)
   ↓
6. Verificar que funciona
   ↓
7. Cambiar RUN_SEEDERS=false
   ↓
8. Redeploy
   ↓
✅ Producción activa
```

---

## Archivos Clave Modificados

| Archivo                                | Cambio                                | Líneas |
| -------------------------------------- | ------------------------------------- | ------ |
| `dockerfile`                           | Reordenado npm build, logs verbosos   | 65-75  |
| `package.json`                         | Agregado script `prod`                | 6-9    |
| `docker/supervisord.conf`              | nodaemon=true, user=root, sin logfile | 1-22   |
| `docker/nginx.conf`                    | Config completa con location /build/  | 1-72   |
| `docker/entrypoint.sh`                 | Agregado RUN_SEEDERS, LF              | 35-40  |
| `database/migrations/2025_11_12_...`   | Idempotencia con Schema::hasColumn()  | 1-114  |
| `bootstrap/app.php`                    | trustProxies(at: '\*')                | 15     |
| `app/Providers/AppServiceProvider.php` | URL::forceScheme('https')             | 20-22  |
| `.gitattributes`                       | LF para _.sh y docker/_.conf          | 10-11  |

---

## Referencias Útiles

-   [Render Documentation](https://render.com/docs)
-   [Railway Documentation](https://docs.railway.app)
-   [Laravel Deployment](https://laravel.com/docs/deployment)
-   [Docker Best Practices](https://docs.docker.com/develop/dev-best-practices)
-   [Vite Guide](https://vitejs.dev/guide)

---

## Contacto & Soporte

Para problemas específicos del proyecto, revisa:

-   Logs en Render Dashboard → Logs
-   GitHub Issues en el repositorio
-   Documentación del proyecto en `/docs`

**Última actualización:** 27 de Noviembre, 2025
