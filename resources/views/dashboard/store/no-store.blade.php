@extends('layouts.dashboard')

@section('title', 'Sin local asignado')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-shop display-4 text-muted"></i>
                    <h1 class="h4 mt-3">Aún no tenés un local asignado</h1>
                    <p class="text-muted mb-4">
                        Para acceder al panel de dueños, un administrador debe asociarte a un local existente.
                        Si ya realizaste la solicitud, aguardá la aprobación. Ante cualquier duda contactá al equipo del shopping.
                    </p>
                    <a href="{{ route('home') }}" class="btn btn-primary">
                        <i class="bi bi-house"></i> Volver al inicio
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
