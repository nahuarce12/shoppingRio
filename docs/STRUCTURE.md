# ShoppingRio - Estructura del Proyecto

Este documento describe la estructura organizada del proyecto después de la limpieza y reorganización del 12 de noviembre de 2025.

## 📁 Estructura de Directorios

```
shoppingRio/
├── 📂 app/                          # Código fuente de Laravel
│   ├── Actions/                     # Fortify Actions (CreateNewUser, etc.)
│   ├── Console/Commands/            # Comandos Artisan personalizados
│   ├── Events/                      # Event classes
│   ├── Http/
│   │   ├── Controllers/             # Controladores (Admin, Store, Client, Auth, Public)
│   │   ├── Middleware/              # Middleware personalizado (Admin, StoreOwner, Client)
│   │   └── Requests/                # Form Request validation classes
│   ├── Jobs/                        # Background Jobs (Category evaluation, News cleanup)
│   ├── Listeners/                   # Event Listeners
│   ├── Mail/                        # Mailable classes (9 email types)
│   ├── Models/                      # Eloquent Models (User, Store, Promotion, News, PromotionUsage)
│   ├── Policies/                    # Authorization Policies
│   ├── Providers/                   # Service Providers
│   └── Services/                    # Business Logic Services
│
├── 📂 bootstrap/                    # Bootstrap de Laravel
│   ├── app.php                      # Application bootstrap
│   ├── providers.php                # Service providers
│   └── cache/                       # Archivos de caché
│
├── 📂 config/                       # Archivos de configuración
│   ├── app.php                      # Configuración general
│   ├── auth.php                     # Autenticación
│   ├── database.php                 # Base de datos
│   ├── mail.php                     # Email (SMTP)
│   ├── shopping.php                 # Configuración personalizada del proyecto
│   └── ...
│
├── 📂 database/                     # Base de datos
│   ├── factories/                   # Model Factories para testing
│   ├── migrations/                  # 9 migrations (users, stores, promotions, etc.)
│   └── seeders/                     # Database Seeders (44 users, 20 stores, 50 promotions)
│
├── 📂 docs/                         # 📚 DOCUMENTACIÓN CENTRALIZADA
│   ├── INDEX.md                     # Índice de documentación
│   ├── STRUCTURE.md                 # Este archivo - estructura del proyecto
│   ├── JAVASCRIPT-MODULES.md        # Documentación de módulos JS
│   │
│   ├── 📂 planning/                 # Planificación y desarrollo
│   │   ├── feature-backend-core-1.md         # Backend completo (Phases 1-10, 100%)
│   │   └── feature-frontend-integration-1.md # Frontend integration
│   │
│   ├── 📂 testing/                  # Testing y QA
│   │   └── TESTING-CHECKLIST.md     # Checklist E2E completo (7 flows, 100% pass)
│   │
│   └── 📂 setup/                    # Configuración del sistema
│       └── SCHEDULER_SETUP.md       # Setup de tareas programadas
│
├── 📂 lang/                         # Archivos de idioma (español)
│   └── es/
│       ├── auth.php
│       ├── pagination.php
│       ├── passwords.php
│       └── validation.php
│
├── 📂 public/                       # Archivos públicos accesibles
│   ├── index.php                    # Entry point
│   ├── robots.txt
│   ├── build/                       # Assets compilados (Vite)
│   └── images/                      # Imágenes públicas
│
├── 📂 resources/                    # Recursos del frontend
│   ├── css/
│   │   └── app.css                  # Estilos principales
│   ├── js/
│   │   ├── app.js                   # JavaScript principal
│   │   ├── bootstrap.js             # Bootstrap configuration
│   │   └── modules/                 # Módulos ES6
│   │       ├── main.js              # Navegación global
│   │       ├── register.js          # Wizard de registro
│   │       ├── perfil-admin.js      # Dashboard admin
│   │       └── perfil-dueno.js      # Dashboard store owner
│   └── views/
│       ├── layouts/                 # Layouts base (app, dashboard, auth)
│       ├── components/              # Componentes Blade reutilizables
│       ├── home/                    # Vista principal
│       ├── pages/                   # Páginas públicas (promociones, locales, static)
│       ├── dashboard/               # Dashboards por rol (admin, store, client)
│       ├── auth/                    # Vistas de autenticación
│       └── emails/                  # Templates de email (9 tipos)
│
├── 📂 routes/                       # Rutas de la aplicación
│   ├── web.php                      # 74 rutas web (public, admin, store, client)
│   ├── auth.php                     # Rutas de autenticación (Fortify)
│   └── console.php                  # Comandos de consola
│
├── 📂 storage/                      # Storage privado
│   ├── app/                         # Application storage
│   │   ├── public/                  # Archivos accesibles vía symlink
│   │   │   ├── stores/logos/        # Logos de locales (uploaded)
│   │   │   ├── promotions/images/   # Imágenes de promociones
│   │   │   └── news/images/         # Imágenes de noticias
│   ├── framework/                   # Framework cache
│   └── logs/                        # Log files
│
├── 📂 tests/                        # Tests automatizados
│   ├── Feature/                     # Feature tests (28 test methods)
│   │   ├── AuthenticationFlowTest.php
│   │   ├── PromotionLifecycleTest.php
│   │   └── CategoryUpgradeTest.php
│   └── Unit/                        # Unit tests (11 test methods)
│       └── PromotionServiceTest.php
│
├── 📂 vendor/                       # Dependencias de Composer
│
├── .env                             # Variables de entorno (DATABASE, MAIL, etc.)
├── .env.example                     # Ejemplo de configuración
├── artisan                          # Laravel Artisan CLI
├── composer.json                    # Dependencias PHP
├── package.json                     # Dependencias Node.js
├── phpunit.xml                      # Configuración PHPUnit
├── vite.config.js                   # Configuración Vite
└── README.md                        # Documentación principal

```

