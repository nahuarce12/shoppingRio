/**
 * Client dashboard module
 * Handles section navigation and button state management in client panel
 */

/**
 * Show specific client dashboard section and update sidebar button states
 * @param {string} sectionId - The ID of the section to display
 */
export function showClientSection(sectionId) {
    // Hide all client sections
    document.querySelectorAll('.client-section').forEach((section) => {
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

    // Highlight active button
    const evt = window.event;
    const activeButton = evt ? evt.target.closest('button') : null;
    if (activeButton) {
        activeButton.classList.remove('btn-outline-primary');
        activeButton.classList.add('btn-primary');
    }
}

/**
 * Initialize client dashboard: display default section (personal info)
 */
function initClientDashboard() {
    const infoPersonal = document.getElementById('info-personal');
    if (infoPersonal) {
        infoPersonal.style.display = 'block';
    }
}

// Auto-initialize on DOM ready
document.addEventListener('DOMContentLoaded', initClientDashboard);

// Export to window for inline onclick handlers (backward compatibility)
window.showClientSection = showClientSection;
