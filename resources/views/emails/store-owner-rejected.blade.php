<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud Rechazada</title>
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
            background-color: #f44336;
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
        .reason-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
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
        <h1>Solicitud No Aprobada</h1>
    </div>
    <div class="content">
        <h2>Hola {{ $userName }},</h2>
        
        <p>Lamentamos informarte que tu solicitud para ser <strong>Dueño de Local</strong> en ShoppingRio no ha sido aprobada en esta ocasión.</p>
        
        <div class="reason-box">
            <h3>Motivo:</h3>
            <p>{{ $reason }}</p>
        </div>
        
        <p>Si consideras que ha habido un error o deseas más información sobre esta decisión, puedes contactarnos en:</p>
        
        <p><strong>Email de contacto:</strong> {{ $contactEmail }}</p>
        
        <p>Agradecemos tu interés en formar parte de ShoppingRio y te invitamos a que corrijas los aspectos necesarios para volver a aplicar en el futuro.</p>
        
        <p>Saludos,<br>
        <strong>Equipo de ShoppingRio</strong></p>
    </div>
    <div class="footer">
        <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
    </div>
</body>
</html>
