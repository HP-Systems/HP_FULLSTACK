
document.addEventListener('DOMContentLoaded', function () {
    $('.edit-btn').click(function() {
        var tipo = $(this).data('tipo'); 
        var id = $(this).data('id');
        
        $('#tipoTarjetaModalLabel').text('Editar tipo de tarjeta');

        $('#id').val(id); 
        $('#tipo_tarjeta').val(tipo); 
    });

    $('.btn-cancelar').click(function() {
        limpiarFormulario();
    });

    $('.btn-guardar').click(function() {
        save();
    });

    var confirmStatusChangeModal = document.getElementById('confirmStatusChangeModal');
    confirmStatusChangeModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');
        var tipoStatus = button.getAttribute('data-status');

        var modalTitle = confirmStatusChangeModal.querySelector('.modal-title');
        var modalBody = confirmStatusChangeModal.querySelector('.modal-body');
        modalTitle.textContent = tipoStatus == '1' ? 'DAR DE BAJA' : 'DAR DE ALTA';
        modalBody.textContent = tipoStatus == '1' 
            ? '¿Está seguro que desea desactivar este tipo de tarjeta? Esto podría afectar a las tarjetas que lo tengan asignado.' 
            : '¿Está seguro que desea activar este tipo de tarjeta?';

        var confirmButton = document.getElementById('confirmChangeStatusButton');
        confirmButton.onclick = function () {
            changeTipoStatus(id, tipoStatus == '1' ? 0 : 1);
        };
    });
});

function limpiarFormulario() {
    $('#id').val(''); 
    $('#tipo_tarjeta').val(''); 
    $('#tipoTarjetaModalLabel').text('Crear tipo de tarjeta');
    setFieldError('tipo_tarjeta', tipo_tarjeta, '');
}

function setFieldError(fieldId, value, errorMessage) {
    const errorElementId = fieldId + '-error';
    document.getElementById(errorElementId).innerText = value ? '' : errorMessage;
}

function validateForm() {
    var isValid = true;

    var tipo_tarjeta = document.getElementById('tipo_tarjeta').value.trim();
    setFieldError('tipo_tarjeta', tipo_tarjeta, 'Por favor complete este campo');
    isValid = tipo_tarjeta;

    return isValid;
}

function save(){
    if (!validateForm()) {
        return;
    }

    var id = $('#id').val();
    var formAction = id ? editTipoTarjetaRoute : insertTipoTarjetaRoute;

    swal({
        text: "Cargando...",
        button: false,
        closeOnClickOutside: false,
        closeOnEsc: false,
    });

    $.ajax({
        url: formAction,
        type: 'POST',
        data: jQuery('#tipoTarjetaForm').serialize(),
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
        }
    });
}

function changeTipoStatus(id, newStatus) {
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
            id: id,
            status: newStatus
        },
        success: function(response) {
            swal.close();

            if (response['msg']) {
                if(newStatus == 0){
                    swal({
                        title: "¡Éxito!",
                        text: "El tipo de tarjeta ha sido desactivado correctamente.",
                        icon: "success",
                    }).then((value) => {
                        location.reload();
                    });
                } else{
                    swal({
                        title: "¡Éxito!",
                        text: "El tipo de tarjeta ha sido activado correctamente.",
                        icon: "success",
                    }).then((value) => {
                        location.reload();
                    });
                }
            } else {
                swal({
                    title: "Error",
                    text: "Ocurrió un error al cambiar el estado del tipo de tarjeta.",
                    icon: "error",
                });
            }
        },
        error: function(xhr, status, error) {
            swal.close();
            swal({
                title: "Error",
                text: "Error de servidor.",
                icon: "error",
            });
        }
    });
}