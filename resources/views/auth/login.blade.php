@extends('layouts.app')

@section('title', 'Iniciar Sesión - Shopping Rosario')
@section('meta_description', 'Accedé a tu cuenta para gestionar promociones y beneficios exclusivos del Shopping Rosario.')

@section('content')
<x-layout.breadcrumbs :items="[['label' => 'Iniciar Sesión']]" />

@php
$loginAction = Route::has('login') ? route('login') : '#';
$registerUrl = Route::has('auth.register') ? route('auth.register') : (Route::has('register') ? route('register') : '#');
$forgotPasswordUrl = Route::has('password.request') ? route('password.request') : '#';
$promotionsUrl = Route::has('pages.promociones') ? route('pages.promociones') : '#';
@endphp

<section class="py-5">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-5 col-md-7">
        <div class="card shadow">
          <div class="card-body p-5">
            <div class="text-center mb-4">
              <i class="bi bi-box-arrow-in-right fs-1 text-primary"></i>
              <h1 class="mt-3 h3">Iniciar Sesión</h1>
              <p class="text-muted">Accedé a tu cuenta para disfrutar de las promociones</p>
            </div>

            <form method="POST" action="{{ $loginAction }}" id="loginForm" novalidate>
              @csrf
              <div class="mb-3">
                <label for="email" class="form-label">Email *</label>
                <div class="input-group">
                  <span class="input-group-text">
                    <i class="bi bi-envelope"></i>
                  </span>
                  <input type="email" 
                         class="form-control @error('email') is-invalid @enderror" 
                         id="email" 
                         name="email" 
                         value="{{ old('email') }}" 
                         required 
                         autocomplete="email"
                         placeholder="tu@email.com">
                  @error('email')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                  <div class="invalid-feedback">Por favor ingresá un email válido.</div>
                </div>
              </div>

              <div class="mb-3">
                <label for="password" class="form-label">Contraseña *</label>
                <div class="input-group">
                  <span class="input-group-text">
                    <i class="bi bi-lock"></i>
                  </span>
                  <input type="password" 
                         class="form-control @error('password') is-invalid @enderror" 
                         id="password" 
                         name="password" 
                         required 
                         autocomplete="current-password"
                         placeholder="Tu contraseña">
                  <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                    <i class="bi bi-eye" id="togglePasswordIcon"></i>
                  </button>
                  @error('password')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                  <div class="invalid-feedback">Por favor ingresá tu contraseña.</div>
                </div>
              </div>

              <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember">
                  Recordarme en este dispositivo
                </label>
              </div>

              <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                  <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                </button>
              </div>
            </form>

            @if($forgotPasswordUrl !== '#')
            <div class="text-center mt-3">
              <a href="{{ $forgotPasswordUrl }}" class="text-decoration-none">¿Olvidaste tu contraseña?</a>
            </div>
            @endif

            <hr class="my-4">

            <div class="text-center">
              <p class="mb-2">¿No tenés cuenta?</p>
              <a href="{{ $registerUrl }}" class="btn btn-outline-primary w-100">
                <i class="bi bi-person-plus"></i> Crear Cuenta Nueva
              </a>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-5 offset-lg-1 col-md-12 mt-4 mt-lg-0">
        <div class="card h-100">
          <div class="card-body p-4">
            <h2 class="card-title h4 mb-4">
              <i class="bi bi-star-fill text-warning"></i> Beneficios al Iniciar Sesión
            </h2>

            <div class="mb-4">
              <h3 class="h5"><i class="bi bi-check-circle-fill text-success"></i> Acceso a Promociones</h3>
              <p>Visualizá y utilizá todas las promociones disponibles según tu categoría de cliente.</p>
            </div>

            <div class="mb-4">
              <h3 class="h5"><i class="bi bi-check-circle-fill text-success"></i> Historial de Compras</h3>
              <p>Consultá las promociones utilizadas y seguí tu progreso de categoría.</p>
            </div>

            <div class="mb-4">
              <h3 class="h5"><i class="bi bi-check-circle-fill text-success"></i> Categorías Progresivas</h3>
              <p>Empezá como Inicial y subí automáticamente a Medium y Premium con tus compras.</p>
            </div>

            <div class="mb-4">
              <h3 class="h5"><i class="bi bi-check-circle-fill text-success"></i> Novedades Exclusivas</h3>
              <p>Recibí notificaciones sobre eventos y promociones especiales del shopping.</p>
            </div>

            <div class="alert alert-primary" role="alert">
              <h4 class="alert-heading h6">
                <i class="bi bi-lightbulb"></i> ¿Sabías que...?
              </h4>
              <p class="mb-0">
                Cada vez que usás una promoción en un local, sumás puntos para subir de categoría. ¡Mientras más participes, mejores beneficios desbloquearás!
              </p>
            </div>

            <div class="text-center mt-4">
              <h3 class="h5 mb-3">Categorías de Cliente</h3>
              <div class="d-flex justify-content-around">
                <div>
                  <span class="badge badge-inicial badge-category fs-6 mb-2">Inicial</span>
                  <p class="small mb-0">Al registrarte</p>
                </div>
                <div>
                  <span class="badge badge-medium badge-category fs-6 mb-2">Medium</span>
                  <p class="small mb-0">Luego de usar
                    promociones</p>
                </div>
                <div>
                  <span class="badge badge-premium badge-category fs-6 mb-2">Premium</span>
                  <p class="small mb-0">Clientes VIP</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="py-5 bg-primaryColor text-white">
  <div class="container text-center">
    <h2 class="h3 mb-3">¿Aún no exploraste nuestras promociones?</h2>
    <p class="lead mb-4">Podés ver todas las promociones sin necesidad de iniciar sesión.</p>
    <a href="{{ $promotionsUrl }}" class="btn btn-light btn-lg">
      <i class="bi bi-tag-fill"></i> Ver Todas las Promociones
    </a>
  </div>
</section>
@endsection

@push('scripts')
@vite('resources/js/frontoffice/main.js')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const loginForm = document.getElementById('loginForm');
  const togglePassword = document.getElementById('togglePassword');
  const passwordInput = document.getElementById('password');
  const toggleIcon = document.getElementById('togglePasswordIcon');
  const submitBtn = document.getElementById('submitBtn');

  // Toggle password visibility
  if (togglePassword) {
    togglePassword.addEventListener('click', function() {
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      toggleIcon.classList.toggle('bi-eye');
      toggleIcon.classList.toggle('bi-eye-slash');
    });
  }

  // Form validation
  loginForm.addEventListener('submit', function(event) {
    if (!loginForm.checkValidity()) {
      event.preventDefault();
      event.stopPropagation();
    } else {
      // Disable submit button to prevent double submission
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Iniciando sesión...';
    }
    
    loginForm.classList.add('was-validated');
  }, false);

  // Real-time email validation
  const emailInput = document.getElementById('email');
  emailInput.addEventListener('blur', function() {
    if (this.value && !this.validity.valid) {
      this.classList.add('is-invalid');
    } else if (this.value) {
      this.classList.remove('is-invalid');
      this.classList.add('is-valid');
    }
  });

  // Real-time password validation
  passwordInput.addEventListener('blur', function() {
    if (!this.value) {
      this.classList.add('is-invalid');
      this.classList.remove('is-valid');
    } else {
      this.classList.remove('is-invalid');
      this.classList.add('is-valid');
    }
  });
});
</script>
@endpush