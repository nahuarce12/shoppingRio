@extends('layouts.dashboard')

@section('title', 'Solicitudes Pendientes - Panel de Dueño')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>
                    <i class="bi bi-inbox-fill"></i> Solicitudes de Promociones
                </h2>
                <a href="{{ route('store.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Volver al Dashboard
                </a>
            </div>

            @if($pendingUsages->count() > 0)
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    Tenés <strong>{{ $pendingUsages->count() }}</strong> {{ Str::plural('solicitud', $pendingUsages->count()) }} pendiente{{ $pendingUsages->count() > 1 ? 's' : '' }} de aprobación.
                </div>

                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Cliente</th>
                                        <th>Promoción</th>
                                        <th>Categoría</th>
                                        <th>Fecha Solicitud</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingUsages as $usage)
                                    <tr>
                                        <td>
                                            <div>
                                                <strong>{{ $usage->client->name }}</strong>
                                            </div>
                                            <small class="text-muted">{{ $usage->client->email }}</small>
                                        </td>
                                        <td>
                                            <div class="promotion-text">
                                                {{ Str::limit($usage->promotion->texto, 60) }}
                                            </div>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar"></i> 
                                                Válida hasta: {{ $usage->promotion->fecha_hasta->format('d/m/Y') }}
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ strtolower($usage->client->categoria_cliente) }} badge-category">
                                                {{ $usage->client->categoria_cliente }}
                                            </span>
                                        </td>
                                        <td>
                                            <div>{{ $usage->created_at->format('d/m/Y') }}</div>
                                            <small class="text-muted">{{ $usage->created_at->diffForHumans() }}</small>
                                        </td>
                                        <td class="text-center">
                                            <x-approval-buttons
                                                :itemId="$usage->id"
                                                :approveRoute="route('store.promotion-usages.accept', $usage->id)"
                                                :rejectRoute="route('store.promotion-usages.reject', $usage->id)"
                                                approveText="Aceptar"
                                                rejectText="Rechazar"
                                                itemType="solicitud"
                                                :showRejectReason="true"
                                            />
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-success">
                    <i class="bi bi-check-circle"></i>
                    No tenés solicitudes pendientes en este momento.
                </div>

                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-inbox display-1 text-muted"></i>
                        <h4 class="mt-3">No hay solicitudes pendientes</h4>
                        <p class="text-muted">Las solicitudes de uso de promociones aparecerán aquí cuando los clientes las soliciten.</p>
                    </div>
                </div>
            @endif

            @if(isset($allUsages) && $allUsages->count() > 0)
                <div class="mt-5">
                    <h4 class="mb-3">
                        <i class="bi bi-clock-history"></i> Historial de Solicitudes
                    </h4>
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Cliente</th>
                                            <th>Promoción</th>
                                            <th>Fecha</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($allUsages as $usage)
                                        <tr>
                                            <td>{{ $usage->client->name }}</td>
                                            <td>{{ Str::limit($usage->promotion->texto, 50) }}</td>
                                            <td>{{ $usage->fecha_uso->format('d/m/Y') }}</td>
                                            <td>
                                                @if($usage->estado === 'aceptada')
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle"></i> Aceptada
                                                    </span>
                                                @elseif($usage->estado === 'rechazada')
                                                    <span class="badge bg-danger">
                                                        <i class="bi bi-x-circle"></i> Rechazada
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning">
                                                        <i class="bi bi-clock"></i> Enviada
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        {{ $allUsages->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
