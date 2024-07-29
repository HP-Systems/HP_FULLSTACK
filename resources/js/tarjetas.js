document.addEventListener('DOMContentLoaded', function () {
    $('#filtroForm').on('submit', function(e) {
        e.preventDefault(); // Evita el envío tradicional del formulario

        swal({
            text: "Cargando...",
            button: false,
            closeOnClickOutside: false,
            closeOnEsc: false,
        });

        $.ajax({
            url: $(this).attr('action'),
            method: 'GET',
            data: $(this).serialize(),
            success: function(response) {
                swal.close();
                $('#tarjetasContainer').html(response);
            },
            error: function() {
                swal.close();
                swal({
                    title: "Error",
                    text: "No se puedieorn cargar los datos",
                    icon: "error",
                });
            }
        });
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
            ? '¿Está seguro que desea desactivar esta tarjeta? Una vez desactivada no podrá ser utilizada.' 
            : '¿Está seguro que desea activar esta tarjeta?';

        var confirmButton = document.getElementById('confirmChangeStatusButton');
        confirmButton.onclick = function () {
            changeStatus(id, tipoStatus == '1' ? 0 : 1);
        };
    });
});


function changeStatus(id, newStatus) {
    console.log(id);
    console.log(newStatus);
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
                        text: "La tarjeta ha sido desactivada correctamente.",
                        icon: "success",
                    }).then((value) => {
                        location.reload();
                    });
                } else{
                    swal({
                        title: "¡Éxito!",
                        text: "La tarjeta ha sido activada correctamente.",
                        icon: "success",
                    }).then((value) => {
                        location.reload();
                    });
                }
            } else {
                swal({
                    title: "Error",
                    text: "Ocurrió un error al cambiar el estado de la tarjeta.",
                    icon: "error",
                });
            }
        },
        error: function(xhr, status, error) {
            console.log(error);
            swal.close();
            swal({
                title: "Error",
                text: "Ocurrió un error al cambiar el estado de la tarjeta.",
                icon: "error",
            });
        }
    });
}
