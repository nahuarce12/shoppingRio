@extends('layouts.app')

@php
use Illuminate\Support\Facades\Route;

$store = $store ?? [
'id' => 1,
'code' => '001',
'name' => 'Fashion Store',
'category' => 'Moda y Accesorios',
'image' => 'https://cdn.bootstrapstudio.io/placeholders/1400x800.png',
'location' => 'Local 101 - Planta Baja',
'phone' => '(0341) 456-7890',
'email' => 'info@fashionstore.com',
'website' => '#',
'schedule' => 'Lunes a Domingo: 10:00 - 22:00',
'description' => 'Las últimas tendencias en moda para toda la familia. Encontrá tu estilo único con nuestra amplia colección de prendas y accesorios de las mejores marcas.',
'extended_description' => [
'Fashion Store es tu destino para la moda contemporánea. Con más de 10 años en el mercado, nos especializamos en ofrecer las últimas tendencias internacionales adaptadas al estilo argentino.',
'Nuestra colección incluye ropa casual, formal, deportiva y accesorios para hombres, mujeres y niños. Trabajamos con marcas reconocidas y también con diseñadores locales emergentes.'
]
];

$promotions = $promotions ?? [
[
'id' => 1,
'title' => '50% de descuento en segunda unidad',
'category' => 'Inicial',
'category_class' => 'badge-inicial',
'valid_until' => '31/12/2025',
'description' => 'Llevá dos prendas y la segunda te sale mitad de precio. Válido en toda la tienda excepto artículos en liquidación.',
'image' => 'https://cdn.bootstrapstudio.io/placeholders/1400x800.png',
'days' => ['L', 'M', 'X', 'J', 'V'],
],
[
'id' => 2,
'title' => '30% OFF en nueva colección',
'category' => 'Medium',
'category_class' => 'badge-medium',
'valid_until' => '30/11/2025',
'description' => 'Descuento especial en toda la colección de primavera-verano. Solo para clientes Medium y Premium.',
'image' => 'https://via.placeholder.com/400x200/3498db/ffffff?text=30%25+OFF',
'days' => ['J', 'V', 'S'],
],
[
'id' => 3,
'title' => 'Acceso exclusivo a preventa',
'category' => 'Premium',
'category_class' => 'badge-premium',
'valid_until' => '15/12/2025',
'description' => 'Comprá con anticipación las piezas de la próxima temporada con 40% de descuento. Solo Premium.',
'image' => 'https://via.placeholder.com/400x200/8e44ad/ffffff?text=Exclusivo',
'days' => ['L', 'M', 'X', 'J', 'V', 'S', 'D'],
],
];
@endphp

@section('title', $store['name'] . ' - Shopping Rosario')
@section('meta_description', 'Conocé las promociones y datos de ' . $store['name'] . ' en Shopping Rosario.')

@section('content')
<x-layout.breadcrumbs :items="[
        ['label' => 'Locales', 'url' => Route::has('pages.locales') ? route('pages.locales') : '#'],
        ['label' => $store['name']]
    ]" />

