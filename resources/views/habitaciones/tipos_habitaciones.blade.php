@extends('../home')

@section('title', 'Tipos de Habitaciones - Hotel Project')

@section('content')
    <div class="height-100 p-4" style="background-color: #EEEEEE !important; margin-top: 5%!important;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h4>Tipos de Habitaciones</h4>
            <button type="button" class="btn btn-negro" data-bs-toggle="modal" data-bs-target="#tipoModal">Agregar Servicio</button>
        </div>
        <div style="background-color: white; margin-top: 15px !important; margin-right: 20px !important; padding: 20px !important">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">Tipo</th>
                        <th scope="col">Descripcion</th>
                        <th scope="col">Precio por noche</th>
                        <th scope="col">Capacidad</th>
                        <th scope="col">Status</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tiposHabitacionesTableBody">
                    <!-- el contenido de la tabla se llenara automaticamente -->
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
    <div class="modal fade" id="tipoModal" tabindex="-1" aria-labelledby="tipoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="padding-top: 10px !important; padding-left: 20px !important; padding-right: 20px !important;">
                <div class="modal-header">
                    <h4 class="modal-title" id="tipoModalLabel">Crear nuevo tipo de habitacion</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="tipoModalForm" action="" method="POST">
                        @csrf
                        <input type="hidden" id="tipoId" name="id">
                        <div class="mb-3">
                            <label for="tipo_habitacion" class="form-label" style="font-weight: 500">Nombre</label>
                            <input type="text" class="form-control border-thick" id="tipo_habitacion" name="tipo_habitacion" required>
                            <div id="tipo_habitacion-error" class="text-danger"></div>
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label" style="font-weight: 500">Descripcion</label>
                            <textarea class="form-control border-thick" id="descripcion" rows="3" name="descripcion" required></textarea>
                            <div id="descripcion-error" class="text-danger"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="precio" class="form-label" style="font-weight: 500">Precio</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-thick">$</span>
                                        <input type="number" step="0.01" min="0" class="form-control border-thick" id="precio" name="precio" required>
                                    </div>
                                    <div id="precio-error" class="text-danger"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="capacidad" class="form-label" style="font-weight: 500">Capacidad</label>
                                    <input type="number" min="0" class="form-control border-thick" id="capacidad" name="capacidad" required>
                                    <div id="capacidad-error" class="text-danger"></div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-custom" id="btnModalTipo">GUARDAR</button>
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
                    ¿Estás seguro de que quieres cambiar el estado de este tipo de habitacion?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmChangeStatusButton">ACEPTAR</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const tiposData = @json($tipos);
        const csrfToken = '{{ csrf_token() }}';
        const changeStatusRoute = '{{ route('cambiarStatusTipoHabitacion') }}';
        const insertTiposHabitacionesRoute = '{{ route('insertTipoHabitacion') }}';
        const editTiposHabitacionesRoute = '{{ route('editTipoHabitacion') }}';
    </script>
    @vite('resources/js/tipos/tipo_habitacion.js')

    
@endsection
