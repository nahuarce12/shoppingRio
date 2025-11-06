# ShoppingRio - Manual E2E Testing Checklist

**Date Started**: November 3, 2025  
**Last Updated**: November 5, 2025  
**Phase**: 10 - Final Testing  
**Status**: 🟡 In Progress

---

## 📊 Testing Progress Summary

### ✅ Completed Issues & Fixes (November 4-5, 2025)

1. **Translation System**: ✅ Created Spanish validation messages (`lang/es/validation.php`)
2. **Navigation Links**: ✅ Fixed navbar routes (from `#promociones` anchors to proper Laravel routes)
3. **Registration Form**: ✅ Added hidden `tipo_usuario` field to both Cliente and Dueño forms
4. **Email Verification**: ✅ Configured Fortify views and EmailVerificationController
5. **Dashboard Routes**: ✅ Fixed all dashboard view paths:
    - Client: `dashboard.client.index`
    - Store: `dashboard.store.index`
    - Admin: `dashboard.admin.index`
6. **Navbar Dropdown**: ✅ Fixed user menu to show dashboard link based on user type
7. **Promotion Dates**: ✅ Fixed PromotionFactory to generate 60% active promotions (dates include TODAY)
8. **Database Seeding**: ✅ Fresh seed with correct dates (20 active promotions available)
9. **NewsService Method**: ✅ Fixed Client Dashboard to use `getActiveNewsForUser()` instead of non-existent method

### 🔧 Dashboard Fixes (November 5, 2025)

1. **Client Dashboard**: ✅ FIXED - Shows real user data (nombre, apellido, email, categoría, usage stats)
2. **Store Owner Dashboard**: ✅ FIXED - Shows real store data (nombre, statistics, pending requests)
3. **Admin Dashboard**: ✅ FIXED - Shows real system statistics (stores, clients, promotions, category distribution)

### 🔧 View Regenerations (November 5, 2025)

1. **Locales Page**: ✅ REGENERATED - Clean view without duplicated lines, uses real $stores data
2. **Promociones Page**: ✅ REGENERATED - Clean view without duplicated lines, uses real $promotions data

### 🎯 Testing Status by Flow

-   **Flow 1 - Cliente Registration**: ✅ READY - Registration, Email Verification, Login working
-   **Flow 2 - Store Owner Management**: ⏳ NOT STARTED
-   **Flow 3 - Admin Dashboard**: ⏳ NOT STARTED
-   **Flow 4 - Business Logic**: ⏳ NOT STARTED
-   **Flow 5 - Form Validation**: ✅ PARTIALLY TESTED (registration forms validated)
-   **Flow 6 - Permissions**: ⏳ NOT STARTED
-   **Flow 7 - Email System**: ✅ PARTIALLY TESTED (verification emails working)

---

## 🚀 Pre-Testing Setup

Run the preparation script first:

```powershell
.\prepare-testing.ps1
```

Or manually:

1. ✅ Start XAMPP (Apache + MySQL)
2. ✅ Run `php artisan migrate:fresh --seed`
3. ✅ Configure email in .env (Mailtrap recommended)
4. ✅ Start queue worker: `php artisan queue:work`
5. ✅ Clear all caches: `php artisan optimize:clear`
6. ✅ Cache routes: `php artisan route:cache`

---

## 📋 Testing Flows

### FLOW 1: Cliente Registration & Usage Request ⏱️ ~10 minutes

#### 1.1 Registration

-   [ ] Navigate to: http://localhost/shoppingRio/public/register
-   [ ] Click "Registrarme como Cliente"
-   [ ] Fill form with test data:
    -   Nombre: `Test Cliente`
    -   Apellido: `Manual Testing`
    -   Email: `testclient@example.com`
    -   Password: `password123` (mínimo 8 chars)
    -   Confirmar Password: `password123`
    -   Teléfono: `341-1234567`
    -   Fecha Nacimiento: (ensure 18+ years old)
    -   Accept terms & conditions
-   [ ] **Test JavaScript validations**:
    -   [ ] Try submit with empty fields → validation errors shown
    -   [ ] Try wrong email format → "email inválido"
    -   [ ] Try password mismatch → "contraseñas no coinciden"
    -   [ ] Try birthdate < 18 years → "debes ser mayor de 18"
    -   [ ] All fields turn green (is-valid) when correct
