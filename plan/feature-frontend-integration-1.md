---
goal: Integrate frontEndEG mockup into Laravel view layer
version: 1.0
date_created: 2025-10-29
date_updated: 2025-10-31
status: In Progress
progress: Phase 4 Complete (80%)
tags: [feature, frontend, integration]
---

# Introduction

Status badge: (status: In Progress, color: yellow)
Progress: Phases 1-4 Complete | Phase 5 Pending (Final Validation)

Integrate the static mockup contained in `frontEndEG/` into the Laravel application structure so the shopping site uses Blade templates, Vite-managed assets, and Laravel routing while preserving the existing visual design. This plan prepares the project for backend feature development.

## 1. Requirements & Constraints

-   **REQ-001**: Port every HTML view from `frontEndEG/` into Blade templates under `resources/views/` while preserving layout fidelity.
-   **REQ-002**: Centralize shared UI elements (header, footer, navigation) into reusable Blade components or includes.
-   **SEC-001**: Ensure no hard-coded credentials or sensitive mock data remain in migrated templates.
-   **UXR-001**: Maintain responsive behavior defined by existing Bootstrap-based mockups across mobile and desktop breakpoints.
-   **CON-001**: Align asset handling with the existing Laravel Vite pipeline (`resources/js/app.js`, `resources/css/app.css`).
-   **GUD-001**: Follow project instructions requiring Bootstrap 5, Blade layouts, and reusable components.
-   **PAT-001**: Adopt a base layout (`resources/views/layouts/app.blade.php`) with section yields for page-specific content per Laravel conventions.

## 2. Implementation Steps

### Implementation Phase 1

-   GOAL-001: Establish migration blueprint and asset inventory for the mockup.

| Task     | Description                                                                                                                                                                    | Completed | Date       |
| -------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ | --------- | ---------- |
| TASK-001 | Inventory HTML templates under `frontEndEG/index.html` and `frontEndEG/pages/*.html`, mapping each to target Blade paths in `resources/views/` with planned route names.       | ✅        | 2025-10-30 |
| TASK-002 | Catalog CSS/JS assets (`frontEndEG/css`, `frontEndEG/js`) and images (`frontEndEG/img`) specifying destination directories (`resources/css`, `resources/js`, `public/images`). | ✅        | 2025-10-30 |
| TASK-003 | Identify shared partials (header/footer/nav) within HTML to design Blade includes/components (`resources/views/components/`).                                                  | ✅        | 2025-10-30 |

#### Phase 1 Findings (2025-10-30)

**HTML to Blade Mapping**

-   `frontEndEG/index.html` → `resources/views/home/index.blade.php` (route name `home.index`).
-   `frontEndEG/pages/locales.html` → `resources/views/pages/locales/index.blade.php` (route `pages.locales`).
-   `frontEndEG/pages/local-detalle.html` → `resources/views/pages/locales/show.blade.php` (route `pages.locales.show`).
-   `frontEndEG/pages/promociones.html` → `resources/views/pages/promociones/index.blade.php` (route `pages.promociones`).
-   `frontEndEG/pages/promocion-detalle.html` → `resources/views/pages/promociones/show.blade.php` (route `pages.promociones.show`).
-   `frontEndEG/pages/novedades.html` → `resources/views/pages/novedades/index.blade.php` (route `pages.novedades`).
-   `frontEndEG/pages/quienes-somos.html` → `resources/views/pages/static/about.blade.php` (route `pages.about`).
-   `frontEndEG/pages/contacto.html` → `resources/views/pages/static/contact.blade.php` (route `pages.contact`).
-   `frontEndEG/pages/login.html` → `resources/views/auth/login.blade.php` (route `auth.login`).
-   `frontEndEG/pages/register.html` → `resources/views/auth/register.blade.php` (route `auth.register`).
-   `frontEndEG/pages/perfil-admin.html` → `resources/views/dashboard/admin/index.blade.php` (route `admin.dashboard`).
-   `frontEndEG/pages/perfil-dueno.html` → `resources/views/dashboard/store/index.blade.php` (route `store.dashboard`).
-   `frontEndEG/pages/perfil-cliente.html` → `resources/views/dashboard/client/index.blade.php` (route `client.dashboard`).

