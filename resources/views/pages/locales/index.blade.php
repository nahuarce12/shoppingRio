@extends('layouts.app')@extends('layouts.app')



@section('title', 'Locales - Shopping Rosario')@section('title', 'Locales - Shopping Rosario')

@section('meta_description', 'Descubre todos los locales disponibles en Shopping Rosario y encontrá tu tienda favorita.')@section('meta_description', 'Descubre todos los locales disponibles en Shopping Rosario y encontrá tu tienda favorita.')



@section('content')@section('content')

<x-layout.breadcrumbs :items="[['label' => 'Locales']]" /><x-layout.breadcrumbs :items="[['label' => 'Locales']]" />



<section class="py-4"><section class="py-4">

  <div class="container text-center">  <div class="container text-center">

    <h1 class="display-5 fw-bold text-primary">Locales</h1>    <h1 class="display-5 fw-bold text-primary">Locales</h1>

    <p class="lead">Explorá todos los locales disponibles en nuestro shopping.</p>    <p class="lead">Explorá todos los locales disponibles en nuestro shopping.</p>

  </div>  </div>

</section></section>



<section class="py-4"><section class="py-4">

  <div class="container">  <div class="container">

    <form method="GET" action="{{ route('locales.index') }}" id="filter-form">    <div class="row mb-3">

      <div class="row mb-3">      <div class="col-12">

        <div class="col-12">        <div class="input-group input-group-lg">

          <div class="input-group input-group-lg">          <span class="input-group-text bg-white border-end-0">

            <span class="input-group-text bg-white border-end-0">            <i class="bi bi-search"></i>

              <i class="bi bi-search"></i>          </span>

            </span>          <input type="text" class="form-control border-start-0 ps-0" id="searchLocal" placeholder="Buscar Local">

            <input type="text" class="form-control border-start-0 ps-0" name="search"         </div>

                   value="{{ request('search') }}" placeholder="Buscar local por nombre o ubicación...">      </div>

          </div>    </div>

        </div>

      </div>    <div class="filter-section">

      <h5 class="d-flex align-items-center gap-2"><i class="bi bi-funnel"></i> Filtrar Locales</h5>

      <div class="filter-section">      <select class="form-select" id="categoryFilter">

        <h5 class="d-flex align-items-center gap-2"><i class="bi bi-funnel"></i> Filtrar Locales</h5>        <option value="">Todas las categorías</option>

        <div class="row g-3">        <option value="moda">Moda y Accesorios</option>

          <div class="col-md-12">        <option value="tecnologia">Tecnología</option>

            <label for="rubro" class="form-label">Rubro</label>        <option value="gastronomia">Gastronomía</option>

            <select class="form-select" name="rubro" id="rubro">        <option value="deportes">Deportes</option>

              <option value="">Todos los rubros</option>        <option value="hogar">Hogar y Decoración</option>

              @foreach($rubros as $rubro)        <option value="entretenimiento">Entretenimiento</option>

                <option value="{{ $rubro }}" {{ request('rubro') == $rubro ? 'selected' : '' }}>        <option value="salud">Salud y Belleza</option>

                  {{ $rubro }}      </select>

                </option>    </div>

              @endforeach  </div>

            </select></section>

          </div>

          <div class="col-12"><section class="py-4">

            <button type="submit" class="btn btn-primary">  <div class="container">

              <i class="bi bi-search"></i> Buscar    <div class="row g-4" id="stores-grid">

            </button>      @foreach([

            <a href="{{ route('locales.index') }}" class="btn btn-outline-secondary">      ['id' => 1, 'title' => 'Fashion Store', 'category' => 'moda', 'category_text' => 'Moda y Accesorios', 'image' => 'https://via.placeholder.com/400x300/e74c3c/ffffff?text=Fashion+Store'],

              <i class="bi bi-x-circle"></i> Limpiar Filtros      ['id' => 2, 'title' => 'Tech World', 'category' => 'tecnologia', 'category_text' => 'Tecnología', 'image' => 'https://via.placeholder.com/400x300/3498db/ffffff?text=Tech+World'],

            </a>      ['id' => 3, 'title' => 'Bella Italia', 'category' => 'gastronomia', 'category_text' => 'Gastronomía', 'image' => 'https://via.placeholder.com/400x300/27ae60/ffffff?text=Bella+Italia'],

          </div>      ['id' => 4, 'title' => 'Sport Zone', 'category' => 'deportes', 'category_text' => 'Deportes', 'image' => 'https://via.placeholder.com/400x300/f39c12/ffffff?text=Sport+Zone'],

        </div>      ['id' => 5, 'title' => 'Home Deco', 'category' => 'hogar', 'category_text' => 'Hogar y Decoración', 'image' => 'https://via.placeholder.com/400x300/9b59b6/ffffff?text=Home+Deco'],

      </div>      ['id' => 6, 'title' => 'Beauty Salon', 'category' => 'salud', 'category_text' => 'Salud y Belleza', 'image' => 'https://via.placeholder.com/400x300/e91e63/ffffff?text=Beauty+Salon'],

    </form>      ['id' => 7, 'title' => 'Café Central', 'category' => 'gastronomia', 'category_text' => 'Gastronomía', 'image' => 'https://via.placeholder.com/400x300/16a085/ffffff?text=Cafe+Central'],

  </div>      ['id' => 8, 'title' => 'Cinema Max', 'category' => 'entretenimiento', 'category_text' => 'Entretenimiento', 'image' => 'https://via.placeholder.com/400x300/d35400/ffffff?text=Cinema+Max'],

</section>      ['id' => 9, 'title' => 'Urban Style', 'category' => 'moda', 'category_text' => 'Moda y Accesorios', 'image' => 'https://via.placeholder.com/400x300/c0392b/ffffff?text=Urban+Style'],

      ] as $store)

