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
<div class="container">
    <h1>Hi</h1>
    <p>Click the Activate button and enter the code to complete login</p>
    <p>Code: {{$random}}</p>
    <a class="btn btn-outline-dark" href="{{$url}}" class="btn">Activate</a>
    </div>
</body>
</html>