-   [ ] Submit form
-   [ ] **Expected**: Redirect to email verification notice page
-   [ ] **Expected**: Flash message "successful registration"

#### 1.2 Email Verification

-   [ ] Check Mailtrap inbox for verification email
-   [ ] **Expected**: Email from "Shopping Rosario" with subject "Verifica tu Email"
-   [ ] Click verification link in email
-   [ ] **Expected**: Redirect to home with "email verified" message
-   [ ] **Expected**: User can now login

#### 1.3 Login as Cliente

-   [ ] Navigate to: http://localhost/shoppingRio/public/login
-   [ ] Enter credentials: `testclient@example.com` / `password123`
-   [ ] **Test JavaScript validations**:
    -   [ ] Try submit with empty email → validation error
    -   [ ] Try invalid email format → validation error
    -   [ ] Toggle password visibility works (eye icon)
-   [ ] Submit form
-   [ ] **Expected**: Redirect to `/client/dashboard`
-   [ ] **Expected**: Welcome message with client name
-   [ ] **Expected**: Category badge shows "Inicial"

#### 1.4 Browse Promotions

-   [ ] Navigate to: http://localhost/shoppingRio/public/promociones
-   [ ] **Expected**: See list of promotions with pagination
-   [ ] **Expected**: Can filter by:
    -   [ ] Search text (updates on change)
    -   [ ] Categoría Mínima (Inicial/Medium/Premium)
    -   [ ] Store (dropdown with all stores)
-   [ ] Test filters:
    -   [ ] Select "Medium" → only Medium and Premium promotions shown
    -   [ ] Select a specific store → only that store's promotions shown
    -   [ ] Clear filters → all promotions shown again

#### 1.5 Request Promotion Usage

-   [ ] Click on a promotion card to view details
-   [ ] **Expected**: See promotion details page
-   [ ] **Expected**: "Solicitar Descuento" button visible (if eligible)
-   [ ] Click "Solicitar Descuento"
-   [ ] **Expected**: Modal opens with promotion details
-   [ ] **Expected**: If NOT eligible, see reason (date, day, category, already used)
-   [ ] If eligible, click "Confirmar Solicitud"
-   [ ] **Expected**: Modal closes, redirect with success message
-   [ ] **Expected**: Usage request status is "enviada"
-   [ ] Navigate to `/client/usages`
-   [ ] **Expected**: See request in "Solicitudes Pendientes" section

#### 1.6 Check Email Notification

-   [ ] Check Mailtrap inbox
-   [ ] **Expected**: Email to store owner with subject "Nueva Solicitud de Descuento"
-   [ ] **Expected**: Email contains client name and promotion details

**✅ FLOW 1 COMPLETE - Record any issues found**

---

### FLOW 2: Store Owner Registration & Promotion Management ⏱️ ~15 minutes

#### 2.1 Registration as Store Owner

-   [ ] Logout current user
-   [ ] Navigate to: http://localhost/shoppingRio/public/register
-   [ ] Click "Registrarme como Dueño"
-   [ ] Fill form with test data:
    -   **Store Data**:
        -   Nombre Local: `Test Store Manual`
        -   Categoría: `tecnologia`
        -   Teléfono Local: `341-9876543`
        -   Descripción: (minimum 20 chars) `Esta es una tienda de prueba para testing manual del sistema de shopping.`
    -   **Owner Data**:
        -   Nombre: `Test Owner`
        -   Apellido: `Manual Testing`
        -   DNI: `12345678` (8 digits)
        -   CUIT: `20123456789` (auto-formats to `20-12345678-9`)
        -   Email: `testowner@example.com`
        -   Password: `password123`
        -   Confirmar Password: `password123`
        -   Teléfono Personal: `341-5555555`
    -   Accept terms
-   [ ] **Test JavaScript validations**:
    -   [ ] Store description character counter updates (X/500)
    -   [ ] DNI only accepts digits, max 8
    -   [ ] CUIT auto-formats with hyphens as you type
    -   [ ] Password match validation in real-time
    -   [ ] Try submit with description < 20 chars → error
    -   [ ] All fields validate and turn green when correct
