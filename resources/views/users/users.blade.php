@extends('../home')

@section('title', 'Usuarios - Hotel Project')

@section('content')
    <div class="height-100 p-4" style="background-color: #EEEEEE !important; margin-top: 5%!important;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h4>Gestión de Personal</h4>
            <button type="button" class="btn btn-negro" data-bs-toggle="modal" data-bs-target="#addUserModal">Agregar
                Personal</button>
        </div>
        <div
            style="background-color: white; margin-top: 15px !important; margin-right: 20px !important; padding: 20px !important">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">Nombre</th>
                        <th scope="col">Correo</th>
                        <th scope="col">Telefono</th>
                        <th scope="col">Tipo de usuario</th>
                        <th scope="col">Status</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($personal as $pers)
                        <tr>
                            <td>{{ $pers->nombre_completo }}</td>
                            <td>{{ $pers->email }}</td>
                            <td>{{ $pers->telefono }}</td>
                            <td>{{ $pers->rol }}</td>
                            <td>
                                @if ($pers->status == 1)
                                    <button disabled type="button" class="btn btn-success">ACTIVO</button>
                                @else
                                    <button disabled type="button" class="btn btn-danger">INACTIVO</button>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#addUserModal"
                                        data-id="{{ $pers->id }}"
                                        data-nombre="{{ $pers->nombre }}"
                                        data-apellido="{{ $pers->apellido }}"
                                        data-email="{{ $pers->email }}"
                                        data-telefono="{{ $pers->telefono }}"
                                        data-rol="{{ $pers->rolID }}">
                                    <i class="fas fa-edit"></i>
                                </button>

                                @if ($pers->id != $currentUserId) <!-- Condición para ocultar el botón del usuario logueado -->
                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#confirmStatusChangeModal"
                                            data-id="{{ $pers->id }}" data-status="{{ $pers->status }}">
                                        <i class="fas fa-sync"></i>
                                    </button>
                                @endif
                            </td>

                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="padding-top: 10px !important; padding-left: 20px !important; padding-right: 20px !important;">
                <div class="modal-header">
                    <h4 class="modal-title" id="addUserModalLabel">Crear Nuevo Personal</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addUserForm" action="{{ route('personal.crear') }}" method="POST">
                        @csrf
                        <input type="hidden" id="personalId" name="id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label" style="font-weight: 500">Nombre</label>
                                    <input type="text" class="form-control border-thick" id="nombre" name="nombre" required>
                                    <div id="nombre-error" class="text-danger"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="apellido" class="form-label" style="font-weight: 500">Apellido(s)</label>
                                    <input type="text" class="form-control border-thick" id="apellido" name="apellido" required>
                                    <div id="apellido-error" class="text-danger"></div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="telefono" class="form-label" style="font-weight: 500">Teléfono</label>
                            <input type="tel" class="form-control border-thick" id="telefono" name="telefono" required>
                            <div id="telefono-error" class="text-danger"></div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label" style="font-weight: 500">Correo</label>
                            <input type="email" class="form-control border-thick" id="email" name="email" required>
                            <div id="email-error" class="text-danger"></div>
                        </div>
                        <div class="mb-3">
                            <label for="rol" class="form-label " style="font-weight: 500">Tipo de Usuario</label>
                            <select class="form-select border-thick" id="rol" name="rol" required>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->nombre }}</option>
                                @endforeach
                            </select>
                            <div id="rol-error" class="text-danger"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-custom" onclick="saveUser()">GUARDAR</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de cambio de status -->
    <div class="modal fade" id="confirmStatusChangeModal" tabindex="-1" aria-labelledby="confirmStatusChangeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmStatusChangeModalLabel">CAMBIO DE ESTADO</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="font-size: 17px">
                    ¿Estás seguro de que quieres cambiar el estado de este personal?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmChangeStatusButton">ACEPTAR</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var addUserModal = document.getElementById('addUserModal');
            addUserModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var id = button.getAttribute('data-id');
                var nombre = button.getAttribute('data-nombre');
                var apellido = button.getAttribute('data-apellido');
                var email = button.getAttribute('data-email');
                var telefono = button.getAttribute('data-telefono');
                var rol = button.getAttribute('data-rol');
    
                var modalTitle = addUserModal.querySelector('.modal-title');
                var formAction = addUserModal.querySelector('#addUserForm').action;
    
                modalTitle.textContent = id ? 'Editar Personal' : 'Crear Nuevo Personal';
                addUserModal.querySelector('#personalId').value = id ? id : '';
                addUserModal.querySelector('#nombre').value = nombre ? nombre : '';
                addUserModal.querySelector('#apellido').value = apellido ? apellido : '';
                addUserModal.querySelector('#email').value = email ? email : '';
                addUserModal.querySelector('#telefono').value = telefono ? telefono : '';
                addUserModal.querySelector('#rol').value = rol ? rol : 1;
    
                addUserModal.querySelector('#addUserForm').action = id ? '{{ route('editPersonal') }}' : '{{ route('insertPersonal') }}';
            });

            var confirmStatusChangeModal = document.getElementById('confirmStatusChangeModal');
            confirmStatusChangeModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var userId = button.getAttribute('data-id');
                var userStatus = button.getAttribute('data-status');
                
                // Update modal content
                var modalTitle = confirmStatusChangeModal.querySelector('.modal-title');
                var modalBody = confirmStatusChangeModal.querySelector('.modal-body');
                modalTitle.textContent = userStatus == '1' ? 'DAR DE BAJA' : 'DAR DE ALTA';
                modalBody.textContent = userStatus == '1' 
                    ? '¿Seguro que deseas desactivar a este miembro del personal? Una vez desactivado, no podrá acceder al sistema.' 
                    : '¿Deseas dar de alta a este miembro del personal? Esto lo marcará como activo y podrá usar el sistema.';

                // Handle confirm button click
                var confirmButton = document.getElementById('confirmChangeStatusButton');
                confirmButton.onclick = function () {
                    changeUserStatus(userId, userStatus == '1' ? 0 : 1);
                };
            });
        });

        function changeUserStatus(userId, newStatus) {
            // Mostrar pantalla de carga
            swal({
                text: "Cargando...",
                button: false,
                closeOnClickOutside: false,
                closeOnEsc: false,
            });

            console.log('buscando user');
            console.log(userId);
            console.log(newStatus);


            jQuery.ajax({
                type: 'POST',
                url: '{{ route('cambiarStatus') }}',  
                data: {
                    _token: '{{ csrf_token() }}',
                    id: userId,
                    status: newStatus
                },
                success: function(response) {
                    // Ocultar pantalla de carga
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
                            title: "Error!",
                            text: response.message,
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
                    console.log(response);
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

       


    </script>
@endsection
