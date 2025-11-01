@extends('layouts.app')

@php
use Illuminate\Support\Facades\Route;

$promotion = $promotion ?? [
'id' => 1,
'title' => '50% de descuento en segunda unidad',
'category' => 'Inicial',
'category_class' => 'badge-inicial',
'store' => [
'id' => 1,
'name' => 'Fashion Store',
'location' => 'Local 101 - Planta Baja',
'code' => '001',
],
'image' => 'https://cdn.bootstrapstudio.io/placeholders/1400x800.png',
'valid_from' => '01/10/2025',
'valid_until' => '31/12/2025',
'days' => ['Lun', 'Mar', 'Mié', 'Jue', 'Vie'],
'category_access' => 'Disponible para clientes Inicial, Medium y Premium',
'description' => 'Llevá dos prendas de la colección actual y la segunda te sale mitad de precio. Esta promoción es válida en toda la tienda, excepto en artículos en liquidación.',
];

$terms = [
'Válido únicamente en Fashion Store, Local 101 - Planta Baja',
'El descuento se aplica sobre la prenda de menor valor',
'No acumulable con otras promociones',
'Válido de lunes a viernes en el horario de atención del local',
'Cada cliente puede usar esta promoción una sola vez durante su vigencia',
'No aplica en artículos en liquidación o con descuento previo',
'Se requiere presentar código de cliente registrado',
'El local se reserva el derecho de aceptar o rechazar la solicitud de descuento',
];

$steps = [
[
'icon' => 'bi-person-plus',
'title' => '1. Registrate',
'text' => 'Creá tu cuenta en el sistema del shopping.',
],
[
'icon' => 'bi-shop',
'title' => '2. Visitá el Local',
'text' => 'Dirigite al local Fashion Store.',
],
[
'icon' => 'bi-upc-scan',
'title' => '3. Ingresá el Código',
'text' => 'Presentá el código del local (001) desde tu cuenta.',
],
[
'icon' => 'bi-check-circle',
'title' => '4. Disfrutá',
'text' => 'El local aprobará tu descuento y podrás usarlo.',
],
];

$otherPromotions = $otherPromotions ?? [
[
'id' => 2,
'title' => '30% OFF en nueva colección',
'category' => 'Medium',
'category_class' => 'badge-medium',
'description' => 'Descuento especial en toda la colección de primavera-verano.',
'image' => 'https://via.placeholder.com/400x200/3498db/ffffff?text=30%25+OFF',
],
[
'id' => 3,
'title' => 'Acceso exclusivo a preventa',
'category' => 'Premium',
'category_class' => 'badge-premium',
'description' => 'Comprá con anticipación las piezas de la próxima temporada.',
'image' => 'https://via.placeholder.com/400x200/8e44ad/ffffff?text=Exclusivo',
],
];

$allDays = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
$detailRouteExists = Route::has('pages.promociones.show');
$storeRoute = Route::has('pages.locales.show') ? route('pages.locales.show', $promotion['store']['id']) : '#';
$promotionsRoute = Route::has('pages.promociones') ? route('pages.promociones') : '#';
$registerUrl = Route::has('auth.register') ? route('auth.register') : (Route::has('register') ? route('register') : '#');
@endphp

@section('title', $promotion['title'] . ' - Shopping Rosario')
@section('meta_description', 'Detalles de la promoción ' . $promotion['title'] . ' disponible en ' . $promotion['store']['name'] . '.')

@section('content')
<x-layout.breadcrumbs :items="[
        ['label' => 'Promociones', 'url' => $promotionsRoute],
        ['label' => $promotion['title']]
    ]" />

