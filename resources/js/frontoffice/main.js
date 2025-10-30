document.addEventListener('DOMContentLoaded', () => {
    const navbarToggle = document.querySelector('.navbar-toggler');
    const navbarLinks = document.querySelectorAll('.navbar-nav a');

    navbarLinks.forEach((link) => {
        link.addEventListener('click', () => {
            if (navbarToggle && window.getComputedStyle(navbarToggle).display !== 'none') {
                navbarToggle.click();
            }
        });
    });

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

    const tooltipTriggerList = Array.from(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach((tooltipTriggerEl) => {
        // eslint-disable-next-line no-undef
        new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
