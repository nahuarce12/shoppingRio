// Register page form navigation
function showClientForm() {
    document.getElementById('step1').style.display = 'none';
    document.getElementById('clienteForm').style.display = 'block';
    document.getElementById('duenoForm').style.display = 'none';
}

function showOwnerForm() {
    document.getElementById('step1').style.display = 'none';
    document.getElementById('clienteForm').style.display = 'none';
    document.getElementById('duenoForm').style.display = 'block';
}

function showStep1() {
    document.getElementById('step1').style.display = 'block';
    document.getElementById('clienteForm').style.display = 'none';
    document.getElementById('duenoForm').style.display = 'none';
}
