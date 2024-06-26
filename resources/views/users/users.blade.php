@extends('../home')

@section('title', 'Usuarios - Hotel Project')

@section('content')
<div class="height-100 p-4" style="background-color: #EEEEEE">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h4>Gestión de Usuarios</h4>
        <button type="button" class="btn btn-negro">Agregar Usuario</button>
    </div>
    <div class="p-4 m-3" style="background-color: white;">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">Nombre</th>
                    <th scope="col">Correo</th>
                    <th scope="col">Tipo de usuario</th>
                    <th scope="col">Status</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($usuarios as $usuario)
                    <tr>
                        <td>{{ $usuario->nombre_completo }}</td> 
                        <td>{{ $usuario->email }}</td>
                        <td>{{ $usuario->rol }}</td> 
                        <td>
                            @if ($usuario->status == 1)
                                <button disabled type="button" class="btn btn-success">ACTIVO</button>
                            @else
                                <button disabled type="button" class="btn btn-danger">INACTIVO</button>
                            @endif
                        </td> 
                        <td>
                            <button type="button" class="btn btn-outline-dark">
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
@endsection