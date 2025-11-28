@extends('layouts.dashboard')

@section('title', 'Panel de Administrador | Shopping Rosario')
@section('meta_description', 'Supervisá locales, promociones y novedades desde el panel administrativo del Shopping Rosario.')

@php
use Illuminate\Support\Str;
@endphp

@section('content')
<x-layout.breadcrumbs :items="[['label' => 'Panel de Administrador']]" />

<section class="py-4">
  <div class="container">
    <div class="row">
      <div class="col-lg-3 mb-4">
        <div class="card">
          <div class="card-body text-center">
            <div class="mb-3">
              <i class="bi bi-shield-check" style="font-size: 5rem; color: var(--primary-color);"></i>
            </div>
            <h2 class="h5">Administrador</h2>
            <p class="text-muted mb-2">Admin Principal</p>
            <span class="badge bg-danger">Acceso Total</span>

            <hr>

            <div class="d-grid gap-2">
              <button class="btn btn-primary btn-sm" onclick="showAdminSection('dashboard')">
                <i class="bi bi-house"></i> Dashboard
              </button>
              <button class="btn btn-outline-primary btn-sm" onclick="showAdminSection('locales')">
                <i class="bi bi-shop"></i> Gestionar Locales
              </button>
              <button class="btn btn-outline-primary btn-sm" onclick="showAdminSection('validar-duenos')">
                <i class="bi bi-person-check"></i> Validar Dueños
              </button>
              <button class="btn btn-outline-warning btn-sm" onclick="showAdminSection('aprobar-promociones')">
                <i class="bi bi-tag"></i> Aprobar Promociones <span class="badge bg-danger">5</span>
              </button>
              <button class="btn btn-outline-success btn-sm" onclick="showAdminSection('novedades')">
                <i class="bi bi-megaphone"></i> Novedades
              </button>
              <button class="btn btn-outline-info btn-sm" onclick="showAdminSection('reportes')">
                <i class="bi bi-graph-up"></i> Reportes
              </button>
              <button class="btn btn-outline-secondary btn-sm" onclick="showAdminSection('configuracion')">
                <i class="bi bi-gear"></i> Configuración
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
                  <i class="bi bi-shop-window fs-1 text-primary"></i>
                  <h3 class="mt-2">{{ $stats['stores_active'] }}</h3>
                  <p class="mb-0">Locales Activos</p>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card text-center">
                <div class="card-body">
                  <i class="bi bi-people-fill fs-1 text-success"></i>
                  <h3 class="mt-2">{{ $stats['clients_total'] }}</h3>
                  <p class="mb-0">Clientes Registrados</p>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card text-center">
                <div class="card-body">
                  <i class="bi bi-tag-fill fs-1 text-warning"></i>
                  <h3 class="mt-2">{{ $stats['promotions_pending'] }}</h3>
                  <p class="mb-0">Promociones Pendientes</p>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card text-center">
                <div class="card-body">
                  <i class="bi bi-person-plus fs-1 text-info"></i>
                  <h3 class="mt-2">{{ $stats['owners_pending'] }}</h3>
                  <p class="mb-0">Dueños por Validar</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div id="locales" class="content-section" style="display: block;">
          <div class="card mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
              <h2 class="h5 mb-0"><i class="bi bi-shop"></i> Gestionar Locales</h2>
              <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoLocal">
                <i class="bi bi-plus-circle"></i> Crear Local
              </button>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Código</th>
                      <th>Local</th>
                      <th>Categoría</th>
                      <th>Dueño</th>
                      <th>Estado</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($stores as $store)
                    <tr>
                      <td>{{ str_pad($store->code, 3, '0', STR_PAD_LEFT) }}</td>
                      <td>{{ $store->name }}</td>
                      <td>{{ ucwords(str_replace(['-', '_'], ' ', $store->category)) }}</td>
                      <td>{{ $store->owner->first()?->name ?? 'Sin asignar' }}</td>
                      <td>
                        <span class="badge {{ $store->trashed() ? 'bg-secondary' : 'bg-success' }}">
                          {{ $store->trashed() ? 'Inactivo' : 'Activo' }}
                        </span>
                      </td>
                      <td class="d-flex gap-2">
                        <a href="{{ route('admin.stores.show', $store->id) }}" class="btn btn-sm btn-outline-primary" title="Ver detalles">
                          <i class="bi bi-eye"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-info" title="Editar" 
                          data-bs-toggle="modal" 
                          data-bs-target="#modalEditarLocal" 
                          data-store-id="{{ $store->id }}"
                          data-store-code="{{ $store->code }}"
                          data-store-name="{{ $store->name }}"
                          data-store-category="{{ $store->category }}"
                          data-store-location="{{ $store->location }}"
                          data-store-description="{{ $store->description ?? '' }}"
                          data-store-logo="{{ $store->logo_url ?? '' }}">
                          <i class="bi bi-pencil"></i>
                        </button>
                        @if(!$store->trashed())
                        <form action="{{ route('admin.stores.destroy', $store->id) }}" method="POST" onsubmit="return confirm('¿Seguro que querés eliminar este local?');">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                            <i class="bi bi-trash"></i>
                          </button>
                        </form>
                        @else
                        <span class="badge bg-secondary align-self-center">Eliminado</span>
                        @endif
                      </td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="6" class="text-center text-muted py-4">
                        <i class="bi bi-info-circle"></i> Todavía no hay locales cargados.
                      </td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <div id="validar-duenos" class="content-section" style="display: none;">
          <div class="card mb-4">
            <div class="card-header bg-info text-white">
              <h2 class="h5 mb-0"><i class="bi bi-person-check"></i> Validar Cuentas de Dueños</h2>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Nombre</th>
                      <th>Email</th>
                      <th>Fecha de Registro</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($pendingOwners as $owner)
                    <tr>
                      <td>{{ $owner->name }}</td>
                      <td>{{ $owner->email }}</td>
                      <td>{{ $owner->created_at?->format('d/m/Y H:i') }}</td>
                      <td class="d-flex gap-2">
                        <form action="{{ route('admin.users.approve', $owner->id) }}" method="POST" onsubmit="return confirm('¿Aprobar la cuenta de {{ $owner->name }}?');">
                          @csrf
                          <button type="submit" class="btn btn-success btn-sm">
                            <i class="bi bi-check-lg"></i> Aprobar
                          </button>
                        </form>
                        <form action="{{ route('admin.users.reject', $owner->id) }}" method="POST" onsubmit="return confirm('¿Rechazar la cuenta de {{ $owner->name }}?');">
                          @csrf
                          <button type="submit" class="btn btn-danger btn-sm">
                            <i class="bi bi-x-lg"></i> Rechazar
                          </button>
                        </form>
                      </td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="4" class="text-center text-muted py-4">
                        <i class="bi bi-check-circle"></i> No hay solicitudes pendientes en este momento.
                      </td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <div id="aprobar-promociones" class="content-section" style="display: none;">
          <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
              <h2 class="h5 mb-0"><i class="bi bi-tag"></i> Aprobar/Denegar Promociones</h2>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Local</th>
                      <th>Promoción</th>
                      <th>Categoría</th>
                      <th>Vigencia</th>
                      <th>Estado</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($pendingPromotions as $promotion)
                    <tr>
                      <td>{{ $promotion->store?->name ?? 'Local eliminado' }}</td>
                      <td>{{ Str::limit($promotion->description, 80) }}</td>
                      <td><span class="badge badge-{{ strtolower($promotion->minimum_category) }}">{{ $promotion->minimum_category }}</span></td>
                      <td>{{ $promotion->start_date?->format('d/m/Y') }} - {{ $promotion->end_date?->format('d/m/Y') }}</td>
                      <td><span class="badge bg-warning text-dark text-uppercase">{{ $promotion->status }}</span></td>
                      <td class="d-flex gap-2">
                        <form action="{{ route('admin.promotions.approve', $promotion->id) }}" method="POST" onsubmit="return confirm('¿Aprobar la promoción \"{{ addslashes($promotion->description) }}\"?');">
                          @csrf
                          <button type="submit" class="btn btn-success btn-sm">
                            <i class="bi bi-check-lg"></i> Aprobar
                          </button>
                        </form>
                        <form action="{{ route('admin.promotions.deny', $promotion->id) }}" method="POST" onsubmit="return confirm('¿Denegar la promoción \"{{ addslashes($promotion->description) }}\"?');">
                          @csrf
                          <button type="submit" class="btn btn-danger btn-sm">
                            <i class="bi bi-x-lg"></i> Denegar
                          </button>
                        </form>
                      </td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="6" class="text-center text-muted py-4">
                        <i class="bi bi-check-circle"></i> No hay promociones pendientes de aprobación.
                      </td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <div id="novedades" class="content-section" style="display: none;">
          <div class="card mb-4">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
              <h2 class="h5 mb-0"><i class="bi bi-megaphone"></i> Gestionar Novedades</h2>
              <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#modalNuevaNovedad">
                <i class="bi bi-plus-circle"></i> Crear Novedad
              </button>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Contenido</th>
                      <th>Categoría Cliente</th>
                      <th>Publicación</th>
                      <th>Vence</th>
                      <th>Estado</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($latestNews as $news)
                    @php
                      $isActive = $news->end_date?->isFuture() ?? false;
                    @endphp
                    <tr>
                      <td>{{ Str::limit($news->description, 80) }}</td>
                      <td><span class="badge badge-{{ strtolower($news->target_category) }}">{{ $news->target_category }}</span></td>
                      <td>{{ $news->start_date?->format('d/m/Y') }}</td>
                      <td>{{ $news->end_date?->format('d/m/Y') }}</td>
                      <td>
                        <span class="badge {{ $isActive ? 'bg-success' : 'bg-secondary' }}">
                          {{ $isActive ? 'Vigente' : 'Expirada' }}
                        </span>
                      </td>
                      <td class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-info" title="Editar" 
                          data-bs-toggle="modal" 
                          data-bs-target="#modalEditarNovedad" 
                          data-news-id="{{ $news->id }}"
                          data-news-code="{{ $news->code }}"
                          data-news-title="{{ $news->title ?? '' }}"
                          data-news-description="{{ $news->description }}"
                          data-news-start-date="{{ $news->start_date?->format('Y-m-d') }}"
                          data-news-end-date="{{ $news->end_date?->format('Y-m-d') }}"
                          data-news-target-category="{{ $news->target_category }}"
                          data-news-imagen="{{ $news->imagen_url ?? '' }}">
                          <i class="bi bi-pencil"></i>
                        </button>
                        <form action="{{ route('admin.news.destroy', $news->id) }}" method="POST" onsubmit="return confirm('¿Eliminar esta novedad?');">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                            <i class="bi bi-trash"></i>
                          </button>
                        </form>
                      </td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="6" class="text-center text-muted py-4">
                        <i class="bi bi-info-circle"></i> No cargaste novedades todavía.
                      </td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <div id="reportes" class="content-section" style="display: none;">
          <div class="card mb-4">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
              <h2 class="h5 mb-0"><i class="bi bi-graph-up"></i> Reportes Gerenciales</h2>
              <span class="badge bg-light text-dark">Datos en tiempo real</span>
            </div>
            <div class="card-body">
              <!-- Resumen General -->
              <h3 class="h6 mb-3">Resumen General del Shopping</h3>
              <div class="row text-center mb-4">
                <div class="col-md-3 mb-3">
                  <div class="p-3 bg-light rounded">
                    <h4 class="mb-1 text-primary">{{ $stats['stores_active'] }}</h4>
                    <p class="mb-0 text-muted small">Locales Activos</p>
                  </div>
                </div>
                <div class="col-md-3 mb-3">
                  <div class="p-3 bg-light rounded">
                    <h4 class="mb-1 text-success">{{ $promotions->count() }}</h4>
                    <p class="mb-0 text-muted small">Promociones</p>
                  </div>
                </div>
                <div class="col-md-3 mb-3">
                  <div class="p-3 bg-light rounded">
                    <h4 class="mb-1 text-info">{{ $stats['clients_total'] }}</h4>
                    <p class="mb-0 text-muted small">Clientes</p>
                  </div>
                </div>
                <div class="col-md-3 mb-3">
                  <div class="p-3 bg-light rounded">
                    <h4 class="mb-1 text-warning">{{ $usageStats['total'] }}</h4>
                    <p class="mb-0 text-muted small">Uso Total</p>
                  </div>
                </div>
              </div>

              <!-- Tabs para reportes específicos -->
              <ul class="nav nav-tabs mb-3" id="reportTabs" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link active" id="promotion-usage-tab" data-bs-toggle="tab" data-bs-target="#promotion-usage" type="button">
                    <i class="bi bi-graph-up"></i> Uso de Promociones
                  </button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link" id="store-performance-tab" data-bs-toggle="tab" data-bs-target="#store-performance" type="button">
                    <i class="bi bi-shop"></i> Rendimiento de Locales
                  </button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link" id="client-activity-tab" data-bs-toggle="tab" data-bs-target="#client-activity" type="button">
                    <i class="bi bi-people"></i> Actividad de Clientes
                  </button>
                </li>
              </ul>

              <div class="tab-content" id="reportTabContent">
                <!-- Tab 1: Promotion Usage Report -->
                <div class="tab-pane fade show active" id="promotion-usage" role="tabpanel">
                  <div class="mb-3">
                    <h4 class="h6">Reporte de Uso de Promociones</h4>
                    <p class="text-muted small mb-3">Estadísticas detalladas del uso de promociones en el shopping</p>
                  </div>

                  <!-- Filtro de período -->
                  <div class="mb-3">
                    <label class="form-label fw-bold">Seleccionar período:</label>
                    <div class="btn-group" role="group">
                      <input type="radio" class="btn-check" name="promotionPeriod" id="promo30days" value="30" checked>
                      <label class="btn btn-outline-primary" for="promo30days">Último mes</label>
                      
                      <input type="radio" class="btn-check" name="promotionPeriod" id="promo3months" value="90">
                      <label class="btn btn-outline-primary" for="promo3months">Últimos 3 meses</label>
                      
                      <input type="radio" class="btn-check" name="promotionPeriod" id="promo1year" value="365">
                      <label class="btn btn-outline-primary" for="promo1year">Último año</label>
                    </div>
                  </div>

                  <div id="promotionUsageTable">
                    <!-- Content will be loaded here -->
                  </div>
                </div>

                <!-- Tab 2: Store Performance Report -->
                <div class="tab-pane fade" id="store-performance" role="tabpanel">
                  <div class="mb-3">
                    <h4 class="h6">Rendimiento de Locales</h4>
                    <p class="text-muted small mb-3">Métricas de desempeño por local del shopping</p>
                  </div>

                  <!-- Filtro de período -->
                  <div class="mb-3">
                    <label class="form-label fw-bold">Seleccionar período:</label>
                    <div class="btn-group" role="group">
                      <input type="radio" class="btn-check" name="storePeriod" id="store1month" value="1">
                      <label class="btn btn-outline-primary" for="store1month">Último mes</label>
                      
                      <input type="radio" class="btn-check" name="storePeriod" id="store3months" value="3" checked>
                      <label class="btn btn-outline-primary" for="store3months">Últimos 3 meses</label>
                      
                      <input type="radio" class="btn-check" name="storePeriod" id="store6months" value="6">
                      <label class="btn btn-outline-primary" for="store6months">Últimos 6 meses</label>

                      <input type="radio" class="btn-check" name="storePeriod" id="store1year" value="12">
                      <label class="btn btn-outline-primary" for="store1year">Último año</label>
                    </div>
                  </div>

                  <div id="storePerformanceTable">
                    <!-- Content will be loaded here -->
                  </div>
                </div>

                <!-- Tab 3: Client Activity Report -->
                <div class="tab-pane fade" id="client-activity" role="tabpanel">
                  <div class="mb-3">
                    <h4 class="h6">Actividad de Clientes</h4>
                    <p class="text-muted small mb-3">Distribución y actividad por categoría de cliente</p>
                  </div>

                  <!-- Filtro de período -->
                  <div class="mb-3">
                    <label class="form-label fw-bold">Seleccionar período:</label>
                    <div class="btn-group" role="group">
                      <input type="radio" class="btn-check" name="clientPeriod" id="client3months" value="3">
                      <label class="btn btn-outline-primary" for="client3months">Últimos 3 meses</label>
                      
                      <input type="radio" class="btn-check" name="clientPeriod" id="client6months" value="6" checked>
                      <label class="btn btn-outline-primary" for="client6months">Últimos 6 meses</label>
                      
                      <input type="radio" class="btn-check" name="clientPeriod" id="client1year" value="12">
                      <label class="btn btn-outline-primary" for="client1year">Último año</label>
                    </div>
                  </div>

                  <div id="clientActivityContent">
                    <!-- Content will be loaded here -->
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div id="configuracion" class="content-section" style="display: none;">
          <div class="card">
            <div class="card-header bg-secondary text-white">
              <h2 class="h5 mb-0"><i class="bi bi-gear"></i> Configuración del Sistema</h2>
            </div>
            <div class="card-body">
              <h3 class="h6">Configuración de Categorías de Cliente</h3>
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Promociones para pasar de Inicial a Medium</label>
                  <input type="number" class="form-control" value="10">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Promociones para pasar de Medium a Premium</label>
                  <input type="number" class="form-control" value="25">
                </div>
              </div>

              <hr>

              <h3 class="h6">Información del Shopping</h3>
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Nombre</label>
                  <input type="text" class="form-control" value="Shopping Rosario">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Email de Contacto</label>
                  <input type="email" class="form-control" value="info@shoppingrosario.com">
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Teléfono</label>
                  <input type="tel" class="form-control" value="(0341) 123-4567">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Dirección</label>
                  <input type="text" class="form-control" value="Av. Pellegrini 1234, Rosario">
                </div>
              </div>

              <hr>

              <h3 class="h6">Cambiar Contraseña</h3>
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
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="modal fade" id="modalNuevoLocal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title h5">Crear Nuevo Local</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form method="POST" action="{{ route('admin.stores.store') }}" enctype="multipart/form-data" id="formNuevoLocal" novalidate>
        @csrf
        <div class="modal-body">
          {{-- Nombre del Local --}}
          <div class="mb-3">
            <label for="name" class="form-label">
              Nombre del Local <span class="text-danger">*</span>
            </label>
            <input 
              type="text" 
              class="form-control @error('name') is-invalid @enderror" 
              id="name" 
              name="name" 
              value="{{ old('name') }}"
              maxlength="100"
              placeholder="Ej: Tienda de Electrodomésticos XYZ"
              required>
            @error('name')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Rubro --}}
          <div class="mb-3">
            <label for="category" class="form-label">
              Rubro <span class="text-danger">*</span>
            </label>
            <select 
              class="form-select @error('category') is-invalid @enderror" 
              id="category" 
              name="category"
              required>
              <option value="">Seleccionar rubro...</option>
              <option value="indumentaria">Indumentaria</option>
              <option value="perfumeria">Perfumería</option>
              <option value="optica">Óptica</option>
              <option value="comida">Comida</option>
              <option value="tecnologia">Tecnología</option>
              <option value="deportes">Deportes</option>
              <option value="libreria">Librería</option>
              <option value="jugueteria">Juguetería</option>
              <option value="otros">Otros</option>
            </select>
            @error('category')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Ubicación --}}
          <div class="mb-3">
            <label for="location" class="form-label">
              Ubicación <span class="text-danger">*</span>
            </label>
            <input 
              type="text" 
              class="form-control @error('location') is-invalid @enderror" 
              id="location" 
              name="location" 
              value="{{ old('location') }}"
              maxlength="50"
              placeholder="Ej: Primer Piso - Local 205"
              required>
            <div class="form-text">
              Indicá el piso y número de local dentro del shopping
            </div>
            @error('location')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Descripción del Local --}}
          <div class="mb-3">
            <label for="description" class="form-label">
              Descripción del Local <span class="text-muted">(Opcional)</span>
            </label>
            <textarea 
              class="form-control @error('description') is-invalid @enderror" 
              id="description" 
              name="description" 
              rows="3"
              maxlength="500"
              placeholder="Descripción que aparecerá en la sección 'Sobre el local' en la página de detalles"></textarea>
            <div class="form-text">
              Máximo 500 caracteres - Esta descripción aparecerá en la página de detalles del local
            </div>
            @error('description')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Logo --}}
          <div class="mb-3">
            <label for="logo" class="form-label">
              Logo del Local <span class="text-muted">(Opcional)</span>
            </label>
            <input 
              type="file" 
              class="form-control @error('logo') is-invalid @enderror" 
              id="logo" 
              name="logo"
              accept="image/*">
            <div class="form-text">
              Formatos aceptados: JPG, PNG, GIF. Tamaño máximo: 2MB
            </div>
            @error('logo')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div id="logo-preview" class="mt-2" style="display: none;">
              <img src="" alt="Vista previa" class="img-thumbnail" style="max-width: 200px;">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-circle"></i> Crear Local
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Modal Editar Local --}}
<div class="modal fade" id="modalEditarLocal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title h5">Editar Local</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form method="POST" id="formEditarLocal" enctype="multipart/form-data" novalidate>
        @csrf
        @method('PUT')
        <div class="modal-body">
          {{-- Código (read-only) --}}
          <div class="mb-3">
            <label for="edit_code" class="form-label">
              Código del Local
            </label>
            <input 
              type="text" 
              class="form-control" 
              id="edit_code" 
              readonly
              disabled>
            <div class="form-text">
              El código del local no puede modificarse
            </div>
          </div>

          {{-- Nombre del Local --}}
          <div class="mb-3">
            <label for="edit_name" class="form-label">
              Nombre del Local <span class="text-danger">*</span>
            </label>
            <input 
              type="text" 
              class="form-control @error('name') is-invalid @enderror" 
              id="edit_name" 
              name="name" 
              maxlength="100"
              placeholder="Ej: Tienda de Electrodomésticos XYZ"
              required>
            @error('name')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Rubro --}}
          <div class="mb-3">
            <label for="edit_category" class="form-label">
              Rubro <span class="text-danger">*</span>
            </label>
            <select 
              class="form-select @error('category') is-invalid @enderror" 
              id="edit_category" 
              name="category"
              required>
              <option value="">Seleccionar rubro...</option>
              <option value="indumentaria">Indumentaria</option>
              <option value="perfumeria">Perfumería</option>
              <option value="optica">Óptica</option>
              <option value="comida">Comida</option>
              <option value="tecnologia">Tecnología</option>
              <option value="deportes">Deportes</option>
              <option value="libreria">Librería</option>
              <option value="jugueteria">Juguetería</option>
              <option value="otros">Otros</option>
            </select>
            @error('category')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Ubicación --}}
          <div class="mb-3">
            <label for="edit_location" class="form-label">
              Ubicación <span class="text-danger">*</span>
            </label>
            <input 
              type="text" 
              class="form-control @error('location') is-invalid @enderror" 
              id="edit_location" 
              name="location" 
              maxlength="50"
              placeholder="Ej: Primer Piso - Local 205"
              required>
            <div class="form-text">
              Indicá el piso y número de local dentro del shopping
            </div>
            @error('location')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Descripción del Local --}}
          <div class="mb-3">
            <label for="edit_description" class="form-label">
              Descripción del Local <span class="text-muted">(Opcional)</span>
            </label>
            <textarea 
              class="form-control @error('description') is-invalid @enderror" 
              id="edit_description" 
              name="description" 
              rows="3"
              maxlength="500"
              placeholder="Descripción que aparecerá en la sección 'Sobre el local' en la página de detalles"></textarea>
            <div class="form-text">
              Máximo 500 caracteres - Esta descripción aparecerá en la página de detalles del local
            </div>
            @error('description')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Logo Actual --}}
          <div class="mb-3" id="edit-current-logo-container" style="display: none;">
            <label class="form-label">Logo Actual</label>
            <div>
              <img id="edit-current-logo" src="" alt="Logo actual" class="img-thumbnail" style="max-width: 200px;">
            </div>
          </div>

          {{-- Nuevo Logo --}}
          <div class="mb-3">
            <label for="edit_logo" class="form-label">
              Cambiar Logo <span class="text-muted">(Opcional)</span>
            </label>
            <input 
              type="file" 
              class="form-control @error('logo') is-invalid @enderror" 
              id="edit_logo" 
              name="logo"
              accept="image/*">
            <div class="form-text">
              Formatos aceptados: JPG, PNG, GIF. Tamaño máximo: 2MB. Dejar vacío para mantener el logo actual.
            </div>
            @error('logo')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div id="edit-logo-preview" class="mt-2" style="display: none;">
              <img src="" alt="Vista previa" class="img-thumbnail" style="max-width: 200px;">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-save"></i> Guardar Cambios
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Modal Nueva Novedad --}}
<div class="modal fade" id="modalNuevaNovedad" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title h5">Crear Nueva Novedad</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form method="POST" action="{{ route('admin.news.store') }}" id="formNuevaNovedad" enctype="multipart/form-data" novalidate>
        @csrf
        <div class="modal-body">
          {{-- Título de la Novedad --}}
          <div class="mb-3">
            <label for="news_title" class="form-label">
              T\u00edtulo de la Novedad <span class="text-danger">*</span>
            </label>
            <input 
              type="text" 
              class="form-control @error('title') is-invalid @enderror" 
              id="news_title" 
              name="title" 
              value="{{ old('title') }}"
              maxlength="100"
              placeholder="Ej: Nuevas promociones de verano, Apertura de nuevo local..."
              required>
            <div class="form-text">
              M\u00e1ximo 100 caracteres - Este ser\u00e1 el t\u00edtulo principal de la novedad
            </div>
            @error('title')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Texto de la Novedad --}}
          <div class="mb-3">
            <label for="description" class="form-label">
              Descripci\u00f3n de la Novedad <span class="text-danger">*</span>
            </label>
            <textarea 
              class="form-control @error('description') is-invalid @enderror" 
              id="description" 
              name="description" 
              rows="3"
              maxlength="200"
              placeholder="Ej: Visita nuestros locales y aprovecha las promociones de verano..."
              required>{{ old('description') }}</textarea>
            <div class="form-text">
              Máximo 200 caracteres - Agrega detalles adicionales sobre la novedad
            </div>
            @error('description')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Imagen --}}
          <div class="mb-3">
            <label for="news_imagen" class="form-label">
              Imagen de la Novedad <span class="text-muted">(Opcional)</span>
            </label>
            <input 
              type="file" 
              class="form-control @error('imagen') is-invalid @enderror" 
              id="news_imagen" 
              name="imagen"
              accept="image/*">
            <div class="form-text">
              Formatos aceptados: JPG, PNG, GIF. Tamaño máximo: 2MB
            </div>
            @error('imagen')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div id="news-imagen-preview" class="mt-2" style="display: none;">
              <img src="" alt="Vista previa" class="img-thumbnail" style="max-width: 200px;">
            </div>
          </div>

          {{-- Fecha Desde --}}
          <div class="mb-3">
            <label for="start_date" class="form-label">
              Fecha de Inicio <span class="text-danger">*</span>
            </label>
            <input 
              type="date" 
              class="form-control @error('start_date') is-invalid @enderror" 
              id="start_date" 
              name="start_date" 
              value="{{ old('start_date', now()->format('Y-m-d')) }}"
              min="{{ now()->format('Y-m-d') }}"
              required>
            @error('start_date')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Fecha Hasta --}}
          <div class="mb-3">
            <label for="end_date" class="form-label">
              Fecha de Vencimiento <span class="text-danger">*</span>
            </label>
            <input 
              type="date" 
              class="form-control @error('end_date') is-invalid @enderror" 
              id="end_date" 
              name="end_date" 
              value="{{ old('end_date', now()->addDays(30)->format('Y-m-d')) }}"
              min="{{ now()->format('Y-m-d') }}"
              required>
            <div class="form-text">
              La novedad será visible hasta esta fecha
            </div>
            @error('end_date')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Categoría Destino --}}
          <div class="mb-3">
            <label for="target_category" class="form-label">
              Categoría de Cliente <span class="text-danger">*</span>
            </label>
            <select 
              class="form-select @error('target_category') is-invalid @enderror" 
              id="target_category" 
              name="target_category"
              required>
              <option value="">Seleccionar categoría...</option>
              <option value="Inicial" {{ old('target_category') == 'Inicial' ? 'selected' : '' }}>
                Inicial (Visible para todos los clientes)
              </option>
              <option value="Medium" {{ old('target_category') == 'Medium' ? 'selected' : '' }}>
                Medium (Visible para Medium y Premium)
              </option>
              <option value="Premium" {{ old('target_category') == 'Premium' ? 'selected' : '' }}>
                Premium (Solo clientes Premium)
              </option>
            </select>
            <div class="form-text">
              Los clientes pueden ver novedades de su categoría o inferiores
            </div>
            @error('target_category')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success">
            <i class="bi bi-check-circle"></i> Publicar Novedad
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Modal Editar Novedad --}}
<div class="modal fade" id="modalEditarNovedad" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title h5">Editar Novedad</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form method="POST" id="formEditarNovedad" enctype="multipart/form-data" novalidate>
        @csrf
        @method('PUT')
        <div class="modal-body">
          {{-- Código (read-only) --}}
          <div class="mb-3">
            <label for="edit_news_code" class="form-label">
              Código de la Novedad
            </label>
            <input 
              type="text" 
              class="form-control" 
              id="edit_news_code" 
              readonly
              disabled>
            <div class="form-text">
              El código de la novedad no puede modificarse
            </div>
          </div>

          {{-- Título de la Novedad --}}
          <div class="mb-3">
            <label for="edit_news_title" class="form-label">
              Título de la Novedad <span class="text-danger">*</span>
            </label>
            <input 
              type="text" 
              class="form-control @error('title') is-invalid @enderror" 
              id="edit_news_title" 
              name="title" 
              maxlength="100"
              placeholder="Ej: Nuevas promociones de verano, Apertura de nuevo local..."
              required>
            <div class="form-text">
              Máximo 100 caracteres - Este será el título principal de la novedad
            </div>
            @error('title')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Texto de la Novedad --}}
          <div class="mb-3">
            <label for="edit_description" class="form-label">
              Texto de la Novedad <span class="text-danger">*</span>
            </label>
            <textarea 
              class="form-control @error('description') is-invalid @enderror" 
              id="edit_description" 
              name="description" 
              rows="3"
              maxlength="200"
              placeholder="Ej: Nuevas promociones de verano disponibles..."
              required></textarea>
            <div class="form-text">
              Máximo 200 caracteres
            </div>
            @error('description')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Imagen Actual --}}
          <div class="mb-3" id="edit-news-current-imagen-container" style="display: none;">
            <label class="form-label">Imagen Actual</label>
            <div>
              <img id="edit-news-current-imagen" src="" alt="Imagen actual" class="img-thumbnail" style="max-width: 200px;">
            </div>
          </div>

          {{-- Nueva Imagen --}}
          <div class="mb-3">
            <label for="edit_news_imagen" class="form-label">
              Cambiar Imagen <span class="text-muted">(Opcional)</span>
            </label>
            <input 
              type="file" 
              class="form-control @error('imagen') is-invalid @enderror" 
              id="edit_news_imagen" 
              name="imagen"
              accept="image/*">
            <div class="form-text">
              Formatos aceptados: JPG, PNG, GIF. Tamaño máximo: 2MB. Dejar vacío para mantener la imagen actual.
            </div>
            @error('imagen')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div id="edit-news-imagen-preview" class="mt-2" style="display: none;">
              <img src="" alt="Vista previa" class="img-thumbnail" style="max-width: 200px;">
            </div>
          </div>

          {{-- Fecha Desde --}}
          <div class="mb-3">
            <label for="edit_start_date" class="form-label">
              Fecha de Inicio <span class="text-danger">*</span>
            </label>
            <input 
              type="date" 
              class="form-control @error('start_date') is-invalid @enderror" 
              id="edit_start_date" 
              name="start_date" 
              required>
            @error('start_date')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Fecha Hasta --}}
          <div class="mb-3">
            <label for="edit_end_date" class="form-label">
              Fecha de Vencimiento <span class="text-danger">*</span>
            </label>
            <input 
              type="date" 
              class="form-control @error('end_date') is-invalid @enderror" 
              id="edit_end_date" 
              name="end_date" 
              required>
            <div class="form-text">
              La novedad será visible hasta esta fecha
            </div>
            @error('end_date')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Categoría Destino --}}
          <div class="mb-3">
            <label for="edit_target_category" class="form-label">
              Categoría de Cliente <span class="text-danger">*</span>
            </label>
            <select 
              class="form-select @error('target_category') is-invalid @enderror" 
              id="edit_target_category" 
              name="target_category"
              required>
              <option value="">Seleccionar categoría...</option>
              <option value="Inicial">Inicial (Visible para todos los clientes)</option>
              <option value="Medium">Medium (Visible para Medium y Premium)</option>
              <option value="Premium">Premium (Solo clientes Premium)</option>
            </select>
            <div class="form-text">
              Los clientes pueden ver novedades de su categoría o inferiores
            </div>
            @error('target_category')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-save"></i> Guardar Cambios
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
  /* Ensure modals are scrollable when content exceeds viewport */
  .modal-body {
    max-height: calc(100vh - 200px);
    overflow-y: auto;
  }
