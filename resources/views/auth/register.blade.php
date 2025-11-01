@extends('layouts.app')

@section('title', 'Registrarse - Shopping Rosario')
@section('meta_description', 'Crea tu cuenta como cliente o dueño de local en Shopping Rosario y empezá a disfrutar de promociones exclusivas.')

@section('content')
<x-layout.breadcrumbs :items="[['label' => 'Registro']]" />

@php
$clientRegisterAction = Route::has('register') ? route('register') : '#';
$ownerRegisterAction = Route::has('store.register') ? route('store.register') : '#';
$loginUrl = Route::has('auth.login') ? route('auth.login') : (Route::has('login') ? route('login') : '#');
@endphp

<section class="py-5">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="card shadow" id="step1">
          <div class="card-body p-5">
            <div class="text-center mb-4">
              <i class="bi bi-person-plus-fill fs-1 text-primary"></i>
              <h1 class="mt-3 h3">Crear Cuenta</h1>
              <p class="text-muted">Seleccioná el tipo de cuenta que querés crear</p>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <div class="card h-100 border-primary" role="button" onclick="showClientForm()">
                  <div class="card-body text-center p-4">
                    <i class="bi bi-person-circle fs-1 text-primary"></i>
                    <h2 class="mt-3 h4">Cliente</h2>
                    <p class="text-muted">Accedé a promociones exclusivas</p>
                    <ul class="list-unstyled text-start mt-3">
                      <li><i class="bi bi-check-circle-fill text-success"></i> Ver promociones</li>
                      <li><i class="bi bi-check-circle-fill text-success"></i> Reclamar descuentos</li>
                      <li><i class="bi bi-check-circle-fill text-success"></i> Subir de categoría</li>
                      <li><i class="bi bi-check-circle-fill text-success"></i> Recibir novedades</li>
                    </ul>
                    <button type="button" class="btn btn-primary mt-2">Registrarme como Cliente</button>
                  </div>
                </div>
              </div>
              <div class="col-md-6 mb-3">
                <div class="card h-100 border-success" role="button" onclick="showOwnerForm()">
                  <div class="card-body text-center p-4">
                    <i class="bi bi-shop-window fs-1 text-success"></i>
                    <h2 class="mt-3 h4">Dueño de Local</h2>
                    <p class="text-muted">Gestioná las promociones de tu local</p>
                    <ul class="list-unstyled text-start mt-3">
                      <li><i class="bi bi-check-circle-fill text-success"></i> Crear promociones</li>
                      <li><i class="bi bi-check-circle-fill text-success"></i> Gestionar solicitudes</li>
                      <li><i class="bi bi-check-circle-fill text-success"></i> Ver reportes</li>
                      <li><i class="bi bi-check-circle-fill text-success"></i> Editar perfil</li>
                    </ul>
                    <button type="button" class="btn btn-success mt-2">Registrarme como Dueño</button>
                  </div>
                </div>
              </div>
            </div>

            <hr class="my-4">
            <div class="text-center">
              <p class="mb-0">¿Ya tenés cuenta? <a href="{{ $loginUrl }}">Iniciá sesión aquí</a></p>
            </div>
          </div>
        </div>

        <div class="card shadow" id="clienteForm" style="display: none;">
          <div class="card-body p-5">
            <div class="text-center mb-4">
              <i class="bi bi-person-circle fs-1 text-primary"></i>
              <h2 class="mt-3 h3">Registro de Cliente</h2>
              <p class="text-muted">Completá tus datos para crear tu cuenta</p>
            </div>

            <form method="POST" action="{{ $clientRegisterAction }}">
              @csrf
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="client-name" class="form-label">Nombre *</label>
                  <input type="text" class="form-control" id="client-name" name="name" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="client-lastname" class="form-label">Apellido *</label>
                  <input type="text" class="form-control" id="client-lastname" name="lastname" required>
                </div>
              </div>

              <div class="mb-3">
                <label for="client-email" class="form-label">Email *</label>
                <input type="email" class="form-control" id="client-email" name="email" required>
                <div class="form-text">Usaremos este email como tu nombre de usuario.</div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="client-password" class="form-label">Contraseña *</label>
                  <input type="password" class="form-control" id="client-password" name="password" required minlength="8">
                </div>
                <div class="col-md-6 mb-3">
                  <label for="client-password-confirmation" class="form-label">Confirmar Contraseña *</label>
                  <input type="password" class="form-control" id="client-password-confirmation" name="password_confirmation" required minlength="8">
                </div>
              </div>

              <div class="mb-3">
                <label for="client-phone" class="form-label">Teléfono *</label>
                <input type="tel" class="form-control" id="client-phone" name="phone" required>
              </div>

              <div class="mb-3">
                <label for="client-birthdate" class="form-label">Fecha de Nacimiento *</label>
                <input type="date" class="form-control" id="client-birthdate" name="birthdate" required>
              </div>

              <div class="mb-3">
                <label for="client-address" class="form-label">Dirección</label>
                <input type="text" class="form-control" id="client-address" name="address">
              </div>

              <div class="row">
                <div class="col-md-8 mb-3">
                  <label for="client-city" class="form-label">Ciudad</label>
                  <input type="text" class="form-control" id="client-city" name="city" value="Rosario">
                </div>
                <div class="col-md-4 mb-3">
                  <label for="client-postal" class="form-label">Código Postal</label>
                  <input type="text" class="form-control" id="client-postal" name="postal_code">
                </div>
              </div>

              <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="client-newsletter" name="newsletter">
                <label class="form-check-label" for="client-newsletter">
                  Quiero recibir novedades y promociones por email
                </label>
              </div>

              <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="client-terms" name="terms" required>
                <label class="form-check-label" for="client-terms">
                  Acepto los <a href="#" target="_blank">términos y condiciones</a> y la
                  <a href="#" target="_blank">política de privacidad</a> *
                </label>
              </div>

              <div class="alert alert-info" role="alert">
                <i class="bi bi-info-circle-fill"></i>
                <strong>Importante:</strong> Al registrarte comenzás como cliente <strong>Inicial</strong>. Usá promociones para subir automáticamente de categoría.
              </div>

              <div class="d-flex gap-2">
                <button type="button" class="btn btn-secondary" onclick="showStep1()">
                  <i class="bi bi-arrow-left"></i> Volver
                </button>
                <button type="submit" class="btn btn-primary flex-grow-1">
                  <i class="bi bi-person-plus"></i> Crear Cuenta de Cliente
                </button>
              </div>
            </form>
          </div>
        </div>

        <div class="card shadow" id="duenoForm" style="display: none;">
          <div class="card-body p-5">
            <div class="text-center mb-4">
              <i class="bi bi-shop-window fs-1 text-success"></i>
              <h2 class="mt-3 h3">Registro de Dueño de Local</h2>
              <p class="text-muted">Completá los datos de tu local para solicitar acceso</p>
            </div>

            <form method="POST" action="{{ $ownerRegisterAction }}">
              @csrf
              <h2 class="h5 mb-3"><i class="bi bi-building"></i> Datos del Local</h2>

              <div class="mb-3">
                <label for="store-name" class="form-label">Nombre del Local *</label>
                <input type="text" class="form-control" id="store-name" name="store_name" required>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="store-category" class="form-label">Categoría *</label>
                  <select class="form-select" id="store-category" name="store_category" required>
                    <option value="">Seleccioná una categoría</option>
                    <option value="moda">Moda y Accesorios</option>
                    <option value="tecnologia">Tecnología</option>
                    <option value="gastronomia">Gastronomía</option>
                    <option value="deportes">Deportes</option>
                    <option value="hogar">Hogar y Decoración</option>
                    <option value="otro">Otro</option>
                  </select>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="store-phone" class="form-label">Teléfono del Local *</label>
                  <input type="tel" class="form-control" id="store-phone" name="store_phone" required>
                </div>
              </div>

              <div class="mb-3">
                <label for="store-description" class="form-label">Descripción del Local *</label>
                <textarea class="form-control" id="store-description" name="store_description" rows="3" required></textarea>
              </div>

              <hr class="my-4">

              <h2 class="h5 mb-3"><i class="bi bi-person"></i> Datos del Responsable</h2>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="owner-name" class="form-label">Nombre *</label>
                  <input type="text" class="form-control" id="owner-name" name="owner_name" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="owner-lastname" class="form-label">Apellido *</label>
                  <input type="text" class="form-control" id="owner-lastname" name="owner_lastname" required>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="owner-dni" class="form-label">DNI *</label>
                  <input type="text" class="form-control" id="owner-dni" name="owner_dni" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="store-cuit" class="form-label">CUIT del Local *</label>
                  <input type="text" class="form-control" id="store-cuit" name="store_cuit" required>
                </div>
              </div>

              <div class="mb-3">
                <label for="owner-email" class="form-label">Email *</label>
                <input type="email" class="form-control" id="owner-email" name="owner_email" required>
                <div class="form-text">Este será tu usuario para ingresar al panel del local.</div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="owner-password" class="form-label">Contraseña *</label>
                  <input type="password" class="form-control" id="owner-password" name="password" required minlength="8">
                </div>
                <div class="col-md-6 mb-3">
                  <label for="owner-password-confirmation" class="form-label">Confirmar Contraseña *</label>
                  <input type="password" class="form-control" id="owner-password-confirmation" name="password_confirmation" required minlength="8">
                </div>
              </div>

              <div class="mb-3">
                <label for="owner-phone" class="form-label">Teléfono Personal *</label>
                <input type="tel" class="form-control" id="owner-phone" name="owner_phone" required>
              </div>

              <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="owner-terms" name="owner_terms" required>
                <label class="form-check-label" for="owner-terms">
                  Acepto los <a href="#" target="_blank">términos y condiciones</a> para dueños de locales *
                </label>
              </div>

              <div class="alert alert-warning" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <strong>Importante:</strong> Tu solicitud será revisada por el administrador del shopping. Recibirás un email cuando tu cuenta sea aprobada.
              </div>

              <div class="d-flex gap-2">
                <button type="button" class="btn btn-secondary" onclick="showStep1()">
                  <i class="bi bi-arrow-left"></i> Volver
                </button>
                <button type="submit" class="btn btn-success flex-grow-1">
                  <i class="bi bi-send"></i> Enviar Solicitud
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="row mt-5">
      <div class="col-12">
        <h2 class="h4 text-center mb-4">Beneficios de Registrarte</h2>
      </div>
      <div class="col-md-4 text-center mb-3">
        <i class="bi bi-tag-fill fs-1 text-primary"></i>
        <h3 class="h5 mt-2">Promociones Exclusivas</h3>
        <p>Accedé a descuentos que no están disponibles para usuarios no registrados.</p>
      </div>
      <div class="col-md-4 text-center mb-3">
        <i class="bi bi-arrow-up-circle-fill fs-1 text-primary"></i>
        <h3 class="h5 mt-2">Categorías Progresivas</h3>
        <p>Subí de categoría y desbloqueá promociones cada vez mejores.</p>
      </div>
      <div class="col-md-4 text-center mb-3">
        <i class="bi bi-bell-fill fs-1 text-primary"></i>
        <h3 class="h5 mt-2">Notificaciones</h3>
        <p>Recibí alertas sobre nuevas promociones en tus locales favoritos.</p>
      </div>
    </div>
  </div>
</section>
@endsection

@push('scripts')
@vite('resources/js/frontoffice/main.js')
@vite('resources/js/frontoffice/register.js')
@endpush