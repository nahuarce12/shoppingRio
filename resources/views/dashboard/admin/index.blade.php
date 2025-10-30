@extends('layouts.dashboard')

@section('title', 'Panel de Administrador | Shopping Rosario')
@section('meta_description', 'Supervisá locales, promociones y novedades desde el panel administrativo del Shopping Rosario.')

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
                  <h3 class="mt-2">48</h3>
                  <p class="mb-0">Locales Activos</p>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card text-center">
                <div class="card-body">
                  <i class="bi bi-people-fill fs-1 text-success"></i>
                  <h3 class="mt-2">1,245</h3>
                  <p class="mb-0">Clientes Registrados</p>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card text-center">
                <div class="card-body">
                  <i class="bi bi-tag-fill fs-1 text-warning"></i>
                  <h3 class="mt-2">5</h3>
                  <p class="mb-0">Promociones Pendientes</p>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card text-center">
                <div class="card-body">
                  <i class="bi bi-person-plus fs-1 text-info"></i>
                  <h3 class="mt-2">3</h3>
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
                    @foreach ([
                    ['codigo' => '001', 'nombre' => 'Fashion Store', 'categoria' => 'Moda y Accesorios', 'dueno' => 'María González', 'estado' => 'Activo'],
                    ['codigo' => '002', 'nombre' => 'Tech World', 'categoria' => 'Tecnología', 'dueno' => 'Carlos Pérez', 'estado' => 'Activo'],
                    ['codigo' => '003', 'nombre' => 'Bella Italia', 'categoria' => 'Gastronomía', 'dueno' => 'Giovanni Rossi', 'estado' => 'Activo'],
                    ] as $local)
                    <tr>
                      <td>{{ $local['codigo'] }}</td>
                      <td>{{ $local['nombre'] }}</td>
                      <td>{{ $local['categoria'] }}</td>
                      <td>{{ $local['dueno'] }}</td>
                      <td><span class="badge bg-success">{{ $local['estado'] }}</span></td>
                      <td class="d-flex gap-2">
                        <button class="btn btn-sm btn-info">
                          <i class="bi bi-pencil"></i>
                        </button>
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
                      <th>Local Solicitado</th>
                      <th>Email</th>
                      <th>CUIT</th>
                      <th>Fecha Solicitud</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ([
                    ['nombre' => 'Laura Martínez', 'local' => 'Beauty & Spa', 'email' => 'laura.martinez@beautyspa.com', 'cuit' => '27-34567890-2', 'fecha' => '20/10/2025'],
                    ['nombre' => 'Roberto Sánchez', 'local' => 'Sports Zone', 'email' => 'roberto@sportszone.com', 'cuit' => '20-28765432-9', 'fecha' => '19/10/2025'],
                    ['nombre' => 'Ana Rodríguez', 'local' => 'Café Central', 'email' => 'ana.rodriguez@cafecentral.com', 'cuit' => '27-39876543-1', 'fecha' => '18/10/2025'],
                    ] as $solicitud)
                    <tr>
                      <td>{{ $solicitud['nombre'] }}</td>
                      <td>{{ $solicitud['local'] }}</td>
                      <td>{{ $solicitud['email'] }}</td>
                      <td>{{ $solicitud['cuit'] }}</td>
                      <td>{{ $solicitud['fecha'] }}</td>
                      <td class="d-inline-flex gap-2">
                        <button class="btn btn-success btn-sm">
                          <i class="bi bi-check-lg"></i> Aprobar
                        </button>
                        <button class="btn btn-danger btn-sm">
                          <i class="bi bi-x-lg"></i> Denegar
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
                    @foreach ([
                    ['local' => 'Fashion Store', 'promo' => '30% OFF en toda la tienda', 'categoria' => 'Premium', 'vigencia' => '01/11 - 30/11/2025'],
                    ['local' => 'Tech World', 'promo' => '15% OFF en notebooks', 'categoria' => 'Medium', 'vigencia' => '25/10 - 15/12/2025'],
                    ['local' => 'Bella Italia', 'promo' => 'Menú ejecutivo $5000', 'categoria' => 'Inicial', 'vigencia' => '01/11 - 31/12/2025'],
                    ] as $promo)
                    <tr>
                      <td>{{ $promo['local'] }}</td>
                      <td>{{ $promo['promo'] }}</td>
                      <td><span class="badge badge-{{ strtolower($promo['categoria']) }}">{{ $promo['categoria'] }}</span></td>
                      <td>{{ $promo['vigencia'] }}</td>
                      <td><span class="badge bg-warning">Pendiente</span></td>
                      <td>
                        <button class="btn btn-success btn-sm">
                          <i class="bi bi-check-lg"></i> Aprobar
                        </button>
                        <button class="btn btn-danger btn-sm">
                          <i class="bi bi-x-lg"></i> Denegar
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
                      <th>Título</th>
                      <th>Categoría Cliente</th>
                      <th>Fecha Publicación</th>
                      <th>Vencimiento</th>
                      <th>Estado</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ([
                    ['titulo' => 'Nueva Temporada Primavera-Verano', 'categoria' => 'Premium', 'publicacion' => '20/10/2025', 'vence' => '15/12/2025'],
                    ['titulo' => 'Expo Tech 2025', 'categoria' => 'Inicial', 'publicacion' => '18/10/2025', 'vence' => '25/10/2025'],
                    ['titulo' => 'Semana Gastronómica', 'categoria' => 'Medium', 'publicacion' => '15/10/2025', 'vence' => '22/10/2025'],
                    ] as $novedad)
                    <tr>
                      <td>{{ $novedad['titulo'] }}</td>
                      <td><span class="badge badge-{{ strtolower($novedad['categoria']) }}">{{ $novedad['categoria'] }}</span></td>
                      <td>{{ $novedad['publicacion'] }}</td>
                      <td>{{ $novedad['vence'] }}</td>
                      <td><span class="badge bg-success">Vigente</span></td>
                      <td class="d-flex gap-2">
                        <button class="btn btn-sm btn-info">
                          <i class="bi bi-pencil"></i>
                        </button>
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
                    <option selected>Todos los locales</option>
                    <option>Fashion Store</option>
                    <option>Tech World</option>
                    <option>Bella Italia</option>
                  </select>
                </div>
              </div>

              <h3 class="h6">Resumen General del Shopping</h3>
              <div class="row text-center mb-4">
                <div class="col-md-3">
                  <h4>1,245</h4>
                  <p>Clientes Totales</p>
                </div>
                <div class="col-md-3">
                  <h4>3,892</h4>
                  <p>Descuentos Utilizados</p>
                </div>
                <div class="col-md-3">
                  <h4>$1,250,000</h4>
                  <p>Valor Total Descuentos</p>
                </div>
                <div class="col-md-3">
                  <h4>+35%</h4>
                  <p>vs. Período Anterior</p>
                </div>
              </div>

              <h3 class="h6">Locales Más Activos</h3>
              <div class="alert alert-info">
                <strong>1. Tech World</strong> - 850 promociones utilizadas<br>
                <strong>2. Fashion Store</strong> - 720 promociones utilizadas<br>
                <strong>3. Bella Italia</strong> - 650 promociones utilizadas
              </div>

              <h3 class="h6">Distribución por Categoría de Cliente</h3>
              <div class="progress mb-2" style="height: 30px;">
                <div class="progress-bar bg-secondary" style="width: 45%">Inicial (45%)</div>
                <div class="progress-bar bg-primary" style="width: 35%">Medium (35%)</div>
                <div class="progress-bar" style="width: 20%; background-color: #8e44ad;">Premium (20%)</div>
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
@endpush