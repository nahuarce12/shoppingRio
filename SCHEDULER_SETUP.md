# Laravel Scheduler Setup for XAMPP on Windows

Este documento explica cómo configurar el Laravel Scheduler para ejecutar tareas programadas en un entorno XAMPP con Windows.

## Tareas Programadas en ShoppingRio

El sistema tiene dos tareas programadas:

1. **EvaluateClientCategoriesJob**: Evalúa y actualiza las categorías de clientes según el uso de promociones (se ejecuta mensualmente)
2. **CleanupExpiredNewsJob**: Elimina noticias expiradas después del período de retención (se ejecuta diariamente a medianoche)

## Requisitos Previos

- XAMPP instalado con PHP en PATH del sistema
- Laravel configurado y funcionando
- Acceso administrativo a Windows

## Método 1: Windows Task Scheduler (Recomendado para Producción)

### Paso 1: Verificar PHP en PATH

Abre PowerShell o CMD y ejecuta:

```powershell
php -v
```

Si no funciona, agrega PHP al PATH:
1. Abre "Variables de entorno del sistema"
2. Agrega `C:\xampp\php` (o la ruta de tu instalación XAMPP) a la variable PATH

### Paso 2: Crear archivo batch para ejecutar el scheduler

Crea un archivo `run-scheduler.bat` en la raíz de tu proyecto con el siguiente contenido:

```batch
@echo off
cd /d C:\Programas\xampp\htdocs\shoppingRio
php artisan schedule:run >> storage\logs\scheduler.log 2>&1
```

Guárdalo con codificación ANSI (no UTF-8) para evitar problemas con caracteres especiales.

### Paso 3: Configurar Task Scheduler

1. Abre **Task Scheduler** (Programador de tareas):
   - Presiona `Win + R`
   - Escribe `taskschd.msc`
   - Presiona Enter

2. Crea una nueva tarea:
   - Click derecho en "Task Scheduler Library" → "Create Task"
   
3. **Pestaña General:**
   - Name: `Laravel Scheduler - ShoppingRio`
   - Description: `Ejecuta tareas programadas de Laravel cada minuto`
   - Marca: "Run whether user is logged on or not"
   - Marca: "Run with highest privileges"
   - Configure for: Windows 10 (o tu versión de Windows)

4. **Pestaña Triggers:**
   - Click "New"
   - Begin the task: "On a schedule"
   - Settings: "Daily"
   - Recur every: 1 days
   - Repeat task every: **1 minute**
   - For a duration of: "Indefinitely"
   - Enabled: Marcado
   - Click "OK"

5. **Pestaña Actions:**
   - Click "New"
   - Action: "Start a program"
   - Program/script: `C:\Programas\xampp\htdocs\shoppingRio\run-scheduler.bat`
   - Start in: `C:\Programas\xampp\htdocs\shoppingRio`
   - Click "OK"

6. **Pestaña Conditions:**
   - Desmarca: "Start the task only if the computer is on AC power"
   - Marca: "Wake the computer to run this task" (opcional)

7. **Pestaña Settings:**
   - Marca: "Allow task to be run on demand"
   - Marca: "Run task as soon as possible after a scheduled start is missed"
   - If the task fails, restart every: 1 minute
   - Attempt to restart up to: 3 times
   - If the running task does not end when requested: "Stop the existing instance"

8. Click "OK" para guardar la tarea

### Paso 4: Probar la configuración

Ejecuta manualmente la tarea para verificar:

```powershell
cd C:\Programas\xampp\htdocs\shoppingRio
php artisan schedule:run
```

Deberías ver en la consola qué comandos fueron ejecutados. También puedes revisar el log:

```powershell
type storage\logs\scheduler.log
```

### Paso 5: Verificar que la tarea se ejecuta automáticamente

1. En Task Scheduler, busca tu tarea "Laravel Scheduler - ShoppingRio"
2. Click derecho → "Run" para ejecutarla manualmente
3. Verifica el "Last Run Result" (debería ser 0x0 si fue exitosa)
4. Revisa los logs en `storage/logs/laravel.log` para ver las ejecuciones de los jobs

## Método 2: Ejecutar manualmente (Solo para Desarrollo/Testing)

