@extends('layouts.app')

@php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

$dayLabels = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
$activeDays = collect($promotion->dias_semana ?? [])
    ->map(fn ($value) => (bool) $value)
    ->pad(7, false)
    ->take(7)
    ->values()
    ->all();

$categoryHierarchy = ['Inicial', 'Medium', 'Premium'];
$minCategoryIndex = array_search($promotion->categoria_minima, $categoryHierarchy, true) ?? 0;
$accessibleCategories = array_slice($categoryHierarchy, $minCategoryIndex);
$storeRoute = route('locales.show', $promotion->store);
$promotionsIndexRoute = route('promociones.index');
$registerRoute = Route::has('register') ? route('register') : '#';
$daysToExpire = now()->diffInDays($promotion->fecha_hasta, false);
$clientEligibility = $clientEligibility ?? null;
$hasRequested = $hasRequested ?? false;
$canRequest = ($clientEligibility['eligible'] ?? false) && !$hasRequested;
$clientRestrictionMessage = $clientEligibility['reason'] ?? null;
@endphp

@section('title', $promotion->texto . ' - Shopping Rosario')
@section('meta_description', 'Detalles de la promoción "' . $promotion->texto . '" disponible en ' . $promotion->store->nombre . '.')

@section('content')
<x-layout.breadcrumbs :items="[
        ['label' => 'Promociones', 'url' => $promotionsIndexRoute],
        ['label' => Str::limit($promotion->texto, 70)]
    ]" />

