// Perfil Admin page section navigation
function showAdminSection(sectionId) {
    // Ocultar todas las pestañas (no dashboard)
    document.querySelectorAll('.content-section').forEach(section => {
        section.style.display = 'none';
    });
    
    // Mostrar la sección seleccionada
    document.getElementById(sectionId).style.display = 'block';
    
    // Actualizar estilos de botones
    document.querySelectorAll('.col-lg-3 button').forEach(btn => {
        btn.classList.remove('btn-primary', 'btn-warning', 'btn-success', 'btn-info');
        
        // Restaurar clase outline original
        if (btn.textContent.includes('Dashboard')) {
            btn.classList.add('btn-outline-primary');
        } else if (btn.textContent.includes('Locales')) {
            btn.classList.add('btn-outline-primary');
        } else if (btn.textContent.includes('Validar')) {
            btn.classList.add('btn-outline-primary');
        } else if (btn.textContent.includes('Aprobar')) {
            btn.classList.add('btn-outline-warning');
        } else if (btn.textContent.includes('Novedades')) {
            btn.classList.add('btn-outline-success');
        } else if (btn.textContent.includes('Reportes')) {
            btn.classList.add('btn-outline-info');
        } else if (btn.textContent.includes('Configuración')) {
            btn.classList.add('btn-outline-secondary');
        }
    });
    
    // Marcar el botón activo
    const activeButton = event.target.closest('button');
    activeButton.classList.remove('btn-outline-primary', 'btn-outline-warning', 'btn-outline-success', 'btn-outline-info', 'btn-outline-secondary');
    
    if (activeButton.textContent.includes('Dashboard')) {
        activeButton.classList.add('btn-primary');
    } else if (activeButton.textContent.includes('Locales')) {
        activeButton.classList.add('btn-primary');
    } else if (activeButton.textContent.includes('Validar')) {
        activeButton.classList.add('btn-primary');
    } else if (activeButton.textContent.includes('Aprobar')) {
        activeButton.classList.add('btn-warning');
    } else if (activeButton.textContent.includes('Novedades')) {
        activeButton.classList.add('btn-success');
    } else if (activeButton.textContent.includes('Reportes')) {
        activeButton.classList.add('btn-info');
    } else if (activeButton.textContent.includes('Configuración')) {
        activeButton.classList.add('btn-secondary');
    }
}

// Mostrar dashboard y la primera pestaña (Locales) por defecto al cargar
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('dashboard').style.display = 'block';
    document.getElementById('locales').style.display = 'block';
});