<section class="py-4">      @php

  <div class="container">      $detailUrl = Route::has('pages.locales.show') ? route('pages.locales.show', $store['id']) : '#';

    @if($stores->count() > 0)      @endphp

      <div class="row g-4" id="stores-grid">      <div class="col-md-6 col-lg-4 col-xl-3">

        @foreach($stores as $store)        <div class="card local-card" data-category="{{ $store['category'] }}">

        <div class="col-md-6 col-lg-4 col-xl-3">          <a href="{{ $detailUrl }}">

          <div class="card local-card" data-category="{{ strtolower($store->rubro) }}">            <img src="{{ $store['image'] }}" class="card-img-top" alt="{{ $store['title'] }}">

            <a href="{{ route('locales.show', $store->id) }}">          </a>

              <img src="https://cdn.bootstrapstudio.io/placeholders/1400x800.png" class="card-img-top" alt="{{ $store->nombre }}">          <div class="card-body">

            </a>            <h5 class="card-title">{{ $store['title'] }}</h5>

            <div class="card-body">            <p class="category-text">{{ $store['category_text'] }}</p>

              <h5 class="card-title">{{ $store->nombre }}</h5>          </div>

              <p class="category-text">        </div>

                <i class="bi bi-tag"></i> {{ $store->rubro }}      </div>

              </p>      @endforeach

              <p class="text-muted small mb-0">    </div>

                <i class="bi bi-geo-alt"></i> {{ $store->ubicacion }}

              </p>    <nav aria-label="Navegación de locales" class="mt-5">

            </div>      <ul class="pagination justify-content-center">

          </div>        <li class="page-item disabled">

        </div>          <a class="page-link" href="#" tabindex="-1">Anterior</a>

        @endforeach        </li>

      </div>        <li class="page-item active"><a class="page-link" href="#">1</a></li>

        <li class="page-item"><a class="page-link" href="#">2</a></li>

      {{-- Laravel Pagination with Bootstrap 5 --}}        <li class="page-item"><a class="page-link" href="#">3</a></li>

      <div class="d-flex justify-content-center mt-5">        <li class="page-item">

        {{ $stores->links('pagination::bootstrap-5') }}          <a class="page-link" href="#">Siguiente</a>

      </div>        </li>

    @else      </ul>

      <div class="alert alert-info text-center">    </nav>

        <i class="bi bi-info-circle fs-3"></i>  </div>

        <p class="mb-0 mt-2">No se encontraron locales con los filtros seleccionados.</p></section>

      </div>@endsection

    @endif

  </div>@push('scripts')

</section>@vite('resources/js/frontoffice/main.js')

@endsection@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit on filter change
    const filterSelect = document.querySelector('#rubro');
    if (filterSelect) {
        filterSelect.addEventListener('change', function() {
            document.getElementById('filter-form').submit();
        });
    }
});
</script>
@endpush
