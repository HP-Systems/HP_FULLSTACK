const rowsPerPage = 10;
let currentPage = 1;

document.addEventListener('DOMContentLoaded', function () {
    renderTable();
    renderPagination();

    var addUserModal = document.getElementById('addUserModal');
    addUserModal.addEventListener('show.bs.modal', function (event) {
        setFieldError('nombre', nombre, '');
        setFieldError('apellido', apellido, '');
        setFieldError('telefono', telefono, '');
        setFieldError('email', email, '');
        setFieldError('rol', rol, '');

        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');
        var nombre = button.getAttribute('data-nombre');
        var apellido = button.getAttribute('data-apellido');
        var email = button.getAttribute('data-email');
        var telefono = button.getAttribute('data-telefono');
        var rol = button.getAttribute('data-rol');

        var modalTitle = addUserModal.querySelector('.modal-title');
        modalTitle.textContent = id ? 'Editar Personal' : 'Crear Nuevo Personal';

        addUserModal.querySelector('#personalId').value = id ? id : '';
        addUserModal.querySelector('#nombre').value = nombre ? nombre : '';
        addUserModal.querySelector('#apellido').value = apellido ? apellido : '';
        addUserModal.querySelector('#email').value = email ? email : '';
        addUserModal.querySelector('#telefono').value = telefono ? telefono : '';
        addUserModal.querySelector('#rol').value = rol ? rol : 1;
        addUserModal.querySelector('#addUserForm').action = id ? editPersonalRoute : insertPersonalRoute;

        var confirmButtonPersonal = document.getElementById('btnModalPersonal');
        confirmButtonPersonal.onclick = function () {
            saveUser();
        };
    });

    var confirmStatusChangeModal = document.getElementById('confirmStatusChangeModal');
    confirmStatusChangeModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var userId = button.getAttribute('data-id');
        var userStatus = button.getAttribute('data-status');

        var modalTitle = confirmStatusChangeModal.querySelector('.modal-title');
        var modalBody = confirmStatusChangeModal.querySelector('.modal-body');
        modalTitle.textContent = userStatus == '1' ? 'DAR DE BAJA' : 'DAR DE ALTA';
        modalBody.textContent = userStatus == '1' 
            ? '¿Seguro que deseas desactivar a este miembro del personal? Una vez desactivado, no podrá acceder al sistema.' 
            : '¿Deseas dar de alta a este miembro del personal? Esto lo marcará como activo y podrá usar el sistema.';

        var confirmButton = document.getElementById('confirmChangeStatusButton');
        confirmButton.onclick = function () {
            changeUserStatus(userId, userStatus == '1' ? 0 : 1);
        };
    });
});

