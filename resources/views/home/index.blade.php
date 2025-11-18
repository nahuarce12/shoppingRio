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
              <a class="btn btn-primary btn-lg me-2" role="button" href="{{ route('promociones.index') }}">Ver Promociones</a>
              <a class="btn btn-outline-primary btn-lg" role="button" href="{{ route('locales.index') }}">Explorar Locales</a>
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
              <a class="btn btn-primary btn-lg me-2" role="button" href="{{ route('register') }}">Registrarme</a>
              <a class="btn btn-outline-primary btn-lg" role="button" href="#">Ver Novedades</a>
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
              <a class="btn btn-primary btn-lg me-2" role="button" href="{{ route('about') }}">Conocenos</a>
              <a class="btn btn-outline-primary btn-lg" role="button" href="{{ route('contact') }}">Contactanos</a>
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
      @forelse($featuredPromotions as $promotion)
      <div class="col-md-6 col-lg-4">
        <div class="card promo-card" data-category="{{ strtolower($promotion->minimum_category) }}" data-local="{{ $promotion->store->name }}">
          @php
            $daysUntilEnd = now()->diffInDays($promotion->end_date, false);
          @endphp
          @if($daysUntilEnd >= 0 && $daysUntilEnd <= 7)
          <span class="badge bg-danger promo-badge">
            <i class="bi bi-exclamation-triangle"></i> ¡Por vencer!
          </span>
          @endif
          <a href="{{ route('promociones.show', $promotion->id) }}">
            <img src="https://cdn.bootstrapstudio.io/placeholders/1400x800.png" class="card-img-top" alt="Promoción {{ $promotion->store->name }}">
          </a>
          <div class="card-body">
            <div class="mb-2">
              <span class="badge badge-{{ strtolower($promotion->minimum_category) }} badge-category">{{ $promotion->minimum_category }}</span>
            </div>
            <h5 class="card-title">{{ Str::limit($promotion->description, 60) }}</h5>
            <p class="promo-info mb-1">
              <i class="bi bi-shop"></i> {{ $promotion->store->name }}
            </p>
            <div class="promo-validity">
              <i class="bi bi-calendar-event"></i> Válido hasta: {{ $promotion->end_date->format('d/m/Y') }}
            </div>
          </div>
        </div>
      </div>
      @empty
      <div class="col-12">
        <div class="alert alert-info text-center">
          <i class="bi bi-info-circle"></i> No hay promociones destacadas disponibles en este momento.
        </div>
      </div>
      @endforelse
    </div>

    <div class="text-center mt-5">
      <a href="{{ route('promociones.index') }}" class="btn btn-primary btn-lg">
        Ver Todas las Promociones <i class="bi bi-arrow-right"></i>
      </a>
    </div>
  </div>
</section>


<section class="py-section">
  <div class="container">
      <hr class="section-separator">
    <div class="text-center mb-5">
      <h2 class="section-title">Locales Destacados</h2>
    </div>
    <div class="row g-4 justify-content-center">
      @forelse($featuredStores as $store)
      <div class="col-6 col-md-4 col-lg-3 col-xl-2">
        <div class="card local-card" data-category="{{ strtolower($store->category) }}">
          <a href="{{ route('locales.show', $store->id) }}">
            <img src="https://cdn.bootstrapstudio.io/placeholders/1400x800.png" class="card-img-top" alt="{{ $store->name }}">
          </a>
          <div class="card-body text-center">
            <h5 class="card-title">{{ $store->name }}</h5>
            <p class="category-text">{{ $store->category }}</p>
            @if($store->promotions_count > 0)
            <small class="text-muted">
              <i class="bi bi-tag"></i> {{ $store->promotions_count }} {{ Str::plural('promoción', $store->promotions_count) }}
            </small>
            @endif
          </div>
        </div>
      </div>
      @empty
      <div class="col-12">
        <div class="alert alert-info text-center">
          <i class="bi bi-info-circle"></i> No hay locales destacados disponibles en este momento.
        </div>
      </div>
      @endforelse
    </div>
    <div class="text-center mt-5">
      <a href="{{ route('locales.index') }}" class="btn btn-primary btn-lg">
        Ver Todos los Locales <i class="bi bi-arrow-right"></i>
      </a>
    </div>
  </div>
</section>


<section class="py-section">
  <div class="container">
      <hr class="section-separator">
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

<section class="py-5 bg-primaryColor text-white">
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
