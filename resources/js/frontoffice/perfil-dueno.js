/**
 * Store owner dashboard module
 * Handles section navigation and button state management in store owner panel
 */

/**
 * Section-specific button color mapping
 */
const SECTION_BUTTON_COLORS = {
    'solicitudes': 'btn-warning',
    'reportes': 'btn-success',
    'editar-perfil': 'btn-secondary',
};

/**
 * Show specific store owner dashboard section and update sidebar button states
 * @param {string} sectionId - The ID of the section to display
 */
export function showSection(sectionId) {
    // Hide all content sections
    document.querySelectorAll('.content-section').forEach((section) => {
        section.style.display = 'none';
    });

    // Display target section
    const section = document.getElementById(sectionId);
    if (section) {
        section.style.display = 'block';
    }

    // Update URL to persist active section
    try {
        const url = new URL(window.location.href);
        if (sectionId) {
            url.searchParams.set('section', sectionId);
        } else {
            url.searchParams.delete('section');
        }
        window.history.replaceState({}, '', url.toString());
    } catch (error) {
        // Ignore URL errors (older browsers)
    }

    // Reset all sidebar buttons to their original outline state
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

    // Highlight active button with section-specific color
    const evt = window.event;
    let activeButton = evt ? evt.target.closest('button') : null;
    if (!activeButton && sectionId) {
        activeButton = document.querySelector(`.col-lg-3 .btn[data-section="${sectionId}"]`);
    }
    if (activeButton) {
        activeButton.classList.remove('btn-outline-primary', 'btn-outline-warning', 'btn-outline-success', 'btn-outline-secondary');
        
        const buttonColor = SECTION_BUTTON_COLORS[sectionId] || 'btn-primary';
        activeButton.classList.add(buttonColor);
    }
}

/**
 * Initialize store owner dashboard: display default sections and set initial button state
 */
function initStoreOwnerDashboard() {
    const dashboard = document.getElementById('dashboard');
    const misPromociones = document.getElementById('mis-promociones');

    if (dashboard) dashboard.style.display = 'block';
    if (misPromociones) misPromociones.style.display = 'block';

    // Highlight the default active button (My Promotions)
    const defaultButton = document.querySelector('.col-lg-3 .btn[data-section="mis-promociones"]');
    if (defaultButton) {
        defaultButton.classList.remove('btn-outline-primary');
        defaultButton.classList.add('btn-primary');
    }
}

// Auto-initialize on DOM ready
document.addEventListener('DOMContentLoaded', initStoreOwnerDashboard);

// Export to window for inline onclick handlers (backward compatibility)
window.showSection = showSection;
