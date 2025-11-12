@extends('emails.layout')

@section('content')
    <h2>Actualización sobre tu solicitud de descuento</h2>
    
    <div class="danger-box">
        <strong>❌ Tu solicitud de descuento ha sido RECHAZADA</strong>
    </div>

    <p>Hola {{ $clientName }},</p>

    <p>Lamentablemente, el local <strong>{{ $storeName }}</strong> ha rechazado tu solicitud para usar la siguiente promoción:</p>

    <div class="info-box">
        <strong>🎯 Promoción:</strong> {{ $promotionText }}<br>
        <strong>📍 Local:</strong> {{ $storeName }}<br>
        <strong> Código:</strong> #{{ $promotionCode }}
    </div>

    <div class="warning-box">
        <strong>Motivo:</strong> {{ $reason }}
    </div>

    <div class="success-box">
        <strong>¡No te desanimes!</strong><br>
        Hay muchas otras promociones disponibles en ShoppingRio. Explora nuestro catálogo y encuentra el descuento perfecto para ti.
    </div>

    <p style="text-align: center;">
        <a href="{{ $promotionsUrl }}" class="btn">Explorar más Promociones</a>
    </p>

    <p>¡Gracias por ser parte de ShoppingRio!</p>
@endsection
