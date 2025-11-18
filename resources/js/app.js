import './bootstrap';

// Import Bootstrap CSS
import '../css/app.css';

// Import Bootstrap JavaScript
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

/**
 * Global application entry point
 * Loaded on ALL pages via @vite(['resources/css/app.css', 'resources/js/app.js'])
 * 
 * Page-specific JavaScript modules are loaded via separate @vite() directives:
 * - resources/js/frontoffice/main.js → All public pages (navbar behavior, tooltips)
 * - resources/js/frontoffice/register.js → auth/register.blade.php (wizard navigation)
 * - resources/js/frontoffice/perfil-admin.js → dashboard/admin/index.blade.php (admin sections)
 * - resources/js/frontoffice/perfil-dueno.js → dashboard/store/index.blade.php (store sections)
 * - resources/js/frontoffice/perfil-cliente.js → dashboard/client/index.blade.php (client sections)
 * 
 * This file provides Bootstrap CSS/JS globally; page-specific behavior is modular.
 */
