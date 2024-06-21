<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código de Verificación para Iniciar Sesión</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #EEEEEE;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 650px;
            margin: 50px auto;
            padding: 10px;
            background-color: #FFFFFF;
            border: 50px solid #EEEEEE;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .logo {
            width: 50px;
            height: 50px;
            margin-bottom: 20px;
        }
        .greeting {
            font-size: 1.5rem;
            color: #333;
        }
        .message {
            font-size: 1rem;
            color: #555;
            margin: 20px 0;
        }
        .verification-code {
            font-size: 2rem;
            font-weight: bold;
            color: #fff;
            background-color: #76ABAE;
            padding: 10px 50px;
            border-radius: 5px;
            display: inline-block;
        }
        .note {
            font-size: 0.9rem;
            color: #777;
            margin-top: 15px;
        }
        .btn{
            display: inline-block; 
            padding: 10px 10px; 
            color: #EEEEEE !important; 
            background-color: #31363F; 
            text-decoration: none; 
            border-radius: 5px;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="greeting">Hola!</h2>
        <p class="message">Hemos recibido una solicitud para iniciar sesión en su cuenta. Su código de verificación es:</p>
        <div class="verification-code">{{$random}}</div>
        <p class="note">Por favor, ingrese este código en la página para completar el proceso.</p>
        <a href="{{$url}}" class="btn">VERIFICAR CUENTA</a>
    </div>
</body>
</html>

