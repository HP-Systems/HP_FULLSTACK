@extends('../home')

@section('title', 'Tipos de Tarjetas - Hotel Project')

@section('content')
    <div class="height-100 p-4" style="background-color: #EEEEEE !important; margin-top: 5%!important;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h4>Tipos de Tarjetas</h4>
        </div>
        <div class="row">    
            <div class="col-sm-8" style="padding-top: 10px;">
                <div style="flex: 1; min-width: 70%; padding: 20px; background-color: white;">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">Tipo</th>
                                <th scope="col">Status</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tiposTarjetasTableBody">
                            @foreach ($tipos as $tipo)
                            <tr>
                                <td>{{ $tipo->tipo }}</td>
                                <td>
                                    {!! $tipo->status == 1 
                                        ? '<button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#confirmStatusChangeModal" 
                                            data-id="' . $tipo->id . '" data-status="' . $tipo->status . '">ACTIVO</button>' 
                                        : '<button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmStatusChangeModal" 
                                            data-id="' . $tipo->id . '" data-status="' . $tipo->status . '">INACTIVO</button>' 
                                    !!}
                                </td>
                                <td>
                                    <button type="button" class="btn btn-outline-dark edit-btn" data-id="{{ $tipo->id }}" data-tipo="{{ $tipo->tipo }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-sm-4" style="padding-top: 10px;">
                <div style="flex: 1; min-width: 20%; background-color: white; height: 250px;">
                    <div style="border-bottom: 2px solid white; padding: 12px; background-color: #222831;">
                        <h5 class="modal-title" style="font-size: 23px; color: white; text-align: center; margin: 0;" id="tipoTarjetaModalLabel">Crear tipo de tarjeta</h5>
                    </div>
                    <div style="padding: 5%;">
                        <form id="tipoTarjetaForm" action="" method="POST">
                            @csrf
                            <input type="hidden" id="id" name="id">
                            <div class="mb-3">
                                <label for="tipo_tarjeta" class="form-label" style="font-weight: 500">Tipo de Tarjeta</label>
                                <input type="text" class="form-control border-thick" id="tipo_tarjeta" name="tipo_tarjeta" required>
                                <div id="tipo_tarjeta-error" class="text-danger"></div>
                            </div>
                        </form>
                        <div class="d-flex justify-content-between" style="margin-top: 10%;">
                            <button type="button" class="btn btn-secondary w-50 btn-cancelar" style="margin-right: 5%;">CANCELAR</button>
                            <button type="button" class="btn btn-custom w-50 btn-guardar">GUARDAR</button>
                        </div>
                    </div>
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
                    ¿Estás seguro de que quieres cambiar el estado de este tipo de tarjeta?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmChangeStatusButton">ACEPTAR</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        const csrfToken = '{{ csrf_token() }}';
        const insertTipoTarjetaRoute = '{{ route('insertTipoTarjeta') }}';
        const editTipoTarjetaRoute = '{{ route('editTipoTarjeta') }}';
        const changeStatusRoute = '{{ route('cambiarStatusTipoTarjeta') }}';
    </script>
    
    @vite('resources/js/tipos/tipo_tarjeta.js')
@endsection
