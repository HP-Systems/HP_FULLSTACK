@extends('../home')

@section('title', 'Reporte 2 - Hotel Project')

@section('content')
@vite('resources/css/reporte2.css')
<div class="height-100 p-5 mx-auto m-3" style="background-color: #EEEEEE">
    <br>
<div style="display: flex; justify-content: space-between; align-items: center;">
    <h4>Ingresos por tipo de habitacion </h4>
    <div style="display: flex; align-items: center;">
    <a href="{{ route('reporte2.pdf') }}"id="download-button"><button type="button" class="btn btn-danger" style="margin-right: 10px;">
            <i class="fa fa-cloud-download"></i>
        </button></a>
        <form action="{{ route('reporte2.filtar') }}" method="POST" style="display: flex; align-items: center;">
            @csrf
            <label for="fecha-inicio" style="margin-right: 5px;">Fecha inicio:</label>
            <input type="date" id="fecha_inicio" name="fecha_inicio" class="date-input" style="margin-right: 10px;"value="{{ old('fecha_inicio', $fecha_inicio) }}">
            <label for="fecha-fin" style="margin-right: 5px;" ">Fecha fin:</label>
            <input type="date" id="fecha_fin" name="fecha_fin" class="date-input" style="margin-right: 10px;" value="{{ old('fecha_fin', $fecha_fin) }}">
            <button type="submit" class="btn btnFiltro" id="filtrar">Filtrar</button>
        </form>
    </div>
</div>
    @foreach($tiposHabitaciones as $tipoHabitacion=>$habitaciones)
    <br>
    <p style="color: black; font-size:18px; font-weight:bold;">{{$tipoHabitacion}}</p>

    <div style="background-color: white; margin-top: 15px !important; margin-right: 20px !important; padding: 20px !important">
    <table class="table table-hover" id="table-{{$loop->index}}">
            <thead>
                <tr>
                    <th scope="col">Numero de reserva</th>
                    <th scope="col">Huesped</th>
                    <th scope="col">Habitacion</th>
                    <th scope="col">Fecha de ingreso</th>
                    <th scope="col">Fecha de salida</th>
                    <th scope="col">Total de alojamiento</th>
                    <th scope="col">Total de servicios</th>
                    <th scope="col">Total</th>
                </tr>
            </thead>
            <tbody id="ventas-table">
                @foreach($habitaciones as $habitacion)
                <tr>
                    <td>{{$habitacion->Numero_Reserva}}</td>
                    <td>{{$habitacion->Huesped}}</td>
                    <td>{{$habitacion->Numero_Habitacion}}</td>
                    <td>{{$habitacion->Fecha_Entrada}}</td>
                    <td>{{$habitacion->Fecha_Salida}}</td>
                    <td>${{$habitacion->Total_Alojamiento}}</td>
                    <td>${{$habitacion->Total_Servicios}}</td>
                    <td>${{$habitacion->Total_General}}</td>

                </tr>
                @endforeach
            </tbody>
        </table>
        <nav aria-label="Page navigation" class="widht:30px;">
            <ul class="pagination justify-content-center" id="pagination-{{$loop->index}}">
                <!-- Paginación dinámica se genera aquí -->
            </ul>
        </nav>
    </div>
    
        @endforeach


    </div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
<script>
   $(document).ready(function() {
    var rowsPerPage = 5;

    // Iterar sobre cada tabla
    $('table[id^="table-"]').each(function(index, table) {
        var rows = $(table).find('tbody tr');
        var rowsCount = rows.length;
        var pageCount = Math.ceil(rowsCount / rowsPerPage);
        var pagination = $('#pagination-' + index);
        let currentPage = 0;

        // Ocultar todas las filas y mostrar solo las de la primera página
        rows.hide();
        rows.slice(0, rowsPerPage).show();

        // Generar los enlaces de paginación
        for (var i = 0; i < pageCount; i++) {
            pagination.append('<li class="page-item"><a class="page-link" href="#">' + (i + 1) + '</a></li>');
        }

        pagination.find('a').click(function(e) {
            e.preventDefault();
            var pageIndex = $(this).text() - 1;
            currentPage = pageIndex;

            // Ocultar todas las filas y mostrar solo las de la página seleccionada
            rows.hide();
            rows.slice(currentPage * rowsPerPage, (currentPage + 1) * rowsPerPage).show();

            // Marcar el enlace de paginación actual como activo
            pagination.find('li').removeClass('active');
            $(this).parent().addClass('active');
        });

        // Marcar el primer enlace de paginación como activo
        pagination.find('li:first').addClass('active');
    });
});

</script>
<script>
    document.getElementById('download-button').addEventListener('click', function(event) {
        event.preventDefault(); // Evita que el enlace se siga inmediatamente

        // Obtén los valores de las fechas
        var fechaInicio = document.getElementById('fecha_inicio').value;
        var fechaFin = document.getElementById('fecha_fin').value;

        // Construye la URL con los parámetros de fecha
        var url = "{{ route('reporte2.pdf') }}?fecha_inicio=" + fechaInicio + "&fecha_fin=" + fechaFin;

        // Redirige a la URL construida
        window.location.href = url;
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