-   [ ] Submit form
-   [ ] **Expected**: Success message "Solicitud enviada al administrador"
-   [ ] **Expected**: Warning shown "Tu cuenta será revisada"

#### 2.2 Check Pending Approval Email

-   [ ] Check Mailtrap inbox
-   [ ] **Expected**: Email to `testowner@example.com` with subject "Solicitud en Revisión"
-   [ ] **Expected**: Email says account is pending admin approval

#### 2.3 Admin Approves Store Owner

-   [ ] Login as admin: `admin@shoppingrio.com` / `password`
-   [ ] **Expected**: Redirect to `/admin/dashboard`
-   [ ] Navigate to: `/admin/user-approvals`
-   [ ] **Expected**: See "Test Owner" in pending approvals list
-   [ ] **Expected**: Store details visible (Test Store Manual, tecnologia)
-   [ ] Click "Aprobar" button
-   [ ] **Expected**: Confirmation dialog appears
-   [ ] Confirm approval
-   [ ] **Expected**: Success message "Usuario aprobado"
-   [ ] **Expected**: User disappears from pending list

#### 2.4 Check Approval Email

-   [ ] Check Mailtrap inbox
-   [ ] **Expected**: Email to `testowner@example.com` with subject "Cuenta Aprobada"
-   [ ] **Expected**: Email contains login instructions

#### 2.5 Login as Store Owner

-   [ ] Logout admin
-   [ ] Navigate to: http://localhost/shoppingRio/public/login
-   [ ] Login with: `testowner@example.com` / `password123`
-   [ ] **Expected**: Redirect to `/store/dashboard`
-   [ ] **Expected**: Welcome message with store name "Test Store Manual"
-   [ ] **Expected**: Dashboard shows store statistics

#### 2.6 Create Promotion

-   [ ] Navigate to: `/store/promotions/create`
-   [ ] Fill promotion form:
    -   Texto: `20% de descuento en todos los productos de tecnología`
    -   Fecha Desde: (today)
    -   Fecha Hasta: (today + 30 days)
    -   Días Semana: Select Monday, Wednesday, Friday (3 días)
    -   Categoría Mínima: `Medium`
-   [ ] **Test JavaScript validations**:
    -   [ ] Character counter updates (X/200)
    -   [ ] Try fecha_hasta < fecha_desde → error
    -   [ ] Try selecting no days → error
    -   [ ] Try texto > 200 chars → error
    -   [ ] All validations work in real-time
-   [ ] Submit form
-   [ ] **Expected**: Success message "Promoción creada, pendiente de aprobación"
-   [ ] **Expected**: Redirect to promotions list
-   [ ] **Expected**: New promotion has status badge "Pendiente"

#### 2.7 Admin Approves Promotion

-   [ ] Logout and login as admin again
-   [ ] Navigate to: `/admin/promotions`
-   [ ] Filter by status: "Pendiente"
-   [ ] **Expected**: See "Test Store Manual" promotion
-   [ ] Click "Aprobar" button
-   [ ] **Expected**: Confirmation dialog
-   [ ] Confirm approval
-   [ ] **Expected**: Success message
-   [ ] **Expected**: Promotion status changes to "Aprobada"

#### 2.8 Check Approval Email

-   [ ] Check Mailtrap inbox
-   [ ] **Expected**: Email to store owner with subject "Promoción Aprobada"

#### 2.9 Verify Promotion is Public

-   [ ] Logout all users
-   [ ] Navigate to: http://localhost/shoppingRio/public/promociones
-   [ ] **Expected**: See "Test Store Manual" promotion in list
-   [ ] **Expected**: Can see details without login

#### 2.10 Store Owner Manages Usage Requests

-   [ ] Login as `testclient@example.com` (from Flow 1)
-   [ ] Request usage for "Test Store Manual" promotion
-   [ ] Logout and login as `testowner@example.com`
-   [ ] Navigate to: `/store/promotion-usages`
-   [ ] **Expected**: See pending usage request from "Test Cliente Manual Testing"
-   [ ] **Test Approval**:
    -   [ ] Click "Aprobar"
    -   [ ] **Expected**: Success message
    -   [ ] **Expected**: Request moves to history with status "Aceptada"
    -   [ ] Check Mailtrap: email to client "Descuento Aceptado"
