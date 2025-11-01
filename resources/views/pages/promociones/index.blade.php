@extends('layouts.app')

@section('title', 'Promociones - Shopping Rosario')
@section('meta_description', 'Descubre todas las promociones activas del Shopping Rosario y encontrá beneficios según tu categoría de cliente.')

@php
use Illuminate\Support\Facades\Route;

$promotions = [
[
'id' => 1,
'title' => '50% de descuento en segunda unidad',
'category' => 'Inicial',
'category_class' => 'badge-inicial',
'category_slug' => 'inicial',
'store' => 'Fashion Store',
'image' => 'https://via.placeholder.com/400x300/e74c3c/ffffff?text=50%25+OFF',
'valid_until' => '31/12/2025',
'is_expiring' => true,
],
[
'id' => 2,
'title' => '20% OFF en accesorios tech',
'category' => 'Medium',
'category_class' => 'badge-medium',
'category_slug' => 'medium',
'store' => 'Tech World',
'image' => 'https://via.placeholder.com/400x300/3498db/ffffff?text=20%25+OFF',
'valid_until' => '15/11/2025',
'is_expiring' => false,
],
[
'id' => 3,
'title' => '2x1 en platos principales',
'category' => 'Premium',
'category_class' => 'badge-premium',
'category_slug' => 'premium',
'store' => 'Bella Italia',
'image' => 'https://via.placeholder.com/400x300/27ae60/ffffff?text=2x1',
'valid_until' => '30/12/2025',
'is_expiring' => false,
],
[
'id' => 4,
'title' => '3x2 en medias deportivas',
'category' => 'Inicial',
'category_class' => 'badge-inicial',
'category_slug' => 'inicial',
'store' => 'Sport Zone',
'image' => 'https://via.placeholder.com/400x300/f39c12/ffffff?text=3x2',
'valid_until' => '20/11/2025',
'is_expiring' => false,
],
[
'id' => 5,
'title' => '25% OFF en decoración',
'category' => 'Medium',
'category_class' => 'badge-medium',
'category_slug' => 'medium',
'store' => 'Home Deco',
'image' => 'https://via.placeholder.com/400x300/9b59b6/ffffff?text=25%25+OFF',
'valid_until' => '10/12/2025',
'is_expiring' => false,
],
[
'id' => 6,
'title' => 'Tratamiento VIP con descuento',
'category' => 'Premium',
'category_class' => 'badge-premium',
'category_slug' => 'premium',
'store' => 'Beauty Salon',
'image' => 'https://via.placeholder.com/400x300/e91e63/ffffff?text=VIP',
'valid_until' => '05/12/2025',
'is_expiring' => false,
],
[
'id' => 7,
'title' => 'Happy Hour cafetería',
'category' => 'Inicial',
'category_class' => 'badge-inicial',
'category_slug' => 'inicial',
'store' => 'Café Central',
'image' => 'https://via.placeholder.com/400x300/16a085/ffffff?text=Happy+Hour',
'valid_until' => '31/12/2025',
'is_expiring' => false,
],
[
'id' => 8,
'title' => '40% OFF entradas miércoles',
'category' => 'Medium',
'category_class' => 'badge-medium',
'category_slug' => 'medium',
'store' => 'Cinema Max',
'image' => 'https://via.placeholder.com/400x300/d35400/ffffff?text=40%25+OFF',
'valid_until' => '25/12/2025',
'is_expiring' => false,
],
[
'id' => 9,
'title' => '15% OFF primera compra',
'category' => 'Inicial',
'category_class' => 'badge-inicial',
'category_slug' => 'inicial',
'store' => 'Urban Style',
'image' => 'https://via.placeholder.com/400x300/c0392b/ffffff?text=15%25+OFF',
'valid_until' => '31/12/2025',
'is_expiring' => false,
],
];

$categoryCards = [
[
'label' => 'Inicial',
'description' => 'Al registrarte obtienes acceso a promociones básicas.',
'benefits' => [
'Acceso a promociones Inicial',
'Newsletter semanal',
],
'badge_class' => 'badge-inicial',
],
[
'label' => 'Medium',
'description' => 'Accedé a mejores promociones con tu historial de compras.',
'benefits' => [
'Todas las anteriores',
'Promociones Medium',
'Descuentos especiales',
],
'badge_class' => 'badge-medium',
],
[
'label' => 'Premium',
'description' => 'Disfrutá de beneficios exclusivos y promociones VIP.',
'benefits' => [
'Todas las anteriores',
'Promociones Premium',
'Acceso anticipado',
],
'badge_class' => 'badge-premium',
],
];

