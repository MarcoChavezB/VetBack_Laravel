<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Activación de Cuenta</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: auto;
            text-align: center;
            background-color: #fff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            margin-top: 50px;
        }
        h2 {
            color: #446dff;
        }
        p {
            color: #333;
        }
        a {
            display: inline-block;
            color: #000000;
            background-color: #446dff;
            padding: 10px 20px;
            margin: 20px 0;
            border-radius: 5px;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Bienvenido!</h2>
    <p>Hola {{$user['nombre']}},</p>
    <p>Por favor haz clic en el enlace de abajo para verificar tu correo electrónico y activar tu cuenta.</p>
    <a href="{{$url}}">Verificar Correo Electrónico</a>
</div>
</body>
</html>
