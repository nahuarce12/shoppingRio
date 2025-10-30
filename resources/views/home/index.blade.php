@extends('layouts.app')

@section('title', 'Shopping Rosario - Inicio')
@section('meta_description', 'Descubre las mejores ofertas y promociones en los locales de Shopping Rosario.')

@section('content')
<div id="carousel-hero" class="carousel slide carousel-fullheight" data-bs-ride="carousel">
  <div class="carousel-inner h-100">
    <div class="carousel-item active h-100">
      <img class="object-fit-cover w-100 d-block position-absolute h-100" src="{{ asset('images/hero/imagenShopping.jpg') }}" alt="Shopping Rosario" style="z-index: -1;" />
      <div class="container d-flex h-100 flex-column justify-content-center">
        <div class="row">
          <div class="col-md-6 col-xl-5">
            <div class="carousel-text-box">
              <h1 class="text-uppercase fw-bold">Viví las mejores experiencias</h1>
              <p class="my-3">Promociones exclusivas, locales destacados y beneficios para cada categoría de clientes.</p>
              <a class="btn btn-primary btn-lg me-2" role="button" href="{{ Route::has('pages.promociones') ? route('pages.promociones') : '#' }}">Ver Promociones</a>
              <a class="btn btn-outline-primary btn-lg" role="button" href="{{ Route::has('pages.locales') ? route('pages.locales') : '#' }}">Explorar Locales</a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="carousel-item h-100">
      <img class="object-fit-cover w-100 d-block position-absolute h-100" src="{{ asset('images/hero/photo-1441986300917-64674bd600d8.jpg') }}" alt="Locales de Shopping Rosario" style="z-index: -1;" />
      <div class="container d-flex h-100 flex-column justify-content-center">
        <div class="row">
          <div class="col-md-6 col-xl-5">
            <div class="carousel-text-box">
              <h1 class="text-uppercase fw-bold">Beneficios pensados para vos</h1>
              <p class="my-3">Subí de categoría y obtené acceso anticipado a las promociones más exclusivas.</p>
              <a class="btn btn-primary btn-lg me-2" role="button" href="{{ Route::has('auth.register') ? route('auth.register') : (Route::has('register') ? route('register') : '#') }}">Registrarme</a>
              <a class="btn btn-outline-primary btn-lg" role="button" href="{{ Route::has('pages.novedades') ? route('pages.novedades') : '#' }}">Ver Novedades</a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="carousel-item h-100">
      <img class="object-fit-cover w-100 d-block position-absolute h-100" src="{{ asset('images/hero/photo-1571645163064-77faa9676a46.jpg') }}" alt="Gastronomía en Shopping Rosario" style="z-index: -1;" />
      <div class="container d-flex h-100 flex-column justify-content-center">
        <div class="row">
          <div class="col-md-6 col-xl-5">
            <div class="carousel-text-box">
              <h1 class="text-uppercase fw-bold">Disfrutá cada visita</h1>
              <p class="my-3">Eventos, gastronomía y experiencias únicas para toda la familia.</p>
              <a class="btn btn-primary btn-lg me-2" role="button" href="{{ Route::has('pages.about') ? route('pages.about') : '#' }}">Conocenos</a>
              <a class="btn btn-outline-primary btn-lg" role="button" href="{{ Route::has('pages.contact') ? route('pages.contact') : '#' }}">Contactanos</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div>
    <a class="carousel-control-prev" href="#carousel-hero" role="button" data-bs-slide="prev"><span></span><span class="visually-hidden">Anterior</span></a>
    <a class="carousel-control-next" href="#carousel-hero" role="button" data-bs-slide="next"><span></span><span class="visually-hidden">Siguiente</span></a>
  </div>
  <div class="carousel-indicators">
    <button type="button" data-bs-target="#carousel-hero" data-bs-slide-to="0" class="active"></button>
    <button type="button" data-bs-target="#carousel-hero" data-bs-slide-to="1"></button>
    <button type="button" data-bs-target="#carousel-hero" data-bs-slide-to="2"></button>
  </div>
