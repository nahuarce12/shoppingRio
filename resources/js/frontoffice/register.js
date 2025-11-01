/**
 * Registration wizard module
 * Handles multi-step registration flow for client and store owner registration
 */

/**
 * Show client registration form
 */
export function showClientForm() {
    const step1 = document.getElementById('step1');
    const clientForm = document.getElementById('clienteForm');
    const ownerForm = document.getElementById('duenoForm');

    if (step1) step1.style.display = 'none';
    if (clientForm) clientForm.style.display = 'block';
    if (ownerForm) ownerForm.style.display = 'none';
}

/**
 * Show store owner registration form
 */
export function showOwnerForm() {
    const step1 = document.getElementById('step1');
    const clientForm = document.getElementById('clienteForm');
    const ownerForm = document.getElementById('duenoForm');

    if (step1) step1.style.display = 'none';
    if (clientForm) clientForm.style.display = 'none';
    if (ownerForm) ownerForm.style.display = 'block';
}

/**
 * Return to step 1 (user type selection)
 */
export function showStep1() {
    const step1 = document.getElementById('step1');
    const clientForm = document.getElementById('clienteForm');
    const ownerForm = document.getElementById('duenoForm');

    if (step1) step1.style.display = 'block';
    if (clientForm) clientForm.style.display = 'none';
    if (ownerForm) ownerForm.style.display = 'none';
}

// Export to window for inline onclick handlers (backward compatibility)
window.showClientForm = showClientForm;
window.showOwnerForm = showOwnerForm;
window.showStep1 = showStep1;
