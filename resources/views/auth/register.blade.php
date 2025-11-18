@extends('layouts.app')

@section('title', 'Registrarse - Shopping Rosario')
@section('meta_description', 'Crea tu cuenta como cliente o dueño de local en Shopping Rosario y empezá a disfrutar de promociones exclusivas.')

@section('content')
<x-layout.breadcrumbs :items="[['label' => 'Registro']]" />

@php
$clientRegisterAction = Route::has('register') ? route('register') : '#';
$ownerRegisterAction = $clientRegisterAction;
$loginUrl = Route::has('auth.login') ? route('auth.login') : (Route::has('login') ? route('login') : '#');
@endphp

<section class="py-5">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="card shadow" id="step1">
          <div class="card-body p-5">
            <div class="text-center mb-4">
              <i class="bi bi-person-plus-fill fs-1" style = "color: #e74c3c"></i>
              <h1 class="mt-3 h3">Crear Cuenta</h1>
              <p class="text-muted">Seleccioná el tipo de cuenta que querés crear</p>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <div class="card h-100" style="border-color: #e74c3c" role="button" onclick="showClientForm()">
                  <div class="card-body text-center p-4">
                    <i class="bi bi-person-circle fs-1" style="color: #e74c3c"></i>
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

            <form method="POST" action="{{ $clientRegisterAction }}" id="clientForm" novalidate">
              @csrf
              <input type="hidden" name="user_type" value="cliente">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="client-name" class="form-label">Nombre *</label>
                  <input type="text" 
                         class="form-control @error('name') is-invalid @enderror" 
                         id="client-name" 
                         name="name" 
                         value="{{ old('name') }}"
                         required 
                         minlength="2"
                         maxlength="50"
                         pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+"
                         placeholder="Tu nombre">
                  @error('name')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @else
                  <div class="invalid-feedback">El nombre debe tener entre 2 y 50 caracteres (solo letras).</div>
                  @enderror
                </div>
                <div class="col-md-6 mb-3">
                  <label for="client-lastname" class="form-label">Apellido *</label>
                  <input type="text" 
                         class="form-control @error('lastname') is-invalid @enderror" 
                         id="client-lastname" 
                         name="lastname" 
                         value="{{ old('lastname') }}"
                         required 
                         minlength="2"
                         maxlength="50"
                         pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+"
                         placeholder="Tu apellido">
                  @error('lastname')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @else
                  <div class="invalid-feedback">El apellido debe tener entre 2 y 50 caracteres (solo letras).</div>
                  @enderror
                </div>
              </div>

              <div class="mb-3">
                <label for="client-email" class="form-label">Email *</label>
                <input type="email" 
                       class="form-control @error('email') is-invalid @enderror" 
                       id="client-email" 
                       name="email" 
                       value="{{ old('email') }}"
                       required
                       placeholder="tu@email.com">
                @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
                @else
                <div class="form-text">Usaremos este email como tu nombre de usuario.</div>
                <div class="invalid-feedback">Por favor ingresá un email válido.</div>
                @enderror
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="client-password" class="form-label">Contraseña *</label>
                  <div class="input-group">
                    <input type="password" 
                           class="form-control @error('password') is-invalid @enderror" 
                           id="client-password" 
                           name="password" 
                           required 
                           minlength="8"
                           placeholder="Mínimo 8 caracteres">
                    <button class="btn btn-outline-secondary" type="button" id="toggleClientPassword">
                      <i class="bi bi-eye"></i>
                    </button>
                    @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @else
                    <div class="invalid-feedback">La contraseña debe tener al menos 8 caracteres.</div>
                    @enderror
                  </div>
                  <div class="form-text" id="passwordHelp">
                    <small>Debe tener al menos 8 caracteres.</small>
                  </div>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="client-password-confirmation" class="form-label">Confirmar Contraseña *</label>
                  <div class="input-group">
                    <input type="password" 
                           class="form-control" 
                           id="client-password-confirmation" 
                           name="password_confirmation" 
                           required 
                           minlength="8"
                           placeholder="Repetí tu contraseña">
                    <button class="btn btn-outline-secondary" type="button" id="toggleClientPasswordConfirm">
                      <i class="bi bi-eye"></i>
                    </button>
                    <div class="invalid-feedback">Las contraseñas no coinciden.</div>
                  </div>
                </div>
              </div>

              <div class="mb-3">
                <label for="client-phone" class="form-label">Teléfono *</label>
                <input type="tel" 
                       class="form-control @error('phone') is-invalid @enderror" 
                       id="client-phone" 
                       name="phone" 
                       value="{{ old('phone') }}"
                       required
                       pattern="[0-9\s\-\+\(\)]+"
                       placeholder="Ej: 0341-1234567">
                @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
                @else
                <div class="invalid-feedback">Por favor ingresá un teléfono válido.</div>
                @enderror
              </div>

              <div class="mb-3">
                <label for="client-birthdate" class="form-label">Fecha de Nacimiento *</label>
                <input type="date" 
                       class="form-control @error('birthdate') is-invalid @enderror" 
                       id="client-birthdate" 
                       name="birthdate" 
                       value="{{ old('birthdate') }}"
                       required
                       max="{{ date('Y-m-d', strtotime('-18 years')) }}">
                @error('birthdate')
                <div class="invalid-feedback">{{ $message }}</div>
                @else
                <div class="invalid-feedback">Debes ser mayor de 18 años para registrarte.</div>
                @enderror
              </div>

              <div class="mb-3">
                <label for="client-address" class="form-label">Dirección</label>
                <input type="text" 
                       class="form-control @error('address') is-invalid @enderror" 
                       id="client-address" 
                       name="address" 
                       value="{{ old('address') }}"
                       maxlength="100"
                       placeholder="Calle y número">
                @error('address')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="row">
                <div class="col-md-8 mb-3">
                  <label for="client-city" class="form-label">Ciudad</label>
                  <input type="text" 
                         class="form-control @error('city') is-invalid @enderror" 
                         id="client-city" 
                         name="city" 
                         value="{{ old('city', 'Rosario') }}"
                         maxlength="50">
                  @error('city')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-4 mb-3">
                  <label for="client-postal" class="form-label">Código Postal</label>
                  <input type="text" 
                         class="form-control @error('postal_code') is-invalid @enderror" 
                         id="client-postal" 
                         name="postal_code" 
                         value="{{ old('postal_code') }}"
                         pattern="[0-9]{4}"
                         placeholder="2000">
                  @error('postal_code')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @else
                  <div class="invalid-feedback">Código postal de 4 dígitos.</div>
                  @enderror
                </div>
              </div>

              <div class="mb-3 form-check">
                <input type="checkbox" 
                       class="form-check-input" 
                       id="client-newsletter" 
                       name="newsletter"
                       {{ old('newsletter') ? 'checked' : '' }}>
                <label class="form-check-label" for="client-newsletter">
                  Quiero recibir novedades y promociones por email
                </label>
              </div>

              <div class="mb-3 form-check">
                <input type="checkbox" 
                       class="form-check-input @error('terms') is-invalid @enderror" 
                       id="client-terms" 
                       name="terms" 
                       required
                       {{ old('terms') ? 'checked' : '' }}>
                <label class="form-check-label" for="client-terms">
                  Acepto los <a href="#" target="_blank">términos y condiciones</a> y la
                  <a href="#" target="_blank">política de privacidad</a> *
                </label>
                @error('terms')
                <div class="invalid-feedback">{{ $message }}</div>
                @else
                <div class="invalid-feedback">Debes aceptar los términos y condiciones.</div>
                @enderror
              </div>

              <div class="alert alert-info" role="alert">
                <i class="bi bi-info-circle-fill"></i>
                <strong>Importante:</strong> Al registrarte comenzás como cliente <strong>Inicial</strong>. Usá promociones para subir automáticamente de categoría.
              </div>

              <div class="d-flex gap-2">
                <button type="button" class="btn btn-secondary" onclick="showStep1()">
                  <i class="bi bi-arrow-left"></i> Volver
                </button>
                <button type="submit" class="btn btn-primary flex-grow-1" id="clientSubmitBtn">
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

            <form method="POST" action="{{ $ownerRegisterAction }}" id="ownerForm" novalidate>
              @csrf
              <input type="hidden" name="user_type" value="dueño de local">
              
              <h2 class="h5 mb-3"><i class="bi bi-building"></i> Seleccionar Local</h2>

              <div class="alert alert-info mb-4">
                <i class="bi bi-info-circle-fill"></i>
                <strong>Importante:</strong> Seleccioná el local del shopping que vas a administrar. Los locales son creados por el administrador del shopping.
              </div>

              <div class="mb-4">
                <label for="store-select" class="form-label">Local *</label>
                <select class="form-select @error('store_id') is-invalid @enderror" 
                        id="store-select" 
                        name="store_id" 
                        required>
                  <option value="">Seleccioná tu local</option>
                  @foreach($stores as $store)
                    <option value="{{ $store->id }}" {{ old('store_id') == $store->id ? 'selected' : '' }}>
                      {{ $store->name }} - {{ $store->location }} ({{ ucfirst($store->category) }})
                    </option>
                  @endforeach
                </select>
                @error('store_id')
                <div class="invalid-feedback">{{ $message }}</div>
                @else
                <div class="invalid-feedback">Por favor seleccioná tu local.</div>
                @enderror
                <div class="form-text">
                  <small>Si tu local no aparece en la lista, contactá al administrador del shopping.</small>
                </div>
              </div>

              <hr class="my-4">

              <h2 class="h5 mb-3"><i class="bi bi-person"></i> Tus Datos</h2>

              <div class="row">
                <div class="col-md-12 mb-3">
                  <label for="owner-name" class="form-label">Nombre Completo *</label>
                  <input type="text" 
                         class="form-control @error('name') is-invalid @enderror" 
                         id="owner-name" 
                         name="name" 
                         value="{{ old('name') }}"
                         required
                         minlength="3"
                         maxlength="255"
                         placeholder="Tu nombre completo">
                  @error('name')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @else
                  <div class="invalid-feedback">Ingresá tu nombre completo (mínimo 3 caracteres).</div>
                  @enderror
                </div>
              </div>

              <div class="mb-3">
                <label for="owner-email" class="form-label">Email *</label>
                <input type="email" 
                       class="form-control @error('email') is-invalid @enderror" 
                       id="owner-email" 
                       name="email" 
                       value="{{ old('email') }}"
                       required
                       placeholder="tu@email.com">
                <div class="form-text">Este será tu usuario para ingresar al panel del local.</div>
                @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
                @else
                <div class="invalid-feedback">Por favor ingresá un email válido.</div>
                @enderror
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="owner-password" class="form-label">Contraseña *</label>
                  <div class="input-group">
                    <input type="password" 
                           class="form-control @error('password') is-invalid @enderror" 
                           id="owner-password" 
                           name="password" 
                           required 
                           minlength="8"
                           placeholder="Mínimo 8 caracteres">
                    <button class="btn btn-outline-secondary" type="button" id="toggleOwnerPassword">
                      <i class="bi bi-eye"></i>
                    </button>
                    @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @else
                    <div class="invalid-feedback">Mínimo 8 caracteres.</div>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="owner-password-confirmation" class="form-label">Confirmar Contraseña *</label>
                  <div class="input-group">
                    <input type="password" 
                           class="form-control" 
                           id="owner-password-confirmation" 
                           name="password_confirmation" 
                           required 
                           minlength="8"
                           placeholder="Repetí tu contraseña">
                    <button class="btn btn-outline-secondary" type="button" id="toggleOwnerPasswordConfirm">
                      <i class="bi bi-eye"></i>
                    </button>
                    <div class="invalid-feedback">Las contraseñas no coinciden.</div>
                  </div>
                </div>
              </div>

              <div class="mb-3 form-check">
                <input type="checkbox" 
                       class="form-check-input @error('terms') is-invalid @enderror" 
                       id="owner-terms" 
                       name="terms" 
                       required
                       {{ old('terms') ? 'checked' : '' }}>
                <label class="form-check-label" for="owner-terms">
                  Acepto los <a href="#" target="_blank">términos y condiciones</a> para dueños de locales *
                </label>
                @error('terms')
                <div class="invalid-feedback">{{ $message }}</div>
                @else
                <div class="invalid-feedback">Debes aceptar los términos y condiciones.</div>
                @enderror
              </div>

              <div class="alert alert-warning" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <strong>Importante:</strong> Tu solicitud será revisada por el administrador del shopping. Recibirás un email cuando tu cuenta sea aprobada.
              </div>

              <div class="d-flex gap-2">
                <button type="button" class="btn btn-secondary" onclick="showStep1()">
                  <i class="bi bi-arrow-left"></i> Volver
                </button>
                <button type="submit" class="btn btn-success flex-grow-1" id="ownerSubmitBtn">
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
        <i class="bi bi-tag-fill fs-1" style="color: #e74c3c"></i>
        <h3 class="h5 mt-2">Promociones Exclusivas</h3>
        <p>Accedé a descuentos que no están disponibles para usuarios no registrados.</p>
      </div>
      <div class="col-md-4 text-center mb-3">
        <i class="bi bi-arrow-up-circle-fill fs-1" style="color: #e74c3c"></i>
        <h3 class="h5 mt-2">Categorías Progresivas</h3>
        <p>Subí de categoría y desbloqueá promociones cada vez mejores.</p>
      </div>
      <div class="col-md-4 text-center mb-3">
        <i class="bi bi-bell-fill fs-1" style="color: #e74c3c"></i>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
  // ========== CLIENT FORM VALIDATION ==========
  const clientForm = document.getElementById('clientForm');
  const clientPassword = document.getElementById('client-password');
  const clientPasswordConfirm = document.getElementById('client-password-confirmation');
  const toggleClientPassword = document.getElementById('toggleClientPassword');
  const toggleClientPasswordConfirm = document.getElementById('toggleClientPasswordConfirm');

  // Toggle client password visibility
  if (toggleClientPassword) {
    toggleClientPassword.addEventListener('click', function() {
      const type = clientPassword.getAttribute('type') === 'password' ? 'text' : 'password';
      clientPassword.setAttribute('type', type);
      this.querySelector('i').classList.toggle('bi-eye');
      this.querySelector('i').classList.toggle('bi-eye-slash');
    });
  }

  if (toggleClientPasswordConfirm) {
    toggleClientPasswordConfirm.addEventListener('click', function() {
      const type = clientPasswordConfirm.getAttribute('type') === 'password' ? 'text' : 'password';
      clientPasswordConfirm.setAttribute('type', type);
      this.querySelector('i').classList.toggle('bi-eye');
      this.querySelector('i').classList.toggle('bi-eye-slash');
    });
  }

  // Client form validation
  if (clientForm) {
    clientForm.addEventListener('submit', function(event) {
      // Check password match
      if (clientPassword.value !== clientPasswordConfirm.value) {
        event.preventDefault();
        event.stopPropagation();
        clientPasswordConfirm.setCustomValidity('Las contraseñas no coinciden');
        clientPasswordConfirm.classList.add('is-invalid');
      } else {
        clientPasswordConfirm.setCustomValidity('');
        clientPasswordConfirm.classList.remove('is-invalid');
      }

      if (!clientForm.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      } else {
        // Disable submit button
        const submitBtn = document.getElementById('clientSubmitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Creando cuenta...';
      }
      
      clientForm.classList.add('was-validated');
    }, false);

    // Real-time password match validation
    clientPasswordConfirm.addEventListener('input', function() {
      if (this.value !== clientPassword.value) {
        this.setCustomValidity('Las contraseñas no coinciden');
        this.classList.add('is-invalid');
        this.classList.remove('is-valid');
      } else {
        this.setCustomValidity('');
        this.classList.remove('is-invalid');
        if (this.value.length >= 8) {
          this.classList.add('is-valid');
        }
      }
    });
  }

  // ========== OWNER FORM VALIDATION ==========
  const ownerForm = document.getElementById('ownerForm');
  const ownerPassword = document.getElementById('owner-password');
  const ownerPasswordConfirm = document.getElementById('owner-password-confirmation');
  const toggleOwnerPassword = document.getElementById('toggleOwnerPassword');
  const toggleOwnerPasswordConfirm = document.getElementById('toggleOwnerPasswordConfirm');
  const storeDescription = document.getElementById('store-description');
  const storeDescCounter = document.getElementById('storeDescCounter');

  // Store description character counter
  if (storeDescription && storeDescCounter) {
    storeDescription.addEventListener('input', function() {
      storeDescCounter.textContent = this.value.length;
      if (this.value.length >= 20 && this.value.length <= 500) {
        this.classList.remove('is-invalid');
        this.classList.add('is-valid');
      } else if (this.value.length > 0) {
        this.classList.add('is-invalid');
        this.classList.remove('is-valid');
      }
    });
    // Initialize counter
    storeDescCounter.textContent = storeDescription.value.length;
  }

  // Toggle owner password visibility
  if (toggleOwnerPassword) {
    toggleOwnerPassword.addEventListener('click', function() {
      const type = ownerPassword.getAttribute('type') === 'password' ? 'text' : 'password';
      ownerPassword.setAttribute('type', type);
      this.querySelector('i').classList.toggle('bi-eye');
      this.querySelector('i').classList.toggle('bi-eye-slash');
    });
  }

  if (toggleOwnerPasswordConfirm) {
    toggleOwnerPasswordConfirm.addEventListener('click', function() {
      const type = ownerPasswordConfirm.getAttribute('type') === 'password' ? 'text' : 'password';
      ownerPasswordConfirm.setAttribute('type', type);
      this.querySelector('i').classList.toggle('bi-eye');
      this.querySelector('i').classList.toggle('bi-eye-slash');
    });
  }

  // Owner form validation
  if (ownerForm) {
    ownerForm.addEventListener('submit', function(event) {
      // Check password match
      if (ownerPassword.value !== ownerPasswordConfirm.value) {
        event.preventDefault();
        event.stopPropagation();
        ownerPasswordConfirm.setCustomValidity('Las contraseñas no coinciden');
        ownerPasswordConfirm.classList.add('is-invalid');
      } else {
        ownerPasswordConfirm.setCustomValidity('');
        ownerPasswordConfirm.classList.remove('is-invalid');
      }

      if (!ownerForm.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      } else {
        // Disable submit button
        const submitBtn = document.getElementById('ownerSubmitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enviando solicitud...';
      }
      
      ownerForm.classList.add('was-validated');
    }, false);

    // Real-time password match validation
    ownerPasswordConfirm.addEventListener('input', function() {
      if (this.value !== ownerPassword.value) {
        this.setCustomValidity('Las contraseñas no coinciden');
        this.classList.add('is-invalid');
        this.classList.remove('is-valid');
      } else {
        this.setCustomValidity('');
        this.classList.remove('is-invalid');
        if (this.value.length >= 8) {
          this.classList.add('is-valid');
        }
      }
    });

    // CUIT formatting
    const cuitInput = document.getElementById('store-cuit');
    if (cuitInput) {
      cuitInput.addEventListener('input', function(e) {
        let value = this.value.replace(/\D/g, ''); // Remove non-digits
        if (value.length > 11) value = value.substr(0, 11);
        
        // Format as XX-XXXXXXXX-X
        if (value.length > 2 && value.length <= 10) {
          value = value.substr(0, 2) + '-' + value.substr(2);
        } else if (value.length > 10) {
          value = value.substr(0, 2) + '-' + value.substr(2, 8) + '-' + value.substr(10);
        }
        
        this.value = value;
      });
    }

    // DNI validation
    const dniInput = document.getElementById('owner-dni');
    if (dniInput) {
      dniInput.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, ''); // Only digits
        if (this.value.length > 8) {
          this.value = this.value.substr(0, 8);
        }
      });
    }
  }

  // ========== REAL-TIME EMAIL VALIDATION ==========
  const emailInputs = document.querySelectorAll('input[type="email"]');
  emailInputs.forEach(input => {
    input.addEventListener('blur', function() {
      if (this.value && !this.validity.valid) {
        this.classList.add('is-invalid');
        this.classList.remove('is-valid');
      } else if (this.value) {
        this.classList.remove('is-invalid');
        this.classList.add('is-valid');
      }
    });
  });

  // ========== PHONE NUMBER VALIDATION ==========
  const phoneInputs = document.querySelectorAll('input[type="tel"]');
  phoneInputs.forEach(input => {
    input.addEventListener('blur', function() {
      if (this.value && !this.validity.valid) {
        this.classList.add('is-invalid');
        this.classList.remove('is-valid');
      } else if (this.value) {
        this.classList.remove('is-invalid');
        this.classList.add('is-valid');
      }
    });
  });

  // ========== BIRTHDATE VALIDATION (18+ years) ==========
  const birthdateInput = document.getElementById('client-birthdate');
  if (birthdateInput) {
    birthdateInput.addEventListener('change', function() {
      const today = new Date();
      const birthDate = new Date(this.value);
      const age = today.getFullYear() - birthDate.getFullYear();
      const monthDiff = today.getMonth() - birthDate.getMonth();
      
      if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
      }
      
      if (age < 18) {
        this.setCustomValidity('Debes ser mayor de 18 años');
        this.classList.add('is-invalid');
        this.classList.remove('is-valid');
      } else {
        this.setCustomValidity('');
        this.classList.remove('is-invalid');
        this.classList.add('is-valid');
      }
    });
  }
});
</script>
@endpush