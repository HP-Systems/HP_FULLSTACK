@if($habitaciones->isEmpty())
<div class="alert alert-warning" role="alert">
    No se encontraron habitaciones con ese número.
</div>
@else
<div class="row">
<h5>Resultados de la busqueda</h5>
    @foreach ($habitaciones as $habitacion)

 
    <div class="col-md-4 mb-3">
        <div class="card" style="width: 100%;">
            <div class="card-body">
                <div class="capacity-container">
                    <h5 class="card-title">{{$habitacion->tipo}} NO°: {{$habitacion->numero}}</h5>
                    <i class="fa fa-circle {{ $habitacion->status == 1 ? 'text-success' : 'text-danger' }}"></i>
                </div>
                <p>{{$habitacion->descripcion}}</p>
                <img class="card-img-top" src="images/{{$habitacion->imagen}}" alt="Card image cap">
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
    @endforeach
</div>
@vite('resources/js/app.js')
@endif