**Asset Inventory & Destinations**

-   Stylesheet `frontEndEG/css/style.css` → merge into `resources/css/app.css`; extracted custom variables to `resources/css/partials/_frontoffice.scss` if splitting becomes necessary (pending Phase 3 refinement).
-   JavaScript files:
    -   `frontEndEG/js/main.js` → `resources/js/frontoffice/main.js` (registrado como entrada de Vite).
    -   `frontEndEG/js/perfil-admin.js` → `resources/js/frontoffice/perfil-admin.js`.
    -   `frontEndEG/js/perfil-dueno.js` → `resources/js/frontoffice/perfil-dueno.js`.
    -   `frontEndEG/js/perfil-cliente.js` → `resources/js/frontoffice/perfil-cliente.js`.
    -   `frontEndEG/js/register.js` → `resources/js/frontoffice/register.js`.
-   Images in `frontEndEG/img/`:
    -   Logos (`logoBYG.png`, `logoShoppingCOmpleto.png`, `logoFavIconBYG.png`) → `public/images/branding/`.
    -   Hero/background photos (`imagenShopping.jpg`, `photo-*.jpg`, generated placeholders) → `public/images/hero/`.
    -   AI generated assets (`Gemini_Generated_*.png`, `image.png`) → `public/images/placeholders/`.
-   `frontEndEG/sitemap.xml` slated for `public/sitemap.xml` after dynamic sitemap implementation.

**Shared Partials & Components**

-   `resources/views/components/nav/main.blade.php`: primary navigation bar; injects dynamic route highlighting and authentication-aware links.
-   `resources/views/components/footer/main.blade.php`: footer with social links and contact info; content to be parameterized for admin-managed settings.
-   `resources/views/components/layout/breadcrumbs.blade.php`: breadcrumb trail rendered on secondary pages.
-   `resources/views/components/promotions/card.blade.php` and `resources/views/components/stores/card.blade.php`: reusable cards for promotions and store listings.
-   `resources/views/layouts/app.blade.php`: base layout including `<head>` metadata, Bootstrap CDN fallbacks (migrated to Vite in Phase 3), and `@vite` directives.
-   `resources/views/layouts/dashboard.blade.php`: specialized layout for role dashboards reusing sidebar/topbar patterns from profile pages.

### Implementation Phase 2

-   GOAL-002: Migrate layout structure into Blade with reusable components.

| Task     | Description                                                                                                                                                                                   | Completed | Date       |
| -------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | --------- | ---------- |
| TASK-004 | Create/extend `resources/views/layouts/app.blade.php` with Bootstrap skeleton and dynamic sections; move navbar/footer markup into `resources/views/components/`.                             | ✅        | 2025-10-30 |
| TASK-005 | Convert `frontEndEG/index.html` into `resources/views/welcome.blade.php` (or `resources/views/home/index.blade.php`) using Blade sections and imported components.                            | ✅        | 2025-10-30 |
| TASK-006 | Convert each `frontEndEG/pages/*.html` into appropriately named Blade views (e.g., `resources/views/pages/locales.blade.php`), replacing static asset references with Vite helpers (`@vite`). | ✅        | 2025-10-30 |

#### Phase 2 Findings (2025-10-30)

