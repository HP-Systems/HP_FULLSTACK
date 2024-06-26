<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecimiento de Contraseña</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f0f0; /* Color de fondo personalizado */
            font-family: Arial, sans-serif;
        }
        .container {
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 32px;
            max-width: 400px;
            width: 100%;
        }
        .header {
            text-align: center;
            margin-bottom: 24px;
            color: #222831;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .content {
            color: #222831;
        }
        .content p {
            margin: 8px 0;
        }
        .password-box {
            color: #fff;
            background-color: #76ABAE; 
            border-radius: 4px;
            padding: 16px;
            text-align: center;
            font-weight: bold;
            font-size: 19px;
            
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Envío de Contraseña</h1>
    </div>
    <div class="content">
    <p>Estimado personal,</p>
    <p>Ha sido registrado con éxito. Su nueva contraseña es:</p>
        <div class="password-box">
            {{ $password }}
        </div>
        
    </div>
</div>
</body>
</html>