</style>
@endpush

@push('scripts')
@vite('resources/js/frontoffice/main.js')
@vite('resources/js/frontoffice/perfil-admin.js')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Image preview for store logo (create form)
    const logoInput = document.getElementById('logo');
    if (logoInput) {
      logoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('logo-preview');
        const previewImg = preview.querySelector('img');
        
        if (file) {
          const reader = new FileReader();
          reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
          }
          reader.readAsDataURL(file);
        } else {
          preview.style.display = 'none';
          previewImg.src = '';
        }
      });
    }
    
    // Image preview for store logo (edit form)
    const editLogoInput = document.getElementById('edit_logo');
    if (editLogoInput) {
      editLogoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('edit-logo-preview');
        const previewImg = preview.querySelector('img');
        
        if (file) {
          const reader = new FileReader();
          reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
          }
          reader.readAsDataURL(file);
        } else {
          preview.style.display = 'none';
          previewImg.src = '';
        }
      });
    }
    
    // Handle edit modal - populate form with store data
    const modalEditarLocal = document.getElementById('modalEditarLocal');
    if (modalEditarLocal) {
      modalEditarLocal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const storeId = button.getAttribute('data-store-id');
        const storeCode = button.getAttribute('data-store-code');
        const storeName = button.getAttribute('data-store-name');
        const storeCategory = button.getAttribute('data-store-category');
        const storeLocation = button.getAttribute('data-store-location');
        const storeDescription = button.getAttribute('data-store-description');
        const storeLogo = button.getAttribute('data-store-logo');
        
        // Update form action - use Laravel route helper with base path
        const form = document.getElementById('formEditarLocal');
        const baseUrl = '{{ url("/") }}';
        form.action = baseUrl + '/admin/stores/' + storeId;
        
        // Populate fields
        document.getElementById('edit_code').value = storeCode;
        document.getElementById('edit_name').value = storeName;
        document.getElementById('edit_category').value = storeCategory;
        document.getElementById('edit_location').value = storeLocation;
        document.getElementById('edit_description').value = storeDescription || '';
        
        // Show current logo if exists
        if (storeLogo) {
          document.getElementById('edit-current-logo').src = storeLogo;
          document.getElementById('edit-current-logo-container').style.display = 'block';
        } else {
          document.getElementById('edit-current-logo-container').style.display = 'none';
        }
        
        // Reset preview
        document.getElementById('edit-logo-preview').style.display = 'none';
        document.getElementById('edit_logo').value = '';
      });
    }
    
    // Image preview for news (create form)
    const newsImagenInput = document.getElementById('news_imagen');
    if (newsImagenInput) {
      newsImagenInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('news-imagen-preview');
        const previewImg = preview.querySelector('img');
        
        if (file) {
          const reader = new FileReader();
          reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
          }
          reader.readAsDataURL(file);
        } else {
          preview.style.display = 'none';
          previewImg.src = '';
        }
      });
    }
    
    // Image preview for news (edit form)
    const editNewsImagenInput = document.getElementById('edit_news_imagen');
    if (editNewsImagenInput) {
      editNewsImagenInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('edit-news-imagen-preview');
        const previewImg = preview.querySelector('img');
        
        if (file) {
          const reader = new FileReader();
          reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
          }
          reader.readAsDataURL(file);
        } else {
          preview.style.display = 'none';
          previewImg.src = '';
        }
      });
    }
    
    // Handle edit news modal - populate form with news data
    const modalEditarNovedad = document.getElementById('modalEditarNovedad');
    if (modalEditarNovedad) {
      modalEditarNovedad.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const newsId = button.getAttribute('data-news-id');
        const newsCode = button.getAttribute('data-news-code');
        const newsTitle = button.getAttribute('data-news-title');
        const newsDescription = button.getAttribute('data-news-description');
        const newsStartDate = button.getAttribute('data-news-start-date');
        const newsEndDate = button.getAttribute('data-news-end-date');
        const newsTargetCategory = button.getAttribute('data-news-target-category');
        const newsImagen = button.getAttribute('data-news-imagen');
        
        // Update form action
        const form = document.getElementById('formEditarNovedad');
        const baseUrl = '{{ url("/") }}';
        form.action = baseUrl + '/admin/news/' + newsId;
        
        // Populate fields
        document.getElementById('edit_news_code').value = newsCode;
        document.getElementById('edit_news_title').value = newsTitle || '';
        document.getElementById('edit_description').value = newsDescription;
        document.getElementById('edit_start_date').value = newsStartDate;
        document.getElementById('edit_end_date').value = newsEndDate;
        document.getElementById('edit_target_category').value = newsTargetCategory;
        
        // Show current imagen if exists
        if (newsImagen) {
          document.getElementById('edit-news-current-imagen').src = newsImagen;
          document.getElementById('edit-news-current-imagen-container').style.display = 'block';
        } else {
          document.getElementById('edit-news-current-imagen-container').style.display = 'none';
        }
        
        // Reset preview
        document.getElementById('edit-news-imagen-preview').style.display = 'none';
        document.getElementById('edit_news_imagen').value = '';
      });
    }
    
    // Show modal if there are validation errors for store creation
    @if ($errors->any() && (old('name') || old('category') || old('location')))
    const modalNuevoLocal = new bootstrap.Modal(document.getElementById('modalNuevoLocal'));
    modalNuevoLocal.show();
    // Navigate to stores section
    if (typeof window.showAdminSection === 'function') {
      window.showAdminSection('locales');
    }
    @endif
    
    // Show modal if there are validation errors for news creation
    @if ($errors->any() && (old('description') || old('target_category')))
    const modalNuevaNovedad = new bootstrap.Modal(document.getElementById('modalNuevaNovedad'));
    modalNuevaNovedad.show();
    // Navigate to news section
    if (typeof window.showAdminSection === 'function') {
      window.showAdminSection('novedades');
    }
    @endif
    
    // Navigate to section if specified
    @if(!empty($activeSection))
    if (typeof window.showAdminSection === 'function') {
      window.showAdminSection('{{ $activeSection }}');
    }
    @endif

    // ===== REPORT FILTERS =====
    
    // Load initial data for reports
    loadPromotionUsageReport(30);
    loadStorePerformanceReport(3);
    loadClientActivityReport(6);
    
    // Promotion Usage Report Filter
    document.querySelectorAll('input[name="promotionPeriod"]').forEach(radio => {
      radio.addEventListener('change', function() {
        loadPromotionUsageReport(parseInt(this.value));
      });
    });
    
    // Store Performance Report Filter
    document.querySelectorAll('input[name="storePeriod"]').forEach(radio => {
      radio.addEventListener('change', function() {
        loadStorePerformanceReport(parseInt(this.value));
      });
    });
    
    // Client Activity Report Filter
    document.querySelectorAll('input[name="clientPeriod"]').forEach(radio => {
      radio.addEventListener('change', function() {
        loadClientActivityReport(parseInt(this.value));
      });
    });
    
    // Functions to load report data
    function loadPromotionUsageReport(days) {
      const container = document.getElementById('promotionUsageTable');
      container.innerHTML = '<div class="text-center py-4"><div class="spinner-border" role="status"></div><p class="mt-2">Cargando datos...</p></div>';
      
      // Calculate data based on days
      const cutoffDate = new Date();
      cutoffDate.setDate(cutoffDate.getDate() - days);
      
      // Get all promotion usages and filter by date
      const allUsages = @json(\App\Models\PromotionUsage::with(['promotion.store'])->get());
      const filteredUsages = allUsages.filter(usage => {
        const usageDate = new Date(usage.fecha_uso);
        return usageDate >= cutoffDate && usage.promotion;
      });
      
      // Group by promotion
      const promotionMap = {};
      filteredUsages.forEach(usage => {
        if (!promotionMap[usage.promotion_id]) {
          promotionMap[usage.promotion_id] = {
            promotion: usage.promotion,
            total: 0,
            accepted: 0,
            rejected: 0,
            pending: 0
          };
        }
        promotionMap[usage.promotion_id].total++;
        if (usage.estado === 'aceptada') promotionMap[usage.promotion_id].accepted++;
        if (usage.estado === 'rechazada') promotionMap[usage.promotion_id].rejected++;
        if (usage.estado === 'enviada') promotionMap[usage.promotion_id].pending++;
      });
      
      // Convert to array and sort
      const stats = Object.values(promotionMap).sort((a, b) => b.total - a.total).slice(0, 10);
      
      // Build HTML
      let html = '';
      if (stats.length > 0) {
        html = `
          <div class="table-responsive">
            <table class="table table-striped table-hover">
              <thead class="table-light">
                <tr>
                  <th>Código</th>
                  <th>Promoción</th>
                  <th>Local</th>
                  <th class="text-center">Total Solicitudes</th>
                  <th class="text-center">Aceptadas</th>
                  <th class="text-center">Rechazadas</th>
                  <th class="text-center">Pendientes</th>
                  <th class="text-center">Tasa Aceptación</th>
                </tr>
              </thead>
              <tbody>`;
        
        stats.forEach(stat => {
          const rate = stat.total > 0 ? Math.round((stat.accepted / stat.total) * 100) : 0;
          const rateClass = rate >= 70 ? 'text-success' : (rate >= 40 ? 'text-warning' : 'text-danger');
          const texto = stat.promotion.texto.length > 40 ? stat.promotion.texto.substring(0, 40) + '...' : stat.promotion.texto;
          
          html += `
            <tr>
              <td><code>${stat.promotion.codigo}</code></td>
              <td>${texto}</td>
              <td><small>${stat.promotion.store.nombre}</small></td>
              <td class="text-center"><strong>${stat.total}</strong></td>
              <td class="text-center"><span class="badge bg-success">${stat.accepted}</span></td>
              <td class="text-center"><span class="badge bg-danger">${stat.rejected}</span></td>
              <td class="text-center"><span class="badge bg-warning text-dark">${stat.pending}</span></td>
              <td class="text-center"><strong class="${rateClass}">${rate}%</strong></td>
            </tr>`;
        });
        
        html += `
              </tbody>
            </table>
          </div>
          <div class="mt-3">
            <a href="{{ route('admin.reports.export-csv') }}" class="btn btn-success">
              <i class="bi bi-file-earmark-excel"></i> Exportar a Excel
            </a>
          </div>`;
      } else {
        html = '<div class="alert alert-info"><i class="bi bi-info-circle"></i> No hay datos de uso de promociones en el período seleccionado.</div>';
      }
      
      container.innerHTML = html;
    }
    
    function loadStorePerformanceReport(months) {
      const container = document.getElementById('storePerformanceTable');
      container.innerHTML = '<div class="text-center py-4"><div class="spinner-border" role="status"></div><p class="mt-2">Cargando datos...</p></div>';
      
      // Calculate cutoff date
      const cutoffDate = new Date();
      cutoffDate.setMonth(cutoffDate.getMonth() - months);
      
      // Get all stores with promotions and usages
      const allStores = @json(\App\Models\Store::with(['promotions.usages'])->get());
      
      const storeStats = allStores.map(store => {
        const usages = [];
        store.promotions.forEach(promo => {
          if (promo.usages) {
            promo.usages.forEach(usage => {
              const usageDate = new Date(usage.fecha_uso);
              if (usageDate >= cutoffDate) {
                usages.push(usage);
              }
            });
          }
        });
        
        return {
          store: store,
          promotions_count: store.promotions.length,
          total_usages: usages.length,
          accepted: usages.filter(u => u.estado === 'aceptada').length,
          rejected: usages.filter(u => u.estado === 'rechazada').length,
          pending: usages.filter(u => u.estado === 'enviada').length
        };
      }).filter(s => s.total_usages > 0).sort((a, b) => b.total_usages - a.total_usages);
      
      // Build HTML
      let html = '';
      if (storeStats.length > 0) {
        html = `
          <div class="table-responsive">
            <table class="table table-striped table-hover">
              <thead class="table-light">
                <tr>
                  <th>Código</th>
                  <th>Local</th>
                  <th>Rubro</th>
                  <th class="text-center">Promociones</th>
                  <th class="text-center">Total Usos</th>
                  <th class="text-center">Aceptadas</th>
                  <th class="text-center">Rechazadas</th>
                  <th class="text-center">Pendientes</th>
                </tr>
              </thead>
              <tbody>`;
        
        storeStats.forEach(stat => {
          html += `
            <tr>
              <td><code>${stat.store.codigo}</code></td>
              <td><strong>${stat.store.nombre}</strong></td>
              <td><span class="badge bg-secondary">${stat.store.rubro}</span></td>
              <td class="text-center">${stat.promotions_count}</td>
              <td class="text-center"><strong>${stat.total_usages}</strong></td>
              <td class="text-center"><span class="badge bg-success">${stat.accepted}</span></td>
              <td class="text-center"><span class="badge bg-danger">${stat.rejected}</span></td>
              <td class="text-center"><span class="badge bg-warning text-dark">${stat.pending}</span></td>
            </tr>`;
        });
        
        html += `
              </tbody>
            </table>
          </div>`;
      } else {
        html = '<div class="alert alert-info"><i class="bi bi-info-circle"></i> No hay datos de uso en los locales en el período seleccionado.</div>';
      }
      
      container.innerHTML = html;
    }
    
    function loadClientActivityReport(months) {
      const container = document.getElementById('clientActivityContent');
      container.innerHTML = '<div class="text-center py-4"><div class="spinner-border" role="status"></div><p class="mt-2">Cargando datos...</p></div>';
      
      // Calculate cutoff date
      const cutoffDate = new Date();
      cutoffDate.setMonth(cutoffDate.getMonth() - months);
      
      // Get client data
      const allUsages = @json(\App\Models\PromotionUsage::with('client')->get());
      const allClients = @json(\App\Models\User::clients()->get());
      
      // Filter usages by date
      const filteredUsages = allUsages.filter(usage => {
        const usageDate = new Date(usage.fecha_uso);
        return usageDate >= cutoffDate && usage.client;
      });
      
      // Group by category
      const categoryStats = {
        'Inicial': { total: 0, accepted: 0, rejected: 0, pending: 0, unique_clients: new Set() },
        'Medium': { total: 0, accepted: 0, rejected: 0, pending: 0, unique_clients: new Set() },
        'Premium': { total: 0, accepted: 0, rejected: 0, pending: 0, unique_clients: new Set() }
      };
      
      filteredUsages.forEach(usage => {
        const cat = usage.client.categoria_cliente;
        if (categoryStats[cat]) {
          categoryStats[cat].total++;
          if (usage.estado === 'aceptada') categoryStats[cat].accepted++;
          if (usage.estado === 'rechazada') categoryStats[cat].rejected++;
          if (usage.estado === 'enviada') categoryStats[cat].pending++;
          categoryStats[cat].unique_clients.add(usage.client_id);
        }
      });
      
      // Count total clients per category
      const categoryTotals = {
        'Inicial': allClients.filter(c => c.categoria_cliente === 'Inicial').length,
        'Medium': allClients.filter(c => c.categoria_cliente === 'Medium').length,
        'Premium': allClients.filter(c => c.categoria_cliente === 'Premium').length
      };
      
      // Calculate percentages for progress bar
      const totalClients = allClients.length;
      const inicialPercent = totalClients > 0 ? (categoryTotals['Inicial'] / totalClients) * 100 : 0;
      const mediumPercent = totalClients > 0 ? (categoryTotals['Medium'] / totalClients) * 100 : 0;
      const premiumPercent = totalClients > 0 ? (categoryTotals['Premium'] / totalClients) * 100 : 0;
      
      // Build HTML
      let html = '';
      if (filteredUsages.length > 0 || totalClients > 0) {
        // Cards for each category
        html += `
          <div class="row mb-4">
            <div class="col-md-4">
              <div class="card border-secondary">
                <div class="card-body">
                  <h5 class="card-title text-secondary"><i class="bi bi-person"></i> Inicial</h5>
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted">Total clientes:</span>
                    <strong>${categoryTotals['Inicial']}</strong>
                  </div>
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted">Solicitudes:</span>
                    <strong>${categoryStats['Inicial'].total}</strong>
                  </div>
                  <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted">Clientes activos:</span>
                    <strong>${categoryStats['Inicial'].unique_clients.size}</strong>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card border-primary">
                <div class="card-body">
                  <h5 class="card-title text-primary"><i class="bi bi-person-check"></i> Medium</h5>
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted">Total clientes:</span>
                    <strong>${categoryTotals['Medium']}</strong>
                  </div>
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted">Solicitudes:</span>
                    <strong>${categoryStats['Medium'].total}</strong>
                  </div>
                  <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted">Clientes activos:</span>
                    <strong>${categoryStats['Medium'].unique_clients.size}</strong>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card" style="border-color: #8e44ad;">
                <div class="card-body">
                  <h5 class="card-title" style="color: #8e44ad;"><i class="bi bi-star-fill"></i> Premium</h5>
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted">Total clientes:</span>
                    <strong>${categoryTotals['Premium']}</strong>
                  </div>
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted">Solicitudes:</span>
                    <strong>${categoryStats['Premium'].total}</strong>
                  </div>
                  <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted">Clientes activos:</span>
                    <strong>${categoryStats['Premium'].unique_clients.size}</strong>
                  </div>
                </div>
              </div>
            </div>
          </div>`;
        
        // Progress bar showing client distribution
        html += `
          <h5 class="h6 mb-3">Distribución de Clientes por Categoría</h5>
          <div class="progress mb-4" style="height: 35px;">
            <div class="progress-bar bg-secondary" style="width: ${inicialPercent}%" 
                 title="Inicial: ${inicialPercent.toFixed(1)}%">
              ${inicialPercent > 10 ? 'Inicial (' + Math.round(inicialPercent) + '%)' : ''}
            </div>
            <div class="progress-bar bg-primary" style="width: ${mediumPercent}%"
                 title="Medium: ${mediumPercent.toFixed(1)}%">
              ${mediumPercent > 10 ? 'Medium (' + Math.round(mediumPercent) + '%)' : ''}
            </div>
            <div class="progress-bar" style="width: ${premiumPercent}%; background-color: #8e44ad;"
                 title="Premium: ${premiumPercent.toFixed(1)}%">
              ${premiumPercent > 10 ? 'Premium (' + Math.round(premiumPercent) + '%)' : ''}
            </div>
          </div>`;
        
        // Activity table
        html += `
          <h5 class="h6 mb-3">Actividad por Categoría</h5>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead class="table-light">
                <tr>
                  <th>Categoría</th>
                  <th class="text-center">Total Clientes</th>
                  <th class="text-center">Clientes Activos</th>
                  <th class="text-center">Total Solicitudes</th>
                  <th class="text-center">Aceptadas</th>
                  <th class="text-center">Rechazadas</th>
                  <th class="text-center">Pendientes</th>
                </tr>
              </thead>
              <tbody>`;
        
        const categories = [
          { name: 'Inicial', color: 'secondary' },
          { name: 'Medium', color: 'primary' },
          { name: 'Premium', color: 'purple' }
        ];
        
        categories.forEach(cat => {
          const stats = categoryStats[cat.name];
          const badgeStyle = cat.color === 'purple' ? 
            'style="background-color: #8e44ad !important; color: white !important;"' : '';
          const badgeClass = cat.color === 'purple' ? 'bg-light text-dark' : `bg-${cat.color}`;
          
          html += `
            <tr>
              <td><span class="badge ${badgeClass}" ${badgeStyle}>${cat.name}</span></td>
              <td class="text-center">${categoryTotals[cat.name]}</td>
              <td class="text-center"><strong>${stats.unique_clients.size}</strong></td>
              <td class="text-center"><strong>${stats.total}</strong></td>
              <td class="text-center"><span class="badge bg-success">${stats.accepted}</span></td>
              <td class="text-center"><span class="badge bg-danger">${stats.rejected}</span></td>
              <td class="text-center"><span class="badge bg-warning text-dark">${stats.pending}</span></td>
            </tr>`;
        });
        
        html += `
              </tbody>
            </table>
          </div>`;
      } else {
        html = '<div class="alert alert-info"><i class="bi bi-info-circle"></i> No hay actividad de clientes en el período seleccionado.</div>';
      }
      
      container.innerHTML = html;
    }
  });
</script>
@endpush