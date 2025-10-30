// ===========================
// JS SIMPLIFICADO - Solo estructura visual
// Versión minimalista: Solo lo esencial sin validaciones ni filtros
// ===========================

document.addEventListener('DOMContentLoaded', function() {
    // Cerrar navbar al hacer click en un enlace (móvil)
    const navbarToggle = document.querySelector('.navbar-toggler');
    const navbarLinks = document.querySelectorAll('.navbar-nav a');
    
    navbarLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (navbarToggle.offsetParent !== null) { // Si está visible en móvil
                navbarToggle.click();
            }
        });
    });
    
    // Navbar: cambiar de fondo borroso a sólido al hacer scroll (solo en index.html)
    const navbar = document.querySelector('.navbar');
    const navbarLogo = document.querySelector('.navbar-brand img');
    const isHomePage = window.location.pathname === '/' || window.location.pathname.includes('index.html');
    
    // Solo aplicar animación de scroll en la página de inicio
    if (isHomePage && navbar) {
        function handleNavbarScroll() {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
                // Cambiar logo al hacer scroll
                if (navbarLogo) {
                    navbarLogo.src = 'img/logoShoppingCompleto.png';
                }
            } else {
                navbar.classList.remove('scrolled');
                // Volver al logo original
                if (navbarLogo) {
                    navbarLogo.src = 'img/logoBYG.png';
                }
            }
        }
        
        // Ejecutar al cargar y al hacer scroll
        handleNavbarScroll();
        window.addEventListener('scroll', handleNavbarScroll);
    }
    
    // Inicializar tooltips de Bootstrap (opcional)
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
