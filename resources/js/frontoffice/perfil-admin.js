/**
 * Administrator dashboard module
 * Handles section navigation and button state management in admin panel
 */

/**
 * Section-specific button color mapping
 */
const SECTION_BUTTON_COLORS = {
    'aprobar-promociones': 'btn-warning',
    'novedades': 'btn-success',
    'reportes': 'btn-info',
    'configuracion': 'btn-secondary',
};

/**
 * Show specific admin dashboard section and update sidebar button states
 * @param {string} sectionId - The ID of the section to display
 */
export function showAdminSection(sectionId) {
    // Hide all content sections
    document.querySelectorAll('.content-section').forEach((section) => {
        section.style.display = 'none';
    });

    // Display target section
    const section = document.getElementById(sectionId);
    if (section) {
        section.style.display = 'block';
    }

    // Reset all sidebar buttons to their original outline state
    const sidebarButtons = document.querySelectorAll('.col-lg-3 .btn');
    sidebarButtons.forEach((btn) => {
        btn.classList.remove('btn-primary', 'btn-warning', 'btn-success', 'btn-info', 'btn-secondary');
        if (btn.dataset.originalClass) {
            btn.classList.add(btn.dataset.originalClass);
        } else {
            btn.classList.add('btn-outline-primary');
        }
    });

    // Highlight active button with section-specific color
    const evt = window.event;
    const activeButton = evt ? evt.target.closest('button') : null;
    if (activeButton) {
        activeButton.classList.remove('btn-outline-primary', 'btn-outline-warning', 'btn-outline-success', 'btn-outline-info', 'btn-outline-secondary');
        
        const buttonColor = SECTION_BUTTON_COLORS[sectionId] || 'btn-primary';
        activeButton.classList.add(buttonColor);
    }
}

/**
 * Initialize admin dashboard: store original button classes and display default sections
 */
function initAdminDashboard() {
    // Cache original button outline classes
    const sidebarButtons = document.querySelectorAll('.col-lg-3 .btn');
    sidebarButtons.forEach((btn) => {
        if (!btn.dataset.originalClass) {
            const outlineClass = Array.from(btn.classList).find((cls) => cls.startsWith('btn-outline-'));
            if (outlineClass) {
                btn.dataset.originalClass = outlineClass;
            }
        }
    });

    // Display default sections on load
    const dashboard = document.getElementById('dashboard');
    const locales = document.getElementById('locales');
    if (dashboard) dashboard.style.display = 'block';
    if (locales) locales.style.display = 'block';
}

// Auto-initialize on DOM ready
document.addEventListener('DOMContentLoaded', initAdminDashboard);

// Export to window for inline onclick handlers (backward compatibility)
window.showAdminSection = showAdminSection;