<section class="py-4">
  <div class="container">
    <div class="row">
      <div class="col-lg-6 mb-4">
        <div class="ratio ratio-4x3 bg-light rounded">
          <img src="https://cdn.bootstrapstudio.io/placeholders/1400x800.png" class="object-fit-cover rounded" alt="Imagen referencial de {{ $promotion->texto }}">
        </div>
      </div>
      <div class="col-lg-6 mb-4">
        <div class="detail-info p-4 bg-white rounded shadow-sm h-100">
          <div class="d-flex justify-content-between align-items-start mb-3">
            <h2 class="mb-0">{{ $promotion->texto }}</h2>
            <span class="badge text-uppercase badge-{{ strtolower($promotion->categoria_minima) }}">{{ $promotion->categoria_minima }}</span>
          </div>

          <p class="text-muted mb-3">Código promoción: {{ sprintf('%04d', $promotion->codigo) }}</p>

          <div class="info-item mb-2">
            <i class="bi bi-shop-window"></i>
            <strong>Local:</strong>
            <a href="{{ $storeRoute }}" class="ms-1">{{ $promotion->store->nombre }}</a>
          </div>

          <div class="info-item mb-2">
            <i class="bi bi-geo-alt-fill"></i>
            <strong>Ubicación:</strong>
            <span class="ms-1">{{ $promotion->store->ubicacion }}</span>
          </div>

          <div class="info-item mb-2">
            <i class="bi bi-calendar-range"></i>
            <strong>Vigencia:</strong>
            <span class="ms-1">{{ $promotion->fecha_desde->format('d/m/Y') }} al {{ $promotion->fecha_hasta->format('d/m/Y') }}</span>
          </div>

          <div class="info-item mb-2">
            <i class="bi bi-clock-history"></i>
            <strong>Días válidos:</strong>
            <div class="promo-days ms-2">
              @foreach($dayLabels as $index => $label)
                <span class="{{ $activeDays[$index] ? 'active' : '' }}">{{ $label }}</span>
              @endforeach
            </div>
          </div>

          <div class="info-item mb-2">
            <i class="bi bi-people"></i>
            <strong>Disponible para:</strong>
            <span class="ms-1">{{ implode(', ', $accessibleCategories) }}</span>
          </div>

          @if($daysToExpire >= 0 && $daysToExpire <= 5)
            <div class="alert alert-warning mt-3" role="alert">
              <i class="bi bi-exclamation-triangle-fill"></i>
              Aprovechá, la promoción vence en {{ $daysToExpire === 0 ? 'hoy' : $daysToExpire . ' día' . ($daysToExpire === 1 ? '' : 's') }}.
            </div>
          @endif

          @auth
            @if(auth()->user()->isClient())
              @if($hasRequested && !session('success'))
                <div class="alert alert-info mt-3" role="alert">
                  <i class="bi bi-info-circle-fill"></i>
                  Ya enviaste una solicitud para esta promoción. Esperá la respuesta del local.
                </div>
              @elseif($clientEligibility && !$clientEligibility['eligible'])
                <div class="alert alert-warning mt-3" role="alert">
                  <i class="bi bi-exclamation-triangle-fill"></i>
                  {{ $clientRestrictionMessage }}
                </div>
              @endif

              <form method="POST" action="{{ route('client.promotion-usages.request') }}" class="mt-4">
                @csrf
                <input type="hidden" name="promotion_id" value="{{ $promotion->id }}">
                <button type="submit" class="btn btn-success btn-lg w-100" @disabled(!$canRequest)>
                  <i class="bi bi-ticket-perforated"></i>
                  Solicitar promoción
                </button>
              </form>
            @else
              <div class="alert alert-info mt-3" role="alert">
                <i class="bi bi-info-circle-fill"></i>
                Ingresaste con un perfil distinto a cliente. Para solicitar promociones accedé con una cuenta de cliente.
              </div>
            @endif
          @else
            <div class="alert alert-info mt-3" role="alert">
              <i class="bi bi-info-circle-fill"></i>
              Para canjearla iniciá sesión o registrate y solicitá el beneficio indicando el local {{ sprintf('%03d', $promotion->store->codigo) }}.
            </div>

            <div class="mt-3 d-grid gap-2">
              <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                <i class="bi bi-box-arrow-in-right"></i> Iniciar sesión
              </a>
              <a href="{{ $registerRoute }}" class="btn btn-outline-primary btn-lg">
                <i class="bi bi-person-plus"></i> Crear cuenta gratuita
              </a>
            </div>
          @endauth

          <div class="mt-4">
            <a href="{{ $storeRoute }}" class="btn btn-outline-primary btn-lg w-100">
              <i class="bi bi-shop"></i> Ver más del local
            </a>
          </div>
        </div>
      </div>
    </div>

    <div class="row mt-4">
      <div class="col-12">
        <div class="card shadow-sm">
          <div class="card-body">
            <h4 class="card-title mb-3"><i class="bi bi-list-check"></i> Condiciones de uso</h4>
            <ul class="list-unstyled mb-0">
              <li class="mb-2"><i class="bi bi-check-circle text-success"></i> Solo disponible para cuentas {{ implode(', ', $accessibleCategories) }}.</li>
              <li class="mb-2"><i class="bi bi-check-circle text-success"></i> Necesitás la aprobación del local antes de utilizarla.</li>
              <li class="mb-2"><i class="bi bi-check-circle text-success"></i> Máximo un uso por cliente durante la vigencia.</li>
              <li class="mb-2"><i class="bi bi-check-circle text-success"></i> Se aplica únicamente dentro de las fechas y días indicados.</li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    @if($similarPromotions->isNotEmpty())
      <div class="row mt-5">
        <div class="col-12 mb-3">
          <h3 class="section-title">Más promociones de {{ $promotion->store->nombre }}</h3>
        </div>

        @foreach($similarPromotions as $similar)
          @php
            $similarDaysToExpire = now()->diffInDays($similar->fecha_hasta, false);
          @endphp
          <div class="col-md-4 mb-4">
            <article class="card h-100 border-0 shadow-sm promo-card" data-category="{{ strtolower($similar->categoria_minima) }}">
              <div class="position-relative">
                @if($similarDaysToExpire >= 0 && $similarDaysToExpire <= 5)
                  <span class="badge bg-danger position-absolute top-0 start-0 m-2"><i class="bi bi-exclamation-triangle-fill"></i> Por vencer</span>
                @endif
                <a href="{{ route('promociones.show', $similar) }}" class="ratio ratio-4x3 bg-light d-block">
                  <img src="https://cdn.bootstrapstudio.io/placeholders/1400x800.png" class="object-fit-cover rounded-top" alt="Imagen referencial de {{ $similar->texto }}">
                </a>
              </div>
              <div class="card-body">
                <span class="badge text-uppercase mb-2 badge-{{ strtolower($similar->categoria_minima) }}">{{ $similar->categoria_minima }}</span>
                <h5 class="card-title">{{ Str::limit($similar->texto, 80) }}</h5>
                <p class="small mb-0"><i class="bi bi-calendar-event"></i> Vigente hasta {{ $similar->fecha_hasta->format('d/m/Y') }}</p>
              </div>
              <div class="card-footer bg-white border-0 text-end">
                <a href="{{ route('promociones.show', $similar) }}" class="btn btn-outline-primary btn-sm">Ver detalle</a>
              </div>
            </article>
          </div>
        @endforeach
      </div>
    @endif

    <div class="row mt-4">
      <div class="col-12">
        <a href="{{ $promotionsIndexRoute }}" class="btn btn-outline-primary">
          <i class="bi bi-arrow-left"></i> Volver a promociones
        </a>
      </div>
    </div>
  </div>
</section>
@endsection