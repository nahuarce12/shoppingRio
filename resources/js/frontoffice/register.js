window.showClientForm = function showClientForm() {
    const step1 = document.getElementById('step1');
    const clientForm = document.getElementById('clienteForm');
    const ownerForm = document.getElementById('duenoForm');

    if (step1) step1.style.display = 'none';
    if (clientForm) clientForm.style.display = 'block';
    if (ownerForm) ownerForm.style.display = 'none';
};

window.showOwnerForm = function showOwnerForm() {
    const step1 = document.getElementById('step1');
    const clientForm = document.getElementById('clienteForm');
    const ownerForm = document.getElementById('duenoForm');

    if (step1) step1.style.display = 'none';
    if (clientForm) clientForm.style.display = 'none';
    if (ownerForm) ownerForm.style.display = 'block';
};

window.showStep1 = function showStep1() {
    const step1 = document.getElementById('step1');
    const clientForm = document.getElementById('clienteForm');
    const ownerForm = document.getElementById('duenoForm');

    if (step1) step1.style.display = 'block';
    if (clientForm) clientForm.style.display = 'none';
    if (ownerForm) ownerForm.style.display = 'none';
};
