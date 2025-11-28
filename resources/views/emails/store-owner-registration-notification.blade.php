<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nueva Solicitud de Registro</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4f46e5; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 20px; border: 1px solid #e5e7eb; }
        .details { background: white; padding: 15px; border-radius: 8px; margin: 15px 0; }
        .details p { margin: 8px 0; }
        .btn { display: inline-block; background: #4f46e5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin-top: 15px; }
        .footer { text-align: center; padding: 15px; color: #6b7280; font-size: 14px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Nueva Solicitud de Registro</h1>
    </div>
    <div class="content">
        <p>Hola Administrador,</p>
        <p>Se ha registrado una nueva solicitud para ser <strong>dueño de un local</strong> en ShoppingRio.</p>
        
        <div class="details">
            <h3>Detalles del Solicitante:</h3>
            <p><strong>Nombre:</strong> {{ $storeOwner->name }}</p>
            <p><strong>Email:</strong> {{ $storeOwner->email }}</p>
            <p><strong>Local:</strong> {{ $storeName }} (ID: {{ $storeId }})</p>
        </div>
        
        <p>Por favor, inicia sesión en el panel de administrador para revisar y aprobar o rechazar esta solicitud.</p>
        
        <a href="{{ route('admin.dashboard') }}" class="btn">Ir al Panel de Administración</a>
    </div>
    <div class="footer">
        <p>ShoppingRio Admin</p>
    </div>
</body>
</html>
