@extends('layouts.app')

@php
use Illuminate\Support\Str;

$dayLabels = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
$promotions = $store->promotions ?? collect();
$owner = $store->owner;
$localesIndexRoute = route('locales.index');
@endphp

@section('title', $store->name . ' - Shopping Rosario')
@section('meta_description', 'Conocé las promociones y datos de ' . $store->name . ' en Shopping Rosario.')

@section('content')
<x-layout.breadcrumbs :items="[
        ['label' => 'Locales', 'url' => $localesIndexRoute],
        ['label' => $store->name]
    ]" />

<section class="py-4">
  <div class="container">
    <div class="row">
      <div class="col-lg-6 mb-4">
        <div class="ratio ratio-4x3 bg-light rounded">
          @if($store->logo)
          <img src="{{ $store->logo_url }}" class="object-fit-cover rounded" alt="Logo de {{ $store->name }}">
          @else
          <img src="https://cdn.bootstrapstudio.io/placeholders/1400x800.png" class="object-fit-cover rounded" alt="Imagen de {{ $store->name }}">
          @endif
        </div>
      </div>
      <div class="col-lg-6 mb-4">
        <div class="detail-info p-4 bg-white rounded shadow-sm h-100">
          <div class="d-flex justify-content-between align-items-start mb-3">
            <h2 class="mb-0">{{ $store->name }}</h2>
          </div>
          <div class="mb-3 d-flex gap-2 flex-wrap">
            <span class="badge bg-primary text-uppercase">{{ $store->category }}</span>
            <span class="badge bg-success"><i class="bi bi-star-fill"></i> {{ $promotionCount }} {{ Str::plural('promoción', $promotionCount) }} activa{{ $promotionCount === 1 ? '' : 's' }}</span>
          </div>
          <p class="lead mb-4">Local especializado en {{ Str::lower($store->category) }} dentro del Shopping Rosario.</p>

          <div class="info-item mb-2"><i class="bi bi-geo-alt-fill"></i> <strong>Ubicación:</strong> <span class="ms-1">{{ $store->location }}</span></div>

          @if($store->owners->count() > 0)
            <div class="info-item mb-2">
              <i class="bi bi-person-circle"></i> <strong>Contacto:</strong>
              <span class="ms-1">
                @foreach($store->owners as $owner)
                  {{ $owner->name }}@if(!$loop->last), @endif
                @endforeach
              </span>
            </div>
          @endif

          <div class="alert alert-light border mt-4" role="status">
            <i class="bi bi-info-circle"></i>
            Encontrá este local en el shopping y solicitá sus beneficios vigentes.
          </div>

          <a href="#promociones" class="btn btn-primary btn-lg w-100 mt-4">
            <i class="bi bi-tag-fill"></i> Ver promociones disponibles
          </a>
        </div>
      </div>
    </div>

    <div class="row mt-4">
      <div class="col-12">
        <div class="card shadow-sm">
          <div class="card-body">
            <h4 class="card-title mb-3"><i class="bi bi-info-circle"></i> Sobre el local</h4>
            @if($store->description)
              <p>{{ $store->description }}</p>
            @else
              <p>{{ $store->name }} ofrece propuestas de {{ Str::lower($store->category) }} diseñadas para los visitantes del Shopping Rosario.</p>
              <p class="mb-0">Consultá sus promociones activas y planificá tu próxima compra con beneficios exclusivos.</p>
            @endif
          </div>
        </div>
      </div>
    </div>

    <div class="row mt-5" id="promociones">
      <div class="col-12 mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h3 class="section-title mb-0"><i class="bi bi-tag-fill"></i> Promociones vigentes</h3>
        <span class="text-muted">Actualizado al {{ now()->format('d/m/Y') }}</span>
      </div>

      @forelse($promotions as $promotion)
        @php
          $days = collect($promotion->weekdays ?? [])->map(fn ($value) => (bool) $value)->pad(7, false)->take(7)->values();
          $daysToExpire = now()->diffInDays($promotion->end_date, false);
        @endphp
        <div class="col-md-6 col-lg-4 mb-4">
          <article class="card promo-card h-100 border-0 shadow-sm" data-category="{{ strtolower($promotion->minimum_category) }}">
            <div class="position-relative">
              @if($daysToExpire >= 0 && $daysToExpire <= 5)
                <span class="badge bg-danger position-absolute top-0 start-0 m-2"><i class="bi bi-exclamation-triangle-fill"></i> Por vencer</span>
              @endif
              <a href="{{ route('promociones.show', $promotion) }}" class="ratio ratio-4x3 bg-light d-block">
                <img src="https://cdn.bootstrapstudio.io/placeholders/1400x800.png" class="object-fit-cover rounded-top" alt="Imagen referencial de {{ $promotion->description }}">
              </a>
            </div>
            <div class="card-body">
              <span class="badge text-uppercase mb-2 badge-{{ strtolower($promotion->minimum_category) }}">{{ $promotion->minimum_category }}</span>
              <h5 class="card-title">{{ Str::limit($promotion->description, 80) }}</h5>
              <p class="small mb-2"><i class="bi bi-calendar-event"></i> Vigente hasta {{ $promotion->end_date->format('d/m/Y') }}</p>
              <div class="promo-days">
                @foreach($dayLabels as $index => $label)
                  <span class="{{ $days[$index] ? 'active' : '' }}">{{ $label }}</span>
                @endforeach
              </div>
            </div>
            <div class="card-footer bg-white border-0 text-end">
              <a href="{{ route('promociones.show', $promotion) }}" class="btn btn-outline-primary btn-sm">Ver detalle</a>
            </div>
          </article>
        </div>
      @empty
        <div class="col-12">
          <div class="alert alert-info" role="status">
            <i class="bi bi-info-circle"></i> Este local no tiene promociones activas en este momento.
          </div>
        </div>
      @endforelse
    </div>

    <div class="row mt-4">
      <div class="col-12">
        <a href="{{ $localesIndexRoute }}" class="btn btn-outline-primary">
          <i class="bi bi-arrow-left"></i> Volver a Locales
        </a>
      </div>
    </div>
  </div>
</section>
@endsection