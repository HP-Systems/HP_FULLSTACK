@extends('../home')

@section('title', 'Reporte 1 - Hotel Project')
@section('content')
@vite('resources/css/reporte1.css')
<div class="height-100 p-5 mx-auto m-3" style="background-color: #EEEEEE">
    <br>
<div style="display: flex; justify-content: space-between; align-items: center;">
    <h4>Ingresos generales</h4>
    <div style="display: flex; align-items: center;">
    <a href="{{ route('reporte1.pdf') }}"id="download-button"><button type="button" class="btn btn-danger" style="margin-right: 10px;" >
            <i class="fa fa-cloud-download"></i>
        </button></a>
        <form action="{{ route('reporte1.filtar') }}" method="POST" style="display: flex; align-items: center;">
            @csrf
            <label for="fecha-inicio" style="margin-right: 5px;">Fecha inicio:</label>
            <input type="date" id="fecha_inicio" name="fecha_inicio" class="date-input" style="margin-right: 10px;"value="{{ old('fecha_inicio', $fecha_inicio) }}">
            <label for="fecha-fin" style="margin-right: 5px;" ">Fecha fin:</label>
            <input type="date" id="fecha_fin" name="fecha_fin" class="date-input" style="margin-right: 10px;" value="{{ old('fecha_fin', $fecha_fin) }}">
            <button type="submit" class="btn btnFiltro" id="filtrar" onclick="showLoadingAlert()">Filtrar</button>
        </form>
    </div>
</div>
    <div style="background-color: white; margin-top: 15px !important; margin-right: 20px !important; padding: 20px !important">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">Mes</th>
                    <th scope="col">Numero reserva</th>
                    <th scope="col">Huesped</th>
                    <th scope="col">Total de alojamiento</th>
                    <th scope="col">Total de servicios</th>
                    <th scope="col">Total general</th>
                </tr>
            </thead>
            <tbody id="ventas-table">
                @foreach ($ventas as $venta)
                <tr>
                    <td>{{$venta->Mes}}</td>
                    <td>{{$venta->Numero_Reserva}}</td>
                    <td>{{$venta->Huesped}}</td>
                    <td>${{$venta->Total_Alojamiento}}</td>
                    <td>${{$venta->Total_Servicios}}</td>
                    <td>${{$venta->Total_General}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <!-- Paginación dinámica se genera aquí -->
            </ul>
        </nav>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
<script>
function showLoadingAlert() {
    swal({
        text: "Cargando...",
        button: false,
        closeOnClickOutside: false,
        closeOnEsc: false,
    });
}
</script>
<script>
    $(document).ready(function() {
        var rowsPerPage = 10;
        var rows = $('#ventas-table tr');
        var rowsCount = rows.length;
        var pageCount = Math.ceil(rowsCount / rowsPerPage);
        var pagination = $('.pagination');
        let currentPage = 0;

        for (var i = 0; i < pageCount; i++) {
            pagination.append(`
                <li class="page-item">
                    <a class="page-link" href="javascript:void(0);">${i + 1}</a>
                </li>
            `);
        }

        pagination.find('li:first-child').addClass('active-page');

        function showPage(page) {
            rows.hide();
            rows.slice((page - 1) * rowsPerPage, page * rowsPerPage).show();
        }

        showPage(1);

        pagination.on('click', 'li', function(e) {
            e.preventDefault();
            var page = $(this).index() + 1;
            currentPage = page;
            pagination.find('li').removeClass('active-page');
            $(this).addClass('active-page');
            showPage(page);
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
        var url = "{{ route('reporte1.pdf') }}?fecha_inicio=" + fechaInicio + "&fecha_fin=" + fechaFin;

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
@endsection