@extends('layouts.dashboard')

@php
use Illuminate\Support\Str;
@endphp

@section('title', 'Detalle de promoción - Panel de Dueño')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">
                <i class="bi bi-tag"></i> Promoción #{{ str_pad($promotion->codigo, 4, '0', STR_PAD_LEFT) }}
            </h1>
            <p class="text-muted mb-0">{{ $store->nombre }} — Vigencia {{ $promotion->fecha_desde->format('d/m/Y') }} a {{ $promotion->fecha_hasta->format('d/m/Y') }}</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('store.promotions.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver al listado
            </a>
            <form action="{{ route('store.promotions.destroy', $promotion) }}" method="POST" onsubmit="return confirm('¿Eliminar esta promoción?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">
                    <i class="bi bi-trash"></i> Eliminar
                </button>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="h2 mb-1">{{ ucfirst($promotion->estado) }}</h3>
                    <p class="text-muted mb-0">Estado actual</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="h2 mb-1">{{ $usageStats['total'] }}</h3>
                    <p class="text-muted mb-0">Solicitudes totales</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="h2 mb-1">{{ $usageStats['accepted'] }}</h3>
                    <p class="text-muted mb-0">Aceptadas</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 shadow-sm">
                @php
                    $totalProcessed = ($usageStats['accepted'] ?? 0) + ($usageStats['rejected'] ?? 0);
                    $acceptRate = $totalProcessed > 0 ? round(($usageStats['accepted'] / $totalProcessed) * 100, 2) : 0;
                @endphp
                <div class="card-body text-center">
                    <h3 class="h2 mb-1">{{ $acceptRate }}%</h3>
                    <p class="text-muted mb-0">Tasa de aprobación</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h2 class="h5 mb-0">Detalle de la promoción</h2>
        </div>
        <div class="card-body">
            <p class="lead">{{ $promotion->texto }}</p>
            <div class="row g-3">
                <div class="col-md-4">
                    <small class="text-muted text-uppercase">Categoría mínima</small>
                    <div><span class="badge badge-{{ strtolower($promotion->categoria_minima) }}">{{ $promotion->categoria_minima }}</span></div>
                </div>
                <div class="col-md-4">
                    <small class="text-muted text-uppercase">Vigencia</small>
                    <div>{{ $promotion->fecha_desde->format('d/m/Y') }} — {{ $promotion->fecha_hasta->format('d/m/Y') }}</div>
                </div>
                <div class="col-md-4">
                    <small class="text-muted text-uppercase">Días válidos</small>
                    @php
                        $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
                        $diasValidos = collect($promotion->dias_semana ?? [])->filter()->map(function ($value, $index) use ($dias) {
                            return $dias[$index] ?? 'Día';
                        });
                    @endphp
                    <div>{{ $diasValidos->isNotEmpty() ? $diasValidos->join(', ') : 'Sin días habilitados' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="h5 mb-0"><i class="bi bi-people"></i> Solicitudes de clientes</h2>
                <a href="{{ route('store.dashboard', ['section' => 'reportes']) }}" class="btn btn-sm btn-outline-secondary">
                    Ver resumen
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Cliente</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($promotion->usages as $usage)
                            <tr>
                                <td>
                                    <strong>{{ $usage->client->name }}</strong><br>
                                    <small class="text-muted">{{ $usage->client->email }}</small>
                                </td>
                                <td>
                                    @if($usage->estado === 'aceptada')
                                        <span class="badge bg-success">Aceptada</span>
                                    @elseif($usage->estado === 'rechazada')
                                        <span class="badge bg-danger">Rechazada</span>
                                    @elseif($usage->estado === 'enviada')
                                        <span class="badge bg-warning text-dark">Pendiente</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($usage->estado) }}</span>
                                    @endif
                                </td>
                                <td>{{ optional($usage->fecha_uso)->format('d/m/Y') ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">
                                    <i class="bi bi-info-circle"></i> No hay solicitudes registradas para esta promoción.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
