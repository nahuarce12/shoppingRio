@extends('layouts.dashboard')

@php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

$profileUpdateRoute = Route::has('store.profile.update') ? route('store.profile.update') : null;
$pendingCount = $usageSummary['pending'] ?? 0;
$totalRequests = $usageSummary['total'] ?? 0;
$acceptedCount = $usageSummary['accepted'] ?? 0;
$rejectedCount = $usageSummary['rejected'] ?? 0;
$uniqueClients = $usageSummary['unique_clients'] ?? 0;
$defaultDays = old('weekdays', array_fill(0, 7, 1));
$ownerUsage = $ownerReport['usage_requests'] ?? [
  'total' => $totalRequests,
  'pending' => $pendingCount,
  'accepted' => $acceptedCount,
  'rejected' => $rejectedCount,
];
$ownerClients = $ownerReport['clients'] ?? [
  'unique_count' => $uniqueClients,
  'by_category' => [],
];
$ownerPromotions = $ownerReport['promotions'] ?? [
  'total' => $promotionStats['total'] ?? 0,
  'approved' => $promotionStats['aprobada'] ?? 0,
  'pending' => $promotionStats['pendiente'] ?? 0,
  'denied' => $promotionStats['denegada'] ?? 0,
];
$topPromotion = $recentPromotions->sortByDesc('accepted_usages_count')->first();
if ($errors->hasAny(['title', 'description', 'start_date', 'end_date', 'minimum_category', 'weekdays'])) {
  $section = 'crear-promocion';
}
@endphp

@section('title', 'Panel de Dueño de Local | Shopping Rosario')
@section('meta_description', 'Gestioná promociones, solicitudes y reportes de tu local desde el panel de dueños de Shopping Rosario.')

@section('content')
<x-layout.breadcrumbs :items="[['label' => 'Panel de Dueño de Local']]" />

