<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuenta Aprobada</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 0 0 5px 5px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>¡Felicitaciones!</h1>
    </div>
    <div class="content">
        <h2>Hola {{ $userName }},</h2>
        
        <p>Nos complace informarte que tu solicitud para ser <strong>Dueño de Local</strong> en ShoppingRio ha sido <strong>aprobada</strong>.</p>
        
        <p>Ya puedes acceder a tu cuenta y comenzar a gestionar las promociones de tu local.</p>
        
        <h3>Datos de tu cuenta:</h3>
        <ul>
            <li><strong>Email:</strong> {{ $userEmail }}</li>
            <li><strong>Rol:</strong> Dueño de Local</li>
        </ul>
        
        <h3>Próximos pasos:</h3>
        <ol>
            <li>Inicia sesión en la plataforma</li>
            <li>Accede a tu panel de control</li>
            <li>Comienza a crear promociones para tu local</li>
            <li>Gestiona las solicitudes de descuento de los clientes</li>
        </ol>
        
        <div style="text-align: center;">
            <a href="{{ $loginUrl }}" class="button">Iniciar Sesión</a>
        </div>
        
        <p>Si tienes alguna pregunta o necesitas ayuda, no dudes en contactarnos.</p>
        
        <p>¡Bienvenido a ShoppingRio!</p>
        
        <p>Saludos,<br>
        <strong>Equipo de ShoppingRio</strong></p>
    </div>
    <div class="footer">
        <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
    </div>
</body>
</html>
