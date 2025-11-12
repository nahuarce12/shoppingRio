@extends('layouts.dashboard')

@php
use Illuminate\Support\Str;
@endphp

@section('title', 'Historial de solicitudes - Panel de Dueño')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1"><i class="bi bi-clock-history"></i> Historial de solicitudes</h1>
            <p class="text-muted mb-0">Todas las solicitudes realizadas por los clientes para tus promociones.</p>
        </div>
        <a href="{{ route('store.dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver al dashboard
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Cliente</th>
                            <th>Promoción</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usageHistory as $usage)
                            <tr>
                                <td>
                                    <strong>{{ $usage->client->name }}</strong><br>
                                    <small class="text-muted">{{ $usage->client->email }}</small>
                                </td>
                                <td>{{ Str::limit($usage->promotion->texto, 80) }}</td>
                                <td>{{ optional($usage->fecha_uso)->format('d/m/Y') ?? '-' }}</td>
                                <td>
                                    @if($usage->estado === 'aceptada')
                                        <span class="badge bg-success">Aceptada</span>
                                    @elseif($usage->estado === 'rechazada')
                                        <span class="badge bg-danger">Rechazada</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pendiente</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="bi bi-info-circle"></i> No hay solicitudes registradas aún.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{ $usageHistory->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
