<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

@extends('../home')


@section('title', 'Configuracion - Hotel Project')

@section('content')
@vite('resources/css/info.css')
<div class="height-100 p-5" style="background-color: #EEEEEE">
    <!-- Contenido específico de la vista de usuarios -->
    <h4>Configuracion del Sistema</h4>

  <div class="bg-white rounded-lg shadow-md p-4">
  @if(isset($hotel->id))
  <form method="POST" action="{{ route('updateHotel', ['id' => $hotel->id]) }}">
    @csrf
    @method('PUT')

      <div class="mb-4">
        <label for="nombre" class="block font-medium mb-1 ">
          Nombre 
        </label>
        <br>
        <input
          id="nombre"
          name="nombre"
          type="text"
          placeholder="Enter hotel name"
          required=""
          value="{{ $hotel->nombre ?? '' }}"
          
        />
      </div>
      <div class="mb-4">
        <label for="direccion" class="block font-medium mb-1">
        Direccion
        </label>
        <br>
        <input
          id="direccion"
          name="direccion"
          placeholder="Enter hotel address"
          required=""
          type="text"
          value="{{ $hotel->direccion ?? '' }}"
        />
      </div>
      <div class="mb-4">
        <label for="email" class="block font-medium mb-1">
          Email
        </label>
        <br>
        <input
          id="email"
          name="email"
          type="email"
          placeholder="Enter hotel email"
          required=""
          value="{{ $hotel->email ?? '' }}"
        />
      </div>
      <div class="mb-4">
        <label for="telefono" class="block font-medium mb-1">
        Telefono
        </label>
        <br>
        <input
          id="telefono"
          name="telefono"
          type="tel"
          placeholder="Enter hotel phone number"
          required=""
          value="{{ $hotel->telefono ?? '' }}"
        />
      </div>
      <div class="mb-4 grid grid-cols-2 gap-4">
        <div>
          <label for="checkin" class="block font-medium mb-1">
            Check-in
          </label>
          <input
            id="checkin"
            name="checkin"
            type="time"
            step="3600"
            required=" "
            value="{{ isset($hotel->checkin) ? (new Carbon\Carbon($hotel->checkin))->format('H:i') : '' }}"
          />
        
          <label for="checkout" class="block font-medium mb-1">
            Check-out
          </label>
          <input
            id="checkout"
            name="checkout"
            type="time"
            step="3600"
            required=""
            value="{{ isset($hotel->checkout) ? (new Carbon\Carbon($hotel->checkout))->format('H:i') : '' }}"
          />
        </div>
      </div>
      <div class="mb-4">
        <label for="descripcion" class="block font-medium mb-1">
        Descripcion
        </label>
        <br>
        <textarea
          id="descripcion"
          name="descripcion"
          placeholder="Enter hotel description"
          required=""
          rows="4"
          
        >{{ $hotel->descripcion ?? '' }}</textarea>
      </div>
      <button type="submit">Guardar</button>
      
    </form>
    @else
    <p>Hotel no especificado.</p>
   @endif
  </div>
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

    
</div>
@endsection
