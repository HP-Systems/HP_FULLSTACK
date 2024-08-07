@extends('../home')
@section('title', 'Habitaciones - Hotel Project')
@section('content')
@vite('resources/css/card.css')

<div class="height-100 p-5" style="background-color: #EEEEEE">
    <br>
    <div class="header-container mb-3">
        <h4>Habitaciones</h4>
        <div class="d-flex justify-content-between">
            <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#addRoom">
                Agregar Habitación
            </button>
            <input type="text" id="search-room" class="form-control form-control-sm color" placeholder="Buscar por número de habitación">
        </div>
    </div>
    <div class="row">
        <div id="room-results"></div>
        @if($habitaciones->isEmpty())
        <div class="alert alert-warning" role="alert">
            Por favor, agrega una habitación.
        </div>

        @endif
        <div class="row" id="card-container">
            @foreach ($habitaciones as $habitacion)
            <div class="col-md-4 mb-3" id="cards">
                <div class="card" style="width: 100%;">
                    <div class="card-body">
                        <div class="capacity-container">
                            <h5 class="card-title">{{$habitacion->tipo}} NO°: {{$habitacion->numero}}</h5>
                            <i class="fa fa-circle {{ $habitacion->status == 1 ? 'text-success' : 'text-danger' }}"></i>
                        </div>
                        <p class="card-description">{{$habitacion->descripcion}}</p>
                        <img class="card-img-top" src="{{$habitacion->imagen}}" alt="Card image cap">
                        <p class="card-text">${{$habitacion->precio_noche}}</p>
                        <div class="capacity-container">
                            <p>capacidad: {{$habitacion->capacidad}}</p>
                            <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#editRoom{{$habitacion->id}}">
                                <i class="fas fa-edit"></i>
                            </button>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Editar habitacion -->
            <div class="modal fade" id="editRoom{{$habitacion->id}}" tabindex="-1" aria-labelledby="editRoomLabel{{$habitacion->id}}" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content" style="padding-top: 10px !important; padding-left: 20px !important; padding-right: 20px !important;">
                        <div class="modal-header">
                            <h4 class="modal-title" id="editRoomLabel{{$habitacion->id}}"> {{$habitacion->tipo}} {{$habitacion->numero}}</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="{{ route('updateRoom', ['id' => $habitacion->id]) }}" enctype="multipart/form-data">
                                @method('PUT')
                                @csrf
                                <div class="mb-3">
                                    <label for="numero" class="form-label">Numero</label>
                                    <input type="text" class="form-control" id="numero" name='numero' value="{{$habitacion->numero}}">
                                </div>
                                <div class="mb-3">
                                    <label for="status{{$habitacion->id}}" class="form-label">Estado</label>
                                    <select class="form-control" id="status{{$habitacion->id}}" name="status">
                                        <option value="1" {{ $habitacion->status == '1' ? 'selected' : '' }}>Activada</option>
                                        <option value="0" {{ $habitacion->status == '0' ? 'selected' : '' }}>Desactivada</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="tipoID" class="form-label">Tipo de Habitación</label>
                                    <select name="tipoID" class="form-select tipoHabitacionSelect">
                                        @foreach ($tipoHabitaciones as $tipoHabitacion)
                                        <option value="{{$tipoHabitacion->id}}" data-capacidad="{{$tipoHabitacion->capacidad}}" data-precio="{{$tipoHabitacion->precio_noche}}" data-descripcion="{{$tipoHabitacion->descripcion}}" data-image="{{$tipoHabitacion->imagen}}" @if($tipoHabitacion->id == $habitacion->tipoID) selected @endif>
                                            {{$tipoHabitacion->tipo}}
                                        </option>
                                        @endforeach
                                    </select>
                                    <label for="capacidad" class="form-label">Capacidad</label>
                                    <input type="text" class="form-control capacidadInput" name="capacidad" readonly>
                                    <label for="precio_noche" class="form-label">Precio por Noche</label>
                                    <input type="text" class="form-control precioNocheInput" name="precio_noche" readonly>
                                    <label for="descripcion" class="form-label">Descripción</label>
                                    <input type="text" class="form-control descripcionInput" name="descripcion" readonly>
                                    <label for="imagen" class="form-label">Imagen</label>
                                    <div>
                                        <img src="{{ asset('default-image.jpg') }}" alt="Imagen actual" style="max-width: 200px;" name="imagen">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-custom">Guardar</button>
                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                                </div>



                            </form>
                        </div>

                    </div>
                </div>
            </div>

            @endforeach
        </div>

        <!-- Modal Aagregar habitacion -->
        <div class="modal fade" id="addRoom" tabindex="-1" aria-labelledby="addRoom" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content" style="padding-top: 10px !important; padding-left: 20px !important; padding-right: 20px !important;">
                    <div class="modal-header">
                        <h4 class="modal-title" id="addRoom">Habitacion</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="{{ route('room.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="numero" class="form-label">Numero</label>
                                <input type="text" class="form-control" id="numero" name='numero' ">
                            </div>
                            <div class=" mb-3">
                                <label for="status" class="form-label">Estado</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="1">Activada</option>
                                    <option value="0">Desactivada</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="tipoID" class="form-label">Tipo de Habitación</label>
                                <select name="tipoID" class="form-select tipoHabitacionSelect">
                                    @foreach ($tipoHabitaciones as $tipoHabitacion)
                                    <option value="{{$tipoHabitacion->id}}" data-capacidad="{{$tipoHabitacion->capacidad}}" data-precio="{{$tipoHabitacion->precio_noche}}" data-descripcion="{{$tipoHabitacion->descripcion}}" data-image="{{$tipoHabitacion->imagen}}" @if($tipoHabitacion->id == $habitacion->tipoID) selected @endif>
                                            {{$tipoHabitacion->tipo}}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="capacidad" class="form-label">Capacidad</label>
                                <input type="text" class="form-control capacidadInput" name="capacidad" readonly>
                                <label for="precio_noche" class="form-label">Precio por Noche</label>
                                <input type="text" class="form-control precioNocheInput" name="precio_noche" readonly>
                                <label for="descripcion" class="form-label">Descripción</label>
                                <input type="text" class="form-control descripcionInput" name="descripcion" readonly>
                                <label for="imagen" class="form-label">Imagen</label>
                                    <div>
                                        <img src="{{ asset('default-image.jpg') }}" alt="Imagen actual" style="max-width: 200px;" name="imagen">
                                    </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-custom">Guardar</button>
                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Contenedor de Paginación -->

    </div>
    <nav aria-label="Page navigation example">
        <ul class="pagination" id="pagination-container"></ul>
    </nav>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    @vite ('resources/js/paginacionR1.js')

    <script>
        $(document).ready(function() {
            // Variable para almacenar el temporizador del debounce
            let debounceTimer;

            // Función para cargar el contenido HTML devuelto por la búsqueda
            $('#search-room').on('keyup', function() {
                clearTimeout(debounceTimer); // Limpiar el temporizador previo

                // Ejecutar la función de búsqueda con un retraso (debounce)
                debounceTimer = setTimeout(() => {
                    var numero = $(this).val();
                    $.ajax({
                        url: '{{ route("habitaciones.buscar") }}',
                        type: 'GET',
                        data: {
                            numero: numero
                        },
                        success: function(response) {
                            $('#room-results').empty(); // Limpiar el contenido previo
                            if (response.html) {
                                $('#room-results').html(response.html);
                                // Reasignar los eventos después de actualizar el DOM
                                attachEvents($('#room-results'));
                            }
                        }
                    });
                }, 300); // 300 ms de retraso
            });
            // Función para actualizar los inputs basándose en el valor seleccionado del select
            function actualizarInputs(selectElement) {
                const container = $(selectElement).closest('div');
                const selectedOption = selectElement.options[selectElement.selectedIndex];
                const capacidad = $(selectedOption).attr('data-capacidad');
                const precio = $(selectedOption).attr('data-precio');
                const descripcion = $(selectedOption).attr('data-descripcion');
                const imagen = $(selectedOption).attr('data-image');

                container.find('.capacidadInput').val(capacidad);
                container.find('.precioNocheInput').val(precio);
                container.find('.descripcionInput').val(descripcion);
                container.find('.imagenInput').val(imagen);
                container.find('img[name="imagen"]').attr('src', imagen);
            }
            // Función para adjuntar eventos a los selects después de actualizar el DOM
            // Se modifica para recibir un contenedor específico y evitar la reasignación global de eventos
            function attachEvents(container = $(document)) {
                container.find('.tipoHabitacionSelect').each(function() {
                    actualizarInputs(this);
                    $(this).off('change').on('change', function() {
                        actualizarInputs(this);
                    });
                });
            }

            // Llamar a attachEvents para inicializar eventos en la carga de la página
            attachEvents();
        });
    </script>
    @vite('resources/js/app.js')

    @if ($errors->any())
    <script>
        swal({
            title: "Error!",
            text: "{{ $errors->first() }}",
            icon: "error",
        });
    </script>

    @endif
    @if(session('success'))
    <script>
        swal({
            title: "¡Éxito!",
            text: "{{ session('success') }}",
            icon: "success",
        });
    </script>
    @endif

    @endsection