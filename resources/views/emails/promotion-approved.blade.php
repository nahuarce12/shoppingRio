@extends('emails.layout')

@section('content')
    <h2>¡Buenas noticias para tu promoción!</h2>
    
    <div class="success-box">
        <strong>✅ Tu promoción ha sido APROBADA</strong>
    </div>

    <p>Te informamos que el administrador de ShoppingRio ha <strong>aprobado</strong> la siguiente promoción de tu local:</p>

    <div class="info-box">
        <strong>📍 Local:</strong> {{ $storeName }}<br>
        <strong>🎯 Promoción:</strong> {{ $promotionText }}<br>
        <strong>📅 Vigencia:</strong> Del {{ $startDate }} al {{ $endDate }}<br>
        <strong>👥 Categoría mínima:</strong> {{ $category }}<br>
        <strong>📋 Código:</strong> #{{ $promotionCode }}
    </div>

    <p><strong>Estado actual:</strong> <span class="highlight">APROBADA</span></p>

    <p>Tu promoción ya está visible para los clientes del shopping y comenzarán a llegar solicitudes de uso. Recuerda que recibirás notificaciones por email cada vez que un cliente solicite aplicar este descuento.</p>

    <div class="warning-box">
        <strong>Recuerda:</strong>
        <ul>
            <li>Revisa las solicitudes de descuento desde tu panel</li>
            <li>Acepta o rechaza cada solicitud según corresponda</li>
            <li>Cada cliente puede usar la promoción solo una vez</li>
            <li>Los días válidos de la promoción están configurados según tu definición</li>
        </ul>
    </div>

    <p style="text-align: center;">
        <a href="{{ $dashboardUrl }}" class="btn">Ver mis Promociones</a>
    </p>

    <p>¡Esperamos que esta promoción sea todo un éxito!</p>
@endsection