function renderTable() {
    const start = (currentPage - 1) * rowsPerPage;
    const end = start + rowsPerPage;
    const paginatedData = personalData.slice(start, end);

    const tableBody = document.getElementById('personalTableBody');
    tableBody.innerHTML = '';

    paginatedData.forEach(pers => {
        const row = `
            <tr>
                <td>${pers.nombre_completo}</td>
                <td>${pers.email}</td>
                <td>${pers.telefono}</td>
                <td>${pers.rol}</td>
                <td>
                    ${pers.status == 1 
                        ? `<button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#confirmStatusChangeModal" data-id="${pers.id}" data-status="${pers.status}" ${pers.id == currentUserId ? 'disabled' : ''}>ACTIVO</button>` 
                        : `<button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmStatusChangeModal" data-id="${pers.id}" data-status="${pers.status}" ${pers.id == currentUserId ? 'disabled' : ''}>INACTIVO</button>`
                    }
                </td>
                <td>
                    <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#addUserModal"
                            data-id="${pers.id}"
                            data-nombre="${pers.nombre}"
                            data-apellido="${pers.apellido}"
                            data-email="${pers.email}"
                            data-telefono="${pers.telefono}"
                            data-rol="${pers.rolID}">
                        <i class="fas fa-edit"></i>
                    </button>
                </td>
            </tr>
        `;
        tableBody.innerHTML += row; 
    });
}

function renderPagination() {
    const pageCount = Math.ceil(personalData.length / rowsPerPage);
    const pagination = document.getElementById('pagination');
    pagination.innerHTML = '';

    const maxPagesToShow = 3;
    const half = Math.floor(maxPagesToShow / 2);
    let startPage = Math.max(1, currentPage - half);
    let endPage = Math.min(pageCount, currentPage + half);

    if (currentPage <= half) {
        endPage = Math.min(pageCount, maxPagesToShow);
    }

    if (currentPage + half > pageCount) {
        startPage = Math.max(1, pageCount - maxPagesToShow + 1);
    }

    if (currentPage > 1) {
        const prevPage = document.createElement('li');
        prevPage.classList.add('page-item');
        prevPage.innerHTML = `
            <a class="page-link" href="#">&laquo;</a>
        `;
        prevPage.querySelector('a').addEventListener('click', function () {
            goToPage(currentPage - 1);
        });
        pagination.appendChild(prevPage);
    }

    for (let i = startPage; i <= endPage; i++) {
        const pageItem = document.createElement('li');
        pageItem.classList.add('page-item');
        pageItem.innerHTML = `
            <a class="page-link" href="#" style="${i === currentPage ? 'background-color: #65999C !important; color: white !important;' : ''}">${i}</a>
        `;
        pageItem.querySelector('a').addEventListener('click', function () {
            goToPage(i);
        });
        pagination.appendChild(pageItem);
    }

    if (currentPage < pageCount) {
        const nextPage = document.createElement('li');
        nextPage.classList.add('page-item');
        nextPage.innerHTML = `
            <a class="page-link" href="#">&raquo;</a>
        `;
        nextPage.querySelector('a').addEventListener('click', function () {
            goToPage(currentPage + 1);
        });
        pagination.appendChild(nextPage);
    }
}

function goToPage(page) {
    currentPage = page;
    renderTable();
    renderPagination();
}

function changeUserStatus(userId, newStatus) {
    swal({
        text: "Cargando...",
        button: false,
        closeOnClickOutside: false,
        closeOnEsc: false,
    });

    jQuery.ajax({
        type: 'POST',
        url: changeStatusRoute,
        data: {
            _token: csrfToken,
            id: userId,
            status: newStatus
        },
        success: function(response) {
            swal.close();

            if (response['msg']) {
                if(newStatus == 0){
                    swal({
                        title: "¡Éxito!",
                        text: "El estado del usuario ha sido desactivado correctamente.",
                        icon: "success",
                    }).then((value) => {
                        location.reload();
                    });
                } else{
                    swal({
                        title: "¡Éxito!",
                        text: "El estado del usuario ha sido activado correctamente.",
                        icon: "success",
                    }).then((value) => {
                        location.reload();
                    });
                }
            } else {
                swal({
                    title: "Error",
                    text: "Ocurrió un error al cambiar el estado del usuario.",
                    icon: "error",
                });
            }
        },
        error: function(xhr, status, error) {
            swal.close();
            swal({
                title: "Error",
                text: "Ocurrió un error al cambiar el estado del usuario.",
                icon: "error",
            });
        }
    });
}

function setFieldError(fieldId, value, errorMessage) {
    const errorElementId = fieldId + '-error';
    document.getElementById(errorElementId).innerText = value ? '' : errorMessage;
}

function validateForm() {
    var isValid = true;

    var nombre = document.getElementById('nombre').value.trim();
    var apellido = document.getElementById('apellido').value.trim();
    var telefono = document.getElementById('telefono').value.trim();
    var email = document.getElementById('email').value.trim();
    var rol = document.getElementById('rol').value;

    setFieldError('nombre', nombre, 'Por favor complete este campo');
    setFieldError('apellido', apellido, 'Por favor complete este campo');
    setFieldError('telefono', telefono, 'Por favor complete este campo');
    setFieldError('email', email, 'Por favor complete este campo');
    setFieldError('rol', rol, 'Por favor seleccione un tipo de usuario');

    isValid = nombre && apellido && telefono && email && rol;

    return isValid;
}

function saveUser() {
    if (!validateForm()) {
        return;
    }

    // Mostrar pantalla de carga
    swal({
        text: "Cargando...",
        button: false,
        closeOnClickOutside: false,
        closeOnEsc: false,
    });

    // Deshabilitar botón de guardar
    $('#saveButton').prop('disabled', true);

    jQuery.ajax({
        type: 'POST',
        url: $('#addUserForm').attr('action'),
        data: jQuery('#addUserForm').serialize(),
        success: function(response) {
            // Ocultar pantalla de carga
            swal.close();

            if (response['msg']) {
                swal({
                    title: "¡Éxito!",
                    text: "El usuario ha sido creado correctamente.",
                    icon: "success",
                }).then((value) => {
                    location.reload();
                });
            } else if (response['edit']) {
                swal({
                    title: "¡Éxito!",
                    text: "El usuario ha sido editado correctamente.",
                    icon: "success",
                }).then((value) => {
                    location.reload();
                });
            } else if (response.errors) {
                swal({
                    title: "Error!",
                    text: response.errors.join("\n"),
                    icon: "error",
                });
            } else {
                swal({
                    title: "Error!",
                    text: "Error al procesar la solicitud.",
                    icon: "error",
                });
            }
        },
        error: function(xhr, status, error) {
            // Ocultar pantalla de carga y mostrar mensaje de error
            swal.close();
            swal({
                title: "Error!",
                text: "Error al procesar la solicitud.",
                icon: "error",
            });
        },
        complete: function() {
            // Habilitar botón de guardar
            $('#saveButton').prop('disabled', false);
        }
    });
}

