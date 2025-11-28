# Configuración de Emails con Resend - ShoppingRio

Guía completa para configurar el sistema de envío de emails usando **Resend** en ShoppingRio.

## 📋 Tabla de Contenidos

1. [Requisitos](#requisitos)
2. [Crear Cuenta en Resend](#crear-cuenta-en-resend)
3. [Verificar Dominio](#verificar-dominio)
4. [Configurar Variables de Entorno](#configurar-variables-de-entorno)
5. [Instalar Paquete Laravel](#instalar-paquete-laravel)
6. [Emails Disponibles](#emails-disponibles)
7. [Troubleshooting](#troubleshooting)

---

## Requisitos

-   Dominio propio (ej: `nahuellarce.me`)
-   Acceso a DNS del dominio (Namecheap, GoDaddy, etc.)
-   Cuenta en [Resend.com](https://resend.com)
-   Render con app desplegada

---

## Crear Cuenta en Resend

1. Ve a [resend.com](https://resend.com)
2. Click en **Sign Up**
3. Crea cuenta con tu email
4. Verifica el email
5. En Dashboard, copia tu **API Key** (necesitarás después)

---

## Verificar Dominio

### Paso 1: Agregar Dominio en Resend

1. En Resend Dashboard, ve a **Domains**
2. Click en **Add Domain**
3. Ingresa tu dominio (ej: `nahuellarce.me`)
4. Resend mostrará 3 registros DNS

**Registros típicos:**

```
Tipo: CNAME
Nombre: default._domainkey.nahuellarce.me
Valor: [valor-resend-aqui]

Tipo: CNAME
Nombre: bounce.nahuellarce.me
Valor: [valor-resend-aqui]

Tipo: TXT
Nombre: nahuellarce.me
Valor: v=spf1 include:resend.com ~all
```

### Paso 2: Agregar Registros en Namecheap

1. Ve a [Namecheap Dashboard](https://www.namecheap.com/dashboard)
2. Busca tu dominio y click en **Manage**
3. Ve a **Advanced DNS**
4. Agrega los 3 registros que Resend te dio:
    - 2 registros CNAME
    - 1 registro TXT

**Ejemplo en Namecheap:**

```
Host: default._domainkey
Type: CNAME
Value: [valor-resend]
TTL: 3600

Host: bounce
Type: CNAME
Value: [valor-resend]
TTL: 3600

Host: @
Type: TXT
Value: v=spf1 include:resend.com ~all
TTL: 3600
```

### Paso 3: Verificar Propagación

1. Espera 10-60 minutos (a veces hasta 24-48 horas)
2. En Resend Dashboard, el estado del dominio cambiará a **Verified**
3. Una vez verificado, puedes enviar emails desde tu dominio

---

## Configurar Variables de Entorno

### En Render

Ve a **Settings → Environment** y agrega:

```env
MAIL_MAILER=resend
RESEND_API_KEY=re_xxxxxxxxxxxxxx
MAIL_FROM_ADDRESS=noreply@nahuellarce.me
MAIL_FROM_NAME=ShoppingRio
```

**Descripción:**

| Variable            | Valor                    | Notas                                   |
| ------------------- | ------------------------ | --------------------------------------- |
| `MAIL_MAILER`       | `resend`                 | Proveedor de emails                     |
| `RESEND_API_KEY`    | `re_xxxxx...`            | Tu API key de Resend (secreto)          |
| `MAIL_FROM_ADDRESS` | `noreply@nahuellarce.me` | Email virtual (no requiere cuenta real) |
| `MAIL_FROM_NAME`    | `ShoppingRio`            | Nombre que aparece en los emails        |

### Email Virtual vs Real

-   **`noreply@nahuellarce.me`**: Email virtual, usuarios NO pueden responder, ideal para notificaciones automáticas
-   **`support@nahuellarce.me`**: Si lo quieres real, crea la cuenta en tu proveedor de hosting y usuarios pueden responder

---

## Instalar Paquete Laravel

El paquete `resend/resend-laravel` debe estar instalado en `composer.json`:

```bash
composer require resend/resend-laravel
```

Si ya está instalado (commit previo), solo asegúrate de que el `composer.lock` esté en el repo:

```bash
git add composer.json composer.lock
git commit -m "Add: Resend email provider"
git push
```

---

## Emails Disponibles

### 1. Email de Verificación de Registro (Clientes)

**Disparador:** Cuando un cliente se registra
**Destinatario:** Email del cliente
**Contenido:** Link de verificación de email

```
From: noreply@nahuellarce.me
To: cliente@example.com
Subject: Verifica tu email - ShoppingRio
```

### 2. Notificación de Aprobación de Dueño (Dueños)

**Disparador:** Cuando el admin aprueba un dueño de local
**Destinatario:** Email del dueño
**Contenido:** Confirmación de aprobación + link de acceso

```
From: noreply@nahuellarce.me
To: dueno@example.com
Subject: Tu cuenta ha sido aprobada - ShoppingRio
```

### 3. Notificación de Rechazo de Dueño

**Disparador:** Cuando el admin rechaza un dueño
**Destinatario:** Email del dueño
**Contenido:** Motivo del rechazo

```
From: noreply@nahuellarce.me
To: dueno@example.com
Subject: Tu solicitud ha sido rechazada - ShoppingRio
```

### 4. Cambio de Categoría de Cliente

**Disparador:** Cuando un cliente sube de categoría (Inicial → Medium → Premium)
**Destinatario:** Email del cliente
**Contenido:** Felicitaciones + nuevos beneficios

```
From: noreply@nahuellarce.me
To: cliente@example.com
Subject: ¡Felicitaciones! Tu categoría ha cambiado - ShoppingRio
```

---

## Troubleshooting

### Error: "Dominio no verificado"

**Causa:** Los registros DNS no se han propagado

**Solución:**

1. Verifica que los registros estén correctos en Namecheap
2. Usa herramientas como [dnschecker.org](https://dnschecker.org) para verificar propagación
3. Espera 24-48 horas máximo
4. Si sigue sin funcionar, revisa los valores exactos de los registros

### Error: "Can only send to your own email"

**Causa:** Estás en modo "testing" de Resend, no en producción verificada

**Solución:**

1. Verifica que el dominio esté en estado **Verified** en Resend
2. Usa un email verificado o espera a que Resend confirme el dominio

### Error: "Connection timeout to SMTP"

**Causa:** Firewall de Render bloquea conexiones SMTP

**Solución:**

-   ✅ Resend ya está configurado para evitar esto (usa API, no SMTP)
-   Verifica que `MAIL_MAILER=resend` esté configurado

### Emails no llegan

**Checklist:**

1. ✅ `RESEND_API_KEY` está configurada correctamente
2. ✅ Dominio está **Verified** en Resend
3. ✅ `MAIL_FROM_ADDRESS` usa tu dominio verificado
4. ✅ Revisa carpeta de SPAM
5. ✅ En Render, ve a Logs y busca errores de Resend

### Probar localmente

```bash
php artisan tinker

# Enviar email de prueba
Mail::to('test@example.com')->send(new \Illuminate\Auth\Notifications\VerifyEmail());
```

---

## Resumen del Flujo

```
1. Crear cuenta en Resend
   ↓
2. Verificar dominio (agregar registros DNS)
   ↓
3. Esperar propagación DNS (10 min - 48 horas)
   ↓
4. Configurar variables en Render
   ↓
5. Redeploy
   ↓
✅ Emails funcionando
```

---

## Emails de Prueba (Resend)

Resend permite enviar pruebas **gratis** a tu email registrado:

```bash
# En desarrollo local
MAIL_MAILER=resend \
RESEND_API_KEY=re_xxxxx \
php artisan mail:send test@example.com
```

---

## Referencias Útiles

-   [Documentación Resend](https://resend.com/docs)
-   [Resend + Laravel](https://resend.com/docs/integrations/laravel)
-   [DNS Checker](https://dnschecker.org)
-   [SPF/DKIM/DMARC Explicado](https://mxtoolbox.com/)

---

## Contacto & Soporte

Para problemas específicos:

-   Revisa logs en Render Dashboard
-   Consulta estado del dominio en Resend Dashboard
-   Verifica propagación DNS con dnschecker.org

**Última actualización:** 28 de Noviembre, 2025