-   [ ] Create another test request (as client)
-   [ ] **Test Rejection**:
    -   [ ] Click "Rechazar"
    -   [ ] **Expected**: Modal opens asking for reason
    -   [ ] Enter reason: `No disponible en este momento`
    -   [ ] Confirm rejection
    -   [ ] **Expected**: Success message
    -   [ ] **Expected**: Request moves to history with status "Rechazada"
    -   [ ] Check Mailtrap: email to client "Descuento Rechazado" with reason

**✅ FLOW 2 COMPLETE - Record any issues found**

---

### FLOW 3: Admin Dashboard & Reports ⏱️ ~10 minutes

#### 3.1 Admin Login & Dashboard

-   [ ] Login as: `admin@shoppingrio.com` / `password`
-   [ ] **Expected**: Redirect to `/admin/dashboard`
-   [ ] **Expected**: Dashboard shows:
    -   [ ] Total stores count
    -   [ ] Total promotions count (aprobadas)
    -   [ ] Total clients count
    -   [ ] Pending approvals count (users + promotions)

#### 3.2 Store Management

-   [ ] Navigate to: `/admin/stores`
-   [ ] **Expected**: See list of all stores with pagination
-   [ ] Click "Crear Local"
-   [ ] Fill form with test data
-   [ ] Submit form
-   [ ] **Expected**: Success message, new store in list
-   [ ] Click "Editar" on a store
-   [ ] Modify store name
-   [ ] Submit form
-   [ ] **Expected**: Success message, changes reflected
-   [ ] **Note**: Don't delete (would cascade delete promotions)

#### 3.3 User Approvals

-   [ ] Navigate to: `/admin/user-approvals`
-   [ ] **Expected**: List of pending store owners (if any)
-   [ ] **Expected**: Can see store details for each owner
-   [ ] **Test rejection**:
    -   [ ] Create another store owner registration
    -   [ ] As admin, click "Rechazar"
    -   [ ] **Expected**: Modal asks for rejection reason
    -   [ ] Enter reason: `Documentación incompleta`
    -   [ ] Confirm rejection
    -   [ ] **Expected**: Success message
    -   [ ] Check Mailtrap: rejection email sent with reason

#### 3.4 Promotion Approvals

-   [ ] Navigate to: `/admin/promotions`
-   [ ] Filter by "Pendiente"
-   [ ] **Expected**: List of pending promotions
-   [ ] **Test approve**: (already tested in Flow 2)
-   [ ] **Test deny**:
    -   [ ] Create a new promotion as store owner
    -   [ ] As admin, click "Denegar"
    -   [ ] **Expected**: Modal asks for reason
    -   [ ] Enter reason: `Descuento excesivo, contra política del shopping`
    -   [ ] Confirm denial
    -   [ ] **Expected**: Success message
    -   [ ] **Expected**: Promotion status "Denegada"
    -   [ ] Check Mailtrap: denial email to store owner with reason

#### 3.5 News Management

-   [ ] Navigate to: `/admin/news`
-   [ ] Click "Crear Novedad"
-   [ ] Fill form:
    -   Texto: `Nuevo horario extendido: de 9am a 10pm`
    -   Fecha Desde: (today)
    -   Fecha Hasta: (today + 7 days)
    -   Categoría: `Medium`
-   [ ] Submit form
-   [ ] **Expected**: Success message
-   [ ] **Expected**: New news item in list
-   [ ] Navigate to `/client/dashboard` (as Medium or Premium client)
-   [ ] **Expected**: News item visible in "Novedades" section

#### 3.6 Reports

-   [ ] Navigate to: `/admin/reports`
-   [ ] **Expected**: Multiple report options
-   [ ] **Test System Summary**:
    -   [ ] Click "Ver Resumen del Sistema"
    -   [ ] **Expected**: See totals for stores, promotions, clients, usages
