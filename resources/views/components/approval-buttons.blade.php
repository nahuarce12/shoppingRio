{{-- Approval Buttons Component for Admin/Store Owner Actions --}}
{{-- Usage: Include in lists where approve/reject actions are needed --}}

@props([
    'itemId',
    'approveRoute',
    'rejectRoute',
    'approveText' => 'Aprobar',
    'rejectText' => 'Rechazar',
    'itemType' => 'item',
    'showRejectReason' => false
])

<div class="btn-group" role="group">
    <form method="POST" action="{{ $approveRoute }}" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-sm btn-success" 
                onclick="return confirm('¿Confirmas que querés aprobar este {{ $itemType }}?')">
            <i class="bi bi-check-circle"></i> {{ $approveText }}
        </button>
    </form>
    
    @if($showRejectReason)
        <button type="button" class="btn btn-sm btn-danger" 
                data-bs-toggle="modal" 
                data-bs-target="#rejectModal-{{ $itemId }}">
            <i class="bi bi-x-circle"></i> {{ $rejectText }}
        </button>

        {{-- Reject Modal with Reason --}}
        <div class="modal fade" id="rejectModal-{{ $itemId }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="bi bi-x-circle"></i> {{ $rejectText }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ $rejectRoute }}">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="reason-{{ $itemId }}" class="form-label">
                                    Motivo del rechazo (opcional)
                                </label>
                                <textarea 
                                    class="form-control" 
                                    id="reason-{{ $itemId }}" 
                                    name="reason" 
                                    rows="3"
                                    placeholder="Ingresá el motivo del rechazo..."></textarea>
                            </div>
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle"></i>
                                Esta acción no se puede deshacer.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                Cancelar
                            </button>
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-x-circle"></i> Confirmar Rechazo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @else
        <form method="POST" action="{{ $rejectRoute }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-sm btn-danger" 
                    onclick="return confirm('¿Confirmas que querés rechazar este {{ $itemType }}?')">
                <i class="bi bi-x-circle"></i> {{ $rejectText }}
            </button>
        </form>
    @endif
</div>
