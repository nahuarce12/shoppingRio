@extends('emails.layout')

@section('content')
    <h2>¡Tu descuento ha sido aceptado!</h2>
    
    <div class="success-box">
        <strong>✅ El local ha ACEPTADO tu solicitud de descuento</strong>
    </div>

    <p>Hola {{ $clientName }},</p>

    <p>Tenemos excelentes noticias: el local <strong>{{ $storeName }}</strong> ha aceptado tu solicitud para usar la siguiente promoción:</p>

    <div class="info-box">
        <strong>🎯 Promoción:</strong> {{ $promotionText }}<br>
        <strong>📍 Local:</strong> {{ $storeName }} - {{ $storeLocation }}<br>
        <strong>📅 Aceptada el:</strong> {{ $usageDate }}<br>
        <strong>📋 Código:</strong> #{{ $promotionCode }}
    </div>

    <p><strong>¿Qué sigue?</strong></p>
    <ul>
        <li>Dirígete al local <strong>{{ $storeName }}</strong> ubicado en <strong>{{ $storeLocation }}</strong></li>
        <li>Menciona que tienes un descuento aprobado (puedes mostrar este email)</li>
        <li>El personal del local aplicará la promoción a tu compra</li>
    </ul>

    <div class="warning-box">
        <strong>⚠️ Importante:</strong>
        <ul>
            <li>Esta promoción solo puede usarse UNA VEZ</li>
            <li>Válida hasta: {{ $validUntil }}</li>
            <li>El descuento debe aplicarse en la misma visita</li>
            <li>Sujeto a términos y condiciones del local</li>
        </ul>
    </div>

    <p>Recuerda que usando promociones en ShoppingRio, acumulas experiencia para subir de categoría y acceder a descuentos aún mejores.</p>

    <p style="text-align: center;">
        <a href="{{ $promotionsUrl }}" class="btn">Ver más Promociones</a>
    </p>

    <p>¡Disfruta tu descuento y sigue explorando las ofertas de ShoppingRio!</p>
@endsection
