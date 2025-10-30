@extends('layouts.dashboard')

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
            <h2 class="h5">Fashion Store</h2>
            <p class="text-muted mb-2">Código: 001</p>
            <p class="text-muted mb-2">María González (Dueña)</p>
            <span class="badge bg-success">Cuenta Activa</span>

            <hr>

            <div class="d-grid gap-2">
              <button class="btn btn-primary btn-sm" onclick="showSection('dashboard')">
                <i class="bi bi-house"></i> Dashboard
              </button>
              <button class="btn btn-outline-primary btn-sm" onclick="showSection('mis-promociones')">
                <i class="bi bi-tag"></i> Mis Promociones
              </button>
              <button class="btn btn-outline-primary btn-sm" onclick="showSection('crear-promocion')">
                <i class="bi bi-plus-circle"></i> Crear Promoción
              </button>
              <button class="btn btn-outline-warning btn-sm" onclick="showSection('solicitudes')">
                <i class="bi bi-inbox"></i> Solicitudes <span class="badge bg-danger">3</span>
              </button>
              <button class="btn btn-outline-success btn-sm" onclick="showSection('reportes')">
                <i class="bi bi-graph-up"></i> Reportes
              </button>
              <button class="btn btn-outline-secondary btn-sm" onclick="showSection('editar-perfil')">
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
                  <h3 class="mt-2">5</h3>
                  <p class="mb-0">Promociones Activas</p>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card text-center">
                <div class="card-body">
                  <i class="bi bi-people-fill fs-1 text-success"></i>
                  <h3 class="mt-2">48</h3>
                  <p class="mb-0">Clientes Este Mes</p>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card text-center">
                <div class="card-body">
                  <i class="bi bi-inbox-fill fs-1 text-warning"></i>
                  <h3 class="mt-2">3</h3>
                  <p class="mb-0">Solicitudes Pendientes</p>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card text-center">
                <div class="card-body">
                  <i class="bi bi-graph-up fs-1 text-info"></i>
                  <h3 class="mt-2">+15%</h3>
                  <p class="mb-0">vs. Mes Anterior</p>
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
                    @foreach ([
                    ['titulo' => '50% OFF segunda unidad', 'categoria' => 'Inicial', 'hasta' => '15/11/2025', 'estado' => 'Aprobada'],
                    ['titulo' => '3x2 prendas seleccionadas', 'categoria' => 'Medium', 'hasta' => '30/12/2025', 'estado' => 'Aprobada'],
                    ['titulo' => '20% OFF toda la tienda', 'categoria' => 'Premium', 'hasta' => '31/01/2026', 'estado' => 'Pendiente'],
                    ] as $promo)
                    <tr>
                      <td>{{ $promo['titulo'] }}</td>
                      <td><span class="badge badge-{{ strtolower($promo['categoria']) }}">{{ $promo['categoria'] }}</span></td>
                      <td>{{ $promo['hasta'] }}</td>
                      <td>
                        <span class="badge {{ $promo['estado'] === 'Pendiente' ? 'bg-warning text-dark' : 'bg-success' }}">
                          {{ $promo['estado'] === 'Pendiente' ? 'Pendiente Aprobación' : 'Aprobada' }}
                        </span>
                      </td>
                      <td>
                        <button class="btn btn-sm btn-danger">
                          <i class="bi bi-trash"></i>
                        </button>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <div id="crear-promocion" class="content-section" style="display: none;">
          <div class="card mb-4">
            <div class="card-header bg-success text-white">
              <h2 class="h5 mb-0"><i class="bi bi-plus-circle"></i> Crear Nueva Promoción</h2>
            </div>
            <div class="card-body">
              <form>
                <div class="row">
                  <div class="col-md-12 mb-3">
                    <label class="form-label">Título de la Promoción *</label>
                    <input type="text" class="form-control" placeholder="Ej: 50% OFF en segunda unidad">
                  </div>
                  <div class="col-md-12 mb-3">
                    <label class="form-label">Descripción *</label>
                    <textarea class="form-control" rows="3" placeholder="Describe la promoción..."></textarea>
                  </div>
                  <div class="col-md-4 mb-3">
                    <label class="form-label">Categoría de Cliente *</label>
                    <select class="form-select">
                      <option value="inicial">Inicial</option>
                      <option value="medium">Medium</option>
                      <option value="premium">Premium</option>
                    </select>
                  </div>
                  <div class="col-md-4 mb-3">
                    <label class="form-label">Válido Desde *</label>
                    <input type="date" class="form-control">
                  </div>
                  <div class="col-md-4 mb-3">
                    <label class="form-label">Válido Hasta *</label>
                    <input type="date" class="form-control">
                  </div>
                  <div class="col-md-12 mb-3">
                    <label class="form-label">Días de la Semana *</label>
                    <div class="d-flex flex-wrap gap-2">
                      @foreach (['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'] as $dia)
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="dia-{{ strtolower($dia) }}">
                        <label class="form-check-label" for="dia-{{ strtolower($dia) }}">{{ $dia }}</label>
                      </div>
                      @endforeach
                    </div>
                  </div>
                </div>
                <div class="alert alert-info">
                  <i class="bi bi-info-circle"></i> La promoción será enviada al administrador para su aprobación.
                </div>
                <button type="submit" class="btn btn-success">
                  <i class="bi bi-check-lg"></i> Crear Promoción
                </button>
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
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Cliente</th>
                      <th>Promoción</th>
                      <th>Fecha Solicitud</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ([
                    ['cliente' => 'Juan Pérez', 'promocion' => '50% OFF segunda unidad', 'fecha' => '20/10/2025 14:30'],
                    ['cliente' => 'María López', 'promocion' => '3x2 en prendas seleccionadas', 'fecha' => '20/10/2025 16:15'],
                    ['cliente' => 'Carlos Gómez', 'promocion' => '20% OFF toda la tienda', 'fecha' => '21/10/2025 10:00'],
                    ] as $solicitud)
                    <tr>
                      <td><i class="bi bi-person-circle"></i> {{ $solicitud['cliente'] }}</td>
                      <td>{{ $solicitud['promocion'] }}</td>
                      <td>{{ $solicitud['fecha'] }}</td>
                      <td class="d-flex gap-2">
                        <button class="btn btn-success btn-sm">
                          <i class="bi bi-check-lg"></i> Aceptar
                        </button>
                        <button class="btn btn-danger btn-sm">
                          <i class="bi bi-x-lg"></i> Rechazar
                        </button>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
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
                  <select class="form-select">
                    <option>Última semana</option>
                    <option>Último mes</option>
                    <option selected>Últimos 3 meses</option>
                    <option>Último año</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Promoción</label>
                  <select class="form-select">
                    <option selected>Todas</option>
                    <option>50% OFF segunda unidad</option>
                    <option>3x2 prendas seleccionadas</option>
                  </select>
                </div>
              </div>

              <h3 class="h6">Resumen del Período</h3>
              <div class="row text-center mb-4">
                <div class="col-md-3">
                  <h4>48</h4>
                  <p>Clientes Totales</p>
                </div>
                <div class="col-md-3">
                  <h4>72</h4>
                  <p>Promociones Usadas</p>
                </div>
                <div class="col-md-3">
                  <h4>$45,000</h4>
                  <p>Descuentos Otorgados</p>
                </div>
                <div class="col-md-3">
                  <h4>+25%</h4>
                  <p>vs. Período Anterior</p>
                </div>
              </div>

              <h3 class="h6">Promoción Más Usada</h3>
              <div class="alert alert-success">
                <strong>50% OFF segunda unidad</strong> - Utilizada 35 veces
              </div>

              <button class="btn btn-primary">
                <i class="bi bi-download"></i> Descargar Reporte Completo
              </button>
            </div>
          </div>
        </div>

        <div id="editar-perfil" class="content-section" style="display: none;">
          <div class="card">
            <div class="card-header bg-secondary text-white">
              <h2 class="h5 mb-0"><i class="bi bi-pencil"></i> Editar Perfil del Local</h2>
            </div>
            <div class="card-body">
              <form>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Nombre del Local</label>
                    <input type="text" class="form-control" value="Fashion Store">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Categoría</label>
                    <select class="form-select">
                      <option selected>Moda y Accesorios</option>
                      <option>Tecnología</option>
                      <option>Gastronomía</option>
                      <option>Deportes</option>
                    </select>
                  </div>
                  <div class="col-md-12 mb-3">
                    <label class="form-label">Descripción</label>
                    <textarea class="form-control" rows="3">Las últimas tendencias en moda para toda la familia.</textarea>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Email del Responsable</label>
                    <input type="email" class="form-control" value="maria.gonzalez@fashionstore.com">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Teléfono</label>
                    <input type="tel" class="form-control" value="(0341) 456-7890">
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
@vite('resources/js/frontoffice/perfil-dueno.js')
@endpush