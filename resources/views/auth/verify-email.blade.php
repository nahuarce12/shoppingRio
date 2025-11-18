@extends('layouts.app')

@section('title', 'Verificar Email')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Verificar Dirección de Email</h4>
                </div>
                <div class="card-body">
                    @if (session('status') == 'verification-link-sent')
                        <div class="alert alert-success" role="alert">
                            ¡Se ha enviado un nuevo link de verificación a tu dirección de email!
                        </div>
                    @endif

                    <p class="mb-3">
                        Gracias por registrarte! Antes de comenzar, ¿podrías verificar tu dirección de email haciendo clic en el link que te acabamos de enviar? Si no recibiste el email, con gusto te enviaremos otro.
                    </p>

                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            Reenviar Email de Verificación
                        </button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}" class="mt-3">
                        @csrf
                        <button type="submit" class="btn btn-link text-muted p-0">
                            Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
