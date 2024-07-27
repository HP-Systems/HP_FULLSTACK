@extends('../home')

@section('title', 'Dashboard - Hotel Project')

@section('content')
<div class="height-100 p-5" style="background-color: #EEEEEE">

    <div style="margin-top: 3%">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Tarjetas</h4>
            <button class="btn btn-custom">Agregar Tarjeta</button>
        </div>
    </div>
    
    <div class="d-flex flex-wrap justify-content-between">
        <!-- Tarjeta 1 -->
        <div class="card m-2" style="flex: 1 1 23%; max-width: 23%; position: relative;">
            <div class="card-body">
                <h5 class="card-title">Tarjeta 1</h5>
                <p class="card-text">Tipo: Limpieza</p>
                <button class="btn btn-danger">ACTIVA</button>
                <button class="btn btn-link p-0" style="position: absolute; top: 10px; right: 10px;">
                    <i class="fas fa-edit" style="color: black;"></i>
                </button>
            </div>
        </div>


        <!-- Tarjeta 2 -->
        <div class="card m-2" style="flex: 1 1 23%; max-width: 23%; position: relative;">
            <div class="card-body">
                <h5 class="card-title">Tarjeta 2</h5>
                <p class="card-text">Tipo: Administrativa</p>
                <button class="btn btn-success">INACTIVA</button>
                <button class="btn btn-link p-0" style="position: absolute; top: 10px; right: 10px;">
                    <i class="fas fa-edit" style="color: black;"></i>
                </button>
            </div>
        </div>

        <!-- Tarjeta 3 -->
        <div class="card m-2" style="flex: 1 1 23%; max-width: 23%; position: relative;">
            <div class="card-body">
                <h5 class="card-title">Tarjeta 3</h5>
                <p class="card-text">Tipo: Limpieza</p>
                <button class="btn btn-success">ACTIVA</button>
                <button class="btn btn-link p-0" style="position: absolute; top: 10px; right: 10px;">
                    <i class="fas fa-edit" style="color: black;"></i>
                </button>
            </div>
        </div>

        <!-- Tarjeta 4 -->
        <div class="card m-2" style="flex: 1 1 23%; max-width: 23%; position: relative;">
            <div class="card-body">
                <h5 class="card-title">Tarjeta 4</h5>
                <p class="card-text">Tipo: Huesped</p>
                <button class="btn btn-success">ACTIVA</button>
                <button class="btn btn-link p-0" style="position: absolute; top: 10px; right: 10px;">
                    <i class="fas fa-edit" style="color: black;"></i>
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
