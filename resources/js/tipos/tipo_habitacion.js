const rowsPerPage = 10;
let currentPage = 1;

document.addEventListener('DOMContentLoaded', function () {
    dibujarTable();
    renderPagination();

    var tipoModal = document.getElementById('tipoModal');
    tipoModal.addEventListener('show.bs.modal', function (event) {
        setFieldError('tipo_habitacion', tipo_habitacion, '');
        setFieldError('descripcion', descripcion, '');
        setFieldError('precio', precio, '');
        setFieldError('capacidad', capacidad, '');
        setFieldError('imgForm', imgForm, '');

        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');
        var tipo_habitacion = button.getAttribute('data-tipo_habitacion');
        var descripcion = button.getAttribute('data-descripcion');
        var precio = button.getAttribute('data-precio');
        var capacidad = button.getAttribute('data-capacidad');

        var modalTitle = tipoModal.querySelector('.modal-title');

        console.log(id);

        modalTitle.textContent = id ? 'Editar tipo de habitación' : 'Crear nuevo tipo de habitación';
        tipoModal.querySelector('#tipoId').value = id ? id : '';
        tipoModal.querySelector('#tipo_habitacion').value = tipo_habitacion ? tipo_habitacion : '';
        tipoModal.querySelector('#descripcion').value = descripcion ? descripcion : '';
        tipoModal.querySelector('#precio').value = precio ? precio : '';
        tipoModal.querySelector('#capacidad').value = capacidad ? capacidad : '';
        tipoModal.querySelector('#imgForm').value = '';
        tipoModal.querySelector('#tipoModalForm').action = id ? editTiposHabitacionesRoute : insertTiposHabitacionesRoute;


        var confirmButton = document.getElementById('btnModalTipo');
        confirmButton.onclick = function () {
            save(id);
        };
    });

    var confirmStatusChangeModal = document.getElementById('confirmStatusChangeModal');
    confirmStatusChangeModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');
        var status = button.getAttribute('data-status');

        var modalTitle = confirmStatusChangeModal.querySelector('.modal-title');
        var modalBody = confirmStatusChangeModal.querySelector('.modal-body');
        modalTitle.textContent = status == '1' ? 'DAR DE BAJA' : 'DAR DE ALTA';
        modalBody.textContent = status == '1' 
            ? '¿Está seguro que desea desactivar este tipo de habitación?' 
            : '¿Está seguro que desea activar este tipo de habitación?';

        var confirmButton = document.getElementById('confirmChangeStatusButton');
        confirmButton.onclick = function () {
            changeStatus(id, status == '1' ? 0 : 1);
        };
    });

    var imagenModal = document.getElementById('fotoModal');
    imagenModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');
        var tipo = button.getAttribute('data-tipo_habitacion');
        var imagenUrl = button.getAttribute('data-imagen');
        imagenModal.querySelector('#imagenID').value = id;

        var modalTitle = imagenModal.querySelector('.modal-title');
        modalTitle.textContent = 'IMAGEN DEL TIPO: ' + tipo;

        var contenedorImg = document.getElementById('contenedorImg');
        var img = document.getElementById('img');
        var imgElement = imagenModal.querySelector('#img');


        if(imagenUrl == 'null'  ){
            img.style.display = 'none';
            contenedorImg.style.display = 'none';
        } else{
            // Verifica si imagenUrl no está vacío o es null
            contenedorImg.style.display = 'block';
            img.style.display = 'block';
            imgElement.src = "http://127.0.0.1:8000/" + imagenUrl;
        }

        var confirmButton = document.getElementById('confirmFotoModal');
        confirmButton.onclick = function () {
            subirFoto();
        };
    });


    
    const precioInput = document.getElementById('precio');
    precioInput.addEventListener('keydown', function(e) {
        if (e.key === '-') {
            e.preventDefault();
        }
    });

    const capacidadInput = document.getElementById('capacidad');
    capacidadInput.addEventListener('keydown', function(e) {
        if (e.key === '0' && this.value.length === 0) {
            e.preventDefault();
        }

        if (e.key === '-') {
            e.preventDefault();
        }
    });
});

