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
                                <button type="button" class="btn btn-outline-dark">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger">
                                    <i class="fas fa-sync"></i>
                                </button>
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
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label" style="font-weight: 500">Nombre</label>
                                    <input type="text" class="form-control border-thick" id="nombre" name="nombre" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="apellido" class="form-label" style="font-weight: 500">Apellido(s)</label>
                                    <input type="text" class="form-control border-thick" id="apellido" name="apellido" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="telefono" class="form-label" style="font-weight: 500">Teléfono</label>
                            <input type="tel" class="form-control border-thick" id="telefono" name="telefono" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label" style="font-weight: 500">Correo</label>
                            <input type="email" class="form-control border-thick" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="rol" class="form-label " style="font-weight: 500">Tipo de Usuario</label>
                            <select class="form-select border-thick" id="rol" name="rol" required>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->nombre }}</option>
                                @endforeach
                            </select>
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

    <script>
        function saveUser() {
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
        url: '{{ route('insertPersonal') }}',
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