<section class="py-4">
  <div class="container">
    <div class="row">
      <div class="col-lg-6 mb-4">
        <img src="{{ $store['image'] }}" class="detail-image img-fluid rounded" alt="{{ $store['name'] }}">
      </div>
      <div class="col-lg-6 mb-4">
        <div class="detail-info p-4 bg-white rounded shadow-sm">
          <div class="d-flex justify-content-between align-items-start mb-3">
            <h2 class="mb-0">{{ $store['name'] }}</h2>
            <span class="badge bg-info fs-6">Código: {{ $store['code'] }}</span>
          </div>
          <div class="mb-3 d-flex gap-2 flex-wrap">
            <span class="badge bg-primary">{{ $store['category'] }}</span>
            <span class="badge bg-success"><i class="bi bi-star-fill"></i> 3 Promociones Activas</span>
          </div>
          <p class="lead mb-4">{{ $store['description'] }}</p>
          <div class="info-item mb-2"><i class="bi bi-geo-alt-fill"></i> <strong>Ubicación:</strong> <span>{{ $store['location'] }}</span></div>
          <div class="info-item mb-2"><i class="bi bi-telephone-fill"></i> <strong>Teléfono:</strong> <span>{{ $store['phone'] }}</span></div>
          <div class="info-item mb-2"><i class="bi bi-envelope-fill"></i> <strong>Email:</strong> <span>{{ $store['email'] }}</span></div>
          <div class="info-item mb-2"><i class="bi bi-clock-fill"></i> <strong>Horario:</strong> <span>{{ $store['schedule'] }}</span></div>
          <div class="info-item mb-4"><i class="bi bi-globe"></i> <strong>Sitio Web:</strong> <span><a href="{{ $store['website'] }}" target="_blank" rel="noopener">Visitar sitio</a></span></div>
          <a href="#promociones" class="btn btn-primary btn-lg w-100">
            <i class="bi bi-tag-fill"></i> Ver Promociones de este Local
          </a>
        </div>
      </div>
    </div>

    <div class="row mt-4">
      <div class="col-12">
        <div class="card shadow-sm">
          <div class="card-body">
            <h4 class="card-title mb-3"><i class="bi bi-info-circle"></i> Sobre {{ $store['name'] }}</h4>
            @foreach($store['extended_description'] as $paragraph)
            <p>{{ $paragraph }}</p>
            @endforeach
            <div class="row mt-4 text-center">
              <div class="col-md-4 mb-3">
                <i class="bi bi-award-fill fs-1 text-primary"></i>
                <h5 class="mt-2">Calidad Garantizada</h5>
                <p class="text-muted mb-0">Productos seleccionados</p>
              </div>
              <div class="col-md-4 mb-3">
                <i class="bi bi-truck fs-1 text-primary"></i>
                <h5 class="mt-2">Envío a Domicilio</h5>
                <p class="text-muted mb-0">Comprá online</p>
              </div>
              <div class="col-md-4 mb-3">
                <i class="bi bi-credit-card fs-1 text-primary"></i>
                <h5 class="mt-2">Múltiples Pagos</h5>
                <p class="text-muted mb-0">Efectivo y tarjetas</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row mt-5" id="promociones">
      <div class="col-12 mb-4">
        <h3 class="section-title"><i class="bi bi-tag-fill"></i> Promociones Vigentes</h3>
      </div>
      @foreach($promotions as $promotion)
      @php
      $detailUrl = Route::has('pages.promociones.show') ? route('pages.promociones.show', $promotion['id']) : '#';
      @endphp
      <div class="col-md-6 col-lg-4 mb-4">
        <div class="card promo-card" data-category="{{ strtolower($promotion['category']) }}">
          <span class="badge bg-warning promo-badge"><i class="bi bi-clock"></i> Vigente</span>
          <img src="{{ $promotion['image'] }}" class="card-img-top" alt="{{ $promotion['title'] }}">
          <div class="card-body">
            <div class="mb-2">
              <span class="badge {{ $promotion['category_class'] }} badge-category">{{ $promotion['category'] }}</span>
            </div>
            <h5 class="card-title">{{ $promotion['title'] }}</h5>
            <p class="card-text">{{ $promotion['description'] }}</p>
            <div class="promo-validity"><i class="bi bi-calendar-event"></i> Válido hasta: {{ $promotion['valid_until'] }}</div>
            <div class="promo-days mt-2">
              @foreach(['L','M','X','J','V','S','D'] as $day)
              <span class="{{ in_array($day, $promotion['days']) ? 'active' : '' }}">{{ $day }}</span>
              @endforeach
            </div>
            <a href="{{ $detailUrl }}" class="btn btn-primary w-100 mt-3">Ver Detalles Completos</a>
          </div>
        </div>
      </div>
      @endforeach
    </div>

    <div class="row mt-4">
      <div class="col-12">
        <a href="{{ Route::has('pages.locales') ? route('pages.locales') : '#' }}" class="btn btn-outline-primary">
          <i class="bi bi-arrow-left"></i> Volver a Locales
        </a>
      </div>
    </div>
  </div>
</section>
@endsection

@push('scripts')
@vite('resources/js/frontoffice/main.js')
@endpush