@extends('layouts.app')

@section('title', 'Novedades - Shopping Rosario')
@section('meta_description', 'Descubrí todas las novedades y noticias del Shopping Rosario. Mantenete informado sobre eventos, aperturas y promociones especiales.')

@php
  use Illuminate\Support\Str;
@endphp

@section('content')
<x-layout.breadcrumbs :items="[['label' => 'Novedades', 'url' => route('novedades.index')]]" />

<section class="py-4">
  <div class="container text-center">
    <h1 class="display-5 fw-bold" style="color: var(--primary-color)">Novedades</h1>
    <p class="lead">Mantenete al día con las últimas noticias, eventos y actualizaciones del shopping.</p>
  </div>
</section>

<section class="py-4">
  <div class="container">
    <form method="GET" action="{{ route('novedades.index') }}" id="news-filter-form" class="card shadow-sm p-3 border-0">
      <div class="row g-3 align-items-end">
        <div class="col-12 col-lg-6">
          <label for="search" class="form-label">Buscar novedad</label>
          <div class="input-group input-group-lg">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="search" class="form-control border-start-0 ps-0" id="search" name="search" style="font-size: 1.1rem;" value="{{ request('search') }}" placeholder="Ingresá título, descripción o palabras clave">
          </div>
        </div>
        <div class="col-12 col-lg-4">
          <label for="target_category" class="form-label">Categoría de cliente</label>
          <select style="font-size: 1.1rem;" class="form-select form-select-lg" id="target_category" name="target_category">
            <option value="">Todas las categorías</option>
            @foreach($categories as $category)
              <option value="{{ $category }}" @selected(request('target_category') === $category)>{{ $category }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-12 col-lg-2 d-flex gap-2">
          <button type="submit" class="btn btn-primary w-100" title="Aplicar filtros"><i class="bi bi-filter"></i></button>
          <a href="{{ route('novedades.index') }}" class="btn btn-outline-secondary w-100" title="Limpiar filtros"><i class="bi bi-x-circle"></i></a>
        </div>
      </div>
    </form>
  </div>
</section>

<section class="py-4">
  <div class="container">
    @if($news->count() > 0)
      <div class="row g-4" id="news-grid">
        @foreach($news as $newsItem)
          @php
            $daysToExpire = now()->diffInDays($newsItem->end_date, false);
            $isExpiring = $daysToExpire >= 0 && $daysToExpire <= 3;
            $isNew = now()->diffInDays($newsItem->created_at, false) <= 7;
          @endphp
          <div class="col-12 col-md-6 col-lg-4">
            <article class="card h-100 border-0 shadow-sm" data-category="{{ strtolower($newsItem->target_category) }}">
              <div class="position-relative">
                @if($isNew)
                  <span class="badge bg-success position-absolute top-0 start-0 m-2 z-1"><i class="bi bi-star-fill"></i> Nuevo</span>
                @endif
                @if($isExpiring)
                  <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-2 z-1"><i class="bi bi-clock-fill"></i> Por vencer</span>
                @endif
                <div class="ratio ratio-16x9">
                  @if($newsItem->imagen)
                    <img src="{{ $newsItem->imagen_url }}" class="object-fit-cover rounded-top" alt="Imagen de {{ $newsItem->title }}">
                  @else
                    <div class="d-flex align-items-center justify-content-center bg-light rounded-top">
                      <i class="bi bi-newspaper text-muted" style="font-size: 4rem;"></i>
                    </div>
                  @endif
                </div>
              </div>
              <div class="card-body d-flex flex-column">
                <div class="mb-3">
                  <span class="badge text-uppercase badge-{{ strtolower($newsItem->target_category) }}">{{ $newsItem->target_category }}</span>
                  <span class="badge bg-secondary ms-1">
                    <i class="bi bi-calendar3"></i> {{ $newsItem->created_at->format('d/m/Y') }}
                  </span>
                </div>
                
                <h3 class="h5 card-title fw-bold">{{ $newsItem->title ?? Str::limit($newsItem->description, 60) }}</h3>
                
                <p class="card-text text-muted flex-grow-1">
                  {{ Str::limit($newsItem->description, 150) }}
                </p>
                
                <div class="mt-auto">
                  <hr class="my-3">
                  <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                      <i class="bi bi-hourglass-split"></i> Vigente hasta: <strong>{{ $newsItem->end_date->format('d/m/Y') }}</strong>
                    </div>
                    @if($isExpiring)
                      <span class="badge bg-warning text-dark">
                        {{ $daysToExpire }} {{ $daysToExpire === 1 ? 'día' : 'días' }}
                      </span>
                    @endif
                  </div>
                </div>
              </div>
            </article>
          </div>
        @endforeach
      </div>

      @if($news->hasPages())
        <div class="d-flex justify-content-center mt-5">
          {{ $news->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
      @endif
    @else
      <div class="alert alert-info text-center" role="status">
        <i class="bi bi-info-circle fs-3"></i>
        <p class="mt-2 mb-0">No encontramos novedades con los filtros seleccionados. Modificá tu búsqueda e intentá nuevamente.</p>
      </div>
    @endif
  </div>
</section>

<section class="py-5 bg-white">
  <div class="container">
    <hr class="section-separator">
    <div class="row g-4 align-items-center">
      <div class="col-lg-6">
        <h2 class="h3 fw-bold">¿Por qué suscribirse a las novedades?</h2>
        <ul class="list-unstyled mt-3 mb-0">
          <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> Enterate primero de nuevas promociones y eventos especiales.</li>
          <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> Recibí notificaciones exclusivas según tu categoría de cliente.</li>
          <li class="mb-0"><i class="bi bi-check-circle-fill text-success"></i> No te pierdas aperturas de locales y actividades del shopping.</li>
        </ul>
      </div>
      <div class="col-lg-6">
        <div class="card border-0 shadow-sm text-center p-4 h-100">
          <i class="bi bi-bell-fill fs-1 text-primary mb-3"></i>
          <h3 class="h5">¡Suscribite a nuestras novedades!</h3>
          <p class="text-muted">Registrate como cliente para recibir notificaciones personalizadas de todas las novedades del shopping.</p>
          @guest
            <a href="{{ route('register') }}" class="btn btn-primary">
              <i class="bi bi-person-plus"></i> Crear cuenta gratuita
            </a>
          @else
            <a href="{{ route('client.dashboard') }}" class="btn btn-primary">
              <i class="bi bi-grid-fill"></i> Ir a mi panel
            </a>
          @endguest
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const targetCategorySelect = document.getElementById('target_category');
    if (targetCategorySelect) {
      targetCategorySelect.addEventListener('change', () => document.getElementById('news-filter-form').submit());
    }
  });
</script>
@endpush