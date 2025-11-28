@extends('layouts.app')

@section('title', 'Promociones - Shopping Rosario')
@section('meta_description', 'Descubrí todas las promociones vigentes del Shopping Rosario y filtrá por categoría, local o palabras clave.')

@php
  use Illuminate\Support\Str;
@endphp

@section('content')
<x-layout.breadcrumbs :items="[['label' => 'Promociones', 'url' => route('promociones.index')]]" />

<section class="py-4">
  <div class="container text-center">
    <h1 class="display-5 fw-bold" style="color: var(--primary-color)">Promociones</h1>
    <p class="lead">Explorá los descuentos activos y elegí los beneficios que mejor se adapten a vos.</p>
  </div>
</section>

<section class="py-4">
  <div class="container">
    <form method="GET" action="{{ route('promociones.index') }}" id="promotions-filter-form" class="card shadow-sm p-3 border-0">
      <div class="row g-3 align-items-end">
        <div class="col-12 col-lg-4">
          <label for="search" class="form-label">Buscar promoción</label>
          <div class="input-group input-group-lg">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="search" class="form-control border-start-0 ps-0" id="search" name="search" value="{{ request('search') }}" style="font-size: 1.1rem;" placeholder="Ingresá texto descriptivo, beneficios o palabras clave">
          </div>
        </div>
        <div class="col-12 col-lg-3">
          <label for="minimum_category" class="form-label">Categoría mínima</label>
          <select class="form-select form-select-lg" id="minimum_category" style="font-size: 1.1rem;" name="minimum_category">
            <option value="">Todas las categorías</option>
            @foreach($categories as $category)
              <option value="{{ $category }}" @selected(request('minimum_category') === $category)>{{ $category }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-12 col-lg-3">
          <label for="store_id" class="form-label">Local</label>
          <select class="form-select form-select-lg" style="font-size: 1.1rem;" id="store_id" name="store_id">
            <option value="">Todos los locales</option>
            @foreach($stores as $store)
              <option value="{{ $store->id }}" @selected((string) request('store_id') === (string) $store->id)>{{ $store->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-12 col-lg-2 d-flex gap-2">
          <button type="submit" class="btn btn-primary w-100" title="Aplicar filtros"><i class="bi bi-filter"></i></button>
          <a href="{{ route('promociones.index') }}" class="btn btn-outline-secondary w-100" title="Limpiar filtros"><i class="bi bi-x-circle"></i></a>
        </div>
      </div>
    </form>
  </div>
</section>

<section class="py-4">
  <div class="container">
    @if($promotions->count() > 0)
      <div class="row g-4" id="promotions-grid">
        @foreach($promotions as $promotion)
          @php
            $daysToExpire = now()->diffInDays($promotion->end_date, false);
          @endphp
          <div class="col-sm-6 col-lg-4 col-xl-3">
            <article class="card h-100 border-0 shadow-sm promo-card" data-category="{{ strtolower($promotion->minimum_category) }}" data-local="{{ $promotion->store->name }}">
              <div class="position-relative">
                @if($daysToExpire >= 0 && $daysToExpire <= 5)
                  <span class="badge bg-danger position-absolute top-0 start-0 m-2"><i class="bi bi-exclamation-triangle-fill"></i> Por vencer</span>
                @endif
                <a href="{{ route('promociones.show', $promotion) }}\" class=\"ratio ratio-4x3 bg-light d-block\">
                  @if($promotion->imagen)
                  <img src=\"{{ $promotion->imagen_url }}\" class=\"object-fit-cover rounded-top\" alt=\"Imagen de {{ $promotion->description }}\">
                  @else
                  <img src=\"https://cdn.bootstrapstudio.io/placeholders/1400x800.png\" class=\"object-fit-cover rounded-top\" alt=\"Imagen referencial de {{ $promotion->description }}\">
                  @endif
                </a>
              </div>
              <div class="card-body">
                <span class="badge text-uppercase mb-2 badge-{{ strtolower($promotion->minimum_category) }}">{{ $promotion->minimum_category }}</span>
                <h3 class="h5 card-title">{{ Str::limit($promotion->description, 80) }}</h3>
                <p class="mb-1 text-muted"><i class="bi bi-shop"></i> {{ $promotion->store->name }}</p>
                <p class="small mb-0"><i class="bi bi-calendar-event"></i> Vigente hasta {{ $promotion->end_date->format('d/m/Y') }}</p>
              </div>
              <div class="card-footer bg-white border-0 text-end">
                <a href="{{ route('promociones.show', $promotion) }}" class="btn btn-outline-primary btn-sm">Ver detalle</a>
              </div>
            </article>
          </div>
        @endforeach
      </div>

      @if($promotions->hasPages())
        <div class="d-flex justify-content-center mt-5">
          {{ $promotions->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
      @endif
    @else
      <div class="alert alert-info text-center" role="status">
        <i class="bi bi-info-circle fs-3"></i>
        <p class="mt-2 mb-0">No encontramos promociones con los filtros seleccionados. Modificá tu búsqueda e intentá nuevamente.</p>
      </div>
    @endif
  </div>
</section>

<section class="py-5 bg-white">
  <div class="container">
    <hr class="section-separator">
    <div class="row g-4 align-items-center">
      <div class="col-lg-6">
        <h2 class="h3 fw-bold">Cómo aprovechar tus beneficios</h2>
        <ul class="list-unstyled mt-3 mb-0">
          <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> Consultá las condiciones de vigencia y combinabilidad antes de canjear.</li>
          <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> Revisá tu categoría de cliente para acceder a más promociones.</li>
          <li class="mb-0"><i class="bi bi-check-circle-fill text-success"></i> Activá las notificaciones para recibir novedades apenas se publican.</li>
        </ul>
      </div>
      <div class="col-lg-6">
        <div class="card border-0 shadow-sm text-center p-4 h-100">
          <i class="bi bi-stars fs-1 text-primary mb-3"></i>
          <h3 class="h5">¡Querés sumar más beneficios?</h3>
          <p class="text-muted">Registrate como cliente para llevar el control de tus descuentos y recibir alertas personalizadas.</p>
          <a href="{{ route('register') }}" class="btn btn-primary">
            <i class="bi bi-person-plus"></i> Crear cuenta gratuita
          </a>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const filterSelects = document.querySelectorAll('#categoria_minima, #store_id');
    filterSelects.forEach(select => {
      select.addEventListener('change', () => document.getElementById('promotions-filter-form').submit());
    });
  });
</script>
@endpush
