const rowsPerPage = 10;
let currentPage = 1;

document.addEventListener('DOMContentLoaded', function () {
    dibujarTable();
    renderPagination();

    var serviceModal = document.getElementById('servicioModal');
    serviceModal.addEventListener('show.bs.modal', function (event) {
        setFieldError('name_servicio', nombre, '');
        setFieldError('descripcion', descripcion, '');
        setFieldError('precio', precio, '');
        setFieldError('tipo', tipo, '');

        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');
        var nombre = button.getAttribute('data-nombre');
        var descripcion = button.getAttribute('data-descripcion');
        var precio = button.getAttribute('data-precio');
        var tipo = button.getAttribute('data-tipo');

        var modalTitle = serviceModal.querySelector('.modal-title');
        var formAction = serviceModal.querySelector('#servicioModalForm').action;

        modalTitle.textContent = id ? 'Editar Servicio' : 'Crear Nuevo Servicio';
        serviceModal.querySelector('#servicioId').value = id ? id : '';
        serviceModal.querySelector('#name_servicio').value = nombre ? nombre : '';
        serviceModal.querySelector('#descripcion').value = descripcion ? descripcion : '';
        serviceModal.querySelector('#precio').value = precio ? precio : '';
        serviceModal.querySelector('#tipo').value = tipo ? tipo : 1;

        serviceModal.querySelector('#servicioModalForm').action = id ? editServiceRoute : insertServiceRoute;

        var confirmButtonService = document.getElementById('btnModalServicio');
        confirmButtonService.onclick = function () {
            saveService();
        };
    });

    var confirmStatusChangeModal = document.getElementById('confirmStatusChangeModal');
    confirmStatusChangeModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var serviceId = button.getAttribute('data-id');
        var serviceStatus = button.getAttribute('data-status');

        var modalTitle = confirmStatusChangeModal.querySelector('.modal-title');
        var modalBody = confirmStatusChangeModal.querySelector('.modal-body');
        modalTitle.textContent = serviceStatus == '1' ? 'DAR DE BAJA' : 'DAR DE ALTA';
        modalBody.textContent = serviceStatus == '1' 
            ? '¿Seguro que deseas desactivar este servicio?' 
            : '¿Seguro que deseas activar este servicio?';

        var confirmButton = document.getElementById('confirmChangeStatusButton');
        confirmButton.onclick = function () {
            changeServiceStatus(serviceId, serviceStatus == '1' ? 0 : 1);
        };
    });
});

function dibujarTable() {
    const start = (currentPage - 1) * rowsPerPage;
    const end = start + rowsPerPage;
    const paginatedData = serviciosData.slice(start, end);

    const tableBody = document.getElementById('serviciosTableBody');
    tableBody.innerHTML = '';

    paginatedData.forEach(service => {
        const row = `
            <tr>
                <td>${service.nombre}</td>
                <td>${service.descripcion}</td>
                <td>$${service.precio}</td>
                <td>${service.tipo}</td>
                <td>
                    ${service.status == 1 
                        ? `<button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#confirmStatusChangeModal" data-id="${service.id}" data-status="${service.status}">ACTIVO</button>` 
                        : `<button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmStatusChangeModal" data-id="${service.id}" data-status="${service.status}">INACTIVO</button>`
                    }
                </td>
                <td>
                    <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#servicioModal"
                        data-id="${service.id}"
                        data-nombre="${service.nombre}"
                        data-descripcion="${service.descripcion}"
                        data-precio="${service.precio}"
                        data-tipo="${service.tipoID}"
                        >
                        <i class="fas fa-edit"></i>
                    </button>
                </td>
            </tr>
        `;
        tableBody.innerHTML += row;
    });
}

function renderPagination() {
    const pageCount = Math.ceil(serviciosData.length / rowsPerPage);
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
    dibujarTable();
    renderPagination();
}

function changeServiceStatus(serviceId, newStatus) {
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
            id: serviceId,
            status: newStatus
        },
        success: function(response) {
            swal.close();

            if (response['msg']) {
                if(newStatus == 0){
                    swal({
                        title: "¡Éxito!",
                        text: "El servicio ha sido desactivado correctamente.",
                        icon: "success",
                    }).then((value) => {
                        location.reload();
                    });
                } else{
                    swal({
                        title: "¡Éxito!",
                        text: "El servicio ha sido activado correctamente.",
                        icon: "success",
                    }).then((value) => {
                        location.reload();
                    });
                }
            } else {
                swal({
                    title: "Error",
                    text: "Ocurrió un error al cambiar el estado del servicio.",
                    icon: "error",
                });
            }
        },
        error: function(xhr, status, error) {
            console.log(error);
            swal.close();
            swal({
                title: "Error",
                text: "Ocurrió un error al cambiar el estado del servicio.",
                icon: "error",
            });
        }
    });
}


function validateForm() {
    var isValid = true;

    var nombre = document.getElementById('name_servicio').value.trim();
    var descripcion = document.getElementById('descripcion').value.trim();
    var precio = document.getElementById('precio').value.trim();
    var tipo = document.getElementById('tipo').value;

    setFieldError('name_servicio', nombre, 'Por favor complete este campo');
    setFieldError('descripcion', descripcion, 'Por favor complete este campo');
    setFieldError('precio', precio, 'Por favor complete este campo');
    setFieldError('tipo', tipo, 'Por favor seleccione un tipo de servicio');

    isValid = nombre && descripcion && precio && tipo;

    return isValid;
}

function setFieldError(fieldId, value, errorMessage) {
    const errorElementId = fieldId + '-error';
    document.getElementById(errorElementId).innerText = value ? '' : errorMessage;
}

function saveService() {
    if (!validateForm()) {
        return;
    }

    swal({
        text: "Cargando...",
        button: false,
        closeOnClickOutside: false,
        closeOnEsc: false,
    });

    $('#saveButton').prop('disabled', true);

    jQuery.ajax({
        type: 'POST',
        url: $('#servicioModalForm').attr('action'),
        data: jQuery('#servicioModalForm').serialize(),
        success: function(response) {
            swal.close();

            if (response['msg']) {
                swal({
                    title: "¡Éxito!",
                    text: response['msg'],
                    icon: "success",
                }).then((value) => {
                    location.reload();
                });
            } else if (response['edit']) {
                swal({
                    title: "¡Éxito!",
                    text: response['edit'],
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