-   [ ] **Test Promotion Usage Report**:
    -   [ ] Select date range (last 30 days)
    -   [ ] **Expected**: Table with promotion usage statistics
    -   [ ] **Expected**: Can see usage count per promotion
    -   [ ] Click "Exportar a Excel"
    -   [ ] **Expected**: Excel file downloads with data
-   [ ] **Test Store Performance Report**:
    -   [ ] Select period (3 months)
    -   [ ] **Expected**: Table with store performance metrics
    -   [ ] **Expected**: Shows usages per store
-   [ ] **Test Client Activity Report**:
    -   [ ] Select period (6 months)
    -   [ ] **Expected**: Table with client activity
    -   [ ] **Expected**: Shows category distribution

**✅ FLOW 3 COMPLETE - Record any issues found**

---

### FLOW 4: Business Logic Validation ⏱️ ~15 minutes

#### 4.1 Category Restrictions

-   [ ] Login as Inicial client: `client1@example.com` / `password`
-   [ ] Navigate to promotions
-   [ ] **Expected**: Only see promotions with category "Inicial"
-   [ ] **Expected**: Cannot see "Medium" or "Premium" promotions
-   [ ] Logout, login as Medium client: `client4@example.com` / `password`
-   [ ] **Expected**: See "Inicial" AND "Medium" promotions
-   [ ] **Expected**: Cannot see "Premium" promotions
-   [ ] Logout, login as Premium client: `client8@example.com` / `password`
-   [ ] **Expected**: See ALL promotions (Inicial, Medium, Premium)

#### 4.2 Single-Use Rule

-   [ ] Login as any client
-   [ ] Request usage for a promotion
-   [ ] Store owner approves it
-   [ ] Try to request same promotion again
-   [ ] **Expected**: Error message "Ya utilizaste esta promoción"
-   [ ] **Expected**: Button disabled or not shown

#### 4.3 Date Range Validation

-   [ ] Login as admin
-   [ ] Create a promotion with:
    -   Fecha Desde: (tomorrow)
    -   Fecha Hasta: (tomorrow + 7 days)
-   [ ] Approve the promotion
-   [ ] As client, try to view/request this promotion TODAY
-   [ ] **Expected**: Not eligible, reason: "Promoción no vigente"
-   [ ] OR **Expected**: Promotion not shown in available list

#### 4.4 Day of Week Validation

-   [ ] Create a promotion valid only for "Monday, Wednesday, Friday"
-   [ ] **On a Tuesday** (or any non-valid day):
    -   [ ] Try to request usage
    -   [ ] **Expected**: Not eligible, reason: "No válida para el día de hoy"
-   [ ] **On a Monday** (valid day):
    -   [ ] Try to request usage
    -   [ ] **Expected**: Request successful

#### 4.5 Category Auto-Upgrade: Inicial → Medium

-   [ ] Create a NEW client (not used before)
-   [ ] **Expected**: Initial category is "Inicial"
-   [ ] Create 5 different approved promotions
-   [ ] As client, request usage for all 5
-   [ ] As store owners, ACCEPT all 5 requests
-   [ ] **Expected**: Client now has 5 "aceptada" usages
-   [ ] Run category evaluation:
    ```powershell
    php artisan app:evaluate-client-categories
    ```
-   [ ] Refresh client profile
-   [ ] **Expected**: Category changed to "Medium"
-   [ ] Check Mailtrap: email "¡Subiste de Categoría!" from Inicial to Medium

#### 4.6 Category Auto-Upgrade: Medium → Premium

-   [ ] Use a Medium client (or the one from 4.5)
-   [ ] Create 10 MORE approved promotions (total 15)
-   [ ] As client, request usage for all 10
-   [ ] As store owners, ACCEPT all 10 requests
-   [ ] Run category evaluation again:
    ```powershell
    php artisan app:evaluate-client-categories
    ```
-   [ ] **Expected**: Category changed to "Premium"
-   [ ] Check Mailtrap: email "¡Subiste de Categoría!" from Medium to Premium

#### 4.7 Only Recent Usages Count (6 months window)

