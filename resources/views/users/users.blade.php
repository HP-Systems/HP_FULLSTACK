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
            <th scope="col">#</th>
            <th scope="col">First</th>
            <th scope="col">Last</th>
            <th scope="col">Handle</th>
            </tr>
        </thead>
        <tbody>
            <tr>
            <th scope="row">1</th>
            <td>Mark</td>
            <td>Otto</td>
            <td>@mdo</td>
            </tr>
            <tr>
            <th scope="row">2</th>
            <td>Jacob</td>
            <td>Thornton</td>
            <td>@fat</td>
            </tr>
            <tr>
            <th scope="row">3</th>
            <td colspan="2">Larry the Bird</td>
            <td>@twitter</td>
            </tr>
        </tbody>
        </table>
    </div>
</div>
@endsection
