@extends('emails.layout')

@section('content')
    <h2>¡Felicitaciones por tu ascenso!</h2>
    
    <div class="success-box">
        <strong>🎉 Has subido de categoría en ShoppingRio</strong>
    </div>

    <p>Hola {{ $clientName }},</p>

    <p>Tenemos excelentes noticias: gracias a tu actividad constante en ShoppingRio, has sido <strong>promovido a una nueva categoría</strong>.</p>

    <div class="info-box" style="text-align: center; font-size: 20px; padding: 25px;">
        <strong>Categoría anterior:</strong> <span style="color: #666;">{{ $oldCategory }}</span>
        <br><br>
        <span style="font-size: 36px;">⬇️</span>
        <br><br>
        <strong>Nueva categoría:</strong> <span class="highlight" style="font-size: 24px;">{{ $newCategory }}</span>
    </div>

    <p><strong>¿Qué significa esto para ti?</strong></p>

    <ul>
        <li><strong>Más descuentos disponibles:</strong> Ahora tienes acceso a todas las promociones de categoría {{ $newCategory }} y categorías inferiores</li>
        <li><strong>Ofertas exclusivas:</strong> Algunos locales crean promociones especiales solo para tu categoría</li>
        <li><strong>Prioridad en novedades:</strong> Recibirás información privilegiada sobre nuevas promociones</li>
        @if($newCategory === 'Premium')
            <li><strong>Acceso total:</strong> Como cliente Premium, puedes acceder a TODAS las promociones del shopping</li>
        @endif
    </ul>

    <div class="success-box">
        <strong>Promociones disponibles para ti:</strong><br>
        Ahora tienes acceso a <strong>{{ $promotionCount }}</strong> promociones activas.<br>
        @if($newCategory === 'Premium')
            ¡Has alcanzado el nivel máximo!
        @else
            Sigue usando promociones para alcanzar la siguiente categoría
        @endif
    </div>

    <p style="text-align: center;">
        <a href="{{ $promotionsUrl }}" class="btn">Explorar Nuevas Promociones</a>
    </p>

    <p><strong>Progresión de categorías en ShoppingRio:</strong></p>
    <ul>
        <li><strong>Inicial:</strong> Categoría de ingreso para nuevos clientes</li>
        <li><strong>Medium:</strong> Para clientes activos con múltiples compras</li>
        <li><strong>Premium:</strong> Acceso VIP a todas las promociones exclusivas</li>
    </ul>

    <p>¡Gracias por ser un cliente tan activo! Esperamos que sigas disfrutando de las ofertas de ShoppingRio.</p>
@endsection
