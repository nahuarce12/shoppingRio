@extends('layouts.app')

@section('title', 'Locales - Shopping Rosario')
@section('meta_description', 'Recorré todos los locales del Shopping Rosario y descubrí sus ubicaciones y rubros disponibles.')

@section('content')
<x-layout.breadcrumbs :items="[['label' => 'Locales', 'url' => route('locales.index')]]" />

<section class="py-4">
  <div class="container text-center">
    <h1 class="display-5 fw-bold text-primary">Locales</h1>
    <p class="lead">Buscá por nombre, rubro o ubicación y planificá tu visita al shopping.</p>
  </div>
</section>

<section class="py-4">
  <div class="container">
    <form method="GET" action="{{ route('locales.index') }}" id="stores-filter-form" class="card shadow-sm p-3 border-0">
      <div class="row g-3 align-items-end">
        <div class="col-12 col-lg-6">
          <label for="search" class="form-label">Buscar local</label>
          <div class="input-group input-group-lg">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="search" class="form-control border-start-0 ps-0" id="search" name="search" value="{{ request('search') }}" placeholder="Ingresá el nombre o la ubicación del local">
          </div>
        </div>
        <div class="col-12 col-lg-4">
          <label for="category" class="form-label">Rubro</label>
          <select class="form-select form-select-lg" id="category" name="category">
            <option value="">Todos los rubros</option>
            @foreach($rubros as $rubro)
              <option value="{{ $rubro }}" @selected(request('category') === $rubro)>{{ $rubro }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-12 col-lg-2 d-flex gap-2">
          <button type="submit" class="btn btn-primary w-100" title="Aplicar filtros"><i class="bi bi-filter"></i></button>
          <a href="{{ route('locales.index') }}" class="btn btn-outline-secondary w-100" title="Limpiar filtros"><i class="bi bi-x-circle"></i></a>
        </div>
      </div>
    </form>
  </div>
</section>

<section class="py-4">
  <div class="container">
    @if($stores->count() > 0)
      <div class="row g-4" id="stores-grid">
        @foreach($stores as $store)
          <div class="col-sm-6 col-lg-4 col-xl-3">
            <article class="card h-100 border-0 shadow-sm local-card" data-rubro="{{ strtolower($store->category) }}">
              <a href="{{ route('locales.show', $store) }}" class="ratio ratio-4x3 bg-light d-block">
                <img src="https://cdn.bootstrapstudio.io/placeholders/1400x800.png" class="object-fit-cover rounded-top" alt="Imagen referencial de {{ $store->name }}">
              </a>
              <div class="card-body">
                <h3 class="h5 card-title mb-1">{{ $store->name }}</h3>
                <p class="mb-1 text-muted"><i class="bi bi-tag"></i> {{ $store->category }}</p>
                <p class="small text-muted mb-0"><i class="bi bi-geo-alt"></i> {{ $store->location }}</p>
              </div>
              <div class="card-footer bg-white border-0 text-end">
                <a href="{{ route('locales.show', $store) }}" class="btn btn-outline-primary btn-sm">Ver detalle</a>
              </div>
            </article>
          </div>
        @endforeach
      </div>

      @if($stores->hasPages())
        <div class="d-flex justify-content-center mt-5">
          {{ $stores->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
      @endif
    @else
      <div class="alert alert-info text-center" role="status">
        <i class="bi bi-info-circle fs-3"></i>
        <p class="mt-2 mb-0">No encontramos locales con los filtros seleccionados. Modificá tu búsqueda e intentá nuevamente.</p>
      </div>
    @endif
  </div>
</section>

<section class="py-5 bg-white">
  <div class="container">
    <div class="row g-4 align-items-center">
      <div class="col-lg-6">
        <h2 class="h3 fw-bold">Consejos para aprovechar tu visita</h2>
        <ul class="list-unstyled mt-3 mb-0">
          <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> Consultá la ubicación de cada local antes de llegar para optimizar tu recorrido.</li>
          <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> Revisá las promociones activas del local para combinarlas con tus beneficios.</li>
          <li class="mb-0"><i class="bi bi-check-circle-fill text-success"></i> Guardá tus locales favoritos para visitarlos en tu próxima salida.</li>
        </ul>
      </div>
      <div class="col-lg-6">
        <div class="card border-0 shadow-sm text-center p-4 h-100">
          <i class="bi bi-chat-dots fs-1 text-primary mb-3"></i>
          <h3 class="h5">¡Tenés un local y querés sumarte?</h3>
          <p class="text-muted">Registrate como dueño de local para gestionar tus promociones y recibir solicitudes de clientes.</p>
          <a href="{{ route('register') }}" class="btn btn-primary">
            <i class="bi bi-person-workspace"></i> Crear cuenta de dueño
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
    const rubroSelect = document.getElementById('category');
    if (rubroSelect) {
      rubroSelect.addEventListener('change', () => document.getElementById('stores-filter-form').submit());
    }
  });
</script>
@endpush
