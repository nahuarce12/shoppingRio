window.showAdminSection = function showAdminSection(sectionId) {
    document.querySelectorAll('.content-section').forEach((section) => {
        section.style.display = 'none';
    });

    const section = document.getElementById(sectionId);
    if (section) {
        section.style.display = 'block';
    }

    const sidebarButtons = document.querySelectorAll('.col-lg-3 .btn');
    sidebarButtons.forEach((btn) => {
        btn.classList.remove('btn-primary', 'btn-warning', 'btn-success', 'btn-info', 'btn-secondary');
        if (btn.dataset.originalClass) {
            btn.classList.add(btn.dataset.originalClass);
        } else {
            btn.classList.add('btn-outline-primary');
        }
    });

    const evt = window.event;
    const activeButton = evt ? evt.target.closest('button') : null;
    if (activeButton) {
        activeButton.classList.remove('btn-outline-primary', 'btn-outline-warning', 'btn-outline-success', 'btn-outline-info', 'btn-outline-secondary');

        if (sectionId === 'aprobar-promociones') {
            activeButton.classList.add('btn-warning');
        } else if (sectionId === 'novedades') {
            activeButton.classList.add('btn-success');
        } else if (sectionId === 'reportes') {
            activeButton.classList.add('btn-info');
        } else if (sectionId === 'configuracion') {
            activeButton.classList.add('btn-secondary');
        } else {
            activeButton.classList.add('btn-primary');
        }
    }
};

document.addEventListener('DOMContentLoaded', () => {
    const sidebarButtons = document.querySelectorAll('.col-lg-3 .btn');
    sidebarButtons.forEach((btn) => {
        if (!btn.dataset.originalClass) {
            const classes = Array.from(btn.classList).find((cls) => cls.startsWith('btn-outline-'));
            if (classes) {
                btn.dataset.originalClass = classes;
            }
        }
    });

    const dashboard = document.getElementById('dashboard');
    const locales = document.getElementById('locales');
    if (dashboard) dashboard.style.display = 'block';
    if (locales) locales.style.display = 'block';
});
