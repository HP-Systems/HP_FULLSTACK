@extends('../home')

@section('title', 'Tipo de Personal - Hotel Project')

@section('content')
<div class="height-100 p-4" style="background-color: #EEEEEE !important; margin-top: 5%!important;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h4>Tipos de Personal</h4>
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
                    <tbody id="personalTableBody">
                        @foreach ($roles as $rol)
                        <tr>
                            <td>{{ $rol->nombre }}</td>
                            <td>
                                {!! $rol->status == 1 ? '<button disabled type="button" class="btn btn-success">ACTIVO</button>' : '<button disabled type="button" class="btn btn-danger">INACTIVO</button>' !!}
                            </td>
                            <td>
                                <button type="button" class="btn btn-outline-dark edit-btn" data-id="{{ $rol->id }}" data-nombre="{{ $rol->nombre }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger">
                                    <i class="fas fa-sync"></i>
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
                    <h5 style="font-size: 23px; color: white; text-align: center; margin: 0;">Registro/Modificación</h5>
                </div>
                <div style="padding: 5%;">
                    <form action="{{ url('/guardar') }}" method="POST"> <!-- Ajusta el action y method según tu ruta y método HTTP -->
                        @csrf
                        <input type="hidden" id="tipoID" name="id">
                        <div class="mb-3">
                            <label for="tipo" class="form-label" style="font-weight: 500">Tipo de Personal</label>
                            <input type="text" class="form-control border-thick" id="tipoPersonal" name="tipo" required>
                        </div>
                    </form>
                    <div class="d-flex justify-content-between" style="margin-top: 10%;">
                        <button type="button" class="btn btn-secondary w-50 btn-cancelar" style="margin-right: 5%;">CANCELAR</button>
                        <button type="button" class="btn btn-custom w-50" onclick="save()">GUARDAR</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Asegúrate de incluir jQuery -->
<script>
    $(document).ready(function() {
        $('.edit-btn').click(function() {
            var tipo = $(this).data('nombre'); 
            var id = $(this).data('id');

            $('#tipoID').val(id); 
            $('#tipoPersonal').val(tipo); 
        });

        $('.btn-cancelar').click(function() {
            limpiarFormulario();
        });
    });

    function limpiarFormulario() {
        $('#tipoID').val(''); 
        $('#tipoPersonal').val(''); 
    }

    function save(){
        var id = $('#tipoID').val();
        var tipo = $('#tipoPersonal').val();

        swal({
            text: "Cargando...",
            button: false,
            closeOnClickOutside: false,
            closeOnEsc: false,
        });

        if(id == ''){
            console.log("Nuevo tipo");
        } else{
            console.log("Actualizar tipo");
        }
    }
</script>
@endsection
