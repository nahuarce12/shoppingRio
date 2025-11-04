@extends('emails.layout')

@section('content')
    <h2>Nueva solicitud de descuento</h2>
    
    <div class="info-box">
        <strong>🔔 Un cliente quiere usar tu promoción</strong>
    </div>

    <p>Un cliente de ShoppingRio ha solicitado aplicar uno de tus descuentos. A continuación, los detalles de la solicitud:</p>

    <div class="success-box">
        <strong>👤 Cliente:</strong> {{ $clientName }}<br>
        <strong>📧 Email:</strong> {{ $clientEmail }}<br>
        <strong>⭐ Categoría:</strong> {{ $clientCategory }}<br>
        <strong>📅 Fecha solicitud:</strong> {{ $requestDate }}
    </div>

    <div class="info-box">
        <strong>🎯 Promoción solicitada:</strong><br>
        {{ $promotionText }}<br><br>
        <strong>📍 Local:</strong> {{ $storeName }}<br>
        <strong>📋 Código promoción:</strong> #{{ $promotionCode }}
    </div>

    <p><strong>Acción requerida:</strong> Debes aceptar o rechazar esta solicitud desde tu panel de control.</p>

    <div class="warning-box">
        <strong>Recuerda verificar:</strong>
        <ul>
            <li>Que el cliente se presente en tu local</li>
            <li>Que cumpla con las condiciones de la promoción</li>
            <li>Que sea el día válido según tu configuración</li>
            <li>Que realice la compra para aplicar el descuento</li>
        </ul>
    </div>

    <p style="text-align: center;">
        <a href="{{ $acceptUrl }}" class="btn" style="background-color: #28a745;">✅ Aceptar Solicitud</a>
        <a href="{{ $rejectUrl }}" class="btn" style="background-color: #dc3545;">❌ Rechazar Solicitud</a>
    </p>

    <p style="text-align: center;">
        <small>También puedes gestionar la solicitud desde tu <a href="{{ $dashboardUrl }}">Panel de Control</a></small>
    </p>

    <p><small><strong>Nota:</strong> Una vez que aceptes o rechaces la solicitud, el cliente recibirá una notificación automática.</small></p>
@endsection