<section class="py-4">
  <div class="container">
    <div class="row">
      <div class="col-lg-3 mb-4">
        <div class="card">
          <div class="card-body text-center">
            <div class="mb-3">
              <i class="bi bi-building" style="font-size: 5rem; color: var(--primary-color);"></i>
            </div>
            <h2 class="h5">{{ $store->name }}</h2>
            <p class="text-muted mb-2">Código: {{ str_pad($store->code ?? $store->id, 3, '0', STR_PAD_LEFT) }}</p>
            <p class="text-muted mb-2">{{ auth()->user()->name }} (Dueño/a)</p>
            <span class="badge bg-success">Cuenta Activa</span>

            <hr>

            <div class="d-grid gap-2">
              <button class="btn btn-primary btn-sm" data-section="dashboard" onclick="showSection('dashboard')">
                <i class="bi bi-house"></i> Dashboard
              </button>
              <button class="btn btn-outline-primary btn-sm" data-section="mis-promociones" onclick="showSection('mis-promociones')">
                <i class="bi bi-tag"></i> Mis Promociones
              </button>
              <button class="btn btn-outline-primary btn-sm" data-section="crear-promocion" onclick="showSection('crear-promocion')">
                <i class="bi bi-plus-circle"></i> Crear Promoción
              </button>
              <button class="btn btn-outline-warning btn-sm" data-section="solicitudes" onclick="showSection('solicitudes')">
                <i class="bi bi-inbox"></i> Solicitudes
                @if($pendingCount > 0)
                  <span class="badge bg-danger">{{ $pendingCount }}</span>
                @endif
              </button>
              <button class="btn btn-outline-success btn-sm" data-section="reportes" onclick="showSection('reportes')">
                <i class="bi bi-graph-up"></i> Reportes
              </button>
              <button class="btn btn-outline-secondary btn-sm" data-section="editar-perfil" onclick="showSection('editar-perfil')">
                <i class="bi bi-pencil"></i> Editar Perfil
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-9">
        <div id="dashboard">
          <div class="row mb-4">
            <div class="col-md-3">
              <div class="card text-center">
                <div class="card-body">
                  <i class="bi bi-tag-fill fs-1 text-primary"></i>
                  <h3 class="mt-2">{{ $promotionStats['active'] }}</h3>
                  <p class="mb-0">Promociones Activas</p>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card text-center">
                <div class="card-body">
                  <i class="bi bi-people-fill fs-1 text-success"></i>
                  <h3 class="mt-2">{{ $acceptedCount }}</h3>
                  <p class="mb-0">Promociones Aceptadas</p>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card text-center">
                <div class="card-body">
                  <i class="bi bi-inbox-fill fs-1 text-warning"></i>
                  <h3 class="mt-2">{{ $pendingCount }}</h3>
                  <p class="mb-0">Solicitudes Pendientes</p>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card text-center">
                <div class="card-body">
                  <i class="bi bi-clock-history fs-1 text-info"></i>
                  <h3 class="mt-2">{{ $promotionStats['pendiente'] }}</h3>
                  <p class="mb-0">En Revisión Admin</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div id="mis-promociones" class="content-section" style="display: block;">
          <div class="card mb-4">
            <div class="card-header bg-success text-white">
              <h2 class="h5 mb-0"><i class="bi bi-tag"></i> Mis Promociones</h2>
            </div>
            <div class="card-body">
              @if($recentPromotions->count() > 0)
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Promoción</th>
                      <th>Categoría</th>
                      <th>Válido hasta</th>
                      <th>Estado</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($recentPromotions as $promo)
                    @php
                      $estadoBadge = match($promo->status) {
                        'pendiente' => 'bg-warning text-dark',
                        'aprobada' => 'bg-success',
                        'denegada' => 'bg-danger',
                        default => 'bg-secondary'
                      };
                      $estadoText = ucfirst($promo->status);
                    @endphp
                    <tr>
                      <td>{{ Str::limit($promo->description, 40) }}</td>
                      <td><span class="badge badge-{{ strtolower($promo->minimum_category) }}">{{ $promo->minimum_category }}</span></td>
                      <td>{{ $promo->end_date->format('d/m/Y') }}</td>
                      <td>
                        <span class="badge {{ $estadoBadge }}">{{ $estadoText }}</span>
                      </td>
                      <td>
                        <form action="{{ route('store.promotions.destroy', $promo->id) }}" method="POST" class="d-inline">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro que deseas eliminar esta promoción?')">
                            <i class="bi bi-trash"></i>
                          </button>
                        </form>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              @else
              <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Aún no has creado promociones. 
                <a href="#" onclick="showSection('crear-promocion'); return false;" class="alert-link">¡Creá tu primera promoción!</a>
              </div>
              @endif
            </div>
          </div>
        </div>

        <div id="crear-promocion" class="content-section" style="display: none;">
          <div class="card mb-4">
            <div class="card-header bg-success text-white">
              <h2 class="h5 mb-0"><i class="bi bi-plus-circle"></i> Crear Nueva Promoción</h2>
            </div>
            <div class="card-body">
              <form method="POST" action="{{ route('store.promotions.store') }}" id="dashboard-promotion-form" novalidate>
                @csrf
                <input type="hidden" name="store_id" value="{{ $store->id }}">
                <div class="row">
                  <div class="col-md-12 mb-3">
                    <label class="form-label">Título de la Promoción *</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" maxlength="100" placeholder="Ej: 2x1 en productos seleccionados, 50% OFF segunda unidad" value="{{ old('title') }}" required>
                    <div class="form-text">Título corto y atractivo que verán los clientes.</div>
                    @error('title')
                      <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-12 mb-3">
                    <label class="form-label">Descripción de la Promoción *</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="3" maxlength="200" placeholder="Incluí detalles, condiciones y restricciones de la promoción" required>{{ old('description') }}</textarea>
                    <div class="form-text"><span id="dashboard-char-count">{{ strlen(old('description', '')) }}</span>/200 caracteres. Incluí condiciones y restricciones.</div>
                    @error('description')
                      <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-4 mb-3">
                    <label class="form-label">Categoría de Cliente *</label>
                    <select class="form-select @error('minimum_category') is-invalid @enderror" name="minimum_category" required>
                      <option value="">Seleccionar categoría...</option>
                      <option value="Inicial" {{ old('minimum_category') === 'Inicial' ? 'selected' : '' }}>Inicial</option>
                      <option value="Medium" {{ old('minimum_category') === 'Medium' ? 'selected' : '' }}>Medium</option>
                      <option value="Premium" {{ old('minimum_category') === 'Premium' ? 'selected' : '' }}>Premium</option>
                    </select>
                    @error('minimum_category')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-4 mb-3">
                    <label class="form-label">Válido Desde *</label>
                    <input type="date" class="form-control @error('start_date') is-invalid @enderror" name="start_date" value="{{ old('start_date', now()->format('Y-m-d')) }}" min="{{ now()->format('Y-m-d') }}" required>
                    @error('start_date')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-4 mb-3">
                    <label class="form-label">Válido Hasta *</label>
                    <input type="date" class="form-control @error('end_date') is-invalid @enderror" name="end_date" value="{{ old('end_date') }}" min="{{ now()->format('Y-m-d') }}" required>
                    @error('end_date')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-12 mb-3">
                    <label class="form-label">Días de la Semana *</label>
                    <div class="d-flex flex-wrap gap-2">
                      @foreach (['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'] as $index => $dia)
                      <div class="form-check form-check-inline">
                        <input type="hidden" name="weekdays[{{ $index }}]" value="0">
                        <input class="form-check-input day-checkbox @error('weekdays') is-invalid @enderror" type="checkbox" id="dashboard-dia-{{ $index }}" name="weekdays[{{ $index }}]" value="1" {{ (!empty($defaultDays[$index]) && (int) $defaultDays[$index] === 1) ? 'checked' : '' }}>
                        <label class="form-check-label" for="dashboard-dia-{{ $index }}">{{ $dia }}</label>
                      </div>
                      @endforeach
                    </div>
                    <div id="dashboard-days-error" class="invalid-feedback" style="display: none;">
                      Debes seleccionar al menos un día.
                    </div>
                    @error('weekdays')
                      <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="alert alert-info">
                  <i class="bi bi-info-circle"></i> La promoción será enviada al administrador para su aprobación.
                </div>
                <div class="d-flex justify-content-end gap-2">
                  <button type="reset" class="btn btn-outline-secondary">Limpiar</button>
                  <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-lg"></i> Crear Promoción
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div id="solicitudes" class="content-section" style="display: none;">
          <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
              <h2 class="h5 mb-0"><i class="bi bi-inbox"></i> Solicitudes de Clientes Pendientes</h2>
            </div>
            <div class="card-body">
              @if($pendingRequests->count() > 0)
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Cliente</th>
                      <th>Promoción</th>
                      <th>Fecha Solicitud</th>
                      <th class="text-center">Código QR</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($pendingRequests as $solicitud)
                    <tr>
                      <td>
                        <i class="bi bi-person-circle"></i> {{ $solicitud->client->name }}<br>
                        <small class="text-muted">{{ $solicitud->client->email }}</small>
                      </td>
                      <td>{{ Str::limit($solicitud->promotion->description, 40) }}</td>
                      <td>{{ $solicitud->usage_date->format('d/m/Y H:i') }}</td>
                      <td class="text-center">
                        @if($solicitud->code_qr)
                          <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#qrModalStore{{ $solicitud->id }}">
                            <i class="bi bi-qr-code"></i>
                          </button>
                        @else
                          <small class="text-muted">—</small>
                        @endif
                      </td>
                      <td class="d-flex gap-2">
                        <form action="{{ route('store.promotion-usages.accept', $solicitud->id) }}" method="POST" class="d-inline">
                          @csrf
                          <button type="submit" class="btn btn-success btn-sm">
                            <i class="bi bi-check-lg"></i> Aceptar
                          </button>
                        </form>
                        <form action="{{ route('store.promotion-usages.reject', $solicitud->id) }}" method="POST" class="d-inline">
                          @csrf
                          <button type="submit" class="btn btn-danger btn-sm">
                            <i class="bi bi-x-lg"></i> Rechazar
                          </button>
                        </form>
                      </td>
                    </tr>

                    <!-- Modal QR para dueño -->
                    @if($solicitud->code_qr)
                    <div class="modal fade" id="qrModalStore{{ $solicitud->id }}" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                          <div class="modal-header bg-secondary text-white">
                            <h5 class="modal-title"><i class="bi bi-qr-code"></i> Código QR - Solicitud</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body text-center">
                            <div class="mb-3">
                              <img src="{{ $solicitud->getQrCodeBase64() }}" alt="QR Code" class="img-fluid" style="max-width: 250px;">
                            </div>
                            <div class="alert alert-secondary mb-2">
                              <strong>Código:</strong> <code>{{ $solicitud->code_qr }}</code>
                            </div>
                            <p class="mb-1"><strong>Cliente:</strong> {{ $solicitud->client->name }}</p>
                            <p class="text-muted"><small>{{ $solicitud->client->email }}</small></p>
                          </div>
                        </div>
                      </div>
                    </div>
                    @endif
                    @endforeach
                  </tbody>
                </table>
              </div>
              @else
              <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> No hay solicitudes pendientes en este momento.
              </div>
              @endif
            </div>
          </div>
        </div>

        <div id="reportes" class="content-section" style="display: none;">
          <div class="card mb-4">
            <div class="card-header bg-info text-white">
              <h2 class="h5 mb-0"><i class="bi bi-graph-up"></i> Reportes de Uso de Promociones</h2>
            </div>
            <div class="card-body">
              <div class="row mb-4">
                <div class="col-md-6">
                  <label class="form-label">Período</label>
                  <select class="form-select" id="reportPeriodFilter">
                    <option value="1">Último mes</option>
                    <option value="3" selected>Últimos 3 meses</option>
                    <option value="6">Últimos 6 meses</option>
                    <option value="12">Último año</option>
                  </select>
                </div>
              </div>

              <h3 class="h6">Resumen del Período</h3>
              <div class="row text-center mb-4">
                <div class="col-md-3">
                  <h4>{{ $ownerClients['unique_count'] ?? $uniqueClients }}</h4>
                  <p>Clientes Únicos</p>
                </div>
                <div class="col-md-3">
                  <h4>{{ $ownerUsage['accepted'] ?? 0 }}</h4>
                  <p>Promociones Aceptadas</p>
                </div>
                <div class="col-md-3">
                  @php
                    $totalReq = $ownerUsage['total'] ?? 0;
                    $acceptRate = $totalReq > 0 ? round((($ownerUsage['accepted'] ?? 0) / $totalReq) * 100, 2) : 0;
                  @endphp
                  <h4>{{ $acceptRate }}%</h4>
                  <p>Tasa de Aprobación</p>
                </div>
                <div class="col-md-3">
                  <h4>{{ $ownerPromotions['approved'] ?? 0 }}</h4>
                  <p>Promociones Aprobadas</p>
                </div>
              </div>

              @if(!empty($ownerClients['by_category']))
                <h3 class="h6">Clientes por categoría</h3>
                <div class="row text-center mb-4">
                  @foreach($ownerClients['by_category'] as $category => $count)
                    <div class="col-md-4">
                      <h4>{{ $count }}</h4>
                      <p>{{ ucfirst(strtolower($category)) }}</p>
                    </div>
                  @endforeach
                </div>
              @endif

              <h3 class="h6">Promoción Más Usada</h3>
              @if($topPromotion && $topPromotion->accepted_usages_count > 0)
                <div class="alert alert-success">
                  <strong>{{ Str::limit($topPromotion->description, 80) }}</strong><br>
                  Utilizada {{ $topPromotion->accepted_usages_count }} {{ Str::plural('vez', $topPromotion->accepted_usages_count) }}.
                </div>
              @else
                <div class="alert alert-info">
                  Aún no hay promociones con usos aceptados en el período analizado.
                </div>
              @endif

              <h3 class="h6">Historial reciente de solicitudes</h3>
              @if($recentUsageHistory->count() > 0)
                <div class="table-responsive">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>Cliente</th>
                        <th>Promoción</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($recentUsageHistory as $usage)
                        <tr>
                          <td>
                            <strong>{{ $usage->client?->name ?? 'Cliente eliminado' }}</strong><br>
                            <small class="text-muted">{{ $usage->client?->email ?? 'Sin email' }}</small>
                          </td>
                          <td>{{ Str::limit($usage->promotion?->description ?? 'Promoción eliminada', 60) }}</td>
                          <td>
                            @switch($usage->status)
                              @case('aceptada')
                                <span class="badge bg-success">Aceptada</span>
                                @break
                              @case('rechazada')
                                <span class="badge bg-danger">Rechazada</span>
                                @break
                              @case('enviada')
                                <span class="badge bg-warning text-dark">Pendiente</span>
                                @break
                              @default
                                <span class="badge bg-secondary">{{ ucfirst($usage->status ?? 'desconocido') }}</span>
                            @endswitch
                          </td>
                          <td>{{ optional($usage->usage_date)->format('d/m/Y') ?? $usage->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
                <p class="text-muted small mb-0">Mostrando las 10 solicitudes más recientes. Para un detalle más amplio contactá al administrador.</p>
              @else
                <div class="alert alert-info">
                  <i class="bi bi-info-circle"></i> Aún no registramos solicitudes para tus promociones.
                </div>
              @endif
            </div>
          </div>
        </div>

        <div id="editar-perfil" class="content-section" style="display: none;">
          <div class="card">
            <div class="card-header bg-secondary text-white">
              <h2 class="h5 mb-0"><i class="bi bi-pencil"></i> Editar Perfil del Local</h2>
            </div>
            <div class="card-body">
              <form action="{{ $profileUpdateRoute ?? '#' }}" method="POST" @if(!$profileUpdateRoute) onsubmit="event.preventDefault();" @endif>
                @csrf
                @method('PATCH')
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Nombre del Local</label>
                    <input type="text" class="form-control" name="name" value="{{ $store->name }}" readonly>
                    <small class="text-muted">Solo el administrador puede cambiar el nombre del local.</small>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Rubro</label>
                    <input type="text" class="form-control" value="{{ $store->category }}" readonly>
                    <small class="text-muted">Solo el administrador puede cambiar el rubro.</small>
                  </div>
                  <div class="col-md-12 mb-3">
                    <label class="form-label">Ubicación</label>
                    <input type="text" class="form-control" value="{{ $store->location ?? 'No especificada' }}" readonly>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Email del Responsable</label>
                    <input type="email" class="form-control" name="email" value="{{ auth()->user()->email }}">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Teléfono</label>
                    <input type="tel" class="form-control" name="telefono" value="{{ auth()->user()->telefono ?? '' }}" placeholder="(0341) 456-7890">
                  </div>
                </div>

                <h3 class="h6 mt-4">Cambiar Contraseña</h3>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Contraseña Actual</label>
                    <input type="password" class="form-control">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Nueva Contraseña</label>
                    <input type="password" class="form-control">
                  </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                  <button type="button" class="btn btn-secondary">Cancelar</button>
                  <button type="submit" class="btn btn-primary" @if(!$profileUpdateRoute) disabled @endif>
                    <i class="bi bi-check-lg"></i> Guardar Cambios
                  </button>
                </div>
              </form>
              @if(!$profileUpdateRoute)
                <div class="alert alert-warning mt-3" role="alert">
                  <i class="bi bi-exclamation-triangle-fill"></i>
                  Esta sección estará habilitada cuando se configure la ruta de actualización de perfil.
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@push('scripts')
@vite('resources/js/frontoffice/main.js')
@vite('resources/js/frontoffice/perfil-dueno.js')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('dashboard-promotion-form');
  const dayCheckboxes = form ? form.querySelectorAll('.day-checkbox') : [];
  const daysError = document.getElementById('dashboard-days-error');
  const textoField = form ? form.querySelector('textarea[name="description"]') : null;
  const charCounter = document.getElementById('dashboard-char-count');
  const fechaDesde = form ? form.querySelector('input[name="start_date"]') : null;
  const fechaHasta = form ? form.querySelector('input[name="end_date"]') : null;

  const validateDays = () => {
    if (!dayCheckboxes.length) {
      return true;
    }
    const anyChecked = Array.from(dayCheckboxes).some((checkbox) => checkbox.checked);
    if (!anyChecked) {
      if (daysError) {
        daysError.style.display = 'block';
      }
      dayCheckboxes.forEach((checkbox) => checkbox.classList.add('is-invalid'));
      return false;
    }
    if (daysError) {
      daysError.style.display = 'none';
    }
    dayCheckboxes.forEach((checkbox) => checkbox.classList.remove('is-invalid'));
    return true;
  };

  dayCheckboxes.forEach((checkbox) => {
    checkbox.addEventListener('change', validateDays);
  });

  if (form) {
    form.addEventListener('submit', function (event) {
      const isValidDays = validateDays();
      if (!isValidDays) {
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add('was-validated');
    });
  }

  if (textoField && charCounter) {
    const updateCounter = () => {
      charCounter.textContent = textoField.value.length;
    };
    textoField.addEventListener('input', updateCounter);
    updateCounter();
  }

  if (fechaDesde && fechaHasta) {
    fechaDesde.addEventListener('change', () => {
      fechaHasta.min = fechaDesde.value;
      if (fechaHasta.value && fechaHasta.value < fechaDesde.value) {
        fechaHasta.value = fechaDesde.value;
      }
    });
  }

  @if(!empty($section))
    const targetSection = @json($section);
    if (typeof window.showSection === 'function' && targetSection) {
      window.showSection(targetSection);
    }
  @endif
});
</script>
@endpush