</div>

<section class="py-section">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="section-title">Promociones Destacadas</h2>
    </div>
    <div class="row g-4">
      <div class="col-md-6 col-lg-4">
        <div class="card promo-card" data-category="inicial" data-local="Fashion Store">
          <span class="badge bg-danger promo-badge">
            <i class="bi bi-exclamation-triangle"></i> ¡Por vencer!
          </span>
          <a href="{{ Route::has('pages.promociones.show') ? route('pages.promociones.show', 1) : '#' }}">
            <img src="https://cdn.bootstrapstudio.io/placeholders/1400x800.png" class="card-img-top" alt="Promoción Fashion Store">
          </a>
          <div class="card-body">
            <div class="mb-2">
              <span class="badge badge-inicial badge-category">Inicial</span>
            </div>
            <h5 class="card-title">50% de descuento en segunda unidad</h5>
            <p class="promo-info mb-1">
              <i class="bi bi-shop"></i> Fashion Store
            </p>
            <div class="promo-validity">
              <i class="bi bi-calendar-event"></i> Válido hasta: 15/11/2025
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-4">
        <div class="card promo-card" data-category="medium" data-local="Tech World">
          <a href="{{ Route::has('pages.promociones.show') ? route('pages.promociones.show', 2) : '#' }}">
            <img src="https://cdn.bootstrapstudio.io/placeholders/1400x800.png" class="card-img-top" alt="Promoción Tech World">
          </a>
          <div class="card-body">
            <div class="mb-2">
              <span class="badge badge-medium badge-category">Medium</span>
            </div>
            <h5 class="card-title">20% OFF en accesorios tech</h5>
            <p class="promo-info mb-1">
              <i class="bi bi-shop"></i> Tech World
            </p>
            <div class="promo-validity">
              <i class="bi bi-calendar-event"></i> Válido hasta: 31/03/2026
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-4">
        <div class="card promo-card" data-category="premium" data-local="Bella Italia">
          <a href="{{ Route::has('pages.promociones.show') ? route('pages.promociones.show', 3) : '#' }}">
            <img src="https://cdn.bootstrapstudio.io/placeholders/1400x800.png" class="card-img-top" alt="Promoción Bella Italia">
          </a>
          <div class="card-body">
            <div class="mb-2">
              <span class="badge badge-premium badge-category">Premium</span>
            </div>
            <h5 class="card-title">2x1 en platos principales</h5>
            <p class="promo-info mb-1">
              <i class="bi bi-shop"></i> Bella Italia
            </p>
            <div class="promo-validity">
              <i class="bi bi-calendar-event"></i> Válido hasta: 30/12/2025
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="text-center mt-5">
      <a href="{{ Route::has('pages.promociones') ? route('pages.promociones') : '#' }}" class="btn btn-primary btn-lg">
        Ver Todas las Promociones <i class="bi bi-arrow-right"></i>
      </a>
    </div>
  </div>
</section>

<hr class="section-separator">