-   [ ] Manually update database:
    ```sql
    UPDATE promotion_usages
    SET fecha_uso = DATE_SUB(NOW(), INTERVAL 7 MONTH)
    WHERE client_id = X LIMIT 3;
    ```
-   [ ] Run category evaluation
-   [ ] **Expected**: Old usages (>6 months) NOT counted
-   [ ] **Expected**: Client category based only on recent usages

#### 4.8 Only Accepted Usages Count

-   [ ] Create a client with:
    -   10 "aceptada" usages
    -   5 "rechazada" usages
    -   3 "enviada" (pending) usages
-   [ ] Run category evaluation
-   [ ] **Expected**: Only "aceptada" usages count (10)
-   [ ] **Expected**: Category based on 10, not 18

**✅ FLOW 4 COMPLETE - Record any issues found**

---

### FLOW 5: Form Validation Deep Dive ⏱️ ~10 minutes

#### 5.1 Client-Side Validation (JavaScript)

**Login Form**:

-   [ ] Empty email → error before submit
-   [ ] Invalid email format → error before submit
-   [ ] Empty password → error before submit
-   [ ] Toggle password visibility works
-   [ ] Fields turn green (is-valid) when correct

**Register Cliente Form**:

-   [ ] Name with numbers → error (pattern validation)
-   [ ] Email format validation
-   [ ] Password < 8 chars → error
-   [ ] Password mismatch → real-time error on confirm field
-   [ ] Birthdate < 18 years → custom JavaScript error
-   [ ] Phone format validation
-   [ ] Postal code must be 4 digits
-   [ ] Terms checkbox required
-   [ ] All fields show real-time feedback on blur

**Register Dueño Form**:

-   [ ] Store description < 20 chars → error
-   [ ] Store description character counter updates live
-   [ ] DNI: can only type digits, max 8
-   [ ] CUIT: auto-formats to XX-XXXXXXXX-X as you type
-   [ ] Try typing letters in CUIT → removed automatically
-   [ ] Password match validation in real-time
-   [ ] All fields validate before submit

**Promotion Creation Form**:

-   [ ] Texto > 200 chars → error
-   [ ] Character counter updates (X/200)
-   [ ] fecha_hasta < fecha_desde → custom error
-   [ ] No days selected → error "Seleccioná al menos un día"
-   [ ] All validations prevent submit

#### 5.2 Server-Side Validation (Laravel FormRequests)

**Test by bypassing JavaScript**:

-   [ ] Use browser dev tools to remove `required` attribute
-   [ ] Submit empty form
-   [ ] **Expected**: Laravel validation errors returned
-   [ ] **Expected**: Flash error messages shown
-   [ ] **Expected**: Old input preserved (form fields repopulated)

**Test unique constraints**:

-   [ ] Try registering with existing email
-   [ ] **Expected**: Error "El email ya está registrado"
-   [ ] Try creating store with existing name
-   [ ] **Expected**: Error "El nombre del local ya existe"

**Test FormRequest custom validation**:

-   [ ] Try requesting promotion not approved
-   [ ] **Expected**: Error from PromotionUsageRequest validation
-   [ ] Try requesting promotion outside date range
-   [ ] **Expected**: Error "Promoción no vigente"

**✅ FLOW 5 COMPLETE - Record any issues found**

---

### FLOW 6: Permissions & Access Control ⏱️ ~8 minutes

#### 6.1 Unregistered User Permissions

-   [ ] Logout all users
-   [ ] **Can access**:
    -   [ ] Home page (/)
    -   [ ] Promociones (/promociones)
    -   [ ] Locales (/locales)
    -   [ ] Contacto (/contacto)
    -   [ ] Quienes Somos (/about)
    -   [ ] Login (/login)
    -   [ ] Register (/register)
-   [ ] **Cannot access** (should redirect to login):
    -   [ ] `/admin/dashboard` → redirect /login
    -   [ ] `/store/dashboard` → redirect /login
    -   [ ] `/client/dashboard` → redirect /login
    -   [ ] `/client/promotion-usages/request` → redirect /login

#### 6.2 Cliente Permissions

-   [ ] Login as client
-   [ ] **Can access**:
    -   [ ] `/client/dashboard`
    -   [ ] `/client/promotions`
    -   [ ] `/client/usages`
    -   [ ] `/client/profile`
