# Frontoffice JavaScript Modules

This directory contains modular JavaScript files ported from the legacy `frontEndEG/js/` mockup structure, refactored to follow ES6 module patterns while maintaining backward compatibility with inline onclick handlers.

## Module Overview

### `main.js` - Global Navigation & UI Behavior
**Usage:** Loaded on all public-facing pages via `layouts/app.blade.php`

**Exports:**
- `initNavbarBehavior()` - Main initialization entry point
- `initMobileNavCollapse()` - Auto-collapse mobile navbar on link click
- `initNavbarOverlayScroll()` - Navbar overlay scroll effects with logo swap
- `initTooltips()` - Bootstrap tooltip initialization

**Auto-loads on:** DOM ready via internal event listener

---

### `register.js` - Registration Wizard
**Usage:** Loaded only on `auth/register.blade.php`

**Exports:**
- `showClientForm()` - Display client registration form
- `showOwnerForm()` - Display store owner registration form
- `showStep1()` - Return to user type selection step

**Window exports:** All functions exposed to `window` for inline onclick handlers

---

### `perfil-admin.js` - Administrator Dashboard
**Usage:** Loaded only on `dashboard/admin/index.blade.php`

**Exports:**
- `showAdminSection(sectionId)` - Navigate between dashboard sections

**Window exports:** `showAdminSection` for inline onclick handlers

**Features:**
- Section-specific button color coding (warning, success, info, secondary)
- Automatic sidebar button state management
- Default sections: `dashboard`, `locales`

---

### `perfil-dueno.js` - Store Owner Dashboard
**Usage:** Loaded only on `dashboard/store/index.blade.php`

**Exports:**
- `showSection(sectionId)` - Navigate between dashboard sections

**Window exports:** `showSection` for inline onclick handlers

**Features:**
- Section-specific button color coding
- Default sections: `dashboard`, `mis-promociones`
- Automatic highlight of "My Promotions" button on load

---

### `perfil-cliente.js` - Client Dashboard
**Usage:** Loaded only on `dashboard/client/index.blade.php`

**Exports:**
- `showClientSection(sectionId)` - Navigate between dashboard sections

**Window exports:** `showClientSection` for inline onclick handlers

**Features:**
- Simplified button state management
- Default section: `info-personal`

---

## Loading Strategy

### Global Loading (app.js)
```javascript
// Loaded on ALL pages
@vite(['resources/css/app.css', 'resources/js/app.js'])
```
- Bootstrap bundle
- Shared styles
- Navigation behavior (imported from main.js)

### Page-Specific Loading
```blade
{{-- Example: Registration page --}}
@vite('resources/js/frontoffice/register.js')

{{-- Example: Admin dashboard --}}
@vite('resources/js/frontoffice/perfil-admin.js')
```

### Public Pages Additional Loading
```blade
{{-- In layouts/app.blade.php for all public pages --}}
@vite('resources/js/frontoffice/main.js')
```

## Vite Configuration

All modules are registered in `vite.config.js` as separate entry points:

```javascript
laravel({
    input: [
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/js/frontoffice/main.js',        // Public pages
        'resources/js/frontoffice/register.js',    // Registration
        'resources/js/frontoffice/perfil-admin.js',   // Admin dashboard
        'resources/js/frontoffice/perfil-dueno.js',   // Store dashboard
        'resources/js/frontoffice/perfil-cliente.js', // Client dashboard
    ],
    refresh: true,
})
```

## Migration Notes

### From Legacy Mockup
- **Original location:** `frontEndEG/js/*.js`
- **Migration strategy:** 
  1. Converted IIFE patterns to ES6 modules with named exports
  2. Maintained window exports for backward compatibility with inline onclick
  3. Added JSDoc comments for better IDE support
  4. Extracted configuration objects (e.g., section button colors)
  5. Preserved all DOM manipulation logic

### Backward Compatibility
- All functions exposed to `window` object to support existing Blade inline onclick handlers
- Future refactoring: Replace inline onclick with event delegation for cleaner code

### Future Enhancements
- [ ] Replace inline onclick handlers with data attributes + event delegation
- [ ] Add TypeScript types for better type safety
- [ ] Extract common dashboard logic into shared base module
- [ ] Implement proper state management for complex dashboard interactions
- [ ] Add unit tests for dashboard section navigation logic

## Development Workflow

### Build Assets
```bash
# Development mode with hot reload
npm run dev

# Production build
npm run build
```

### Debugging
1. Check browser console for JavaScript errors
2. Verify Vite manifest exists: `public/build/manifest.json`
3. Inspect network tab for proper asset loading
4. Use Vue DevTools or React DevTools if SPA features added later

## Related Files
- `vite.config.js` - Vite entry point configuration
- `resources/js/app.js` - Global JavaScript entry
- `resources/css/app.css` - Consolidated styles
- `resources/views/layouts/*.blade.php` - Layout templates with @vite directives
