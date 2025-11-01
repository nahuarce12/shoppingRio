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
            <h2 class="h5">Juan Pérez</h2>
            <p class="text-muted mb-2">juan.perez@email.com</p>
            <span class="badge badge-medium badge-category fs-6">Cliente Medium</span>

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
                  <p class="mb-0">Juan Pérez</p>
                </div>
                <div class="col-md-6 mb-3">
                  <strong>Email:</strong>
                  <p class="mb-0">juan.perez@email.com</p>
                </div>
                <div class="col-md-6 mb-3">
                  <strong>Teléfono:</strong>
                  <p class="mb-0">+54 9 341 123-4567</p>
                </div>
                <div class="col-md-6 mb-3">
                  <strong>Fecha de Nacimiento:</strong>
                  <p class="mb-0">15/05/1990</p>
                </div>
                <div class="col-md-6 mb-3">
                  <strong>Categoría:</strong>
                  <p class="mb-0"><span class="badge badge-medium badge-category">Medium</span></p>
                </div>
                <div class="col-md-6 mb-3">
                  <strong>Miembro desde:</strong>
                  <p class="mb-0">Enero 2024</p>
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
                    <h3 class="text-primary">12</h3>
                    <p class="mb-0">Promociones Utilizadas</p>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="p-3 border rounded">
                    <h3 class="text-success">$15,000</h3>
                    <p class="mb-0">Ahorro Total</p>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="p-3 border rounded">
                    <h3 class="text-warning">3</h3>
                    <p class="mb-0">Para Premium</p>
                  </div>
                </div>
              </div>

              <h3 class="h6">Progreso a Categoría Premium</h3>
              <div class="progress" style="height: 25px;">
                <div class="progress-bar bg-success" role="progressbar" style="width: 80%;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100">12 de 15 promociones</div>
              </div>
              <p class="text-muted mt-2">Te faltan 3 promociones para alcanzar la categoría Premium.</p>
            </div>
          </div>
        </div>

        <div id="mis-promociones" class="client-section" style="display: none;">
          <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
              <h2 class="h5 mb-0"><i class="bi bi-tag-fill"></i> Mis Promociones Reclamadas</h2>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Fecha</th>
                      <th>Local</th>
                      <th>Promoción</th>
                      <th>Estado</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ([
                    ['fecha' => '20/10/2025', 'local' => 'Fashion Store', 'promo' => '50% OFF segunda unidad', 'estado' => ['label' => 'Usada', 'class' => 'success']],
                    ['fecha' => '18/10/2025', 'local' => 'Tech World', 'promo' => '20% OFF accesorios', 'estado' => ['label' => 'Usada', 'class' => 'success']],
                    ['fecha' => '15/10/2025', 'local' => 'Bella Italia', 'promo' => '2x1 platos principales', 'estado' => ['label' => 'Pendiente', 'class' => 'info']],
                    ['fecha' => '12/10/2025', 'local' => 'Sport Zone', 'promo' => '3x2 medias deportivas', 'estado' => ['label' => 'Usada', 'class' => 'success']],
                    ['fecha' => '10/10/2025', 'local' => 'Home Deco', 'promo' => '25% OFF decoración', 'estado' => ['label' => 'Usada', 'class' => 'success']],
                    ] as $uso)
                    <tr>
                      <td>{{ $uso['fecha'] }}</td>
                      <td><i class="bi bi-shop"></i> {{ $uso['local'] }}</td>
                      <td>{{ $uso['promo'] }}</td>
                      <td><span class="badge bg-{{ $uso['estado']['class'] }}">{{ $uso['estado']['label'] }}</span></td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
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
                    <input type="text" class="form-control" value="Juan">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Apellido</label>
                    <input type="text" class="form-control" value="Pérez">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" value="juan.perez@email.com">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Teléfono</label>
                    <input type="tel" class="form-control" value="+54 9 341 123-4567">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Fecha de Nacimiento</label>
                    <input type="date" class="form-control" value="1990-05-15">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Ciudad</label>
                    <input type="text" class="form-control" value="Rosario">
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