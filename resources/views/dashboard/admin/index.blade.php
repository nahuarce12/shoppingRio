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
                      <td>{{ str_pad($store->codigo, 3, '0', STR_PAD_LEFT) }}</td>
                      <td>{{ $store->nombre }}</td>
                      <td>{{ ucwords(str_replace(['-', '_'], ' ', $store->rubro)) }}</td>
                      <td>{{ $store->owner?->name ?? 'Sin asignar' }}</td>
                      <td>
                        <span class="badge {{ $store->trashed() ? 'bg-secondary' : 'bg-success' }}">
                          {{ $store->trashed() ? 'Inactivo' : 'Activo' }}
                        </span>
                      </td>
                      <td class="d-flex gap-2">
                        <a href="{{ route('admin.stores.show', $store->id) }}" class="btn btn-sm btn-outline-primary" title="Ver detalles">
                          <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('admin.stores.edit', $store->id) }}" class="btn btn-sm btn-info" title="Editar">
                          <i class="bi bi-pencil"></i>
                        </a>
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
                      <td>{{ $promotion->store?->nombre ?? 'Local eliminado' }}</td>
                      <td>{{ Str::limit($promotion->texto, 80) }}</td>
                      <td><span class="badge badge-{{ strtolower($promotion->categoria_minima) }}">{{ $promotion->categoria_minima }}</span></td>
                      <td>{{ $promotion->fecha_desde?->format('d/m/Y') }} - {{ $promotion->fecha_hasta?->format('d/m/Y') }}</td>
                      <td><span class="badge bg-warning text-dark text-uppercase">{{ $promotion->estado }}</span></td>
                      <td class="d-flex gap-2">
                        <form action="{{ route('admin.promotions.approve', $promotion->id) }}" method="POST" onsubmit="return confirm('¿Aprobar la promoción \"{{ addslashes($promotion->texto) }}\"?');">
                          @csrf
                          <button type="submit" class="btn btn-success btn-sm">
                            <i class="bi bi-check-lg"></i> Aprobar
                          </button>
                        </form>
                        <form action="{{ route('admin.promotions.deny', $promotion->id) }}" method="POST" onsubmit="return confirm('¿Denegar la promoción \"{{ addslashes($promotion->texto) }}\"?');">
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
                      $isActive = $news->fecha_hasta?->isFuture() ?? false;
                    @endphp
                    <tr>
                      <td>{{ Str::limit($news->texto, 80) }}</td>
                      <td><span class="badge badge-{{ strtolower($news->categoria_destino) }}">{{ $news->categoria_destino }}</span></td>
                      <td>{{ $news->fecha_desde?->format('d/m/Y') }}</td>
                      <td>{{ $news->fecha_hasta?->format('d/m/Y') }}</td>
                      <td>
                        <span class="badge {{ $isActive ? 'bg-success' : 'bg-secondary' }}">
                          {{ $isActive ? 'Vigente' : 'Expirada' }}
                        </span>
                      </td>
                      <td class="d-flex gap-2">
                        <a href="{{ route('admin.news.edit', $news->id) }}" class="btn btn-sm btn-info" title="Editar">
                          <i class="bi bi-pencil"></i>
                        </a>
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
            <div class="card-header bg-info text-white">
              <h2 class="h5 mb-0"><i class="bi bi-graph-up"></i> Reportes Gerenciales</h2>
            </div>
            <div class="card-body">
              <div class="row mb-4">
                <div class="col-md-6">
                  <label class="form-label">Período</label>
                  <select class="form-select">
                    <option>Última semana</option>
                    <option>Último mes</option>
                    <option selected>Últimos 3 meses</option>
                    <option>Último año</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Local</label>
                  <select class="form-select">
                    <option value="" selected>Todos los locales</option>
                    @foreach($stores->whereNull('deleted_at') as $store)
                      <option value="{{ $store->id }}">{{ $store->nombre }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <h3 class="h6">Resumen General del Shopping</h3>
              <div class="row text-center mb-4">
                <div class="col-md-3">
                  <h4>{{ $stats['clients_total'] }}</h4>
                  <p>Clientes Totales</p>
                </div>
                <div class="col-md-3">
                  <h4>{{ $usageStats['total'] }}</h4>
                  <p>Descuentos Utilizados</p>
                </div>
                <div class="col-md-3">
                  <h4>{{ $stats['stores_active'] }}</h4>
                  <p>Locales Activos</p>
                </div>
                <div class="col-md-3">
                  <h4>{{ $usageStats['this_month'] }}</h4>
                  <p>Este Mes</p>
                </div>
              </div>

              <h3 class="h6">Locales Más Activos</h3>
              @if($topStores->count() > 0)
              <div class="alert alert-info">
                @foreach($topStores as $index => $store)
                  <strong>{{ $index + 1 }}. {{ $store->nombre }}</strong> - {{ $store->usage_count }} promociones utilizadas<br>
                @endforeach
              </div>
              @else
              <div class="alert alert-warning">
                <i class="bi bi-info-circle"></i> Aún no hay datos de uso de promociones.
              </div>
              @endif

              <h3 class="h6">Distribución por Categoría de Cliente</h3>
              @php
                $total = array_sum($categoryDistribution);
                $inicialPercent = $total > 0 ? ($categoryDistribution['Inicial'] / $total) * 100 : 0;
                $mediumPercent = $total > 0 ? ($categoryDistribution['Medium'] / $total) * 100 : 0;
                $premiumPercent = $total > 0 ? ($categoryDistribution['Premium'] / $total) * 100 : 0;
              @endphp
              <div class="progress mb-2" style="height: 30px;">
                <div class="progress-bar bg-secondary" style="width: {{ $inicialPercent }}%">Inicial ({{ round($inicialPercent) }}%)</div>
                <div class="progress-bar bg-primary" style="width: {{ $mediumPercent }}%">Medium ({{ round($mediumPercent) }}%)</div>
                <div class="progress-bar" style="width: {{ $premiumPercent }}%; background-color: #8e44ad;">Premium ({{ round($premiumPercent) }}%)</div>
              </div>

              <div class="d-flex gap-2 mt-3">
                <button class="btn btn-primary">
                  <i class="bi bi-download"></i> Descargar Reporte Completo (PDF)
                </button>
                <button class="btn btn-success">
                  <i class="bi bi-file-earmark-excel"></i> Exportar a Excel
                </button>
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
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title h5">Crear Nuevo Local</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <form>
          <div class="mb-3">
            <label class="form-label">Nombre del Local</label>
            <input type="text" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Categoría</label>
            <select class="form-select">
              <option>Moda y Accesorios</option>
              <option>Tecnología</option>
              <option>Gastronomía</option>
              <option>Deportes</option>
              <option>Otro</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea class="form-control" rows="3"></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary">Crear Local</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalNuevaNovedad" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title h5">Crear Nueva Novedad</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <form>
          <div class="mb-3">
            <label class="form-label">Título</label>
            <input type="text" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea class="form-control" rows="3"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Categoría de Cliente</label>
            <select class="form-select">
              <option>Inicial</option>
              <option>Medium</option>
              <option>Premium</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Fecha de Vencimiento</label>
            <input type="date" class="form-control">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success">Publicar Novedad</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
@vite('resources/js/frontoffice/main.js')
@vite('resources/js/frontoffice/perfil-admin.js')
@if(!empty($activeSection))
<script>
  document.addEventListener('DOMContentLoaded', function () {
    if (typeof window.showAdminSection === 'function') {
      window.showAdminSection('{{ $activeSection }}');
    }
  });
</script>
@endif
@endpush