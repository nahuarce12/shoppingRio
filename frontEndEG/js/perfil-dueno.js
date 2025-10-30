// Perfil Dueño page section navigation
function showSection(sectionId) {
    document.querySelectorAll('.content-section').forEach(section => {
        section.style.display = 'none';
    });
    document.getElementById(sectionId).style.display = 'block';
    document.querySelectorAll('.col-lg-3 button').forEach(btn => {
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-outline-primary');
    });
    event.target.closest('button').classList.remove('btn-outline-primary', 'btn-outline-warning', 'btn-outline-success', 'btn-outline-secondary');
    event.target.closest('button').classList.add('btn-primary');
}

// Mostrar dashboard y primera pestaña por defecto al cargar
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('dashboard').style.display = 'block';
    document.getElementById('mis-promociones').style.display = 'block';
    document.querySelectorAll('.col-lg-3 button').forEach(btn => {
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-outline-primary');
    });
    document.querySelector('button[onclick*="mis-promociones"]').classList.remove('btn-outline-primary');
    document.querySelector('button[onclick*="mis-promociones"]').classList.add('btn-primary');
});
