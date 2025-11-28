@props(['promotion'])

<div class="card promotion-card h-100">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="card-title mb-0">{{ $promotion->title }}</h5>
    @if($promotion->category_restriction)
    <x-category-badge :category="$promotion->category_restriction" />
    @endif
  </div>

  @if($promotion->imagen)
  <img src="{{ $promotion->imagen_url }}" class="card-img-top" alt="{{ $promotion->title }}" style="height: 200px; object-fit: cover;">
  @endif

  <div class="card-body d-flex flex-column">
    <p class="card-text">{{ Str::limit($promotion->description, 100) }}</p>

    <div class="mt-auto">
      <div class="row mb-2">
        <div class="col-6">
          <small class="text-muted">
            <i class="fas fa-calendar"></i>
            Desde: {{ $promotion->start_date->format('d/m/Y') }}
          </small>
        </div>
        <div class="col-6">
          <small class="text-muted">
            <i class="fas fa-calendar"></i>
            Hasta: {{ $promotion->end_date->format('d/m/Y') }}
          </small>
        </div>
      </div>

      @if($promotion->valid_days)
      <div class="mb-2">
        <small class="text-muted">
          <i class="fas fa-clock"></i>
          Días válidos: {{ implode(', ', $promotion->valid_days) }}
        </small>
      </div>
      @endif

      <div class="d-flex justify-content-between align-items-center">
        <small class="text-muted">
          <i class="fas fa-store"></i>
          {{ $promotion->store->name }}
        </small>

        <div class="btn-group">
          <a href="{{ route('promotions.show', $promotion) }}" class="btn btn-primary btn-sm">
            Ver Detalles
          </a>

          @auth
          @if(auth()->user()->isClient() && $promotion->isAvailableForUser(auth()->user()))
          <form method="POST" action="{{ route('promotions.request', $promotion) }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-success btn-sm">
              Solicitar
            </button>
          </form>
          @endif
          @endauth
        </div>
      </div>
    </div>
  </div>

  @if($promotion->hasActiveRequest(auth()->user() ?? null))
  <div class="card-footer bg-warning text-dark">
    <small><i class="fas fa-clock"></i> Solicitud pendiente</small>
  </div>
  @endif

  @if($promotion->hasUsedPromotion(auth()->user() ?? null))
  <div class="card-footer bg-success text-white">
    <small><i class="fas fa-check"></i> Promoción utilizada</small>
  </div>
  @endif
</div>