-   [ ] **Cannot access** (should return 403 Forbidden):
    -   [ ] `/admin/dashboard` → 403 error
    -   [ ] `/store/dashboard` → 403 error
    -   [ ] `/admin/stores` → 403 error
    -   [ ] `/store/promotions/create` → 403 error

#### 6.3 Store Owner Permissions

-   [ ] Login as store owner
-   [ ] **Can access**:
    -   [ ] `/store/dashboard`
    -   [ ] `/store/promotions` (own store only)
    -   [ ] `/store/promotions/create`
    -   [ ] `/store/promotion-usages` (own store only)
-   [ ] **Cannot access**:
    -   [ ] `/admin/dashboard` → 403
    -   [ ] `/client/dashboard` → 403
    -   [ ] Other store's promotions → 403 or filtered out

#### 6.4 Admin Permissions

-   [ ] Login as admin
-   [ ] **Can access ALL routes**:
    -   [ ] `/admin/*` (all admin routes)
    -   [ ] `/store/*` (all store routes, any store)
    -   [ ] `/client/*` (all client routes, any client)
    -   [ ] Public routes

#### 6.5 Login Redirects by Role

-   [ ] Login as admin
-   [ ] **Expected**: Redirect to `/admin/dashboard`
-   [ ] Logout, login as store owner
-   [ ] **Expected**: Redirect to `/store/dashboard`
-   [ ] Logout, login as client
-   [ ] **Expected**: Redirect to `/client/dashboard`

#### 6.6 Store Owner Approval Check

-   [ ] Create store owner but DON'T approve (admin rejects or leaves pending)
-   [ ] Try to login as that store owner
-   [ ] **Expected**: Error "Tu cuenta aún no ha sido aprobada"
-   [ ] **Expected**: Cannot login until approved

**✅ FLOW 6 COMPLETE - Record any issues found**

---

### FLOW 7: Email System Verification ⏱️ ~12 minutes

#### 7.1 Email Configuration Check

-   [ ] Verify `.env` has correct mail settings:
    ```
    MAIL_MAILER=smtp
    MAIL_HOST=smtp.mailtrap.io
    MAIL_PORT=2525
    MAIL_USERNAME=<your-mailtrap-username>
    MAIL_PASSWORD=<your-mailtrap-password>
    ```
-   [ ] Queue worker is running: `php artisan queue:work`

#### 7.2 Test All Email Types

**1. Client Email Verification**:

-   [ ] Register new client
-   [ ] Check Mailtrap inbox
-   [ ] **Expected**: Email with subject "Verifica tu Email - Shopping Rosario"
-   [ ] **Expected**: Contains client name
-   [ ] **Expected**: Has verification link
-   [ ] Click link works and verifies account

**2. Store Owner Pending Approval**:

-   [ ] Register new store owner
-   [ ] Check Mailtrap inbox
-   [ ] **Expected**: Email "Solicitud en Revisión"
-   [ ] **Expected**: Says account is pending admin approval

**3. Store Owner Approval**:

-   [ ] Admin approves store owner
-   [ ] Check Mailtrap inbox
-   [ ] **Expected**: Email "Cuenta Aprobada"
-   [ ] **Expected**: Contains login instructions
-   [ ] **Expected**: Has link to login page

**4. Store Owner Rejection**:

-   [ ] Admin rejects store owner (with reason)
-   [ ] Check Mailtrap inbox
-   [ ] **Expected**: Email "Solicitud Rechazada"
-   [ ] **Expected**: Contains rejection reason
-   [ ] **Expected**: Professional and respectful tone

**5. Promotion Approval**:

-   [ ] Admin approves promotion
-   [ ] Check store owner's Mailtrap inbox
-   [ ] **Expected**: Email "Promoción Aprobada"
-   [ ] **Expected**: Contains promotion details

**6. Promotion Rejection**:

-   [ ] Admin rejects promotion (with reason)
-   [ ] Check store owner's Mailtrap inbox
-   [ ] **Expected**: Email "Promoción Rechazada"
-   [ ] **Expected**: Contains rejection reason