<section class="py-4">
  <div class="container">
    <div class="row">
      <div class="col-lg-6 mb-4">
        <img src="{{ $promotion['image'] }}" class="detail-image img-fluid rounded" alt="{{ $promotion['title'] }}">
      </div>
      <div class="col-lg-6 mb-4">
        <div class="detail-info p-4 bg-white rounded shadow-sm">
          <div class="d-flex justify-content-between align-items-start mb-3">
            <h2 class="mb-0">{{ $promotion['title'] }}</h2>
          </div>
          <div class="mb-3">
            <span class="badge {{ $promotion['category_class'] }} badge-category">Categoría {{ $promotion['category'] }}</span>
          </div>
          <p class="lead mb-4">{{ $promotion['description'] }}</p>

          <div class="info-item">
            <i class="bi bi-shop-window"></i>
            <strong>Local:</strong>
            <span><a href="{{ $storeRoute }}">{{ $promotion['store']['name'] }}</a></span>
          </div>

          <div class="info-item">
            <i class="bi bi-geo-alt-fill"></i>
            <strong>Ubicación:</strong>
            <span>{{ $promotion['store']['location'] }}</span>
          </div>

          <div class="info-item">
            <i class="bi bi-calendar-range"></i>
            <strong>Vigencia:</strong>
            <span>Desde {{ $promotion['valid_from'] }} hasta {{ $promotion['valid_until'] }}</span>
          </div>

          <div class="info-item">
            <i class="bi bi-clock-history"></i>
            <strong>Días válidos:</strong>
            <div class="promo-days ms-2">
              @foreach($allDays as $day)
              <span class="{{ in_array($day, $promotion['days']) ? 'active' : '' }}">{{ $day }}</span>
              @endforeach
            </div>
          </div>

          <div class="info-item">
            <i class="bi bi-person-badge"></i>
            <strong>Categoría:</strong>
            <span>{{ $promotion['category_access'] }}</span>
          </div>

          <div class="alert alert-info mt-4" role="alert">
            <i class="bi bi-info-circle-fill"></i>
            <strong>Nota:</strong> Para acceder a esta promoción debés estar registrado. Ingresá el código del local {{ $promotion['store']['code'] }} al solicitarla.
          </div>

          <div class="mt-4 d-grid gap-2">
            <a href="{{ $registerUrl }}" class="btn btn-primary btn-lg">
              <i class="bi bi-person-plus"></i> Registrarme para Acceder
            </a>
            <a href="{{ $storeRoute }}" class="btn btn-outline-primary btn-lg">
              <i class="bi bi-shop"></i> Ver Local
            </a>
          </div>
        </div>
      </div>
    </div>

    <div class="row mt-4">
      <div class="col-12">
        <div class="card shadow-sm">
          <div class="card-body">
            <h4 class="card-title mb-3">
              <i class="bi bi-file-text"></i> Términos y Condiciones
            </h4>
            <ul class="list-unstyled">
              @foreach($terms as $term)
              <li class="mb-2"><i class="bi bi-check-circle text-success"></i> {{ $term }}</li>
              @endforeach
            </ul>
          </div>
        </div>
      </div>
    </div>

    <div class="row mt-4">
      <div class="col-12">
        <div class="card shadow-sm">
          <div class="card-body">
            <h4 class="card-title mb-3">
              <i class="bi bi-question-circle"></i> ¿Cómo usar esta promoción?
            </h4>
            <div class="row">
              @foreach($steps as $step)
              <div class="col-md-3 text-center mb-3">
                <div class="feature-box bg-white h-100">
                  <i class="bi {{ $step['icon'] }}"></i>
                  <h5>{{ $step['title'] }}</h5>
                  <p>{{ $step['text'] }}</p>
                </div>
              </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row mt-5">
      <div class="col-12">
        <h3 class="section-title mb-4">Otras promociones de {{ $promotion['store']['name'] }}</h3>
      </div>

      @foreach($otherPromotions as $otherPromotion)
      @php
      $otherUrl = $detailRouteExists ? route('pages.promociones.show', $otherPromotion['id']) : '#';
      @endphp
      <div class="col-md-6 mb-4">
        <div class="card promo-card h-100">
          <span class="badge bg-warning promo-badge">Vigente</span>
          <img src="{{ $otherPromotion['image'] }}" class="card-img-top" alt="{{ $otherPromotion['title'] }}">
          <div class="card-body">
            <span class="badge {{ $otherPromotion['category_class'] }} badge-category">{{ $otherPromotion['category'] }}</span>
            <h5 class="card-title mt-2">{{ $otherPromotion['title'] }}</h5>
            <p class="card-text">{{ $otherPromotion['description'] }}</p>
            <a href="{{ $otherUrl }}" class="btn btn-primary w-100 mt-2">Ver Detalles</a>
          </div>
        </div>
      </div>
      @endforeach
    </div>

    <div class="row mt-4">
      <div class="col-12">
        <a href="{{ $promotionsRoute }}" class="btn btn-outline-primary">
          <i class="bi bi-arrow-left"></i> Volver a Promociones
        </a>
      </div>
    </div>
  </div>
</section>
@endsection

@push('scripts')
@vite('resources/js/frontoffice/main.js')
@endpush