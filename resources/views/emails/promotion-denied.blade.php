@extends('emails.layout')

@section('content')
    <h2>Actualización sobre tu promoción</h2>
    
    <div class="danger-box">
        <strong>❌ Tu promoción ha sido DENEGADA</strong>
    </div>

    <p>Lamentamos informarte que el administrador de ShoppingRio ha <strong>denegado</strong> la siguiente promoción de tu local:</p>

    <div class="info-box">
        <strong>📍 Local:</strong> {{ $storeName }}<br>
        <strong>🎯 Promoción:</strong> {{ $promotionText }}<br>
        <strong>� Código:</strong> #{{ $promotionCode }}
    </div>

    <div class="warning-box">
        <strong>Motivo:</strong> {{ $reason }}
    </div>

    <p><strong>Estado actual:</strong> <span class="highlight" style="color: #dc3545;">DENEGADA</span></p>

    <div class="warning-box">
        <strong>Posibles razones:</strong>
        <ul>
            <li>La promoción no cumple con las políticas comerciales del shopping</li>
            <li>El texto descriptivo requiere modificaciones</li>
            <li>Las fechas o condiciones no están alineadas con la estrategia comercial</li>
            <li>Conflicto con otras promociones activas</li>
        </ul>
    </div>

    <p>Te recomendamos:</p>
    <ul>
        <li>Contactar al administrador para conocer los detalles específicos</li>
        <li>Ajustar la promoción según las políticas del shopping</li>
        <li>Crear una nueva promoción con las correcciones necesarias</li>
    </ul>

    <p style="text-align: center;">
        <a href="{{ $dashboardUrl }}" class="btn">Ir a mi Panel</a>
    </p>

    <p>Recuerda que puedes crear nuevas promociones en cualquier momento. El equipo de ShoppingRio está disponible para ayudarte.</p>
@endsection
