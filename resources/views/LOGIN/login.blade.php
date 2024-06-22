<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Project</title>
    @vite('resources/css/app.css')
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <link rel="icon" href="{{ asset('images/logo.jpg') }}" type="image/jpg">
</head>

<body class="d-flex justify-content-center align-items-center vh-100 custom-bg">
    <div class="card shadow" style="width: 24rem;">
        <div class="card-body">
            <div class="d-flex justify-content-center mb-4">
                <div class="circle-image">
                    <img src="{{ asset('images/logo.jpg') }}" alt="Logo">
                </div>
            </div>
            <form method="POST" action="{{ url('/login') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label"><strong>Correo</strong></label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Ingresa tu correo" required autofocus>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label"><strong>Contraseña</strong></label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Ingresa tu contraseña" required>
                </div>
                <button type="submit" class="btn btn-custom w-100">INICIAR SESIÓN</button>
            </form>
        </div>
    </div>
    @vite('resources/js/app.js')
    @if ($errors->has('error'))
        <script>
            swal({
                title: "Error!",
                text: "{{ $errors->first() }}",
                icon: "error",
            });
        </script>
    @endif
</body>

</html>