-   `resources/views/layouts/app.blade.php` now wraps all public pages with Bootstrap 5 CDN, Vite assets, flash messaging, and slots for page-specific metadata.
-   Navigation (`resources/views/components/nav/main.blade.php`) and footer (`resources/views/components/footer/main.blade.php`) components encapsulate shared header/footer markup with route-aware links and auth states.
-   Breadcrumb component (`resources/views/components/layout/breadcrumbs.blade.php`) renders hierarchical trails with sensible defaults for public routes.
-   Home view migrated to `resources/views/home/index.blade.php`, reusing carousel hero, featured promotions/locales, and CTA sections while sourcing assets via `asset()`.
-   Local listing and detail pages (`resources/views/pages/locales/index.blade.php`, `resources/views/pages/locales/show.blade.php`) provide mock data arrays, filter UI, and promotion cards wired to Laravel routes.
-   Promotions listing and detail pages (`resources/views/pages/promociones/index.blade.php`, `resources/views/pages/promociones/show.blade.php`) mirror the mockup structure, expose filter controls, and reuse shared assets/components.
-   Login and registration (`resources/views/auth/login.blade.php`, `resources/views/auth/register.blade.php`) migrated with form helpers and step toggles ready for future backend wiring.
-   Admin, store-owner, and client dashboards (`resources/views/dashboard/*/index.blade.php`) leverage `layouts/dashboard.blade.php`, mock data, and section navigation scripts.
-   Frontoffice JavaScript entry points (`resources/js/frontoffice/*.js`) are enqueued via `@vite` to preserve navbar, form, and dashboard interactivity.

**Phase 2 Review & Corrections (2025-10-31):**

-   **FIXED:** Removed duplicate Bootstrap CSS/JS loading - layouts were loading Bootstrap via both CDN and Vite, causing conflicts and bloat. Now Bootstrap loads only via Vite from `resources/css/app.css` and `resources/js/app.js`.
-   **FIXED:** Removed redundant `main.js` import from `app.js` - `main.js` is already loaded per-page via `@vite('resources/js/frontoffice/main.js')` in view templates, preventing double initialization.
-   **FIXED:** Navbar overlay conditional rendering - `navbar-overlay` class now applies only on home page where transparent navbar over hero carousel is needed; other pages use standard `bg-white` navbar.
-   **IMPROVED:** Clarified asset loading strategy in comments: `app.js` provides global Bootstrap bundle, page-specific modules load separately for code splitting.

### Implementation Phase 3 ✅

-   GOAL-003: Integrate CSS/JS assets into Laravel's Vite build pipeline.
-   **Status:** COMPLETED (2025-10-31)

| Task     | Description                                                                                                                                   | Completed | Date       |
| -------- | --------------------------------------------------------------------------------------------------------------------------------------------- | --------- | ---------- |
| TASK-007 | Merge `frontEndEG/css/style.css` into `resources/css/app.css`, preserving selectors and resolving conflicts; document removed styles.         | ✅        | 2025-10-30 |
| TASK-008 | Port `frontEndEG/js/*.js` modules into `resources/js/` (splitting into ES modules as needed) and register them within `resources/js/app.js`.  | ✅        | 2025-10-31 |
| TASK-009 | Add any required third-party dependencies (e.g., Bootstrap plugins) to `package.json` and update `vite.config.js` paths for asset resolution. | ✅        | 2025-10-31 |

#### Phase 3 Findings (2025-10-30 / 2025-10-31)

**CSS Integration (TASK-007, 2025-10-30):**

-   Consolidated the legacy mockup styles into `resources/css/app.css`, keeping the palette, hero, card, and dashboard aesthetics intact while aligning variable names with the Vite pipeline.
-   Normalized navbar overlay behavior to work with the Blade component (`<x-nav.main>`) by ensuring default and scrolled states are defined in the shared stylesheet.
-   Revalidated responsive breakpoints for hero, cards, and navbar toggles so mobile layouts match the mockup behavior now that Vite serves the CSS bundle.

**JavaScript Modularization (TASK-008, 2025-10-31):**

-   Refactored all five frontoffice JavaScript files (`main.js`, `register.js`, `perfil-admin.js`, `perfil-dueno.js`, `perfil-cliente.js`) from IIFE/window-attached patterns to ES6 modules with named exports.
-   Maintained backward compatibility by re-exporting functions to `window` object for existing inline onclick handlers in Blade templates (planned for future refactoring to event delegation).
-   Enhanced code maintainability with JSDoc comments, extracted configuration objects (section-to-button color mappings), and clear module documentation.
-   Verified modular loading strategy: `app.js` serves as global entry importing shared navbar behavior, while page-specific modules load via dedicated `@vite()` directives in respective Blade views.
-   Created `resources/js/frontoffice/README.md` documenting module purposes, exports, loading strategy, and future enhancement roadmap.
-   Confirmed all modules pass Node.js syntax validation; Vite entry points remain registered in `vite.config.js` for proper bundling.
-   Bootstrap 5.3.8 and `@popperjs/core` confirmed installed in `package.json`; no additional dependencies required for current functionality.

