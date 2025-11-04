<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'ShoppingRio' }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 3px solid #007bff;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 28px;
        }
        .header p {
            color: #666;
            margin: 5px 0 0 0;
            font-size: 14px;
        }
        .content {
            margin-bottom: 30px;
        }
        .content h2 {
            color: #333;
            font-size: 22px;
            margin-top: 0;
        }
        .content p {
            margin: 15px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background-color: #007bff;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .info-box {
            background-color: #e7f3ff;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .success-box {
            background-color: #d4edda;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .warning-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .danger-box {
            background-color: #f8d7da;
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 12px;
        }
        .footer a {
            color: #007bff;
            text-decoration: none;
        }
        ul {
            padding-left: 20px;
        }
        ul li {
            margin: 8px 0;
        }
        .highlight {
            color: #007bff;
            font-weight: bold;
        }
        strong {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🛍️ ShoppingRio</h1>
            <p>Tu centro comercial de confianza</p>
        </div>

        <div class="content">
            @yield('content')
        </div>

        <div class="footer">
            <p>
                Este es un correo electrónico automático de <strong>ShoppingRio</strong>.<br>
                Por favor, no respondas a este mensaje.
            </p>
            <p>
                <a href="{{ config('app.url') }}">Visitar ShoppingRio</a> | 
                <a href="{{ config('app.url') }}/contacto">Contacto</a>
            </p>
            <p style="margin-top: 15px; color: #999;">
                &copy; {{ date('Y') }} ShoppingRio. Todos los derechos reservados.
            </p>
        </div>
    </div>
</body>
</html>
