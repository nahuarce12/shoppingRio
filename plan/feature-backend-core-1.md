---
goal: Implement core backend functionality and business logic
version: 1.0
date_created: 2025-10-31
last_updated: 2025-11-01
owner: Development Team
status: In Progress
progress: Phase 2 Complete (20%)
tags: [feature, backend, database, authentication, business-logic]
---

# Introduction

Status badge: (status: In Progress, color: yellow)
Progress: Phases 1-2 Complete (Database + Models) | Phases 3-10 Pending

Implement the core backend functionality for the ShoppingRio application, including database schema, Eloquent models with relationships, authentication system with role-based access control, and business logic services. This plan builds upon the completed frontend integration (feature-frontend-integration-1) and establishes the foundation for all CRUD operations, user workflows, and automated processes required by the project specifications.

## 1. Requirements & Constraints

### Functional Requirements

-   **REQ-001**: Implement database schema matching the minimum data model specified in project instructions (USUARIOS, NOVEDADES, PROMOCIONES, LOCALES, USO_PROMOCIONES).
-   **REQ-002**: Create Eloquent models with proper relationships for all entities (User, Store, Promotion, News, PromotionUsage).
-   **REQ-003**: Extend Laravel's authentication to support 4 user types: Administrator, Store Owner (dueño local), Client, and Unregistered User.
-   **REQ-004**: Implement email verification for client registration per project requirement.
-   **REQ-005**: Implement admin approval workflow for store owner accounts before granting access.
-   **REQ-006**: Support client category system (Inicial, Medium, Premium) with automatic upgrade logic based on promotion usage.
-   **REQ-007**: Implement promotion validation rules: date ranges, day-of-week restrictions, category access, single-use constraint.
-   **REQ-008**: Support promotion approval workflow where store owners create promotions and admins approve/deny them.
-   **REQ-009**: Implement promotion usage request flow where clients request discounts and store owners accept/reject.
-   **REQ-010**: Generate sequential numeric codes for stores and promotions as specified in business rules.
-   **REQ-011**: Auto-expire news based on date ranges.
-   **REQ-012**: Category-based visibility for promotions and news (e.g., Premium clients see all, Inicial clients see only Inicial promotions).

### Technical Requirements

-   **TECH-001**: Use Laravel 11.x migrations for all schema changes with proper indexes and foreign keys.
-   **TECH-002**: Implement soft deletes for Stores and Promotions to preserve historical data.
-   **TECH-003**: Use Form Request classes for server-side validation of all user inputs.
-   **TECH-004**: Create Policy classes for authorization logic (StorePolicy, PromotionPolicy, NewsPolicy).
-   **TECH-005**: Use Laravel's Mailable classes for all email notifications (verification, approval, promotion status).
-   **TECH-006**: Implement scheduled job for automatic category upgrades (evaluates every 6 months).
-   **TECH-007**: Use Eloquent scopes for complex queries (active promotions, category-filtered content).
-   **TECH-008**: Implement database transactions for critical operations (promotion usage, category upgrades).

### Security Requirements

-   **SEC-001**: Hash all passwords using Laravel's bcrypt (minimum 8 characters per spec).
-   **SEC-002**: Implement middleware for role-based access control (AdminMiddleware, StoreOwnerMiddleware, ClientMiddleware).
-   **SEC-003**: Validate email uniqueness across all user types.
-   **SEC-004**: Prevent SQL injection via Eloquent ORM and parameterized queries.
-   **SEC-005**: Implement CSRF protection on all state-changing forms.
-   **SEC-006**: Rate limit authentication endpoints to prevent brute force attacks.

### Business Rules

-   **BUS-001**: Each store has unique sequential numeric code (auto-increment).
-   **BUS-002**: Each promotion has unique sequential numeric code (auto-increment).
-   **BUS-003**: Promotions specify valid days of week (array of 7 booleans, Monday=0 to Sunday=6).
-   **BUS-004**: Promotions have minimum client category (Inicial, Medium, Premium).
-   **BUS-005**: Client categories follow hierarchy: Inicial → Medium → Premium (access to lower tiers).
-   **BUS-006**: New clients default to 'Inicial' category upon registration.
-   **BUS-007**: Category upgrade based on accepted promotion count in last 6 months (threshold to be defined).
-   **BUS-008**: Clients can use each promotion only once (enforced via unique constraint on pivot table).
-   **BUS-009**: Category evaluation runs automatically every 6 months via scheduled task.
-   **BUS-010**: Promotion requests have 3 states: 'enviada' (pending), 'aceptada' (accepted), 'rechazada' (rejected).
-   **BUS-011**: Promotions have 3 approval states: 'pendiente' (pending admin approval), 'aprobada' (approved), 'denegada' (denied).
-   **BUS-012**: News auto-expire when current date exceeds fechaHastaNovedad.
-   **BUS-013**: News visibility follows same category hierarchy as promotions.
-   **BUS-014**: Unregistered users can view all promotions but cannot request usage.

### Constraints

-   **CON-001**: Must work in XAMPP environment (Apache + MySQL, Windows-based development).
-   **CON-002**: Database must be MySQL/MariaDB compatible.
-   **CON-003**: Email sending must support SMTP configuration for both development (Mailtrap) and production.
-   **CON-004**: Sequential codes must handle concurrent requests safely (use database auto-increment or locks).
-   **CON-005**: Category upgrade thresholds are configurable (not hardcoded).

### Guidelines

-   **GUD-001**: Follow Laravel naming conventions (PascalCase models, snake_case tables/columns).
-   **GUD-002**: Use resource controllers for CRUD operations where applicable.
-   **GUD-003**: Implement model factories for all entities to support testing and seeding.
-   **GUD-004**: Create comprehensive seeders with realistic test data for all user roles.
-   **GUD-005**: Log important business events (promotion approvals, category changes, usage requests).
-   **GUD-006**: Use database transactions for operations affecting multiple tables.

### Design Patterns

-   **PAT-001**: Repository pattern NOT required (use Eloquent directly per Laravel conventions).
-   **PAT-002**: Service classes for complex business logic (PromotionService, CategoryUpgradeService, ReportService).
-   **PAT-003**: Observer pattern for model events (e.g., send email on promotion approval).
-   **PAT-004**: Policy-based authorization for all resource access checks.
-   **PAT-005**: Form Request classes for validation logic separation from controllers.

## 2. Implementation Steps

### Implementation Phase 1: Database Schema & Migrations ✅

-   GOAL-001: Create complete database schema with all tables, relationships, and constraints.
-   **Status:** COMPLETED (2025-11-01)