<section class="py-section">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="section-title">Locales Destacados</h2>
    </div>
    <div class="row g-4 justify-content-center">
      <div class="col-6 col-md-4 col-lg-3 col-xl-2">
        <div class="card local-card" data-category="moda">
          <a href="{{ Route::has('pages.locales.show') ? route('pages.locales.show', 1) : '#' }}">
            <img src="https://cdn.bootstrapstudio.io/placeholders/1400x800.png" class="card-img-top" alt="Fashion Store">
          </a>
          <div class="card-body text-center">
            <h5 class="card-title">Fashion Store</h5>
            <p class="category-text">Moda y Accesorios</p>
          </div>
        </div>
      </div>

      <div class="col-6 col-md-4 col-lg-3 col-xl-2">
        <div class="card local-card" data-category="tecnologia">
          <a href="{{ Route::has('pages.locales.show') ? route('pages.locales.show', 2) : '#' }}">
            <img src="https://cdn.bootstrapstudio.io/placeholders/1400x800.png" class="card-img-top" alt="Tech World">
          </a>
          <div class="card-body text-center">
            <h5 class="card-title">Tech World</h5>
            <p class="category-text">Tecnología</p>
          </div>
        </div>
      </div>

      <div class="col-6 col-md-4 col-lg-3 col-xl-2">
        <div class="card local-card" data-category="gastronomia">
          <a href="{{ Route::has('pages.locales.show') ? route('pages.locales.show', 3) : '#' }}">
            <img src="https://cdn.bootstrapstudio.io/placeholders/1400x800.png" class="card-img-top" alt="Bella Italia">
          </a>
          <div class="card-body text-center">
            <h5 class="card-title">Bella Italia</h5>
            <p class="category-text">Gastronomía</p>
          </div>
        </div>
      </div>

      <div class="col-6 col-md-4 col-lg-3 col-xl-2">
        <div class="card local-card" data-category="deportes">
          <a href="{{ Route::has('pages.locales.show') ? route('pages.locales.show', 4) : '#' }}">
            <img src="https://cdn.bootstrapstudio.io/placeholders/1400x800.png" class="card-img-top" alt="Sport Zone">
          </a>
          <div class="card-body text-center">
            <h5 class="card-title">Sport Zone</h5>
            <p class="category-text">Deportes</p>
          </div>
        </div>
      </div>

      <div class="col-6 col-md-4 col-lg-3 col-xl-2">
        <div class="card local-card" data-category="hogar">
          <a href="{{ Route::has('pages.locales.show') ? route('pages.locales.show', 5) : '#' }}">
            <img src="https://cdn.bootstrapstudio.io/placeholders/1400x800.png" class="card-img-top" alt="Home Deco">
          </a>
          <div class="card-body text-center">
            <h5 class="card-title">Home Deco</h5>
            <p class="category-text">Hogar</p>
          </div>
        </div>
      </div>

      <div class="col-6 col-md-4 col-lg-3 col-xl-2">
        <div class="card local-card" data-category="salud">
          <a href="{{ Route::has('pages.locales.show') ? route('pages.locales.show', 6) : '#' }}">
            <img src="https://cdn.bootstrapstudio.io/placeholders/1400x800.png" class="card-img-top" alt="Beauty Salon">
          </a>
          <div class="card-body text-center">
            <h5 class="card-title">Beauty Salon</h5>
            <p class="category-text">Belleza</p>
          </div>
        </div>
      </div>
    </div>
    <div class="text-center mt-5">
      <a href="{{ Route::has('pages.locales') ? route('pages.locales') : '#' }}" class="btn btn-primary btn-lg">
        Ver Todos los Locales <i class="bi bi-arrow-right"></i>
      </a>
    </div>
  </div>
</section>

<hr class="section-separator">

<section class="py-section">
  <div class="container">
    <div class="row g-4">
      <div class="col-md-4">
        <div class="feature-box">
          <i class="bi bi-percent"></i>
          <h4>Mejores Ofertas</h4>
          <p>Accedé a descuentos exclusivos en todos nuestros locales.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="feature-box">
          <i class="bi bi-star-fill"></i>
          <h4>Programa de Categorías</h4>
          <p>Subí de categoría y desbloqueá promociones premium.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="feature-box">
          <i class="bi bi-phone"></i>
          <h4>100% Digital</h4>
          <p>Accedé a las promociones desde tu celular de forma rápida.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="py-5 bg-primary text-white">
  <div class="container text-center">
    <h2 class="mb-4">¿Listo para comenzar a ahorrar?</h2>
    <p class="lead mb-4">Registrate ahora y empezá a disfrutar de promociones exclusivas.</p>
    <a href="{{ Route::has('auth.register') ? route('auth.register') : (Route::has('register') ? route('register') : '#') }}" class="btn btn-light btn-lg">
      <i class="bi bi-person-plus"></i> Crear Cuenta Gratis
    </a>
  </div>
</section>
@endsection

@push('scripts')
@vite('resources/js/frontoffice/main.js')
@endpush