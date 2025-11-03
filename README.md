## ShoppingRio - Shopping Center Management System

Laravel-based web application for managing discounts and promotions in a shopping center with 4 user types: Administrator, Store Owners, Clients, and Unregistered Users.

### Project Status

| Phase              | Status      | Tasks | Coverage                         |
| ------------------ | ----------- | ----- | -------------------------------- |
| 1: Database Schema | ✅ Complete | 6/6   | Migrations, tables, indexes      |
| 2: Eloquent Models | ✅ Complete | 6/6   | Models, relationships, scopes    |
| 3: Authentication  | ✅ Complete | 7/7   | Fortify, middleware, policies    |
| 4: Controllers     | ⏳ Ready    | 0/5   | Business logic, CRUD             |
| 5-12: Other Phases | ⏳ Pending  | 0/27  | Views, jobs, testing, deployment |

**Overall Progress**: 20/53 tasks (38%) ✅

### Documentation

-   **[WELCOME.md](.github/WELCOME.md)** - Start here! Quick overview of current status
-   **[DOCUMENTATION_INDEX.md](.github/DOCUMENTATION_INDEX.md)** - Navigation hub for all docs
-   **[BACKEND_IMPLEMENTATION_PLAN.md](.github/BACKEND_IMPLEMENTATION_PLAN.md)** - Master plan for all 12 phases
-   **[PHASE4_ROADMAP.md](.github/PHASE4_ROADMAP.md)** - Ready-to-start Phase 4 guide
-   **[QUICK_REFERENCE.md](.github/QUICK_REFERENCE.md)** - Quick lookup for files and usage

### What's Implemented (Phase 3)

✅ **Authentication System** (Laravel Fortify v1.31.2)

-   Multi-user type registration
-   Email verification for clients
-   Password reset functionality

✅ **Authorization Layer** (3 Policies, 3 Middleware)

-   Admin-only features
-   Store owner management with approval workflow
-   Client promotions and category-based access
-   28+ authorization methods

✅ **Store Owner Approval Workflow**

-   Pending store owner approval system
-   Email notifications (approved/rejected)
-   Admin approval interface

✅ **Database** (5 Migrations)

-   Users with roles and categories
-   Stores with soft deletes
-   Promotions with day-of-week and category rules
-   News with auto-expiration
-   Promotion usage tracking

✅ **Models** (5 Eloquent Models)

-   40+ query scopes
-   35+ helper methods
-   Sequential code generation
-   Full relationship support

### Quick Start

```bash
# Install dependencies
composer install

# Configure environment
cp .env.example .env
php artisan key:generate

# Run migrations
php artisan migrate

# Start development server
php artisan serve
```

### User Types & Access

-   **Admin**: Full system access (manage stores, approve promotions, create news)
-   **Store Owner**: Manage own store (create promotions, handle client requests)
-   **Client**: Browse and request promotions (after email verification)
-   **Unregistered**: View-only access to promotions

### Next Phase: Phase 4 - Controllers

Ready to implement 15+ controllers for CRUD operations:

-   Admin controllers for stores, promotions, news
-   Store owner controllers for managing their data
-   Client controllers for browsing and requesting
-   Public controllers for unregistered users

See: [PHASE4_ROADMAP.md](.github/PHASE4_ROADMAP.md)

### Technology Stack

-   **Framework**: Laravel 12.31.1
-   **PHP**: 8.2.12
-   **Database**: MySQL/MariaDB
-   **Authentication**: Laravel Fortify v1.31.2
-   **Frontend**: Bootstrap 5
-   **Deployment**: XAMPP (Development)

### Project Requirements

See: `.github/instructions/EnunciadoProyecto.instructions.md`

---

## About Laravel

This project is built on Laravel, a web application framework with expressive, elegant syntax.