$detailRouteExists = Route::has('pages.promociones.show');
$registerUrl = Route::has('auth.register') ? route('auth.register') : (Route::has('register') ? route('register') : '#');
@endphp

@section('content')
<x-layout.breadcrumbs :items="[['label' => 'Promociones']]" />

<section class="py-4">
  <div class="container text-center">
    <h1 class="display-5 fw-bold text-primary">Promociones</h1>
    <p class="lead">Descubrí las mejores ofertas y descuentos del shopping.</p>
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
          <input type="text" class="form-control border-start-0 ps-0" id="searchPromo" placeholder="Buscar Promoción">
        </div>
      </div>
    </div>

    <div class="filter-section">
      <h5 class="d-flex align-items-center gap-2"><i class="bi bi-funnel"></i> Filtrar Promociones</h5>
      <div class="row g-3">
        <div class="col-md-6">
          <label for="categoryPromo" class="form-label">Categoría de Cliente</label>
          <select class="form-select" id="categoryPromo">
            <option value="">Todas las categorías</option>
            <option value="inicial">Inicial</option>
            <option value="medium">Medium</option>
            <option value="premium">Premium</option>
          </select>
        </div>
        <div class="col-md-6">
          <label for="localFilter" class="form-label">Local</label>
          <select class="form-select" id="localFilter">
            <option value="">Todos los locales</option>
            @foreach(collect($promotions)->pluck('store')->unique()->sort() as $storeName)
            <option value="{{ $storeName }}">{{ $storeName }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="py-4">
  <div class="container">
    <div class="row g-4" id="promotions-grid">
      @foreach($promotions as $promotion)
      @php
      $detailUrl = $detailRouteExists ? route('pages.promociones.show', $promotion['id']) : '#';
      @endphp
      <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="card promo-card" data-category="{{ $promotion['category_slug'] }}" data-local="{{ $promotion['store'] }}">
          @if($promotion['is_expiring'])
          <span class="badge bg-danger promo-badge">
            <i class="bi bi-exclamation-triangle"></i> ¡Por vencer!
          </span>
          @endif
          <a href="{{ $detailUrl }}">
            <img src="{{ $promotion['image'] }}" class="card-img-top" alt="{{ $promotion['title'] }}">
          </a>
          <div class="card-body">
            <div class="mb-2">
              <span class="badge {{ $promotion['category_class'] }} badge-category">{{ $promotion['category'] }}</span>
            </div>
            <h5 class="card-title">{{ $promotion['title'] }}</h5>
            <p class="promo-info mb-1">
              <i class="bi bi-shop"></i> {{ $promotion['store'] }}
            </p>
            <div class="promo-validity">
              <i class="bi bi-calendar-event"></i> Válido hasta: {{ $promotion['valid_until'] }}
            </div>
          </div>
        </div>
      </div>
      @endforeach
    </div>

    <nav aria-label="Navegación de promociones" class="mt-5">
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

<hr class="section-separator">

<section class="py-5">
  <div class="container">
    <div class="text-center mb-4">
      <h3 class="section-title">Categorías de Clientes</h3>
      <p class="section-subtitle">Descubrí los beneficios de cada categoría</p>
    </div>
    <div class="row g-4">
      @foreach($categoryCards as $card)
      <div class="col-md-4">
        <div class="card text-center h-100">
          <div class="card-body">
            <span class="badge {{ $card['badge_class'] }} badge-category fs-5 mb-3">{{ $card['label'] }}</span>
            <h5 class="mb-3">Cliente {{ $card['label'] }}</h5>
            <p>{{ $card['description'] }}</p>
            <ul class="list-unstyled text-start">
              @foreach($card['benefits'] as $benefit)
              <li><i class="bi bi-check-circle-fill text-success"></i> {{ $benefit }}</li>
              @endforeach
            </ul>
          </div>
        </div>
      </div>
      @endforeach
    </div>
    <div class="text-center mt-4">
      <p class="lead">¿Querés acceder a todas las promociones?</p>
      <a href="{{ $registerUrl }}" class="btn btn-primary btn-lg">
        <i class="bi bi-person-plus"></i> Registrate Ahora
      </a>
    </div>
  </div>
</section>
@endsection

@push('scripts')
@vite('resources/js/frontoffice/main.js')
@endpush