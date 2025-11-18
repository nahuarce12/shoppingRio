{{-- Promotion Usage Request Modal Component --}}
{{-- Usage: Include this in promotion detail pages and trigger with data-bs-target="#requestModal-{promotionId}" --}}

@props(['promotion', 'eligibility' => null])

<div class="modal fade" id="requestModal-{{ $promotion->id }}" tabindex="-1" aria-labelledby="requestModalLabel-{{ $promotion->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="requestModalLabel-{{ $promotion->id }}">
                    <i class="bi bi-tag-fill"></i> Solicitar Promoción
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('client.promotion-usages.request') }}">
                @csrf
                <input type="hidden" name="promotion_id" value="{{ $promotion->id }}">
                
                <div class="modal-body">
                    @if($eligibility && !$eligibility['eligible'])
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            <strong>No disponible:</strong> {{ $eligibility['reason'] }}
                        </div>
                    @else
                        <div class="promotion-details mb-3">
                            <h6 class="fw-bold">{{ $promotion->description }}</h6>
                            <p class="mb-2">
                                <i class="bi bi-shop"></i> 
                                <strong>Local:</strong> {{ $promotion->store->name }}
                            </p>
                            <p class="mb-2">
                                <i class="bi bi-calendar-event"></i> 
                                <strong>Válido:</strong> {{ $promotion->start_date->format('d/m/Y') }} - {{ $promotion->end_date->format('d/m/Y') }}
                            </p>
                            <p class="mb-0">
                                <span class="badge badge-{{ strtolower($promotion->minimum_category) }} badge-category">
                                    {{ $promotion->minimum_category }}
                                </span>
                            </p>
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <strong>Importante:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Tu solicitud será enviada al local</li>
                                <li>El dueño del local debe aprobar tu solicitud</li>
                                <li>Recibirás un email cuando sea aprobada o rechazada</li>
                                <li>Solo podés usar cada promoción una vez</li>
                            </ul>
                        </div>

                        <p class="mb-0">¿Confirmas que querés solicitar esta promoción?</p>
                    @endif
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </button>
                    @if(!$eligibility || $eligibility['eligible'])
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Confirmar Solicitud
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
