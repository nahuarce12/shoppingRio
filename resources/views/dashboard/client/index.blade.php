@extends('layouts.dashboard')

@section('title', 'Mi Perfil - Cliente | Shopping Rosario')
@section('meta_description', 'Consultá tu información personal, promociones y progreso de categoría en Shopping Rosario.')

@section('content')
<x-layout.breadcrumbs :items="[['label' => 'Mi Perfil']]" />

<section class="py-4">
  <div class="container">
    <div class="row">
      <div class="col-lg-3 mb-4">
        <div class="card">
          <div class="card-body text-center">
            <div class="mb-3">
              <i class="bi bi-person-circle" style="font-size: 5rem; color: var(--primary-color);"></i>
            </div>
            <h2 class="h5">{{ $client->name }} {{ $client->apellido }}</h2>
            <p class="text-muted mb-2">{{ $client->email }}</p>
            <span class="badge badge-{{ strtolower($client->client_category) }} badge-category fs-6">Cliente {{ $client->client_category }}</span>

            <hr>

            <div class="d-grid gap-2">
              <button class="btn btn-primary btn-sm" onclick="showClientSection('info-personal')">
                <i class="bi bi-person"></i> Información Personal
              </button>
              <button class="btn btn-outline-primary btn-sm" onclick="showClientSection('mis-promociones')">
                <i class="bi bi-tag"></i> Mis Promociones
              </button>
              <button class="btn btn-outline-primary btn-sm" onclick="showClientSection('mi-progreso')">
                <i class="bi bi-graph-up"></i> Mi Progreso
              </button>
              <button class="btn btn-outline-primary btn-sm" onclick="showClientSection('editar-perfil')">
                <i class="bi bi-pencil"></i> Editar Perfil
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-9">
        <div id="info-personal" class="client-section">
          <div class="card mb-4">
            <div class="card-header bg-primary text-white">
              <h2 class="h5 mb-0"><i class="bi bi-person"></i> Información Personal</h2>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <strong>Nombre Completo:</strong>
                  <p class="mb-0">{{ $client->name }} {{ $client->apellido }}</p>
                </div>
                <div class="col-md-6 mb-3">
                  <strong>Email:</strong>
                  <p class="mb-0">{{ $client->email }}</p>
                </div>
                <div class="col-md-6 mb-3">
                  <strong>Teléfono:</strong>
                  <p class="mb-0">{{ $client->telefono ?? 'No especificado' }}</p>
                </div>
                <div class="col-md-6 mb-3">
                  <strong>Fecha de Nacimiento:</strong>
                  <p class="mb-0">{{ $client->fecha_nacimiento ? $client->fecha_nacimiento->format('d/m/Y') : 'No especificada' }}</p>
                </div>
                <div class="col-md-6 mb-3">
                  <strong>Categoría:</strong>
                  <p class="mb-0"><span class="badge badge-{{ strtolower($client->client_category) }} badge-category">{{ $client->client_category }}</span></p>
                </div>
                <div class="col-md-6 mb-3">
                  <strong>Miembro desde:</strong>
                  <p class="mb-0">{{ $client->created_at->format('F Y') }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div id="mi-progreso" class="client-section" style="display: none;">
          <div class="card mb-4">
            <div class="card-header bg-success text-white">
              <h2 class="h5 mb-0"><i class="bi bi-graph-up"></i> Mi Progreso</h2>
            </div>
            <div class="card-body">
              <div class="row text-center mb-4">
                <div class="col-md-4">
                  <div class="p-3 border rounded">
                    <h3 class="text-primary">{{ $usageStats['aceptada'] }}</h3>
                    <p class="mb-0">Promociones Utilizadas</p>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="p-3 border rounded">
                    <h3 class="text-info">{{ $usageStats['enviada'] }}</h3>
                    <p class="mb-0">Solicitudes Pendientes</p>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="p-3 border rounded">
                    <h3 class="text-success">{{ $availablePromotionsCount }}</h3>
                    <p class="mb-0">Promociones Disponibles</p>
                  </div>
                </div>
              </div>

              <h3 class="h6">Progreso de Categoría</h3>
              @php
                $currentCategory = $client->client_category;
                $totalUsed = $usageStats['aceptada'];
                $mediumThreshold = config('shopping.category_thresholds.medium', 10);
                $premiumThreshold = config('shopping.category_thresholds.premium', 25);
                
                if ($currentCategory === 'Inicial') {
                  $nextThreshold = $mediumThreshold;
                  $nextCategory = 'Medium';
                } elseif ($currentCategory === 'Medium') {
                  $nextThreshold = $premiumThreshold;
                  $nextCategory = 'Premium';
                } else {
                  $nextThreshold = $premiumThreshold;
                  $nextCategory = 'Premium (¡Ya alcanzado!)';
                }
                
                $percentage = ($totalUsed / $nextThreshold) * 100;
                $remaining = max(0, $nextThreshold - $totalUsed);
              @endphp
              
              @if($currentCategory !== 'Premium')
              <div class="progress" style="height: 25px;">
                <div class="progress-bar bg-success" role="progressbar" style="width: {{ min(100, $percentage) }}%;" 
                     aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                  {{ $totalUsed }} de {{ $nextThreshold }} promociones
                </div>
              </div>
              <p class="text-muted mt-2">Te faltan {{ $remaining }} promociones para alcanzar la categoría {{ $nextCategory }}.</p>
              @else
              <div class="alert alert-success">
                <i class="bi bi-trophy-fill"></i> ¡Felicitaciones! Ya alcanzaste la categoría máxima: <strong>Premium</strong>
              </div>
              @endif
            </div>
          </div>
        </div>

        <div id="mis-promociones" class="client-section" style="display: none;">
          <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
              <h2 class="h5 mb-0"><i class="bi bi-tag-fill"></i> Mis Promociones Reclamadas</h2>
            </div>
            <div class="card-body">
              @if($recentUsages->count() > 0)
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Fecha</th>
                      <th>Local</th>
                      <th>Promoción</th>
                      <th>Estado</th>
                      <th class="text-center">Código QR</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($recentUsages as $usage)
                    @php
                      $estadoLabels = [
                        'enviada' => ['label' => 'Pendiente', 'class' => 'warning'],
                        'aceptada' => ['label' => 'Aceptada', 'class' => 'success'],
                        'rechazada' => ['label' => 'Rechazada', 'class' => 'danger'],
                      ];
                      $estado = $estadoLabels[$usage->status] ?? ['label' => ucfirst($usage->status), 'class' => 'secondary'];
                    @endphp
                    <tr>
                      <td>{{ $usage->usage_date->format('d/m/Y') }}</td>
                      <td><i class="bi bi-shop"></i> {{ $usage->promotion->store->name }}</td>
                      <td>{{ Str::limit($usage->promotion->description, 40) }}</td>
                      <td><span class="badge bg-{{ $estado['class'] }}">{{ $estado['label'] }}</span></td>
                      <td class="text-center">
                        @if($usage->code_qr && $usage->status === 'aceptada')
                          <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#qrModal{{ $usage->id }}">
                            <i class="bi bi-qr-code"></i> Ver QR
                          </button>
                        @elseif($usage->status === 'enviada')
                          <small class="text-muted">Pendiente</small>
                        @else
                          <small class="text-muted">—</small>
                        @endif
                      </td>
                    </tr>

                    <!-- Modal para mostrar QR -->
                    @if($usage->code_qr && $usage->status === 'aceptada')
                    <div class="modal fade" id="qrModal{{ $usage->id }}" tabindex="-1" aria-labelledby="qrModalLabel{{ $usage->id }}" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                          <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="qrModalLabel{{ $usage->id }}">
                              <i class="bi bi-qr-code"></i> Código QR de tu Descuento
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body text-center">
                            <div class="mb-3">
                              <img src="{{ $usage->getQrCodeBase64() }}" alt="QR Code" class="img-fluid" style="max-width: 300px;">
                            </div>
                            <div class="alert alert-info">
                              <strong>Código:</strong> {{ $usage->code_qr }}
                            </div>
                            <p class="text-muted mb-2">
                              <strong>Local:</strong> {{ $usage->promotion->store->name }}
                            </p>
                            <p class="text-muted mb-2">
                              <strong>Promoción:</strong> {{ $usage->promotion->description }}
                            </p>
                            <p class="text-muted mb-0">
                              <small>Mostrá este código en el local para usar tu descuento</small>
                            </p>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
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
                <i class="bi bi-info-circle"></i> Aún no has solicitado ninguna promoción. 
                <a href="{{ route('promociones.index') }}" class="alert-link">¡Explorá las ofertas disponibles!</a>
              </div>
              @endif
            </div>
          </div>
        </div>

        <div id="editar-perfil" class="client-section" style="display: none;">
          <div class="card">
            <div class="card-header bg-primary text-white">
              <h2 class="h5 mb-0"><i class="bi bi-pencil"></i> Editar Mi Perfil</h2>
            </div>
            <div class="card-body">
              <form>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" class="form-control" value="{{ $client->name }}">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Apellido</label>
                    <input type="text" class="form-control" value="{{ $client->apellido }}">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" value="{{ $client->email }}">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Teléfono</label>
                    <input type="tel" class="form-control" value="{{ $client->telefono ?? '' }}" placeholder="+54 9 341 123-4567">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Fecha de Nacimiento</label>
                    <input type="date" class="form-control" value="{{ $client->fecha_nacimiento ? $client->fecha_nacimiento->format('Y-m-d') : '' }}">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Ciudad</label>
                    <input type="text" class="form-control" value="{{ $client->ciudad ?? '' }}" placeholder="Rosario">
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
                  <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> Guardar Cambios
                  </button>
                </div>
              </form>
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
@vite('resources/js/frontoffice/perfil-cliente.js')
@endpush