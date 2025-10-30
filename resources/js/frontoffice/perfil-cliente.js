window.showClientSection = function showClientSection(sectionId) {
    document.querySelectorAll('.client-section').forEach((section) => {
        section.style.display = 'none';
    });

    const section = document.getElementById(sectionId);
    if (section) {
        section.style.display = 'block';
    }

    const sidebarButtons = document.querySelectorAll('.col-lg-3 .btn');
    sidebarButtons.forEach((btn) => {
        btn.classList.remove('btn-primary');
        if (!Array.from(btn.classList).some((cls) => cls.startsWith('btn-outline'))) {
            btn.classList.add('btn-outline-primary');
        } else {
            btn.classList.forEach((cls) => {
                if (cls.startsWith('btn-outline')) {
                    btn.dataset.outlineClass = cls;
                }
            });
            btn.classList.add(btn.dataset.outlineClass || 'btn-outline-primary');
        }
    });

    const evt = window.event;
    const activeButton = evt ? evt.target.closest('button') : null;
    if (activeButton) {
        activeButton.classList.remove('btn-outline-primary');
        activeButton.classList.add('btn-primary');
    }
};

document.addEventListener('DOMContentLoaded', () => {
    const infoPersonal = document.getElementById('info-personal');
    if (infoPersonal) {
        infoPersonal.style.display = 'block';
    }
});