## 🗑️ Archivos/Carpetas Eliminados

Los siguientes archivos y carpetas fueron eliminados por estar obsoletos o no ser utilizados:

### ❌ Carpetas Completas Eliminadas:

-   **frontEndEG/** - Mockup HTML estático antiguo (ya migrado a Laravel Blade) - ~15-20 MB
-   **plan/** - Movido a `docs/planning/`
-   **doc/** - Reorganizado en `docs/`
-   **app/Events/** - Carpeta vacía sin Event classes
-   **app/Listeners/** - Carpeta vacía sin Listener classes
-   **resources/views/client/** - Carpeta vacía sin vistas (funcionalidad en dashboard/client)
-   **resources/views/store/** - Carpeta vacía sin vistas (funcionalidad en dashboard/store)

### ❌ Controladores PHP Obsoletos Eliminados:

1. **HomeController.php** - Duplicado de PublicController@home (no usado en routes)
2. **LocalController.php** - Funcionalidad migrada a Admin/StoreController (no usado)
3. **NovedadController.php** - Funcionalidad migrada a Admin/NewsController (no usado)
4. **PageController.php** - Funcionalidad migrada a PublicController (no usado)
5. **PromocionController.php** - Funcionalidad migrada a Admin/PromotionController (no usado)

### ❌ Archivos de Vistas Eliminados:

-   **welcome.blade.php** - Vista por defecto de Laravel (no usada, reemplazada por home/index)

### ❌ Scripts Temporales Eliminados:

-   **validate-system.php** - Script temporal de validación (ya ejecutado)
-   **prepare-testing.ps1** - Script de preparación de testing (ya usado)

### ❌ Documentación Temporal Eliminada:

-   **doc/phase-4-summary.md** - Documentación temporal de desarrollo
-   **doc/phase-5-smoke-tests.md** - Documentación temporal de desarrollo
-   **doc/task-008-summary.md** - Documentación temporal de tasks
-   **doc/MOCKUP-ARCHIVE-README.md** - Documentación obsoleta del mockup

**Total eliminado**:

-   **7 carpetas** (2 con contenido, 5 vacías)
-   **12 archivos** (5 controladores obsoletos, 1 vista, 2 scripts temporales, 4 documentos)
-   **Espacio liberado**: ~15-20 MB

## 📋 Archivos Reorganizados

### Movimientos realizados:

```
ANTES                                    → DESPUÉS
────────────────────────────────────────────────────────────────────────
TESTING-CHECKLIST.md                     → docs/testing/TESTING-CHECKLIST.md
SCHEDULER_SETUP.md                       → docs/setup/SCHEDULER_SETUP.md
plan/feature-backend-core-1.md           → docs/planning/feature-backend-core-1.md
plan/feature-frontend-integration-1.md   → docs/planning/feature-frontend-integration-1.md
doc/README.md                            → docs/JAVASCRIPT-MODULES.md
```

## 📊 Estadísticas del Proyecto

### Código Fuente:

-   **Líneas de código PHP**: ~15,000+ líneas
-   **Archivos PHP**: ~150 archivos
-   **Controladores**: 12 controladores (~1,743 líneas)
-   **Modelos**: 5 modelos con 40+ scopes
-   **Servicios**: 5 servicios de lógica de negocio
-   **Migrations**: 9 migrations
-   **Seeders**: 5 seeders principales
-   **Mailable Classes**: 9 tipos de emails
-   **Form Requests**: 6 clases de validación
-   **Policies**: 3 políticas de autorización
-   **Middleware**: 3 middleware personalizados
-   **Jobs**: 2 background jobs

### Testing:

-   **Test Files**: 4 archivos de test
-   **Test Methods**: 39 métodos (28 feature + 11 unit)
-   **E2E Test Flows**: 7 flows (100% pass rate)
-   **Total Test Cases**: 100+ test cases validados

### Frontend:

-   **Blade Views**: 50+ vistas
-   **JavaScript Modules**: 4 módulos ES6
-   **Email Templates**: 9 templates Blade
-   **Components**: 5+ componentes reutilizables

### Documentación:

-   **Archivos .md**: 6 archivos organizados
-   **Total líneas de doc**: ~5,000+ líneas
-   **Guías completas**: 3 (planning, testing, setup)

## 🎯 Estructura de Rutas

### Rutas Públicas (11 rutas):

-   `/` - Home page
-   `/promociones` - Browse promotions
-   `/locales` - Browse stores
-   `/about`, `/contacto` - Static pages

### Rutas de Autenticación (Auth):

-   `/login`, `/register`, `/logout`
-   `/forgot-password`, `/reset-password`
-   `/email/verify`

### Rutas de Admin (26 rutas):

-   `/admin/dashboard`
-   `/admin/stores/*` - CRUD de locales
-   `/admin/users/approval/*` - Aprobación de store owners
-   `/admin/promotions/approval/*` - Aprobación de promociones
-   `/admin/news/*` - CRUD de noticias
-   `/admin/reports/*` - 7 tipos de reportes

### Rutas de Store Owner (9 rutas):

-   `/store/dashboard`
-   `/store/promotions/*` - Gestión de promociones propias
-   `/store/promotion-usages/*` - Solicitudes de descuentos

### Rutas de Cliente (6 rutas):

-   `/client/dashboard`
-   `/client/promotions` - Browse promotions
-   `/client/promotion-usages/request` - Solicitar descuento

**Total: 74 rutas definidas**

## 🔒 Permisos y Roles

### Administrador:

-   Acceso completo a todas las rutas
-   CRUD de locales, noticias
-   Aprobación de store owners y promociones
-   Generación de reportes

### Store Owner (Dueño de Local):

-   Dashboard con estadísticas propias
-   CRUD de promociones (solo crear/eliminar, no editar)
-   Aceptar/rechazar solicitudes de descuentos
-   Ver reportes de uso de sus promociones
-   **Requiere aprobación del admin para acceder**

### Cliente:

-   Dashboard personal con categoría y estadísticas
-   Browse promociones (filtrado por categoría)
-   Solicitar descuentos (validación de elegibilidad)
-   Ver historial de descuentos usados
-   **Requiere verificación de email**

### Usuario No Registrado:

-   Ver todas las promociones publicadas
-   Ver todos los locales
-   Páginas estáticas (about, contact)
-   **No puede solicitar descuentos**

## 🗄️ Base de Datos

### Tablas (9 total):

1. **users** - Usuarios con roles y categorías
2. **stores** - Locales del shopping
3. **promotions** - Promociones con validaciones complejas
4. **news** - Noticias con auto-expiración
5. **promotion_usage** - Solicitudes de descuentos (pivot)
6. **password_reset_tokens** - Tokens de reset
7. **sessions** - Sesiones de usuario
8. **jobs** - Queue jobs
9. **failed_jobs** - Jobs fallidos

### Relaciones:

-   User → Stores (1:N)
-   Store → Promotions (1:N)
-   User → News (1:N as creator)
-   Client → Promotions (N:N via promotion_usage)

## 📧 Sistema de Emails

### 9 Tipos de Notificaciones:

1. **Client Verification** - Verificación de email al registrarse
2. **Store Owner Pending** - Solicitud en revisión (auto)
3. **Store Owner Approved** - Cuenta aprobada por admin
4. **Store Owner Rejected** - Cuenta rechazada con razón
5. **Promotion Approved** - Promoción aprobada por admin
6. **Promotion Denied** - Promoción rechazada con razón
7. **Usage Request** - Cliente solicita descuento (a store owner)
8. **Usage Accepted** - Descuento aceptado (a cliente)
9. **Usage Rejected** - Descuento rechazado con alternativas
10. **Category Upgrade** - Cliente sube de categoría

**Sistema de Queue**: Configurado con sync (instantáneo) o database (async)

## 🔧 Configuración Necesaria

### Variables de Entorno Principales (.env):

```env
# Base de datos
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=shopping_rio
DB_USERNAME=root
DB_PASSWORD=

# Email (Gmail SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls

# Queue
QUEUE_CONNECTION=sync  # o 'database' para async
```

## 🚀 Estado del Proyecto

-   **Versión**: 1.0
-   **Status**: ✅ COMPLETE (100%)
-   **Tasks Completadas**: 79/79
-   **Testing**: All 7 E2E flows PASSED
-   **Production**: READY FOR DEPLOYMENT

## 📖 Navegación Rápida

-   **README Principal**: `../README.md`
-   **Índice de Docs**: `INDEX.md`
-   **Testing Guide**: `testing/TESTING-CHECKLIST.md`
-   **Backend Plan**: `planning/feature-backend-core-1.md`
-   **Setup Guide**: `setup/SCHEDULER_SETUP.md`

---

**Última actualización**: November 12, 2025
**Autor**: Development Team
**Versión**: 1.0
