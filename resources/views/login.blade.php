<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Project</title>
    @vite('resources/css/app.css')
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
            @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif
            <form method="POST" action="{{ route('login') }}">
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
            @if ($errors->any())
            <div class="alert alert-danger mt-3" role="alert">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </div>
            @endif





        </div>
    </div>
    @vite('resources/js/app.js')
</body>

</html>