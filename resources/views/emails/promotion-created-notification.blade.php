<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nueva Promoción Pendiente</title>
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
        <h1>Nueva Promoción Pendiente</h1>
    </div>
    <div class="content">
        <p>Hola Administrador,</p>
        <p>Se ha creado una nueva promoción que requiere tu aprobación en <strong>ShoppingRio</strong>.</p>
        
        <div class="details">
            <h3>Detalles de la Promoción:</h3>
            <p><strong>Descripción:</strong> {{ $promotion->description }}</p>
            <p><strong>Local:</strong> {{ $storeName }}</p>
            <p><strong>Dueño:</strong> {{ $storeOwner }}</p>
            <p><strong>Categoría de Cliente:</strong> {{ $promotion->client_category }}</p>
            <p><strong>Vigencia:</strong> {{ $promotion->start_date->format('d/m/Y') }} al {{ $promotion->end_date->format('d/m/Y') }}</p>
            <p><strong>Estado:</strong> Pendiente de Aprobación</p>
        </div>
        
        <p>Por favor, inicia sesión en el panel de administrador para revisar y aprobar o rechazar esta promoción.</p>
        
        <a href="{{ route('admin.dashboard') }}" class="btn">Ir al Panel de Administración</a>
    </div>
    <div class="footer">
        <p>ShoppingRio Admin</p>
    </div>
</body>
</html>
