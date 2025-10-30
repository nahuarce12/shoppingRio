---
goal: Integrate frontEndEG mockup into Laravel view layer
version: 1.0
date_created: 2025-10-29
status: Planned
tags: [feature, frontend, integration]
---

# Introduction

Status badge: (status: Planned, color: blue)

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

| Task     | Description                                                                                                                                                                    | Completed | Date |
| -------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ | --------- | ---- |
| TASK-001 | Inventory HTML templates under `frontEndEG/index.html` and `frontEndEG/pages/*.html`, mapping each to target Blade paths in `resources/views/` with planned route names.       |           |      |
| TASK-002 | Catalog CSS/JS assets (`frontEndEG/css`, `frontEndEG/js`) and images (`frontEndEG/img`) specifying destination directories (`resources/css`, `resources/js`, `public/images`). |           |      |
| TASK-003 | Identify shared partials (header/footer/nav) within HTML to design Blade includes/components (`resources/views/components/`).                                                  |           |      |

### Implementation Phase 2

-   GOAL-002: Migrate layout structure into Blade with reusable components.

| Task     | Description                                                                                                                                                                                   | Completed | Date |
| -------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | --------- | ---- |
| TASK-004 | Create/extend `resources/views/layouts/app.blade.php` with Bootstrap skeleton and dynamic sections; move navbar/footer markup into `resources/views/components/`.                             |           |      |
| TASK-005 | Convert `frontEndEG/index.html` into `resources/views/welcome.blade.php` (or `resources/views/home/index.blade.php`) using Blade sections and imported components.                            |           |      |
| TASK-006 | Convert each `frontEndEG/pages/*.html` into appropriately named Blade views (e.g., `resources/views/pages/locales.blade.php`), replacing static asset references with Vite helpers (`@vite`). |           |      |

### Implementation Phase 3

-   GOAL-003: Integrate CSS/JS assets into Laravel's Vite build pipeline.

| Task     | Description                                                                                                                                   | Completed | Date |
| -------- | --------------------------------------------------------------------------------------------------------------------------------------------- | --------- | ---- |
| TASK-007 | Merge `frontEndEG/css/style.css` into `resources/css/app.css`, preserving selectors and resolving conflicts; document removed styles.         |           |      |
| TASK-008 | Port `frontEndEG/js/*.js` modules into `resources/js/` (splitting into ES modules as needed) and register them within `resources/js/app.js`.  |           |      |
| TASK-009 | Add any required third-party dependencies (e.g., Bootstrap plugins) to `package.json` and update `vite.config.js` paths for asset resolution. |           |      |

### Implementation Phase 4

-   GOAL-004: Wire Laravel routes and controllers to serve migrated views.

| Task     | Description                                                                                                                                  | Completed | Date |
| -------- | -------------------------------------------------------------------------------------------------------------------------------------------- | --------- | ---- |
| TASK-010 | Update `routes/web.php` with named routes for each new Blade page, grouping by role (admin, store, client, public) per instructions.         |           |      |
| TASK-011 | Scaffold placeholder controllers (e.g., `App\Http\Controllers\PageController`) returning the new Blade views, ready for backend logic later. |           |      |
| TASK-012 | Configure navigation links within Blade components to use Laravel `route()` helpers instead of static `.html` paths.                         |           |      |

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
