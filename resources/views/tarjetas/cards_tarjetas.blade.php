<!-- resources/views/partials/tarjetas.blade.php -->
@foreach ($tarjetas as $tarjeta)
<div class="card m-2" style="flex: 1 1 calc(25% - 1rem); max-width: calc(25% - 1rem);">
    <div class="card-body">
        <h5 class="card-title">Tarjeta {{ $tarjeta->id }}</h5>
        <p class="card-text">Tipo: {{ $tarjeta->tipo}}</p>
        <p class="card-text">Disponibilidad: 
            {{ $tarjeta->disponibilidad == 1 ? 'Libre' : 'Asignada' }}
        </p>
        {!! $tarjeta->status == 1 
            ? '<button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#confirmStatusChangeModal"
                data-id="' . $tarjeta->id . '" data-status="' . $tarjeta->status . '" ' . ($tarjeta->status_tipo == 0 ? 'disabled' : '') . '>ACTIVA</button>' 
            : '<button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmStatusChangeModal"
                data-id="' . $tarjeta->id . '" data-status="' . $tarjeta->status . '" ' . ($tarjeta->status_tipo == 0 ? 'disabled' : '') . '>INACTIVA</button>' 
        !!}
        <!-- <button class="btn btn-link p-0" style="position: absolute; top: 10px; right: 10px;">
            <i class="fas fa-edit" style="color: black;"></i>
        </button>
        -->
    </div>
</div>        
@endforeach