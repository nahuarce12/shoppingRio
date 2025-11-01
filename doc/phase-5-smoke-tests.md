# Phase 5 - Browser Smoke Test Checklist

**Date:** October 31, 2025  
**Test Environment:** Laravel Development Server (http://127.0.0.1:8000)  
**Vite Dev Server:** http://localhost:5173

---

## Pre-Test Validation ✅

- [x] `npm run build` - Production build successful (1.79s, 7 assets)
- [x] `npm run dev` - Dev server running on localhost:5173
- [x] `php artisan serve` - Laravel server running on 127.0.0.1:8000
- [x] Route verification - All 15 routes registered
- [x] View existence - All Blade templates exist
- [x] Vite manifest - Generated correctly with 7 entry points
- [x] Image assets - All directories present (branding, hero, placeholders)

---

## Desktop Smoke Tests (1920x1080)

### Public Pages

#### ✅ Home Page (/)
- [ ] Hero carousel loads with 3 slides
- [ ] Carousel controls functional (prev/next/indicators)
- [ ] Logo swaps on scroll (transparent → solid navbar)
- [ ] Featured promotions section renders
- [ ] Featured stores section renders
- [ ] Call-to-action buttons link correctly
- [ ] Footer displays correctly
- [ ] Navbar links highlight active page

#### ✅ Locales (/locales)
- [ ] Store cards grid displays (3 columns desktop)
- [ ] Alphabet filter renders
- [ ] Category dropdown functional
- [ ] Search input present
- [ ] Store images load
- [ ] Store cards hover effect works
- [ ] "Ver Detalles" buttons link to correct routes
- [ ] Pagination controls (if applicable)

#### ✅ Local Detail (/locales/{id})
- [ ] Store header with image and info
- [ ] Store details (location, category, hours)
- [ ] Related promotions section
- [ ] Breadcrumb navigation correct
- [ ] Back button functional
- [ ] Social media links (if present)

#### ✅ Promociones (/promociones)
- [ ] Promotion cards grid displays
- [ ] Filter section (category, date, store)
- [ ] Category badges display correctly (Inicial/Medium/Premium)
- [ ] Day-of-week indicators show
- [ ] "¡Por vencer!" badge on expiring promotions
- [ ] Promotion images load
- [ ] Cards link to detail pages

#### ✅ Promoción Detail (/promociones/{id})
- [ ] Promotion image displays
- [ ] Promotion details (dates, days, description)
- [ ] Store information section
- [ ] Category restriction badge
- [ ] Validity dates and days shown
- [ ] "Solicitar Descuento" button (if authenticated)
- [ ] Related promotions section

#### ✅ Novedades (/novedades)
- [ ] News cards display in list/grid
- [ ] Category badges show
- [ ] Expiration dates visible
- [ ] News content readable
- [ ] Images load correctly

#### ✅ Quiénes Somos (/quienes-somos)
- [ ] Hero section renders
- [ ] About text content displays
- [ ] Feature boxes (3 columns)
- [ ] Team section (if present)
- [ ] Images load correctly

#### ✅ Contacto (/contacto)
- [ ] Contact form renders
- [ ] Contact info boxes display (address, phone, email, hours)
- [ ] Map placeholder (if present)
- [ ] Form validation (client-side)
- [ ] Submit button functional

---

### Authentication Pages

#### ✅ Login (/login)
- [ ] Login form renders correctly
- [ ] Email and password inputs functional
- [ ] "Remember me" checkbox
- [ ] "Forgot password" link
- [ ] "Register" CTA section
- [ ] Form validation works
- [ ] CSRF token present

#### ✅ Register (/register)
- [ ] Step 1: User type selection (Cliente/Dueño)
- [ ] Step 2: Client form (name, email, password)
- [ ] Step 3: Store owner form (additional fields)
- [ ] Wizard navigation works (showClientForm, showOwnerForm, showStep1)
- [ ] Form validation functional
- [ ] Password strength indicator (if present)
- [ ] Back buttons work

---

### Dashboard Pages (Require Authentication)

#### ✅ Admin Dashboard (/admin/dashboard)
- [ ] Sidebar navigation renders
- [ ] Dashboard section shows statistics
- [ ] Locales management section
- [ ] Promotions approval queue
- [ ] News management section
- [ ] Reports section
- [ ] Tab switching works (showAdminSection)
- [ ] Section-specific button colors (primary/warning/success/info/secondary)

#### ✅ Store Owner Dashboard (/local/dashboard)
- [ ] Sidebar navigation renders
- [ ] Dashboard overview with stats
- [ ] "Mis Promociones" section (default active)
- [ ] Create promotion form
- [ ] Promotion list with delete buttons
- [ ] Discount requests section
- [ ] Reports section
- [ ] Tab switching works (showSection)

#### ✅ Client Dashboard (/cliente/mi-cuenta)
- [ ] Sidebar navigation renders
- [ ] Personal information section (default active)
- [ ] Category badge displays (Inicial/Medium/Premium)
- [ ] Available promotions section
- [ ] Usage history section
- [ ] Profile edit form
- [ ] Tab switching works (showClientSection)

---

## Mobile Smoke Tests (375x667 - iPhone SE)

### Layout & Navigation

- [ ] Navbar collapses to hamburger menu
- [ ] Mobile menu opens/closes correctly
- [ ] Menu auto-closes on link click
- [ ] Navbar logo visible and sized correctly
- [ ] Sticky navbar works on scroll

### Content Responsiveness

- [ ] Hero carousel full-height on mobile
- [ ] Carousel text box readable (not cut off)
- [ ] Cards stack to 1 column
- [ ] Images scale correctly (object-fit)
- [ ] Buttons full-width on mobile
- [ ] Forms adapt to narrow screen
- [ ] Tables scroll horizontally (if present)
- [ ] Footer stacks vertically

### Dashboard Mobile

- [ ] Sidebar collapses or converts to dropdown
- [ ] Dashboard sections scroll correctly
- [ ] Tables responsive (cards or scroll)
- [ ] Action buttons accessible
- [ ] Forms usable on small screen

---

## Tablet Smoke Tests (768x1024 - iPad)

- [ ] Cards display 2 columns
- [ ] Navbar expanded (not collapsed)
- [ ] Dashboard sidebar visible
- [ ] Content readable without horizontal scroll
- [ ] Touch targets large enough

---

## Cross-Browser Tests (Optional - TASK-014 Extension)

### Chrome/Edge (Chromium)
- [ ] All features functional
- [ ] CSS Grid/Flexbox rendering correct
- [ ] Vite HMR works in dev mode

### Firefox
- [ ] CSS compatibility (especially backdrop-filter)
- [ ] JavaScript modules load

### Safari (if available)
- [ ] Webkit-specific prefixes working
- [ ] iOS touch events

---

## Performance Checks

- [ ] Page load time < 3s (production build)
- [ ] Images lazy load (if implemented)
- [ ] No console errors
- [ ] No 404s for assets
- [ ] Vite manifest references correct hashes
- [ ] Bootstrap CSS/JS not loaded twice

---

## Accessibility Quick Checks

- [ ] Navbar keyboard navigable (Tab key)
- [ ] Forms have labels
- [ ] Images have alt text
- [ ] Color contrast sufficient
- [ ] Focus indicators visible

---

## Known Issues / Expected Behavior

### Mock Data
- All content is static/mock data embedded in Blade templates
- Database queries return placeholder content
- Authentication may fail (no auth system fully wired yet)
- Form submissions return mock success messages

### TODO Functionality
- Contact form doesn't send emails (TODO in PageController)
- Dashboard actions don't persist (controllers return views only)
- No real data filtering (controllers have TODO comments)
- User permissions not enforced (Gates/Policies not implemented)

---

## Test Results Summary

**Date Tested:** _____________  
**Tester:** _____________  
**Browser:** _____________  
**Resolution:** _____________  

**Passed:** _____ / _____  
**Failed:** _____ / _____  
**Blocked:** _____ / _____  

**Critical Issues Found:**
1. 
2. 
3. 

**Notes:**
