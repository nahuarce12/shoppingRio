@extends('emails.layout')

@section('content')
    <h2>¡Bienvenido a ShoppingRio, {{ $clientName }}!</h2>
    
    <p>Gracias por registrarte en nuestro centro comercial. Estás a un paso de acceder a increíbles descuentos y promociones exclusivas.</p>

    <div class="info-box">
        <strong>Para activar tu cuenta, necesitamos verificar tu correo electrónico:</strong><br>
        {{ $clientEmail }}
    </div>

    <p style="text-align: center;">
        <a href="{{ $verificationUrl }}" class="btn">Verificar mi cuenta</a>
    </p>

    <p><small>Este enlace expirará en {{ $expirationMinutes }} minutos.</small></p>

    <div class="success-box">
        <strong>Beneficios de ser miembro verificado:</strong>
        <ul>
            @foreach($benefits as $benefit)
                <li>{{ $benefit }}</li>
            @endforeach
        </ul>
    </div>

    <p>Una vez verificada tu cuenta, comenzarás en la categoría <span class="highlight">Inicial</span> y podrás ascender a <span class="highlight">Medium</span> y <span class="highlight">Premium</span> conforme uses más promociones.</p>

    <p><strong>¿No solicitaste esta cuenta?</strong><br>
    Si no creaste una cuenta en ShoppingRio, simplemente ignora este correo y el enlace expirará automáticamente.</p>
@endsection
