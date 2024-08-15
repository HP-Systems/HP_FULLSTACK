@extends('../home')

@section('title', 'Personal - Hotel Project')

@section('content')
    <div class="height-100 p-4" style="background-color: #EEEEEE !important; margin-top: 5%!important;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h4>Gestión de Personal</h4>
            <button type="button" class="btn btn-negro" data-bs-toggle="modal" data-bs-target="#addUserModal">Agregar
                Personal</button>
        </div>
        <div style="background-color: white; margin-top: 15px !important; margin-right: 20px !important; padding: 20px !important">
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
                <tbody id="personalTableBody">
                    
                </tbody>
            </table>
            <!-- Paginación -->
            <div class="d-flex justify-content-end">
                <ul class="pagination custom-pagination" id="pagination" style="background-color: #EEEEEE !important">
                    <!-- Los enlaces de paginación se llenarán dinámicamente -->
                </ul>
            </div>
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
                    <form id="addUserForm" action="{{ route('personal.crear') }}" method="POST" autocomplete="off">
                        @csrf
                        <input type="hidden" id="personalId" name="id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label" style="font-weight: 500">Nombre</label>
                                    <input type="text" autocomplete="nombre" class="form-control border-thick" id="nombre" name="nombre" required>
                                    <div id="nombre-error" class="text-danger"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="apellido" class="form-label" style="font-weight: 500">Apellido(s)</label>
                                    <input type="text" autocomplete="apellido" class="form-control border-thick" id="apellido" name="apellido" required>
                                    <div id="apellido-error" class="text-danger"></div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="telefono" class="form-label" style="font-weight: 500">Teléfono</label>
                            <input type="tel" maxlength="10"  autocomplete="tel"  class="form-control border-thick" id="telefono" name="telefono"
                            required="">
                            <div id="telefono-error" class="text-danger"></div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label" style="font-weight: 500">Correo</label>
                            <input type="email" autocomplete="email" class="form-control border-thick" id="email" name="email" required>
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
                    <button type="button" class="btn btn-custom" id="btnModalPersonal">GUARDAR</button>
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
        const personalData = @json($personal);
        const csrfToken = '{{ csrf_token() }}';
        const currentUserId = {{ $currentUserId }};
        const editPersonalRoute = '{{ route('editPersonal') }}';
        const insertPersonalRoute = '{{ route('insertPersonal') }}';
        const changeStatusRoute = '{{ route('cambiarStatus') }}';
    </script>
    @vite('resources/js/personal.js')
    
@endsection