function dibujarTable() {
    const start = (currentPage - 1) * rowsPerPage;
    const end = start + rowsPerPage;
    const paginatedData = tiposData.slice(start, end);

    const tableBody = document.getElementById('tiposHabitacionesTableBody');
    tableBody.innerHTML = '';

    paginatedData.forEach(tipo => {
        const row = `
            <tr>
                <td>${tipo.tipo}</td>
                <td>${tipo.descripcion}</td>
                <td>$${tipo.precio_noche}</td>
                <td>${tipo.capacidad}</td>
                <td>
                    ${tipo.status == 1 
                        ? `<button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#confirmStatusChangeModal" data-id="${tipo.id}" data-status="${tipo.status}">ACTIVO</button>` 
                        : `<button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmStatusChangeModal" data-id="${tipo.id}" data-status="${tipo.status}">INACTIVO</button>`
                    }
                </td>
                <td>
                    <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#tipoModal"
                        data-id="${tipo.id}"
                        data-tipo_habitacion="${tipo.tipo}"
                        data-descripcion="${tipo.descripcion}"
                        data-precio="${tipo.precio_noche}"
                        data-capacidad="${tipo.capacidad}"
                        >
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#fotoModal"
                        data-id="${tipo.id}"
                        data-tipo_habitacion="${tipo.tipo}"
                        data-imagen="${tipo.imagen}"
                        >
                        <i class="fas fa-image"></i>
                    </button>
                </td>
            </tr>
        `;
        tableBody.innerHTML += row;
    });
}

function validateImagen(){
    var validacion = true;
    var imagen = document.getElementById('imagen').value.trim();
    
    validacion = imagen;
    return validacion;
}

function subirFoto(){
    if (!validateImagen()) {
        return;
    }

    var form = document.getElementById('subirImgForm');
    var formData = new FormData(form);

    swal({
        text: "Cargando...",
        button: false,
        closeOnClickOutside: false,
        closeOnEsc: false,
    });

    jQuery.ajax({
        type: 'POST',
        url: $('#subirImgForm').attr('action'),
        data: formData,
        success: function(response) {
            swal.close();

            /*if (response['msg']) {
                swal({
                    title: "¡Éxito!",
                    text: "La imagen ha sido subida correctamente.",
                    icon: "success",
                }).then(() => {
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
            }*/
        },
        error: function(xhr, status, error) {
            swal.close();
            swal({
                title: "Error!",
                text: "Error al procesar la solicitud.",
                icon: "error",
            });
        },
    });
}


function renderPagination() {
    const pageCount = Math.ceil(tiposData.length / rowsPerPage);
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

function changeStatus(serviceId, newStatus) {
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
            //console.log(error);
            swal.close();
            swal({
                title: "Error",
                text: "Ocurrió un error al cambiar el estado del servicio.",
                icon: "error",
            });
        }
    });
}

function validateForm(id) {
    var isValid = true;

    var tipo_habitacion = document.getElementById('tipo_habitacion').value.trim();
    var descripcion = document.getElementById('descripcion').value.trim();
    var precio = document.getElementById('precio').value.trim();
    var capacidad = document.getElementById('capacidad').value.trim();
    var imgForm = document.getElementById('imgForm').value.trim();

    setFieldError('tipo_habitacion', tipo_habitacion, 'Por favor complete este campo');
    setFieldError('descripcion', descripcion, 'Por favor complete este campo');
    setFieldError('precio', precio, 'Por favor complete este campo');
    setFieldError('capacidad', capacidad, 'Por favor complete este campo');

    
    if(id == '' || id== null){
        if(imgForm == ''){
            document.getElementById('imgForm-error').innerText = 'Por favor seleccione una imagen';
        } else{
            document.getElementById('imgForm-error').innerText = '';
        }
    
        isValid = tipo_habitacion && descripcion && precio && capacidad && imgForm;
    } else{
        isValid = tipo_habitacion && descripcion && precio && capacidad;
    }

    return isValid;
}

function setFieldError(fieldId, value, errorMessage) {
    const errorElementId = fieldId + '-error';
    document.getElementById(errorElementId).innerText = value ? '' : errorMessage;
}

function save(id) {
    if (!validateForm(id)) {
        return;
    }

    console.log(id);

    swal({
        text: "Cargando...",
        button: false,
        closeOnClickOutside: false,
        closeOnEsc: false,
    });

    $('#saveButton').prop('disabled', true);

    var form = document.getElementById('tipoModalForm');
    var formData = new FormData(form);


    for (var pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }

    jQuery.ajax({
        type: 'POST',
        url: $('#tipoModalForm').attr('action'),
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            swal.close();

            if (response['msg']) {
                swal({
                    title: "¡Éxito!",
                    text: response.msg,
                    icon: "success",
                }).then((value) => {
                    location.reload();
                });
            } else if (response['edit']) {
                swal({
                    title: "¡Éxito!",
                    text: response.edit,
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

