@extends('../home')

@section('title', 'Servicios - Hotel Project')

@section('content')
    <div class="height-100 p-4" style="background-color: #EEEEEE !important; margin-top: 5%!important;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h4>Gestión de Servicios</h4>
            <button type="button" class="btn btn-negro" data-bs-toggle="modal" data-bs-target="#servicioModal">Agregar Servicio</button>
        </div>
        <div style="background-color: white; margin-top: 15px !important; margin-right: 20px !important; padding: 20px !important">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">Nombre</th>
                        <th scope="col">Descripcion</th>
                        <th scope="col">Precio</th>
                        <th scope="col">Tipo de servicio</th>
                        <th scope="col">Status</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody id="serviciosTableBody">
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
    <div class="modal fade" id="servicioModal" tabindex="-1" aria-labelledby="servicioModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="padding-top: 10px !important; padding-left: 20px !important; padding-right: 20px !important;">
                <div class="modal-header">
                    <h4 class="modal-title" id="servicioModalLabel">Crear Nuevo Servicio</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="servicioModalForm" action="" method="POST">
                        @csrf
                        <input type="hidden" id="servicioId" name="id">
                        <div class="mb-3">
                            <label for="name_servicio" class="form-label" style="font-weight: 500">Nombre</label>
                            <input type="text" class="form-control border-thick" id="name_servicio" name="name_servicio" required>
                            <div id="name_servicio-error" class="text-danger"></div>
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
                                        <input type="number" step="0.01" class="form-control border-thick" id="precio" name="precio" required>
                                    </div>
                                    <div id="precio-error" class="text-danger"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tipo" class="form-label " style="font-weight: 500">Tipo de Servicio</label>
                                    <select class="form-select border-thick" id="tipo" name="tipo" required>
                                        @foreach($tipos as $tipo)
                                            <option value="{{ $tipo->id }}">{{ $tipo->tipo }}</option>
                                        @endforeach
                                    </select>
                                    <div id="tipo-error" class="text-danger"></div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-custom" id="btnModalServicio">GUARDAR</button>
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
                    ¿Estás seguro de que quieres cambiar el estado de este servicio?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmChangeStatusButton">ACEPTAR</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const serviciosData = @json($servicios);
        const csrfToken = '{{ csrf_token() }}';
        const changeStatusRoute = '{{ route('cambiarStatusServicio') }}';
        const insertServiceRoute = '{{ route('insertService') }}';
        const editServiceRoute = '{{ route('editService') }}';
    </script>
    @vite('resources/js/servicios.js')

    
@endsection