| Task     | Description                                                                                                                                                                                                                                                        | Completed | Date       |
| -------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ | --------- | ---------- |
| TASK-001 | Create migration for `users` table extension: add `tipo_usuario` enum ('administrador', 'dueño de local', 'cliente'), `categoria_cliente` enum ('Inicial', 'Medium', 'Premium'), `approved_at` timestamp, `approved_by` foreign key.                               | ✅        | 2025-11-01 |
| TASK-002 | Create migration for `stores` table (locales): `id` (auto-increment), `codigo` (unique sequential), `nombre`, `ubicacion`, `rubro`, `owner_id` (FK to users), soft deletes, timestamps.                                                                            | ✅        | 2025-11-01 |
| TASK-003 | Create migration for `promotions` table (promociones): `id`, `codigo` (unique sequential), `texto`, `fecha_desde`, `fecha_hasta`, `dias_semana` JSON (7 booleans), `categoria_minima` enum, `estado` enum, `store_id` FK, soft deletes, timestamps.                | ✅        | 2025-11-01 |
| TASK-004 | Create migration for `news` table (novedades): `id`, `codigo`, `texto`, `fecha_desde`, `fecha_hasta`, `categoria_destino` enum ('Inicial', 'Medium', 'Premium'), `created_by` FK to users, timestamps.                                                             | ✅        | 2025-11-01 |
| TASK-005 | Create migration for `promotion_usage` pivot table (uso_promociones): `id`, `client_id` FK to users, `promotion_id` FK to promotions, `fecha_uso`, `estado` enum ('enviada', 'aceptada', 'rechazada'), unique constraint on (client_id, promotion_id), timestamps. | ✅        | 2025-11-01 |
| TASK-006 | Add database indexes: `users.tipo_usuario`, `users.categoria_cliente`, `stores.codigo`, `promotions.codigo`, `promotions.estado`, `promotions.fecha_desde/hasta`, `news.fecha_hasta`, `promotion_usage.estado`.                                                    | ✅        | 2025-11-01 |

#### Phase 1 Findings (2025-11-01)

**Migration Files Created:**

-   `2025_11_01_170951_add_role_fields_to_users_table.php` - Extended users table with role system
-   `2025_11_01_171024_create_stores_table.php` - Created stores table with sequential codes
-   `2025_11_01_171057_create_promotions_table.php` - Created promotions table with complex business rules
-   `2025_11_01_171145_create_news_table.php` - Created news table with category targeting
-   `2025_11_01_171218_create_promotion_usage_table.php` - Created pivot table for usage tracking

**Users Table Extensions (TASK-001):**

-   Added `tipo_usuario` enum field with values ('administrador', 'dueño de local', 'cliente'), defaults to 'cliente', indexed for performance
-   Added `categoria_cliente` enum ('Inicial', 'Medium', 'Premium'), nullable, defaults to 'Inicial' for new clients, indexed
-   Added `approved_at` timestamp to track when store owner accounts were approved by admin
-   Added `approved_by` foreign key referencing users table to track which admin approved the account (set null on admin deletion)
-   All new fields positioned after existing fields for logical grouping
-   Proper down() migration to rollback changes cleanly

**Stores Table (TASK-002):**

-   Primary key `id` using auto-increment
-   `codigo` field as unsigned integer with unique constraint and index (sequential codes will be generated via model events in Phase 2)
-   `nombre` varchar(100) for store name
-   `ubicacion` varchar(50) for location within shopping center
-   `rubro` varchar(20) for business category (indexed for filtering queries)
-   `owner_id` foreign key to users table with cascade deletion
-   Soft deletes enabled to preserve historical data and references
-   Timestamps for audit trail
-   Indexes on `codigo`, `rubro`, and `owner_id` for query optimization

**Promotions Table (TASK-003):**

-   Primary key `id` using auto-increment
-   `codigo` field as unsigned integer with unique constraint (sequential generation via model)
-   `texto` varchar(200) for promotion description
-   `fecha_desde` and `fecha_hasta` date fields defining validity period (both indexed)
-   `dias_semana` JSON column storing array of 7 boolean values (Monday=0 to Sunday=6) for day-of-week restrictions
-   `categoria_minima` enum ('Inicial', 'Medium', 'Premium') defining minimum client category, defaults to 'Inicial'
-   `estado` enum ('pendiente', 'aprobada', 'denegada') for admin approval workflow, defaults to 'pendiente', indexed
-   `store_id` foreign key with cascade deletion
-   Soft deletes to preserve usage history even after deletion
-   Composite index on (`estado`, `fecha_desde`, `fecha_hasta`) for efficient active promotions queries
-   Individual indexes on `categoria_minima` for filtering

**News Table (TASK-004):**

-   Primary key `id` using auto-increment
-   `codigo` unsigned integer with unique constraint (sequential generation via model)
-   `texto` varchar(200) for news content
-   `fecha_desde` and `fecha_hasta` date fields for validity period
-   `categoria_destino` enum ('Inicial', 'Medium', 'Premium') for category-based visibility, defaults to 'Inicial'
-   `created_by` foreign key to users (admin) with cascade deletion
-   Timestamps for creation/update tracking
-   Index on `fecha_hasta` for auto-expiration queries
-   Composite index on (`fecha_hasta`, `categoria_destino`) for efficient active news filtering by category

**Promotion Usage Pivot Table (TASK-005):**

-   Primary key `id` using auto-increment
-   `client_id` foreign key to users table (cascade deletion)
-   `promotion_id` foreign key to promotions table (cascade deletion)
-   `fecha_uso` date field recording when usage was requested/accepted
-   `estado` enum ('enviada', 'aceptada', 'rechazada') tracking request lifecycle, defaults to 'enviada', indexed
-   **Unique constraint on (`client_id`, `promotion_id`) to enforce single-use business rule (BUS-008)**
-   Composite index on (`promotion_id`, `estado`) for store owners to query pending requests
-   Index on `fecha_uso` for date range queries and category upgrade calculations

**Index Strategy (TASK-006):**
All required indexes implemented directly in table creation migrations:

-   `users`: indexes on `tipo_usuario`, `categoria_cliente` for role-based queries
-   `stores`: indexes on `codigo` (unique), `rubro`, `owner_id` for listing/filtering
-   `promotions`: indexes on `codigo` (unique), `estado`, `fecha_desde`, `fecha_hasta`, `categoria_minima`, plus composite index for active promotion queries
-   `news`: indexes on `fecha_hasta`, `categoria_destino`, plus composite index for active news queries
-   `promotion_usage`: indexes on `estado`, `fecha_uso`, plus composite index for request management

**Database Validation:**

-   All 8 migrations executed successfully in batch 1
-   Zero errors during migration execution
-   Foreign key constraints properly established (cascade deletions configured)
-   Enum values match project specifications exactly
-   JSON column type supported (requires MySQL 5.7+ / MariaDB 10.2+, compatible with XAMPP default installations)
-   Soft deletes configured for `stores` and `promotions` tables as required by TECH-002
-   Unique constraints enforced at database level (single-use promotion rule, sequential codes)

**Schema Alignment with Requirements:**

-   ✅ REQ-001: Minimum data model implemented (USUARIOS, LOCALES, PROMOCIONES, NOVEDADES, USO_PROMOCIONES)
-   ✅ TECH-001: Laravel 11.x migrations with proper indexes and foreign keys
-   ✅ TECH-002: Soft deletes implemented for Stores and Promotions
-   ✅ BUS-001: Store sequential codes ready (field created, generation logic pending Phase 2)
-   ✅ BUS-002: Promotion sequential codes ready (field created, generation logic pending Phase 2)
-   ✅ BUS-003: Days of week stored as JSON array (validation logic pending Phase 5)
-   ✅ BUS-008: Single-use constraint enforced via unique index on pivot table
-   ✅ BUS-010: Promotion usage states defined ('enviada', 'aceptada', 'rechazada')
-   ✅ BUS-011: Promotion approval states defined ('pendiente', 'aprobada', 'denegada')
-   ✅ CON-002: MySQL/MariaDB compatible (no PostgreSQL-specific features used)

**Technical Decisions:**

