window.showSection = function showSection(sectionId) {
    document.querySelectorAll('.content-section').forEach((section) => {
        section.style.display = 'none';
    });

    const section = document.getElementById(sectionId);
    if (section) {
        section.style.display = 'block';
    }

    const sidebarButtons = document.querySelectorAll('.col-lg-3 .btn');
    sidebarButtons.forEach((btn) => {
        btn.classList.remove('btn-primary');
        const outlineClass = Array.from(btn.classList).find((cls) => cls.startsWith('btn-outline'));
        if (!btn.dataset.outlineClass && outlineClass) {
            btn.dataset.outlineClass = outlineClass;
        }
        if (btn.dataset.outlineClass) {
            btn.classList.add(btn.dataset.outlineClass);
        } else {
            btn.classList.add('btn-outline-primary');
        }
    });

    const evt = window.event;
    const activeButton = evt ? evt.target.closest('button') : null;
    if (activeButton) {
        activeButton.classList.remove('btn-outline-primary', 'btn-outline-warning', 'btn-outline-success', 'btn-outline-secondary');
        if (sectionId === 'solicitudes') {
            activeButton.classList.add('btn-warning');
        } else if (sectionId === 'reportes') {
            activeButton.classList.add('btn-success');
        } else if (sectionId === 'editar-perfil') {
            activeButton.classList.add('btn-secondary');
        } else {
            activeButton.classList.add('btn-primary');
        }
    }
};

document.addEventListener('DOMContentLoaded', () => {
    const dashboard = document.getElementById('dashboard');
    const misPromociones = document.getElementById('mis-promociones');

    if (dashboard) dashboard.style.display = 'block';
    if (misPromociones) misPromociones.style.display = 'block';

    const defaultButton = document.querySelector("button[onclick*='mis-promociones']");
    if (defaultButton) {
        defaultButton.classList.remove('btn-outline-primary');
        defaultButton.classList.add('btn-primary');
    }
});
