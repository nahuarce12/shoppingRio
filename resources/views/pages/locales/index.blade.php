@extends('layouts.app')

@section('title', 'Locales - Shopping Rosario')
@section('meta_description', 'Descubre todos los locales disponibles en Shopping Rosario y encontrá tu tienda favorita.')

@section('content')
<x-layout.breadcrumbs :items="[['label' => 'Locales']]" />

<section class="py-4">
  <div class="container text-center">
    <h1 class="display-5 fw-bold text-primary">Locales</h1>
    <p class="lead">Explorá todos los locales disponibles en nuestro shopping.</p>
  </div>
</section>

<section class="py-4">
  <div class="container">
    <div class="row mb-3">
      <div class="col-12">
        <div class="input-group input-group-lg">
          <span class="input-group-text bg-white border-end-0">
            <i class="bi bi-search"></i>
          </span>
          <input type="text" class="form-control border-start-0 ps-0" id="searchLocal" placeholder="Buscar Local">
        </div>
      </div>
    </div>

    <div class="filter-section">
      <h5 class="d-flex align-items-center gap-2"><i class="bi bi-funnel"></i> Filtrar Locales</h5>
      <select class="form-select" id="categoryFilter">
        <option value="">Todas las categorías</option>
        <option value="moda">Moda y Accesorios</option>
        <option value="tecnologia">Tecnología</option>
        <option value="gastronomia">Gastronomía</option>
        <option value="deportes">Deportes</option>
        <option value="hogar">Hogar y Decoración</option>
        <option value="entretenimiento">Entretenimiento</option>
        <option value="salud">Salud y Belleza</option>
      </select>
    </div>
  </div>
</section>

<section class="py-4">
  <div class="container">
    <div class="row g-4" id="stores-grid">
      @foreach([
      ['id' => 1, 'title' => 'Fashion Store', 'category' => 'moda', 'category_text' => 'Moda y Accesorios', 'image' => 'https://via.placeholder.com/400x300/e74c3c/ffffff?text=Fashion+Store'],
      ['id' => 2, 'title' => 'Tech World', 'category' => 'tecnologia', 'category_text' => 'Tecnología', 'image' => 'https://via.placeholder.com/400x300/3498db/ffffff?text=Tech+World'],
      ['id' => 3, 'title' => 'Bella Italia', 'category' => 'gastronomia', 'category_text' => 'Gastronomía', 'image' => 'https://via.placeholder.com/400x300/27ae60/ffffff?text=Bella+Italia'],
      ['id' => 4, 'title' => 'Sport Zone', 'category' => 'deportes', 'category_text' => 'Deportes', 'image' => 'https://via.placeholder.com/400x300/f39c12/ffffff?text=Sport+Zone'],
      ['id' => 5, 'title' => 'Home Deco', 'category' => 'hogar', 'category_text' => 'Hogar y Decoración', 'image' => 'https://via.placeholder.com/400x300/9b59b6/ffffff?text=Home+Deco'],
      ['id' => 6, 'title' => 'Beauty Salon', 'category' => 'salud', 'category_text' => 'Salud y Belleza', 'image' => 'https://via.placeholder.com/400x300/e91e63/ffffff?text=Beauty+Salon'],
      ['id' => 7, 'title' => 'Café Central', 'category' => 'gastronomia', 'category_text' => 'Gastronomía', 'image' => 'https://via.placeholder.com/400x300/16a085/ffffff?text=Cafe+Central'],
      ['id' => 8, 'title' => 'Cinema Max', 'category' => 'entretenimiento', 'category_text' => 'Entretenimiento', 'image' => 'https://via.placeholder.com/400x300/d35400/ffffff?text=Cinema+Max'],
      ['id' => 9, 'title' => 'Urban Style', 'category' => 'moda', 'category_text' => 'Moda y Accesorios', 'image' => 'https://via.placeholder.com/400x300/c0392b/ffffff?text=Urban+Style'],
      ] as $store)
      @php
      $detailUrl = Route::has('pages.locales.show') ? route('pages.locales.show', $store['id']) : '#';
      @endphp
      <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="card local-card" data-category="{{ $store['category'] }}">
          <a href="{{ $detailUrl }}">
            <img src="{{ $store['image'] }}" class="card-img-top" alt="{{ $store['title'] }}">
          </a>
          <div class="card-body">
            <h5 class="card-title">{{ $store['title'] }}</h5>
            <p class="category-text">{{ $store['category_text'] }}</p>
          </div>
        </div>
      </div>
      @endforeach
    </div>

    <nav aria-label="Navegación de locales" class="mt-5">
      <ul class="pagination justify-content-center">
        <li class="page-item disabled">
          <a class="page-link" href="#" tabindex="-1">Anterior</a>
        </li>
        <li class="page-item active"><a class="page-link" href="#">1</a></li>
        <li class="page-item"><a class="page-link" href="#">2</a></li>
        <li class="page-item"><a class="page-link" href="#">3</a></li>
        <li class="page-item">
          <a class="page-link" href="#">Siguiente</a>
        </li>
      </ul>
    </nav>
  </div>
</section>
@endsection

@push('scripts')
@vite('resources/js/frontoffice/main.js')
@endpush