Para pruebas rápidas, puedes ejecutar manualmente los jobs:

```powershell
# Ejecutar el scheduler una vez
php artisan schedule:run

# Ejecutar un job específico directamente
php artisan tinker
>>> App\Jobs\EvaluateClientCategoriesJob::dispatch();
>>> App\Jobs\CleanupExpiredNewsJob::dispatch();
```

O crear comandos Artisan personalizados:

```powershell
# Evaluar categorías de clientes
php artisan app:evaluate-categories

# Limpiar noticias expiradas
php artisan app:cleanup-news
```

## Método 3: Usar un script PowerShell en bucle (Alternativa)

Crea un archivo `scheduler-loop.ps1`:

```powershell
while ($true) {
    Set-Location "C:\Programas\xampp\htdocs\shoppingRio"
    php artisan schedule:run
    Start-Sleep -Seconds 60
}
```

Ejecuta el script en una ventana de PowerShell:

```powershell
powershell -File scheduler-loop.ps1
```

**Nota:** Este método requiere mantener la ventana de PowerShell abierta.

## Verificación de Logs

Los logs de las tareas programadas se encuentran en:

- **Laravel Log**: `storage/logs/laravel.log`
- **Scheduler Log** (si configuraste el batch): `storage/logs/scheduler.log`

Busca entradas como:

```
[2025-11-03 02:00:00] local.INFO: Starting client category evaluation job
[2025-11-03 02:05:00] local.INFO: Client category evaluation job completed {"duration_seconds":300,"stats":{...}}

[2025-11-03 00:00:00] local.INFO: Starting expired news cleanup job
[2025-11-03 00:00:10] local.INFO: Expired news cleanup job completed {"duration_seconds":10,"stats":{...}}
```

## Configuración de las Tareas

Las tareas pueden ser configuradas en `config/shopping.php`:

```php
'scheduled_jobs' => [
    'category_evaluation' => [
        'enabled' => env('JOB_CATEGORY_EVALUATION_ENABLED', true),
        'schedule' => 'monthly', // Primer día de cada mes a las 2 AM
    ],
    'news_cleanup' => [
        'enabled' => env('JOB_NEWS_CLEANUP_ENABLED', true),
        'schedule' => 'daily', // Diariamente a medianoche
        'retention_days' => env('NEWS_RETENTION_DAYS', 30),
    ]
],
```

Para deshabilitar una tarea temporalmente, agrega en `.env`:

```env
JOB_CATEGORY_EVALUATION_ENABLED=false
JOB_NEWS_CLEANUP_ENABLED=false
```

## Solución de Problemas

### La tarea no se ejecuta

1. Verifica que Task Scheduler esté corriendo:
   ```powershell
   Get-Service -Name "Task Scheduler"
   ```

2. Revisa el "Last Run Result" en Task Scheduler (0x0 = éxito)

3. Ejecuta manualmente el batch file para ver errores:
   ```powershell
   C:\Programas\xampp\htdocs\shoppingRio\run-scheduler.bat
   ```

### Errores de permisos

- Asegúrate de que la tarea se ejecuta "con privilegios más altos"
- Verifica que el usuario tenga permisos de escritura en `storage/logs`

### PHP no encontrado

- Verifica que PHP esté en el PATH del sistema
- Usa la ruta completa en el batch: `C:\xampp\php\php.exe artisan schedule:run`

### Logs no se generan

- Verifica permisos de escritura en `storage/logs`
- Ejecuta: `php artisan cache:clear` y `php artisan config:clear`

## Monitoreo en Producción

Para producción, considera:

1. **Monitoreo de Jobs**: Usar Laravel Horizon o Laravel Telescope
2. **Alertas de Fallos**: Configurar emails cuando los jobs fallen (ver método `failed()` en los jobs)
3. **Logs Centralizados**: Usar servicios como Papertrail, Loggly, o similar
4. **Health Checks**: Verificar periódicamente que el scheduler esté corriendo

## Recursos Adicionales

- [Laravel Task Scheduling Documentation](https://laravel.com/docs/11.x/scheduling)
- [Windows Task Scheduler Documentation](https://docs.microsoft.com/en-us/windows/win32/taskschd/task-scheduler-start-page)
