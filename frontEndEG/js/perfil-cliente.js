// Perfil Cliente page section navigation
function showClientSection(sectionId) {
    // Ocultar todas las secciones
    document.querySelectorAll('.client-section').forEach(section => {
        section.style.display = 'none';
    });
    
    // Mostrar la sección seleccionada
    document.getElementById(sectionId).style.display = 'block';
    
    // Actualizar estilos de botones
    document.querySelectorAll('.col-lg-3 button').forEach(btn => {
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-outline-primary');
    });
    
    // Marcar el botón activo
    event.target.closest('button').classList.remove('btn-outline-primary');
    event.target.closest('button').classList.add('btn-primary');
}

// Mostrar información personal por defecto al cargar
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('info-personal').style.display = 'block';
});