-   Used unsigned integers for `codigo` fields instead of VARCHAR to save space and improve index performance
-   Sequential code generation deferred to model events (Phase 2) rather than database triggers for better Laravel integration
-   JSON column for `dias_semana` chosen over 7 separate boolean columns to reduce table width and simplify queries
-   Composite indexes added proactively for anticipated common queries (active promotions, active news by category)
-   Soft deletes use Laravel's built-in `deleted_at` timestamp column (no custom implementation needed)
-   Foreign keys configured with appropriate deletion strategies: cascade for mandatory relationships, set null for optional audit fields

**Next Steps:**

-   Phase 2: Create Eloquent models with relationships, casts, and scopes to interact with these tables
-   Implement model observers for sequential code generation (BUS-001, BUS-002)
-   Add date/JSON casting in models for type safety
-   Create query scopes for common filtering patterns (active promotions, category-based visibility)

### Implementation Phase 2: Eloquent Models & Relationships ✅

-   GOAL-002: Create Eloquent models with proper relationships, casts, and scopes.
-   **Status:** COMPLETED (2025-11-01)

| Task     | Description                                                                                                                                                                                                                                | Completed | Date       |
| -------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ | --------- | ---------- |
| TASK-007 | Extend `User` model: add fillable fields, casts for enums, relationships (`stores()`, `promotionUsages()`, `createdNews()`, `approvedUsers()`), scopes (`clients()`, `storeOwners()`, `admins()`), accessor for category hierarchy check.  | ✅        | 2025-11-01 |
| TASK-008 | Create `Store` model: fillable, soft deletes, relationships (`owner()`, `promotions()`), accessor for sequential `codigo` generation, scope `active()`.                                                                                    | ✅        | 2025-11-01 |
| TASK-009 | Create `Promotion` model: fillable, soft deletes, casts for `dias_semana` JSON and date fields, relationships (`store()`, `usages()`), scopes (`approved()`, `active()`, `validToday()`, `forCategory()`), accessor for eligibility check. | ✅        | 2025-11-01 |
| TASK-010 | Create `News` model: fillable, casts for dates, relationships (`creator()`), scopes (`active()`, `forCategory()`), automatic expiration check in scope.                                                                                    | ✅        | 2025-11-01 |
| TASK-011 | Create `PromotionUsage` pivot model: fillable, relationships (`client()`, `promotion()`), casts for estado enum, scopes (`pending()`, `accepted()`, `rejected()`).                                                                         | ✅        | 2025-11-01 |
| TASK-012 | Implement model events (boot method): auto-generate sequential codes for Store/Promotion, send notification emails on promotion approval/usage status changes.                                                                             | ✅        | 2025-11-01 |

#### Phase 2 Findings (2025-11-01)

**Models Created:**

-   `app/Models/User.php` - Extended existing model
-   `app/Models/Store.php` - New model with soft deletes
-   `app/Models/Promotion.php` - New model with soft deletes
-   `app/Models/News.php` - New model
-   `app/Models/PromotionUsage.php` - Pivot model

**User Model Extensions (TASK-007):**