**Vite Configuration & Dependencies (TASK-009, 2025-10-31):**

-   Verified Bootstrap 5.3.8 and Popper.js already present in `package.json` (installed during Laravel Breeze scaffolding); no additional third-party plugins required for current mockup functionality.
-   Enhanced `vite.config.js` with path aliases for cleaner imports: `~bootstrap` (Bootstrap modules), `@` (JS root), `@css` (CSS root).
-   All seven asset entry points correctly registered in laravel-vite-plugin input array (1 CSS, 6 JS bundles).
-   Confirmed Tailwind CSS v4 plugin active alongside Bootstrap for future utility-first styling if needed (current mockup uses pure Bootstrap classes).
-   Vite refresh enabled for hot module replacement during development.
-   **Build Validation:** Successfully executed `npm run build` with zero errors, producing optimized production bundles:
    -   `app.css`: 242.54 KB (33.65 KB gzipped) - consolidated stylesheet
    -   `app.js`: 117.03 KB (38.87 KB gzipped) - global bundle with Bootstrap
    -   5 page-specific bundles ranging from 0.72-1.19 KB each (register, dashboards, main nav)
    -   Build manifest generated at `public/build/manifest.json` for asset fingerprinting

### Implementation Phase 4 ✅

-   GOAL-004: Wire Laravel routes and controllers to serve migrated views.
-   **Status:** COMPLETED (2025-10-31)

| Task     | Description                                                                                                                                  | Completed | Date       |
| -------- | -------------------------------------------------------------------------------------------------------------------------------------------- | --------- | ---------- |
| TASK-010 | Update `routes/web.php` with named routes for each new Blade page, grouping by role (admin, store, client, public) per instructions.         | ✅        | 2025-10-31 |
| TASK-011 | Scaffold placeholder controllers (e.g., `App\Http\Controllers\PageController`) returning the new Blade views, ready for backend logic later. | ✅        | 2025-10-31 |
| TASK-012 | Configure navigation links within Blade components to use Laravel `route()` helpers instead of static `.html` paths.                         | ✅        | 2025-10-31 |

#### Phase 4 Findings (2025-10-31)

**Route Structure (TASK-010):**

