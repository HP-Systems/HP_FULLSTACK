@extends('../home')

@section('title', 'Tarjetas - Hotel Project')

@section('content')
    <div class="height-100 p-5" style="background-color: #EEEEEE">
        <div style="margin-top: 3%">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Tarjetas</h4>

                <!-- Formulario de filtro -->
                <form method="GET" action="{{ route('tarjetas') }}" id="filtroForm">
                    <div class="form-group">
                        <div class="d-flex align-items-center">
                            <!-- Filtro de tipo -->
                            <label for="tipo" style="font-weight: 600; font-size: 15px; padding-right: 5px">Tipo:</label>
                            <div class="dropdown-wrapper" style="padding-right: 15px">
                                <select name="tipo" id="tipo" class="form-control fixed-width-select">
                                    <option value="todos" {{ request('tipo') == 'todos' ? 'selected' : '' }}>Todos</option>
                                    @foreach($tipos as $tipo)
                                        <option value="{{ $tipo->id }}" {{ request('tipo') == $tipo->id ? 'selected' : '' }}>{{ $tipo->tipo }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Filtro de disponibilidad -->
                            <label for="filtro" style="font-weight: 600; font-size: 15px; padding-right: 5px">Disponibilidad:</label>
                            <div class="dropdown-wrapper" style="padding-right: 15px">
                                <select name="filtro" id="filtro" class="form-control fixed-width-select">
                                    <option value="todas" {{ request('filtro') == 'todas' ? 'selected' : '' }}>Todas</option>
                                    <option value="ocupadas" {{ request('filtro') == 'ocupadas' ? 'selected' : '' }}>Libres</option>
                                    <option value="disponibles" {{ request('filtro') == 'disponibles' ? 'selected' : '' }}>Asignadas</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-custom">Filtrar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="d-flex flex-wrap" style="margin-top: 2%" id="tarjetasContainer">
            @include('tarjetas.cards_tarjetas', ['tarjetas' => $tarjetas])
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
                    Mensaje
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmChangeStatusButton">ACEPTAR</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const csrfToken = '{{ csrf_token() }}';
        const changeStatusRoute = '{{ route('cambiarStatusTarjeta') }}';
    </script>

    @vite('resources/js/tarjetas.js')
@endsection