-   **Fillable Fields Added:** `tipo_usuario`, `categoria_cliente`, `approved_at`, `approved_by`
-   **Casts Implemented:** `approved_at` as datetime (email_verified_at and password already casted)
-   **Relationships Implemented:**
    -   `stores()` - HasMany to Store (one owner can have multiple stores)
    -   `promotionUsages()` - HasMany to PromotionUsage (client's usage history)
    -   `createdNews()` - HasMany to News (admin's created news)
    -   `approvedUsers()` - HasMany to User (admin's approved store owners)
    -   `approver()` - BelongsTo User (who approved this store owner)
-   **Query Scopes:**
    -   `clients()` - Filter users by tipo_usuario='cliente'
    -   `storeOwners()` - Filter users by tipo_usuario='dueño de local'
    -   `admins()` - Filter users by tipo_usuario='administrador'
    -   `approved()` - Filter approved users (approved_at not null)
    -   `pending()` - Filter pending store owners awaiting approval
    -   `byCategory($category)` - Filter clients by specific category
-   **Helper Methods:**
    -   `isAdmin()`, `isStoreOwner()`, `isClient()` - Role checking
    -   `isApproved()` - Check if store owner is approved
    -   `canAccessCategory($requiredCategory)` - Hierarchical category access check
    -   `getCategoryLevel()` - Get numeric category level (1-3)

**Store Model (TASK-008):**

-   **Traits:** HasFactory, SoftDeletes
-   **Fillable Fields:** `codigo`, `nombre`, `ubicacion`, `rubro`, `owner_id`
-   **Casts:** `codigo` as integer
-   **Relationships:**
    -   `owner()` - BelongsTo User (store owner)
    -   `promotions()` - HasMany Promotion (store's promotions)
-   **Query Scopes:**
    -   `active()` - Filter non-deleted stores (explicit soft delete filter)
    -   `byRubro($rubro)` - Filter by business category
    -   `search($search)` - Search by name (LIKE query)
-   **Model Events:**
    -   `creating` event - Auto-generate sequential `codigo` (max + 1, includes soft deleted)
-   **Accessors:**
    -   `getActivePromotionsCountAttribute()` - Count approved active promotions

**Promotion Model (TASK-009):**

-   **Traits:** HasFactory, SoftDeletes
-   **Fillable Fields:** `codigo`, `texto`, `fecha_desde`, `fecha_hasta`, `dias_semana`, `categoria_minima`, `estado`, `store_id`
-   **Casts:**
    -   `codigo` as integer
    -   `fecha_desde`, `fecha_hasta` as date
    -   `dias_semana` as array (JSON array of 7 booleans)
-   **Relationships:**
    -   `store()` - BelongsTo Store
    -   `usages()` - HasMany PromotionUsage
-   **Query Scopes:**
    -   `approved()` - Filter estado='aprobada'
    -   `pending()` - Filter estado='pendiente'
    -   `denied()` - Filter estado='denegada'
    -   `active()` - Approved + within date range (uses Carbon::today())
    -   `validToday()` - Active + valid for current day of week (JSON_EXTRACT query)
    -   `forCategory($clientCategory)` - Filter by accessible category (hierarchy aware)
    -   `byStore($storeId)` - Filter by store
-   **Model Events:**
    -   `creating` event - Auto-generate sequential `codigo` (max + 1, includes soft deleted)
-   **Helper Methods:**
    -   `isActive()` - Check if promotion is approved and within date range
    -   `isValidForDay($dayOfWeek)` - Check if valid for specific day (0=Monday to 6=Sunday)
    -   `isValidToday()` - Check if valid for today (date + day of week)
    -   `isAccessibleByCategory($clientCategory)` - Check category eligibility
    -   `hasBeenUsedBy($clientId)` - Check if client already used this promotion
    -   `isEligibleForClient(User $client)` - Full eligibility check (active, valid, category, not used)
    -   `getAcceptedUsageCount()` - Count accepted usages

**News Model (TASK-010):**

-   **Traits:** HasFactory (no soft deletes - news can be hard deleted)
-   **Fillable Fields:** `codigo`, `texto`, `fecha_desde`, `fecha_hasta`, `categoria_destino`, `created_by`
-   **Casts:**
    -   `codigo` as integer
    -   `fecha_desde`, `fecha_hasta` as date
-   **Relationships:**
    -   `creator()` - BelongsTo User (admin who created)
-   **Query Scopes:**
    -   `active()` - Within date range (not expired)
    -   `expired()` - Past fecha_hasta
    -   `forCategory($clientCategory)` - Filter by accessible category (hierarchy aware)
-   **Model Events:**
    -   `creating` event - Auto-generate sequential `codigo` (max + 1)
-   **Helper Methods:**
    -   `isActive()` - Check if not expired
    -   `isExpired()` - Check if past fecha_hasta
    -   `isAccessibleByCategory($clientCategory)` - Check category eligibility
    -   `getDaysUntilExpiration()` - Calculate remaining days

**PromotionUsage Model (TASK-011):**

-   **Traits:** HasFactory
-   **Table Name:** `promotion_usage` (explicit table name)
-   **Fillable Fields:** `client_id`, `promotion_id`, `fecha_uso`, `estado`
-   **Casts:** `fecha_uso` as date
-   **Relationships:**
    -   `client()` - BelongsTo User
    -   `promotion()` - BelongsTo Promotion
-   **Query Scopes:**
    -   `pending()` - Filter estado='enviada'
    -   `accepted()` - Filter estado='aceptada'
    -   `rejected()` - Filter estado='rechazada'
    -   `byClient($clientId)` - Filter by client
    -   `byPromotion($promotionId)` - Filter by promotion
    -   `betweenDates($start, $end)` - Filter by date range
    -   `lastMonths($months)` - Filter last N months (for category upgrade calculation)
-   **Helper Methods:**
    -   `isPending()`, `isAccepted()`, `isRejected()` - Status checking
    -   `accept()`, `reject()` - Change status and save

**Model Events Implementation (TASK-012):**

-   **Sequential Code Generation:**
    -   Store: `boot()` method generates sequential `codigo` on creation (max + 1, includes soft deleted records)
    -   Promotion: Same pattern as Store
    -   News: Same pattern (no soft deletes)
    -   Implementation uses `withTrashed()` for Store/Promotion to avoid code reuse after soft delete
-   **Email Notifications:** Deferred to Phase 6 (Email Notifications) as per plan structure
    -   Placeholder structure ready in boot methods for future observer implementation
    -   Will use Laravel's Observer pattern for promotion approval/usage status change emails

**Technical Implementation Details:**

**Carbon Integration:**

-   Imported Carbon for date operations in Promotion and News models
-   Used `Carbon::today()` for date comparisons (avoids time component issues)
-   Day of week calculation adjusted from PHP convention (0=Sunday) to project convention (0=Monday to 6=Sunday)

**JSON Query Handling:**

-   `dias_semana` JSON array queried using `JSON_EXTRACT` in MySQL for day-of-week validation
-   Array cast ensures proper serialization/deserialization
-   Validation in helper methods checks array structure (7 booleans)

**Hierarchy Logic:**

-   Category hierarchy implemented consistently across User, Promotion, and News models
-   Numeric mapping: Inicial=1, Medium=2, Premium=3
-   Higher level users can access lower level content (Premium sees all, etc.)

**Query Optimization:**

-   Composite scopes created for common queries (active + valid today, active + category)
-   Relationships use proper foreign key naming for automatic key resolution
-   Eager loading ready via `with()` method on relationships

**Soft Deletes Strategy:**

-   Enabled for Store and Promotion models only (preserve historical data)
-   News does not use soft deletes (can be purged after expiration)
-   PromotionUsage does not use soft deletes (audit trail must be permanent)
-   Sequential code generation accounts for soft deleted records to avoid collisions

**Type Safety:**

-   All casts properly defined (dates, integers, arrays)
-   Return types specified on helper methods
-   Relationship return types use Laravel 11's typed relations (HasMany, BelongsTo)

**Validation & Testing:**

-   ✅ All models compiled without syntax errors
-   ✅ `php artisan about` confirms application structure valid
-   ✅ Optimized caches cleared successfully
-   ✅ No lint errors after all models created (circular dependency resolved)

**Alignment with Requirements:**

-   ✅ REQ-002: Eloquent models with proper relationships created
-   ✅ TECH-002: Soft deletes implemented for Stores and Promotions
-   ✅ TECH-007: Eloquent scopes for complex queries (active, category filtering)
-   ✅ BUS-001: Sequential store codes generated via model event
-   ✅ BUS-002: Sequential promotion codes generated via model event
-   ✅ BUS-003: Days of week stored as JSON array with validation helpers
-   ✅ BUS-005: Category hierarchy logic implemented in models
-   ✅ BUS-008: Single-use enforcement ready (hasBeenUsedBy check, unique constraint in DB)
-   ✅ BUS-010: Usage estados handled in PromotionUsage model
-   ✅ BUS-011: Promotion approval estados handled in Promotion model

**Next Steps:**

-   Phase 3: Implement authentication system with Laravel Breeze
-   Create middleware for role-based access control
-   Implement Policies for model authorization
-   Wire up email verification for clients and approval workflow for store owners

### Implementation Phase 3: Authentication & Authorization

-   GOAL-003: Implement multi-role authentication with email verification and approval workflows.

| Task     | Description                                                                                                                                                                                                                          | Completed | Date |
| -------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ | --------- | ---- |
| TASK-013 | Configure Laravel Breeze or Fortify for basic authentication (login, registration, password reset).                                                                                                                                  |           |      |
| TASK-014 | Customize registration to add `tipo_usuario` field, set default `categoria_cliente='Inicial'` for clients, enable email verification for clients.                                                                                    |           |      |
| TASK-015 | Create `AdminMiddleware`, `StoreOwnerMiddleware`, `ClientMiddleware` to check `tipo_usuario` and approved status, redirect unauthorized users.                                                                                       |           |      |
| TASK-016 | Implement admin approval workflow for store owners: create admin dashboard route/controller for approving pending users, send approval notification email.                                                                           |           |      |
| TASK-017 | Create `StorePolicy` with methods: `viewAny`, `view`, `create` (admin only), `update` (admin only), `delete` (admin only), `manage` (owner or admin).                                                                                |           |      |
| TASK-018 | Create `PromotionPolicy` with methods: `viewAny` (all), `view` (all), `create` (store owner for own store), `update` (none - promotions immutable), `delete` (owner for own store), `approve` (admin only), `request` (client only). |           |      |
| TASK-019 | Create `NewsPolicy` with methods: `viewAny` (clients based on category), `create` (admin), `update` (admin), `delete` (admin).                                                                                                       |           |      |

### Implementation Phase 4: Core Business Logic Services

-   GOAL-004: Implement business logic services for promotions, categories, and usage tracking.

| Task     | Description                                                                                                                                                                                                            | Completed | Date |
| -------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | --------- | ---- |
| TASK-020 | Create `PromotionService`: methods for eligibility checking (date range, day of week, category, already used), filtering available promotions for client, approval/denial by admin.                                    |           |      |
| TASK-021 | Create `PromotionUsageService`: methods for creating usage request (validates eligibility, checks single-use rule), accepting/rejecting request by store owner, calculating usage statistics.                          |           |      |
| TASK-022 | Create `CategoryUpgradeService`: method to evaluate client category based on accepted promotions in last 6 months, configurable thresholds (e.g., 5 for Medium, 15 for Premium), update user category and log changes. |           |      |
| TASK-023 | Create `NewsService`: methods for filtering active news by category and date, auto-expire checking, admin CRUD operations.                                                                                             |           |      |
| TASK-024 | Create `ReportService`: methods for generating admin reports (promotion usage stats by store, client category distribution), store owner reports (promotion usage count, client list per promotion).                   |           |      |
| TASK-025 | Implement configuration file `config/shopping.php` for category upgrade thresholds, promotion code prefix, news expiration defaults, report date ranges.                                                               |           |      |

### Implementation Phase 5: Form Requests & Validation

-   GOAL-005: Implement server-side validation for all user inputs.

| Task     | Description                                                                                                                                                                                                                   | Completed | Date |
| -------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | --------- | ---- |
| TASK-026 | Create `StoreStoreRequest`: validate `nombre` required string max 100, `ubicacion` required max 50, `rubro` required max 20, `owner_id` exists in users table with tipo='dueño de local'.                                     |           |      |
| TASK-027 | Create `StorePromotionRequest`: validate `texto` required max 200, `fecha_desde/hasta` required dates with hasta >= desde, `dias_semana` array of 7 booleans, `categoria_minima` enum, `store_id` exists and user owns store. |           |      |
| TASK-028 | Create `StoreNewsRequest`: validate `texto` required max 200, `fecha_desde/hasta` dates with auto-expiration logic, `categoria_destino` enum.                                                                                 |           |      |
| TASK-029 | Create `PromotionUsageRequest`: validate `promotion_id` exists and is approved/active, check client hasn't used promotion before, verify category eligibility, check day of week validity.                                    |           |      |
| TASK-030 | Create `ApproveUserRequest`: validate `user_id` exists and is pending store owner approval.                                                                                                                                   |           |      |
| TASK-031 | Create `UpdatePromotionStatusRequest`: validate `estado` enum ('aprobada', 'denegada'), `promotion_id` exists and is pending, optional admin notes field.                                                                     |           |      |

### Implementation Phase 6: Email Notifications

-   GOAL-006: Implement all email notifications required by the system.

| Task     | Description                                                                                                                                           | Completed | Date |
| -------- | ----------------------------------------------------------------------------------------------------------------------------------------------------- | --------- | ---- |
| TASK-032 | Configure mail settings in `.env` and `config/mail.php` for SMTP (Mailtrap for dev, production SMTP for prod).                                        |           |      |
| TASK-033 | Create `ClientVerificationMail` Mailable: welcome message, email verification link, shopping benefits intro.                                          |           |      |
| TASK-034 | Create `StoreOwnerApprovalMail` Mailable: approval notification, login instructions, dashboard link, admin contact for questions.                     |           |      |
| TASK-035 | Create `StoreOwnerRejectionMail` Mailable: rejection notification with reason (optional), contact info for appeals.                                   |           |      |
| TASK-036 | Create `PromotionApprovedMail` Mailable: notify store owner of approved promotion, include promotion details and start date.                          |           |      |
| TASK-037 | Create `PromotionDeniedMail` Mailable: notify store owner of denied promotion with reason, guidelines for resubmission.                               |           |      |
| TASK-038 | Create `PromotionUsageRequestMail` Mailable: notify store owner of client request, include client info and promotion details, links to accept/reject. |           |      |
| TASK-039 | Create `PromotionUsageAcceptedMail` Mailable: notify client their request was accepted, include usage instructions and store location.                |           |      |
| TASK-040 | Create `PromotionUsageRejectedMail` Mailable: notify client their request was rejected, suggest alternative promotions.                               |           |      |

### Implementation Phase 7: Background Jobs & Scheduled Tasks

-   GOAL-007: Implement automated tasks for category upgrades and news expiration.

| Task     | Description                                                                                                                                                                     | Completed | Date |
| -------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | --------- | ---- |
| TASK-041 | Create `EvaluateClientCategoriesJob`: iterates all clients, calls `CategoryUpgradeService`, sends notification emails on category changes, logs upgrade events.                 |           |      |
| TASK-042 | Create `CleanupExpiredNewsJob`: marks or deletes expired news (fecha_hasta < now), configurable retention period.                                                               |           |      |
| TASK-043 | Register jobs in `app/Console/Kernel.php` schedule: run `EvaluateClientCategoriesJob` every 6 months (or configurable interval), run `CleanupExpiredNewsJob` daily at midnight. |           |      |
| TASK-044 | Create `SendCategoryUpgradeNotificationMail` Mailable: congratulate client on upgrade, explain new benefits, list accessible promotions.                                        |           |      |
| TASK-045 | Configure Windows Task Scheduler command for XAMPP: `php artisan schedule:run` every minute, document setup steps in README.                                                    |           |      |

### Implementation Phase 8: Controller Implementation

-   GOAL-008: Implement controller logic to replace placeholder TODOs from Phase 1.

| Task     | Description                                                                                                                                                                | Completed | Date |
| -------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | --------- | ---- |
| TASK-046 | Implement `LocalController@index`: fetch all stores with pagination, apply filters (rubro, search), pass to view.                                                          |           |      |
| TASK-047 | Implement `LocalController@show`: fetch store by ID with promotions relationship, check policy authorization, pass to view.                                                |           |      |
| TASK-048 | Implement `PromocionController@index`: fetch approved/active promotions, filter by category for authenticated clients, apply search/category filters, paginate.            |           |      |
| TASK-049 | Implement `PromocionController@show`: fetch promotion with store relationship, check eligibility for authenticated client, pass usage status to view.                      |           |      |
| TASK-050 | Implement `NovedadController@index`: fetch active news filtered by client category (or all for unregistered), paginate.                                                    |           |      |
| TASK-051 | Implement `Admin\AdminDashboardController@index`: dashboard stats (total stores, pending approvals, promotion stats), recent activity, navigation to management sections.  |           |      |
| TASK-052 | Implement `Admin\StoreController`: CRUD for stores (create, edit, delete), assign owners, manage store status.                                                             |           |      |
| TASK-053 | Implement `Admin\UserController`: list pending store owners, approve/reject with email notifications, view all users with filters.                                         |           |      |
| TASK-054 | Implement `Admin\PromotionController`: list all promotions with filters (status, store, date), approve/deny pending promotions with email notifications, view usage stats. |           |      |
| TASK-055 | Implement `Admin\NewsController`: CRUD for news (create, edit, delete), set category targets and date ranges.                                                              |           |      |
| TASK-056 | Implement `Admin\ReportController`: generate reports (promotion usage by store, client distribution, category trends), export to Excel/PDF.                                |           |      |
| TASK-057 | Implement `Store\StoreDashboardController@index`: show owned store info, promotion list, pending usage requests count, recent activity.                                    |           |      |
| TASK-058 | Implement `Store\PromotionController`: create promotions (validated by FormRequest), delete own promotions, view usage statistics, cannot edit (immutable).                |           |      |
| TASK-059 | Implement `Store\UsageRequestController`: list pending requests for owned store's promotions, accept/reject with email notifications, view accepted usage history.         |           |      |
| TASK-060 | Implement `Client\ClientDashboardController@index`: show client category, usage history, available promotions count, category upgrade progress.                            |           |      |
| TASK-061 | Implement `Client\PromotionUsageController`: search promotions by store code, request promotion usage (validates eligibility via service), view request status history.    |           |      |
| TASK-062 | Implement `PageController@contact`: handle contact form submission, validate inputs, send email to admin, return success message.                                          |           |      |

### Implementation Phase 9: Database Seeders & Factories

-   GOAL-009: Create comprehensive test data for development and testing.

| Task     | Description                                                                                                                                                                                            | Completed | Date |
| -------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ | --------- | ---- |
| TASK-063 | Create `UserFactory`: generate realistic users for all types (admin, store owners, clients) with proper categories, email verification states.                                                         |           |      |
| TASK-064 | Create `StoreFactory`: generate stores with sequential codes, varied rubros (indumentaria, comida, etc.), assign to existing store owners.                                                             |           |      |
| TASK-065 | Create `PromotionFactory`: generate promotions with realistic date ranges, varied dias_semana combinations, all estados (pendiente, aprobada, denegada), assign to stores.                             |           |      |
| TASK-066 | Create `NewsFactory`: generate news with varied category targets, some expired, some active, realistic text content.                                                                                   |           |      |
| TASK-067 | Create `PromotionUsageFactory`: generate usage records for clients with mixed estados, respect single-use rule, realistic date distribution.                                                           |           |      |
| TASK-068 | Create `DatabaseSeeder`: seed 1 admin, 5 store owners (3 approved, 2 pending), 20 stores, 50 promotions (30 approved, 10 pending, 10 denied), 30 clients (10 per category), 15 news, 80 usage records. |           |      |
| TASK-069 | Create `TestCategoriesSeeder`: seed specific test cases for category upgrade logic (clients with exactly threshold counts, edge cases for 6-month window).                                             |           |      |

### Implementation Phase 10: Integration & Testing

-   GOAL-010: Validate all backend functionality and integrate with frontend views.

| Task     | Description                                                                                                                                                                   | Completed | Date |
| -------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | --------- | ---- |
| TASK-070 | Update all Blade views to use real data from controllers instead of mock arrays (home, locales, promociones, dashboards).                                                     |           |      |
| TASK-071 | Implement pagination controls in Blade views using Laravel's pagination links.                                                                                                |           |      |
| TASK-072 | Add form submissions to views: promotion creation forms in store dashboard, usage request forms in client interface, admin approval forms.                                    |           |      |
| TASK-073 | Implement client-side validation using Bootstrap validation classes matching server-side FormRequest rules.                                                                   |           |      |
| TASK-074 | Add flash message displays for success/error feedback in all forms (promotion submitted, approval granted, etc.).                                                             |           |      |
| TASK-075 | Create feature tests: user registration flows (client email verification, store owner approval), promotion lifecycle (create, approve, request, accept), category upgrades.   |           |      |
| TASK-076 | Create unit tests: `PromotionService` eligibility logic, `CategoryUpgradeService` threshold calculations, date/day validations.                                               |           |      |
| TASK-077 | Test email sending in development environment (Mailtrap), verify all Mailable templates render correctly with test data.                                                      |           |      |
| TASK-078 | Run `php artisan migrate:fresh --seed` and verify all seeders execute without errors, inspect database tables for data integrity.                                             |           |      |
| TASK-079 | Perform manual end-to-end testing: register as client, request promotion, login as store owner to accept, verify emails sent, check category upgrade after threshold reached. |           |      |
| TASK-080 | Execute scheduled jobs manually (`php artisan schedule:run --verbose`), verify category evaluation and news cleanup work correctly.                                           |           |      |

## 3. Alternatives

-   **ALT-001**: Use UUID primary keys instead of auto-increment integers for stores/promotions; rejected to match project specification requiring sequential numeric codes visible to users.
-   **ALT-002**: Store dias_semana as separate boolean columns (lunes, martes, etc.) instead of JSON array; rejected to reduce table width and simplify queries with JSON operations.
-   **ALT-003**: Implement soft approval for promotions (bypass admin for trusted stores); rejected because project explicitly requires admin approval for all promotions.
-   **ALT-004**: Use Laravel Passport or Sanctum for API authentication; deferred as project specs don't require API, web session auth sufficient for current scope.
-   **ALT-005**: Implement real-time notifications using Laravel Echo/Pusher; deferred as email notifications meet current requirements, can enhance later.
-   **ALT-006**: Use external reporting library (e.g., Spatie Laravel Analytics); opted for custom ReportService to maintain control over report structure and avoid external dependencies.

## 4. Dependencies

-   **DEP-001**: Laravel Framework 11.x (core dependency, already installed).
-   **DEP-002**: MySQL/MariaDB database (XAMPP includes MariaDB 10.x, compatible).
-   **DEP-003**: Laravel Breeze or Fortify for authentication scaffolding (choose one, install via Composer).
-   **DEP-004**: Mailtrap account for development email testing (free tier sufficient, alternative: MailHog).
-   **DEP-005**: Laravel Excel package (`maatwebsite/excel`) for report generation (install via Composer).
-   **DEP-006**: Carbon library for date operations (included with Laravel, no extra install).
-   **DEP-007**: Faker library for factories (included with Laravel, no extra install).

## 5. Files

### New Files to Create

-   **FILE-001**: `database/migrations/*_create_stores_table.php` - Stores schema
-   **FILE-002**: `database/migrations/*_create_promotions_table.php` - Promotions schema
-   **FILE-003**: `database/migrations/*_create_news_table.php` - News schema
-   **FILE-004**: `database/migrations/*_create_promotion_usage_table.php` - Usage tracking pivot
-   **FILE-005**: `database/migrations/*_extend_users_table.php` - Add role/category fields to users
-   **FILE-006**: `app/Models/Store.php` - Store Eloquent model
-   **FILE-007**: `app/Models/Promotion.php` - Promotion Eloquent model
-   **FILE-008**: `app/Models/News.php` - News Eloquent model
-   **FILE-009**: `app/Models/PromotionUsage.php` - Promotion usage pivot model
-   **FILE-010**: `app/Services/PromotionService.php` - Promotion business logic
-   **FILE-011**: `app/Services/PromotionUsageService.php` - Usage tracking logic
-   **FILE-012**: `app/Services/CategoryUpgradeService.php` - Category upgrade logic
-   **FILE-013**: `app/Services/NewsService.php` - News management logic
-   **FILE-014**: `app/Services/ReportService.php` - Report generation logic
-   **FILE-015**: `app/Http/Middleware/AdminMiddleware.php` - Admin access control
-   **FILE-016**: `app/Http/Middleware/StoreOwnerMiddleware.php` - Store owner access control
-   **FILE-017**: `app/Http/Middleware/ClientMiddleware.php` - Client access control
-   **FILE-018**: `app/Policies/StorePolicy.php` - Store authorization
-   **FILE-019**: `app/Policies/PromotionPolicy.php` - Promotion authorization
-   **FILE-020**: `app/Policies/NewsPolicy.php` - News authorization
-   **FILE-021-031**: `app/Http/Requests/*Request.php` - 11 Form Request classes
-   **FILE-032-040**: `app/Mail/*Mail.php` - 9 Mailable classes for notifications
-   **FILE-041-042**: `app/Jobs/*.php` - 2 scheduled job classes
-   **FILE-043**: `app/Console/Kernel.php` - Update with job schedule (modify existing)
-   **FILE-044-050**: `app/Http/Controllers/Admin/*.php` - 7 admin controllers
-   **FILE-051-053**: `app/Http/Controllers/Store/*.php` - 3 store owner controllers
-   **FILE-054-055**: `app/Http/Controllers/Client/*.php` - 2 client controllers
-   **FILE-056-061**: `database/factories/*.php` - 5 factory classes
-   **FILE-062-063**: `database/seeders/*.php` - 2 seeder classes
-   **FILE-064**: `config/shopping.php` - Application configuration file
-   **FILE-065**: `tests/Feature/AuthenticationFlowTest.php` - Auth feature tests
-   **FILE-066**: `tests/Feature/PromotionLifecycleTest.php` - Promotion workflow tests
-   **FILE-067**: `tests/Unit/PromotionServiceTest.php` - Promotion service unit tests
-   **FILE-068**: `tests/Unit/CategoryUpgradeServiceTest.php` - Category logic unit tests

### Files to Modify

-   **FILE-MOD-001**: `app/Models/User.php` - Extend with new fields, relationships, scopes
-   **FILE-MOD-002**: `routes/web.php` - Add admin, store, client route groups with middleware
-   **FILE-MOD-003**: `config/auth.php` - Update guards/providers if needed for multi-role auth
-   **FILE-MOD-004**: `app/Http/Kernel.php` - Register custom middleware
-   **FILE-MOD-005**: `app/Providers/AuthServiceProvider.php` - Register policies
-   **FILE-MOD-006**: `.env` - Add mail configuration, category thresholds, app-specific settings
-   **FILE-MOD-007**: `composer.json` - Add Laravel Excel dependency
-   **FILE-MOD-008**: `resources/views/**/*.blade.php` - Update views to use real data (13 views from Phase 1)
-   **FILE-MOD-009**: `app/Http/Controllers/*.php` - Implement TODO logic in 8 placeholder controllers

## 6. Testing

### Automated Tests

-   **TEST-001**: Feature test for client registration with email verification flow.
-   **TEST-002**: Feature test for store owner registration requiring admin approval.
-   **TEST-003**: Feature test for promotion creation by store owner, admin approval, client request, store acceptance workflow.
-   **TEST-004**: Unit test for `PromotionService::isEligible()` covering all eligibility rules (date, day, category, single-use).
-   **TEST-005**: Unit test for `CategoryUpgradeService::evaluateClient()` with various usage counts and date ranges.
-   **TEST-006**: Unit test for promotion day-of-week validation logic.
-   **TEST-007**: Feature test for category-based news visibility filtering.
-   **TEST-008**: Feature test for admin reports generation with various filters.
-   **TEST-009**: Integration test for scheduled jobs execution (category upgrade, news cleanup).
-   **TEST-010**: Database test for sequential code generation under concurrent requests.

### Manual Tests

-   **TEST-011**: Complete user journey as client: register → verify email → browse promotions → request usage → receive acceptance email → verify category upgrade after threshold.
-   **TEST-012**: Complete user journey as store owner: register → wait for admin approval → create promotion → wait for admin approval → accept client request → view usage report.
-   **TEST-013**: Complete user journey as admin: approve store owner → review pending promotions → approve/deny → view usage reports → create news → verify expiration.
-   **TEST-014**: Test responsive behavior of all forms on mobile devices (Bootstrap validation, error displays).
-   **TEST-015**: Test email delivery in Mailtrap for all 9 Mailable types.
-   **TEST-016**: Test Windows Task Scheduler integration for `php artisan schedule:run`.

### Validation Checklist

-   **VAL-001**: All database migrations run without errors on fresh database.
-   **VAL-002**: All seeders execute successfully and produce expected record counts.
-   **VAL-003**: All Eloquent relationships query correctly (no N+1 issues, use `with()` for eager loading).
-   **VAL-004**: All Form Requests reject invalid inputs and display errors in views.
-   **VAL-005**: All Policies correctly authorize/deny actions based on user roles.
-   **VAL-006**: All emails send successfully and render correctly in email clients.
-   **VAL-007**: Category upgrade logic correctly calculates thresholds using 6-month window.
-   **VAL-008**: Promotion single-use constraint enforced (database unique constraint + application validation).
-   **VAL-009**: Sequential codes generate without gaps or collisions under load.
-   **VAL-010**: Soft deletes work correctly (deleted stores/promotions don't appear in listings but preserve references).

## 7. Risks & Assumptions

### Risks

-   **RISK-001**: Sequential code generation may have race conditions under high concurrent load; mitigation via database auto-increment or pessimistic locking.
-   **RISK-002**: 6-month category evaluation window may be computationally expensive for large user bases; mitigation via indexed queries and background job optimization.
-   **RISK-003**: Email delivery may fail in production if SMTP not properly configured; mitigation via queue system retry logic and monitoring.
-   **RISK-004**: Category upgrade thresholds may need tuning based on real usage patterns; mitigation via configurable values in `config/shopping.php`.
-   **RISK-005**: JSON column for dias_semana may have portability issues across database systems; mitigation via Laravel's JSON casting abstraction.
-   **RISK-006**: Soft deletes on stores/promotions may complicate foreign key relationships; mitigation via explicit scoping in queries.
-   **RISK-007**: Windows Task Scheduler may not reliably run artisan schedule in XAMPP environment; mitigation via documentation and testing, alternative cron simulation tools.

### Assumptions

-   **ASSUMPTION-001**: XAMPP MySQL configuration allows JSON column type (MySQL 5.7+ or MariaDB 10.2+).
-   **ASSUMPTION-002**: Category upgrade thresholds are set to 5 promotions for Medium, 15 for Premium (subject to project team decision).
-   **ASSUMPTION-003**: News auto-expiration is sufficient via query scopes; physical deletion can be handled by scheduled job if needed.
-   **ASSUMPTION-004**: Store owners can only manage one store (one-to-one relationship); if multi-store ownership needed, extend to one-to-many.
-   **ASSUMPTION-005**: Promotion immutability (no edits) is strict requirement; if modification needed, implement versioning system.
-   **ASSUMPTION-006**: Email verification for clients uses Laravel's built-in `MustVerifyEmail` interface.
-   **ASSUMPTION-007**: Admin approval for store owners is manual process via dashboard; automated approval not required.
-   **ASSUMPTION-008**: Report generation uses in-memory processing; for very large datasets, implement chunked processing or queue export jobs.
-   **ASSUMPTION-009**: Unregistered users don't need tracking; no analytics on anonymous browsing behavior.
-   **ASSUMPTION-010**: Single-use promotion rule applies per promotion instance, not per store (client can use different promotions from same store).

## 8. Related Specifications / Further Reading

### Internal Documentation

-   **Previous Plan**: `plan/feature-frontend-integration-1.md` - Completed frontend integration providing Blade views and route structure for backend to populate.
-   **Project Instructions**: `.github/instructions/EnunciadoProyecto.instructions.md` - Official project requirements from UTN FRR.
-   **Coding Guidelines**: `.github/copilot-instructions.md` - Laravel development patterns and project-specific conventions.

### Laravel Documentation

-   **Eloquent ORM**: https://laravel.com/docs/11.x/eloquent - Model relationships, scopes, casts
-   **Database Migrations**: https://laravel.com/docs/11.x/migrations - Schema building, indexes, foreign keys
-   **Authentication**: https://laravel.com/docs/11.x/authentication - Breeze/Fortify setup, email verification
-   **Authorization**: https://laravel.com/docs/11.x/authorization - Policies, gates, middleware
-   **Validation**: https://laravel.com/docs/11.x/validation - Form Requests, custom rules
-   **Mail**: https://laravel.com/docs/11.x/mail - Mailable classes, mail configuration
-   **Task Scheduling**: https://laravel.com/docs/11.x/scheduling - Job scheduling, cron setup
-   **Queues**: https://laravel.com/docs/11.x/queues - Background job processing (if needed for scale)

### Third-Party Packages

-   **Laravel Excel**: https://laravel-excel.com/ - Report export functionality
-   **Laravel Debugbar**: https://github.com/barryvdh/laravel-debugbar - Development debugging (optional)
-   **Mailtrap**: https://mailtrap.io/blog/laravel-mail/ - Email testing configuration guide

---

## Next Steps After Plan Completion

Once this backend implementation plan is fully executed, the following work remains to complete the ShoppingRio project:

### 1. Advanced Features & Enhancements (Post-MVP)

-   **NEXT-001**: Implement advanced search and filtering UI with AJAX for real-time results without page reloads.
-   **NEXT-002**: Add image upload functionality for stores and promotions (use Laravel's file storage, S3 integration for production).
-   **NEXT-003**: Implement notification preferences system (allow clients to opt-in/out of promotional emails).
-   **NEXT-004**: Create admin analytics dashboard with charts (Chart.js or similar) showing promotion trends, category distribution over time.
-   **NEXT-005**: Implement promotion favoriting/bookmarking for clients to save promotions for later.
-   **NEXT-006**: Add store rating/review system for clients (requires new tables, moderation workflow).
-   **NEXT-007**: Implement QR code generation for promotions (clients scan at store to activate discount).
-   **NEXT-008**: Create mobile-optimized PWA features (offline browsing, push notifications).

### 2. Performance Optimization

-   **NEXT-009**: Implement Redis caching for frequently accessed data (active promotions, store listings).
-   **NEXT-010**: Add database query optimization (eager loading audits, index tuning based on slow query logs).
-   **NEXT-011**: Implement pagination for large datasets (promotions, usage history) with cursor-based pagination for performance.
-   **NEXT-012**: Set up Laravel Horizon for queue monitoring if background jobs become intensive.
-   **NEXT-013**: Profile application with Laravel Telescope to identify bottlenecks.

### 3. Security Hardening

-   **NEXT-014**: Implement two-factor authentication (2FA) for admin and store owner accounts.
-   **NEXT-015**: Add activity logging for sensitive actions (promotion approvals, category changes) using Laravel's audit trail packages.
-   **NEXT-016**: Set up rate limiting for API endpoints if REST API is added later.
-   **NEXT-017**: Implement CAPTCHA on registration forms to prevent spam signups.
-   **NEXT-018**: Add content security policies (CSP) headers to prevent XSS attacks.

### 4. Testing & Quality Assurance

-   **NEXT-019**: Expand test coverage to 80%+ code coverage using PHPUnit.
-   **NEXT-020**: Implement browser tests using Laravel Dusk for critical user flows.
-   **NEXT-021**: Set up continuous integration (CI) pipeline with GitHub Actions (run tests, linting on every push).
-   **NEXT-022**: Perform accessibility audit (WCAG 2.1 AA compliance) on all public pages.
-   **NEXT-023**: Conduct security audit using tools like Laravel Security Checker, OWASP ZAP.

### 5. Deployment & Operations

-   **NEXT-024**: Prepare production deployment guide for LAMP/LEMP stack (Apache/Nginx + MySQL + PHP 8.2+).
-   **NEXT-025**: Configure production `.env` file with secure secrets, HTTPS enforcement, production mail driver.
-   **NEXT-026**: Set up database backup strategy (automated daily backups, restore testing).
-   **NEXT-027**: Configure Linux cron jobs for scheduled tasks (replace Windows Task Scheduler).
-   **NEXT-028**: Implement application monitoring (logs aggregation, error tracking with Sentry or similar).
-   **NEXT-029**: Set up SSL certificate with Let's Encrypt for production domain.
-   **NEXT-030**: Configure CDN for static assets (images, CSS, JS) to improve load times.

### 6. Documentation

-   **NEXT-031**: Create user documentation for all 4 user roles (Administrator manual, Store Owner guide, Client FAQ).
-   **NEXT-032**: Document API endpoints if REST API is implemented for mobile app integration.
-   **NEXT-033**: Create database schema diagram (ERD) for reference and onboarding new developers.
-   **NEXT-034**: Write deployment runbook with troubleshooting common issues.
-   **NEXT-035**: Create video tutorials for admin/store owner dashboards (screen recordings with narration).

### 7. Compliance & Legal

-   **NEXT-036**: Add privacy policy and terms of service pages (GDPR compliance if applicable).
-   **NEXT-037**: Implement cookie consent banner if using analytics or third-party tracking.
-   **NEXT-038**: Add data export functionality for clients (GDPR right to data portability).
-   **NEXT-039**: Implement account deletion workflow with data retention policies.

### 8. Project Deliverables for Academic Submission

-   **NEXT-040**: Prepare final project report including architecture diagrams, business logic explanations, testing results.
-   **NEXT-041**: Create presentation slides for project defense (demo key features, explain technical decisions).
-   **NEXT-042**: Record demo video showing all user roles and workflows (5-10 minutes).
-   **NEXT-043**: Prepare GitHub repository for submission (clean commit history, comprehensive README, setup instructions).
-   **NEXT-044**: Document all deviations from original project specifications with justifications.

### 9. Optional Enhancements Beyond Requirements

-   **NEXT-045**: Multi-language support (i18n) for Spanish/English (using Laravel's localization).
-   **NEXT-046**: Dark mode theme toggle (CSS variables + localStorage persistence).
-   **NEXT-047**: Social media integration (share promotions on Facebook/Twitter).
-   **NEXT-048**: Loyalty program integration (points system tied to promotion usage).
-   **NEXT-049**: Store locator map (Google Maps API integration showing store locations).
-   **NEXT-050**: Chatbot for customer support (FAQ automation using simple NLP or rule-based bot).

### Priority Matrix for Next Steps

**Critical (Must Complete for Project Submission):**

-   All backend implementation tasks (TASK-001 through TASK-080)
-   Testing validation (TEST-001 through TEST-016)
-   Documentation for submission (NEXT-040 through NEXT-044)

**High Priority (Should Complete Before Submission):**

-   Performance optimization basics (NEXT-010, NEXT-011)
-   Security hardening (NEXT-014, NEXT-017)
-   User documentation (NEXT-031)

**Medium Priority (Nice to Have for Demo):**

-   Advanced features (NEXT-001, NEXT-004, NEXT-007)
-   Mobile optimization (NEXT-008)
-   Deployment preparation (NEXT-024, NEXT-025)

**Low Priority (Post-Submission Enhancements):**

-   Optional features (NEXT-045 through NEXT-050)
-   Advanced analytics (NEXT-004 with ML predictions)
-   Third-party integrations beyond email

**Estimated Timeline:**

-   Phase 1-3 (Database, Models, Auth): 2 weeks
-   Phase 4-6 (Services, Validation, Emails): 2 weeks
-   Phase 7-9 (Jobs, Controllers, Seeders): 2 weeks
-   Phase 10 (Integration & Testing): 1 week
-   Final polish and documentation: 1 week
-   **Total: 8 weeks for complete MVP**
