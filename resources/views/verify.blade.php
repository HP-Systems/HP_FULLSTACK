<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Project - Verificación</title>
    @vite('resources/css/app.css')
    <link rel="icon" href="{{ asset('images/logo.jpg') }}" type="image/jpg">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
</head>
<body class="d-flex justify-content-center align-items-center vh-100 custom-bg">
    <div class="card shadow d-flex flex-column justify-content-center align-items-center" style="width: 30rem; height: 20rem;">
        <div class="card-body text-center d-flex flex-column justify-content-center align-items-center">
            <h3 class="card-title">Verificación de cuenta</h3>
            <p class="card-text">Ingresa el código que enviamos a tu correo electrónico.</p>
            <form method="POST" action="{{ url('/verifyNumber') }}">
                @csrf
                <div class="d-flex justify-content-between mb-3">
                    @for ($i = 1; $i <= 6; $i++)
                        <input type="text" class="form-control text-center verification-code" maxlength="1" style="width: 50px;" required>
                    @endfor
                    <input type="hidden" name="verification_code" id="verification_code">
                </div>
                <button type="submit" class="btn btn-custom w-100">VERIFICAR</button>
            </form>
        </div>
    </div>
    @vite('resources/js/app.js')
    @vite('resources/js/custom.js')
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
