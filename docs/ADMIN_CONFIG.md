# Admin Configuration Guide

## Cambiar Email del Administrador

Hay dos formas de cambiar el email del administrador en ShoppingRio:

### Opción 1: Actualizar el Seeder (Recomendado para Fresh Install)

Si aún no has hecho deploy en producción o deseas resetear la base de datos:

1. Edita `database/seeders/DatabaseSeeder.php`
2. Busca la sección "Create Administrator"
3. Modifica el email:

```php
$admin = User::factory()->admin()->create([
    'name' => 'Administrator',
    'email' => 'tu-email@example.com',
    'password' => Hash::make('Admin123!'),
]);
```

4. Ejecuta el seeder:

```bash
php artisan migrate:fresh --seed
```

**Ventajas:**

-   ✅ Limpia y simple
-   ✅ Se aplica a todos los deploys nuevos
-   ✅ Ideal para ambiente de desarrollo

### Opción 2: Usar Comando Artisan (Recomendado para Producción)

Para cambiar el email en una base de datos existente SIN resetear todos los datos:

```bash
php artisan admin:update-email nuevo-email@example.com
```

O sin proporcionar el email (te pedirá que lo ingreses):

```bash
php artisan admin:update-email
```

**Ejemplo de uso:**

```bash
# Con email como argumento
php artisan admin:update-email nahuellarce@gmail.com

# Sin argumento (prompta interactivo)
php artisan admin:update-email
```

**Validaciones:**

-   ✅ Valida que el email tenga formato correcto
-   ✅ Verifica que el email no esté en uso por otro usuario
-   ✅ Muestra confirmación con email anterior y nuevo

**Salida esperada:**

```
✅ Administrator email successfully updated!
   Old email: admin@shoppingrio.com
   New email: nahuellarce@gmail.com
```

## Email del Administrador y Notificaciones

El email del administrador se usa para recibir:

1. **Solicitudes de aprobación de Dueños de Locales** - Cuando un nuevo dueño se registra
2. **Solicitudes de aprobación de Promociones** - Cuando un dueño de local crea una nueva promoción
3. **Reportes del Sistema** - Notificaciones de errores o eventos importantes

### Configuración Actual (Post-Deploy)

-   **Email del Admin:** `nahuellarce@gmail.com`
-   **Email From:** `noreply@nahuellarce.me` (Dominio verificado en Resend)
-   **Proveedor de Email:** Resend

### Verificar Email del Admin Actual

Para ver qué email tiene registrado el administrador actualmente:

```bash
# Acceder a la consola de Laravel
php artisan tinker

# Dentro de tinker:
>>> $admin = App\Models\User::where('user_type', 'administrador')->first();
>>> echo $admin->email;
```

## En Render (Producción)

Si necesitas cambiar el email del admin en Render:

### Opción A: Mediante Render CLI

```bash
# Conectarse al contenedor en Render
render exec -s shoppingrio bash

# Dentro del contenedor
php artisan admin:update-email shppngrio@gmail.com
```

### Opción B: Mediante Panel Render

1. Ve a tu servicio en [https://dashboard.render.com](https://dashboard.render.com)
2. Accede a "Shell" en la sección "Environment" → "Logs"
3. Ejecuta el comando:

```bash
cd /app && php artisan admin:update-email nuevo-email@example.com
```

### Opción C: Redeploy con Nuevo Seeder

1. Actualiza el email en `database/seeders/DatabaseSeeder.php`
2. Asegúrate que `RUN_SEEDERS=true` esté configurado en las variables de entorno
3. Haz push a la rama feature/deployFixes
4. Render redesplegará automáticamente

**Nota:** La opción C reseteará TODOS los datos de la base de datos. Usa solo en desarrollo o cuando sea intencional.

## Troubleshooting

### Problema: El email del admin no recibe notificaciones

**Causas posibles:**

1. Email mal configurado en el servidor
2. API key de Resend inválida o vencida
3. Dominio no verificado en Resend

**Soluciones:**

1. Verifica el email actual: `php artisan tinker` → `App\Models\User::where('user_type', 'administrador')->first()->email`
2. Revisa los logs: `storage/logs/laravel.log`
3. Verifica que `MAIL_FROM_ADDRESS` y `RESEND_API_KEY` estén configurados correctamente
4. Consulta [Email Setup Guide](./EMAIL_SETUP.md)

### Problema: "Email is already in use"

El comando `admin:update-email` rechazará emails que ya están registrados en el sistema. Solución:

```bash
# Listar todos los emails registrados
php artisan tinker
>>> App\Models\User::pluck('email');

# Si necesitas cambiar el email de otro usuario primero
>>> $user = App\Models\User::find(id);
>>> $user->update(['email' => 'nuevo@email.com']);
>>> $user->save();
```

### Problema: Comando no reconocido

Si ejecutas `php artisan admin:update-email` y ves "Command not found":

1. Asegúrate que estés en la rama correcta con los cambios
2. Ejecuta `composer dump-autoload`
3. Verifica que `UpdateAdminEmailCommand.php` exista en `app/Console/Commands/`
4. Reinicia el servidor artisan: `php artisan serve`

## Referencias

-   [Email Setup Guide](./EMAIL_SETUP.md) - Configuración de Resend y DNS
-   [Deployment Guide](./DEPLOY.md) - Guía general de deployment
-   Laravel Artisan Docs: https://laravel.com/docs/11.x/artisan
