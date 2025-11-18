@extends('layouts.dashboard')

@php
use Illuminate\Support\Str;
@endphp

@section('title', 'Mis promociones - Panel de Dueño')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1"><i class="bi bi-tag"></i> Mis promociones</h1>
            <p class="text-muted mb-0">Administrá las promociones activas y pendientes de tu local.</p>
        </div>
        <a href="{{ route('store.promotions.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Crear promoción
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <h2 class="h3 mb-1">{{ $stats['total'] }}</h2>
                    <p class="text-muted mb-0">Total de promociones</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <h2 class="h3 mb-1">{{ $stats['aprobada'] }}</h2>
                    <p class="text-muted mb-0">Aprobadas</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <h2 class="h3 mb-1">{{ $stats['pendiente'] }}</h2>
                    <p class="text-muted mb-0">En revisión</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <h2 class="h3 mb-1">{{ $stats['active'] }}</h2>
                    <p class="text-muted mb-0">Activas hoy</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form class="row g-3 align-items-end" method="GET" action="{{ route('store.promotions.index') }}">
                <div class="col-lg-4">
                    <label class="form-label" for="search">Buscar</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Descripción de la promoción">
                </div>
                <div class="col-md-3 col-lg-2">
                    <label class="form-label" for="status">Estado</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Todos</option>
                        @foreach(['pendiente' => 'Pendiente', 'aprobada' => 'Aprobada', 'denegada' => 'Denegada'] as $value => $label)
                            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-lg-2">
                    <label class="form-label" for="start_date">Desde</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3 col-lg-2">
                    <label class="form-label" for="end_date">Hasta</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-3 col-lg-2 d-grid">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-funnel"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Categoría mínima</th>
                            <th>Vigencia</th>
                            <th>Estado</th>
                            <th>Solicitudes aceptadas</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($promotions as $promotion)
                            <tr>
                                <td>#{{ str_pad($promotion->code, 4, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ Str::limit($promotion->description, 80) }}</td>
                                <td><span class="badge badge-{{ strtolower($promotion->minimum_category) }}">{{ $promotion->minimum_category }}</span></td>
                                <td>
                                    <div>{{ $promotion->start_date->format('d/m/Y') }} - {{ $promotion->end_date->format('d/m/Y') }}</div>
                                </td>
                                <td>
                                    @php
                                        $badgeClass = match($promotion->status) {
                                            'aprobada' => 'bg-success',
                                            'pendiente' => 'bg-warning text-dark',
                                            'denegada' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ ucfirst($promotion->status) }}</span>
                                </td>
                                <td>{{ $promotion->accepted_usages_count ?? 0 }}</td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('store.promotions.show', $promotion) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <form action="{{ route('store.promotions.destroy', $promotion) }}" method="POST" onsubmit="return confirm('¿Eliminar esta promoción?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-info-circle"></i> Aún no creaste promociones.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{ $promotions->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