**7. Usage Request (to Store Owner)**:

-   [ ] Client requests promotion usage
-   [ ] Check store owner's Mailtrap inbox
-   [ ] **Expected**: Email "Nueva Solicitud de Descuento"
-   [ ] **Expected**: Contains client details and promotion info
-   [ ] **Expected**: Has link to manage usages

**8. Usage Acceptance (to Client)**:

-   [ ] Store owner accepts usage request
-   [ ] Check client's Mailtrap inbox
-   [ ] **Expected**: Email "Descuento Aceptado"
-   [ ] **Expected**: Contains promotion details
-   [ ] **Expected**: Congratulations message

**9. Usage Rejection (to Client)**:

-   [ ] Store owner rejects usage (with reason)
-   [ ] Check client's Mailtrap inbox
-   [ ] **Expected**: Email "Descuento Rechazado"
-   [ ] **Expected**: Contains rejection reason

**10. Category Upgrade**:

-   [ ] Trigger category upgrade (5 usages → Medium)
-   [ ] Check client's Mailtrap inbox
-   [ ] **Expected**: Email "¡Subiste de Categoría!"
-   [ ] **Expected**: Shows old category (Inicial) and new category (Medium)
-   [ ] **Expected**: Explains benefits of new category

#### 7.3 Email Queue Processing

-   [ ] Stop queue worker
-   [ ] Trigger an email action (e.g., register client)
-   [ ] **Expected**: Email NOT sent immediately
-   [ ] **Expected**: Job added to `jobs` table in database
-   [ ] Start queue worker: `php artisan queue:work`
-   [ ] **Expected**: Job processed
-   [ ] **Expected**: Email sent to Mailtrap
-   [ ] **Expected**: Job removed from `jobs` table

#### 7.4 Email Template Quality Check

For each email type, verify:

-   [ ] Subject line is clear and descriptive
-   [ ] Greeting is personalized with recipient name
-   [ ] Content is well-formatted (HTML + plain text)
-   [ ] All links work correctly
-   [ ] Footer contains contact information
-   [ ] Responsive design (check in mobile view)
-   [ ] No typos or grammar errors
-   [ ] Professional appearance

**✅ FLOW 7 COMPLETE - Record any issues found**

---

## 📊 Testing Summary

### Completion Checklist:

-   [ ] Flow 1: Cliente Registration & Usage (10 min)
-   [ ] Flow 2: Store Owner Registration & Management (15 min)
-   [ ] Flow 3: Admin Dashboard & Reports (10 min)
-   [ ] Flow 4: Business Logic Validation (15 min)
-   [ ] Flow 5: Form Validation Deep Dive (10 min)
-   [ ] Flow 6: Permissions & Access Control (8 min)
-   [ ] Flow 7: Email System Verification (12 min)

**Total Estimated Time**: ~80 minutes

### Issues Found:

Record all bugs, issues, or unexpected behavior here:

1. **Issue #1**:

    - Flow:
    - Description:
    - Severity: (Critical/High/Medium/Low)
    - Steps to Reproduce:
    - Expected:
    - Actual:

2. **Issue #2**:
    - Flow:
    - Description:
    - Severity:
    - Steps to Reproduce:
    - Expected:
    - Actual:

(Add more as needed)

---

## ✅ Sign-Off

**Tester**: ****\*\*\*\*****\_\_\_****\*\*\*\*****
**Date**: ****\*\*\*\*****\_\_\_****\*\*\*\*****
**Overall Result**: [ ] PASS [ ] PASS with minor issues [ ] FAIL

**Notes**:

---

## 🚀 Next Steps After Testing

If all tests pass:

1. ✅ Mark TASK-079 as complete
2. ✅ Update project status to 100%
3. ✅ Deploy to production (if ready)
4. ✅ Monitor production for first 48 hours

If issues found:

1. ⚠️ Document all issues in this checklist
2. ⚠️ Create GitHub issues for each bug
3. ⚠️ Prioritize fixes (Critical → High → Medium → Low)
4. ⚠️ Fix issues and re-test affected flows
5. ⚠️ Repeat until all flows pass

---

**Good luck with testing! 🎯**
