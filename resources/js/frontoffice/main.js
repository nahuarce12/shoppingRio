/**
 * Navbar behavior module
 * Handles mobile menu toggle, overlay scroll effects, and Bootstrap tooltips
 */

/**
 * Initialize mobile navbar auto-collapse on link click
 */
export function initMobileNavCollapse() {
    const navbarToggle = document.querySelector('.navbar-toggler');
    const navbarLinks = document.querySelectorAll('.navbar-nav a');

    navbarLinks.forEach((link) => {
        link.addEventListener('click', () => {
            if (navbarToggle && window.getComputedStyle(navbarToggle).display !== 'none') {
                navbarToggle.click();
            }
        });
    });
}

/**
 * Initialize navbar overlay scroll behavior with logo swap
 */
export function initNavbarOverlayScroll() {
    const navbar = document.querySelector('.navbar');
    const navbarLogo = document.querySelector('.navbar-brand img');
    const originalLogoSrc = navbarLogo ? navbarLogo.getAttribute('src') : null;
    const scrolledLogoSrc = '/images/branding/logoShoppingCompleto.png';
    const isHomePage = window.location.pathname === '/' || window.location.pathname.includes('home');

    if (isHomePage && navbar && navbarLogo) {
        const handleNavbarScroll = () => {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
                navbarLogo.setAttribute('src', scrolledLogoSrc);
            } else {
                navbar.classList.remove('scrolled');
                if (originalLogoSrc) {
                    navbarLogo.setAttribute('src', originalLogoSrc);
                }
            }
        };

        handleNavbarScroll();
        window.addEventListener('scroll', handleNavbarScroll);
    }
}

/**
 * Initialize Bootstrap tooltips across the page
 */
export function initTooltips() {
    const tooltipTriggerList = Array.from(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach((tooltipTriggerEl) => {
        // Bootstrap tooltip initialization
        new window.bootstrap.Tooltip(tooltipTriggerEl);
    });
}

/**
 * Main initialization entry point
 */
export function initNavbarBehavior() {
    initMobileNavCollapse();
    initNavbarOverlayScroll();
    initTooltips();
}

// Auto-initialize on DOM ready
document.addEventListener('DOMContentLoaded', initNavbarBehavior);
