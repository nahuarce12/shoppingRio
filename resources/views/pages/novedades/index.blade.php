@extends('layouts.app')

@section('title', 'Novedades - Shopping Rosario')
@section('meta_description', 'Enterate de las últimas novedades, eventos y anuncios especiales del Shopping Rosario.')

@php
use Illuminate\Support\Facades\Route;

$newsItems = [
[
'title' => 'Nueva Temporada Primavera-Verano',
'category' => 'Premium',
'category_class' => 'badge-premium',
'date' => '20/10/2025',
'expires' => '15/12/2025',
'image' => 'https://via.placeholder.com/400x250/e74c3c/ffffff?text=Nueva+Temporada',
'description' => 'Descubre las últimas tendencias en moda para esta temporada. Todos los locales de indumentaria renuevan su stock con las mejores propuestas.',
'is_active' => true,
],
[
'title' => 'Expo Tech 2025',
'category' => 'Inicial',
'category_class' => 'badge-inicial',
'date' => '18/10/2025',
'expires' => '25/10/2025',
'image' => 'https://via.placeholder.com/400x250/3498db/ffffff?text=Evento+Tech',
'description' => 'Gran exhibición de tecnología con lanzamientos exclusivos. Todos los locales de tecnología participan con ofertas especiales y demostraciones en vivo.',
'is_active' => true,
],
[
'title' => 'Semana Gastronómica',
'category' => 'Medium',
'category_class' => 'badge-medium',
'date' => '15/10/2025',
'expires' => '22/10/2025',
'image' => 'https://via.placeholder.com/400x250/27ae60/ffffff?text=Semana+Gourmet',
'description' => 'Disfruta de menús especiales en todos nuestros restaurantes. Una semana dedicada a los mejores sabores de la ciudad con propuestas únicas.',
'is_active' => true,
],
[
'title' => 'Anticipo Black Friday',
'category' => 'Inicial',
'category_class' => 'badge-inicial',
'date' => '10/10/2025',
'expires' => '31/10/2025',
'image' => 'https://via.placeholder.com/400x250/f39c12/ffffff?text=Black+Friday',
'description' => 'No esperes hasta noviembre. Adelantamos algunas ofertas exclusivas para nuestros clientes registrados en categorías Inicial y superiores.',
'is_active' => true,
],
[
'title' => 'Día del Niño - Actividades Especiales',
'category' => 'Inicial',
'category_class' => 'badge-inicial',
'date' => '05/10/2025',
'expires' => null,
'image' => 'https://via.placeholder.com/400x250/9b59b6/ffffff?text=Kids+Day',
'description' => 'Celebramos el Día del Niño con actividades, juegos y sorpresas en todo el shopping. Entrada gratuita a todas las actividades.',
'is_active' => false,
],
[
'title' => 'Inauguración: Sports Zone',
'category' => 'Premium',
'category_class' => 'badge-premium',
'date' => '01/10/2025',
'expires' => '31/10/2025',
'image' => 'https://via.placeholder.com/400x250/e67e22/ffffff?text=Nuevo+Local',
'description' => 'Damos la bienvenida a Sports Zone, tu nueva tienda de deportes con las mejores marcas. Promociones especiales de inauguración todo el mes.',
'is_active' => true,
],
];
@endphp

@section('content')
<x-layout.breadcrumbs :items="[['label' => 'Novedades']]" />

<section class="py-4">
  <div class="container text-center">
    <h1 class="display-5 fw-bold text-primary">Novedades</h1>
    <p class="lead">Mantente al día con las últimas noticias y eventos del centro comercial.</p>
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
          <input type="text" class="form-control border-start-0 ps-0" id="searchNews" placeholder="Buscar Novedad">
        </div>
      </div>
    </div>

    <div class="filter-section">
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label" for="categoryNews">Categoría de Cliente</label>
          <select class="form-select" id="categoryNews">
            <option value="">Todas las categorías</option>
            @foreach(collect($newsItems)->pluck('category')->unique() as $category)
            <option value="{{ strtolower($category) }}">{{ $category }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label" for="dateFrom">Desde</label>
          <input type="date" class="form-control" id="dateFrom">
        </div>
        <div class="col-md-4">
          <label class="form-label" for="dateTo">Hasta</label>
          <input type="date" class="form-control" id="dateTo">
        </div>
      </div>
    </div>
  </div>
</section>

<section class="py-5">
  <div class="container">
    <div class="row g-4" id="news-grid">
      @foreach($newsItems as $item)
      <div class="col-md-6 col-lg-4">
        <div class="card h-100 hover-shadow" data-category="{{ strtolower($item['category']) }}">
          <img src="{{ $item['image'] }}" class="card-img-top" alt="{{ $item['title'] }}">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="badge {{ $item['category_class'] }}">{{ $item['category'] }}</span>
              <small class="text-muted">
                <i class="bi bi-calendar3"></i> {{ $item['date'] }}
              </small>
            </div>
            <h5 class="card-title">{{ $item['title'] }}</h5>
            <p class="card-text">{{ $item['description'] }}</p>
            <div class="mt-3">
              @if($item['is_active'] && $item['expires'])
              <span class="badge bg-info">
                <i class="bi bi-clock"></i> Vigente hasta: {{ $item['expires'] }}
              </span>
              @elseif(!$item['is_active'])
              <span class="badge bg-warning text-dark">
                <i class="bi bi-clock"></i> Finalizada
              </span>
              @endif
            </div>
          </div>
        </div>
      </div>
      @endforeach
    </div>

    <nav aria-label="Navegación de novedades" class="mt-5">
      <ul class="pagination justify-content-center">
        <li class="page-item disabled"><a class="page-link" href="#">Anterior</a></li>
        <li class="page-item active"><a class="page-link" href="#">1</a></li>
        <li class="page-item"><a class="page-link" href="#">2</a></li>
        <li class="page-item"><a class="page-link" href="#">3</a></li>
        <li class="page-item"><a class="page-link" href="#">Siguiente</a></li>
      </ul>
    </nav>
  </div>
</section>
@endsection

@push('scripts')
@vite('resources/js/frontoffice/main.js')
@endpush