-   Created comprehensive route definitions in `routes/web.php` organized by access level:
    -   **Public routes** (no auth required): Home, Locales, Promociones, Novedades, static pages (About, Contact)
    -   **Auth aliases**: `auth.login` and `auth.register` redirect to Laravel's built-in auth routes for consistency with mockup conventions
    -   **Admin routes**: Protected by `auth` + `verified` middleware, prefixed with `/admin`, namespace `Admin\`
    -   **Store owner routes**: Protected middleware, prefixed with `/local`, namespace `Store\`
    -   **Client routes**: Protected middleware, prefixed with `/cliente`, namespace `Client\`
-   All routes use named route convention matching Phase 1 planning (e.g., `home.index`, `pages.locales`, `pages.promociones.show`, `admin.dashboard`)
-   Implemented RESTful patterns with `{id}` parameters for detail pages (locales, promociones)
-   Route verification: 15 routes registered successfully, cached without errors

**Controller Scaffolding (TASK-011):**

-   Created 8 placeholder controllers with clear TODO comments for future backend integration:
    -   `HomeController` - Home page with featured content
    -   `LocalController` - Store listing (index) and details (show) with filtering placeholders
    -   `PromocionController` - Promotion listing and details with category restriction logic placeholders
    -   `NovedadController` - News listing with category-based visibility logic placeholders
    -   `PageController` - Static pages (about, contact) + contact form submission placeholder
    -   `Admin\AdminDashboardController` - Admin panel with management sections TODO
    -   `Store\StoreDashboardController` - Store owner panel with promotions CRUD and request handling TODO
    -   `Client\ClientDashboardController` - Client panel with profile, usage history, and category info TODO
-   All controllers return appropriate Blade views from Phase 2 migration
-   Controllers pass minimal data (e.g., `storeId`, `promoId`) to views where needed
-   Type hints added for Request and View returns following Laravel best practices
-   TODO comments document business logic requirements per project instructions (category restrictions, approval workflows, usage tracking)

**Navigation Route Integration (TASK-012):**

-   Verified Phase 2 navbar component already implements Laravel `route()` helpers with `Route::has()` fallbacks
-   All internal links use `route('route.name')` instead of hardcoded URLs
-   Auth-dependent dashboard links check route existence and user permissions before rendering
-   Breadcrumbs and footer components use same pattern for consistency
-   No static `.html` references remain in navigation; all links are Laravel-aware

**Validation & Testing:**

-   Routes cached successfully (`php artisan route:cache`) with zero errors
-   Route list confirms all 15 routes accessible with correct controllers
-   Development server starts without errors (`php artisan serve`)
-   All controllers autoload correctly (no undefined class errors)

### Implementation Phase 5

-   GOAL-005: Validate integration and retire legacy mockup directory.

| Task     | Description                                                                                                                                                    | Completed | Date |
| -------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------- | --------- | ---- |
| TASK-013 | Run `npm run build` and `npm run dev` to verify Vite bundling, fixing any missing asset references or build errors.                                            |           |      |
| TASK-014 | Execute browser smoke tests for desktop and mobile breakpoints using Laravel-served pages (`php artisan serve`) to confirm responsiveness and navigation flow. |           |      |
| TASK-015 | Archive or delete `frontEndEG/` once parity is confirmed, updating `.gitignore` or documentation accordingly.                                                  |           |      |

## 3. Alternatives

-   **ALT-001**: Serve the static mockup directly from `public/` without Blade migration; rejected because it blocks backend integration and template reuse.
-   **ALT-002**: Rewrite the frontend with a SPA framework; rejected for scope creep and misalignment with provided mockup and instructions.

## 4. Dependencies

-   **DEP-001**: Node.js toolchain for Vite (`npm install`, `npm run dev/build`).
-   **DEP-002**: Bootstrap 5 (already included via Laravel Breeze scaffolding or CDN, verify local availability).
-   **DEP-003**: Laravel routing and Blade support (core framework components).

## 5. Files

-   **FILE-001**: `resources/views/layouts/app.blade.php` – base layout updates.
-   **FILE-002**: `resources/views/components/` – new Blade components for shared UI.
-   **FILE-003**: `resources/views/pages/` – migrated page templates.
-   **FILE-004**: `resources/css/app.css` – consolidated stylesheet.
-   **FILE-005**: `resources/js/app.js` and `resources/js/*.js` – migrated scripts.
-   **FILE-006**: `routes/web.php` – route definitions.
-   **FILE-007**: `vite.config.js` – asset alias and build configuration.

## 6. Testing

-   **TEST-001**: Manual UI regression checklist covering navigation, forms, and responsive breakpoints on key pages (home, promotions, profiles).
-   **TEST-002**: `npm run build` verification ensuring Vite outputs without warnings or missing asset errors.
-   **TEST-003**: `php artisan test` placeholder feature test ensuring main routes return 200 status (to be expanded later).

## 7. Risks & Assumptions

-   **RISK-001**: CSS class collisions during merge may break layout; mitigation via scoped components and incremental testing.
-   **RISK-002**: JavaScript dependencies might rely on DOM IDs altered during Blade conversion.
-   **ASSUMPTION-001**: Existing mockup assets are fully responsive and require no design changes beyond integration.

## 8. Related Specifications / Further Reading

-   ShoppingRio project instructions: `.github/instructions/EnunciadoProyecto.instructions.md`
-   Project coding guidelines: `.github/copilot-instructions.md`
-   Laravel documentation on Blade: https://laravel.com/docs/blade
-   Laravel Vite integration guide: https://laravel.com/docs/vite
