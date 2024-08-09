<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Ingresos</title>
    <style>
        footer {
            position: fixed;
            bottom: -60px;
            left: 0px;
            right: 0px;
            height: 50px;


        }

        @page {
            margin: 50px 25px;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 0.700rem;
        }

        .container {
            width: auto;
            padding: 0 20px;
        }

        .bg-white {
            background-color: white;
        }

        .p-4 {
            padding: 1.5rem;
        }

        .rounded-lg {
            border-radius: 0.5rem;
        }

        .shadow-lg {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1),
                0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .mt-4 {
            margin-top: 1.5rem;
        }

        .d-flex {
            display: flex;
        }

        .justify-content-between {
            justify-content: space-between;
        }

        .align-items-center {
            align-items: center;
        }

        .mb-4 {
            margin-bottom: 1.5rem;
        }

        .font-weight-bold {
            font-weight: 700;
        }

        .align-self-center {
            align-self: center;
        }

        .px-4 {
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .font-weight-bold {
            font-weight: 700;
        }

        .thead-light {
            background-color: #f8f9fa;
        }

        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
        }

        .table th,
        .table td {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
        }

        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
        }

        .table tbody+tbody {
            border-top: 2px solid #dee2e6;
        }

        .table-bordered {
            border: 1px solid #dee2e6;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #dee2e6;
        }
    </style>
</head>

<body>
    <footer>
        <div class="text-center">
            <p class="text-muted">Derechos Reservados &copy; {{$fechaInicio}}</p>
        </div>
    </footer>
    <main>
        <div class="container bg-white p-4 rounded-lg shadow-lg mt-4 ">
            <table style="width: 100%;">
                <tr>
                    <td style="width:18%">
                        <img src="{{ $logo}}" alt="Logo" width="100" height="100" style="border-radius: 50%;" />
                    </td>
                    <td>
                        <div class="font-weight-bold text-left">
                            <h2>Hotel</h2>
                        </div>
                    </td>
                    <td>
                        <div class="text-right">
                            <h1 class=" font-weight-bold">Reporte de Ingresos</h1>
                            <b>
                                <p class="text-muted font-weight-bold">{{ $fechaInicio }} - {{$fechaFin}}</p>
                            </b>
                        </div>
                    </td>
                </tr>
            </table>
            <table class="table">
                <thead class="thead-light">
                    <tr>
                        <th>Mes</th>
                        <th>Número de Reserva</th>
                        <th>Huésped</th>
                        <th class="text-right">Total Alojamiento</th>
                        <th class="text-right">Total Servicios</th>
                        <th class="text-center">Total</th>
                    </tr>
                </thead>
                <tbody>
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

                    <tr>
                        <td colspan="2" class="text-center font-weight-bold"></td>
                        <td class="text-left font-weight-bold"><b>Total</b></td>
                        <td class="text-left font-weight-bold"><b>{{$alojamiento}}</b></td>
                        <td class="text-left font-weight-bold"><b>{{$servicios}}</b></td>
                        <td class="text-left font-weight-bold"><b>{{$total}}</b></td>

                    </tr>
                </tbody>
            </table>



        </div>
    </main>

</body>

</html>