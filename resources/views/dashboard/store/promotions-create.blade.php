@extends('layouts.dashboard')

@section('title', 'Crear Promoción - Panel de Dueño')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-tag-fill"></i> Crear Nueva Promoción
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <strong>Importante:</strong> Una vez creada, la promoción no podrá ser editada. Asegurate de revisar toda la información antes de enviar. La promoción quedará pendiente de aprobación por un administrador.
                    </div>

                    <form method="POST" action="{{ route('store.promotions.store') }}" id="promotion-form" novalidate>
                        @csrf

                        {{-- Store Selection (hidden if user has only one store) --}}
                        <input type="hidden" name="store_id" value="{{ auth()->user()->stores->first()->id ?? '' }}">

                        {{-- Promotion Description --}}
                        <div class="mb-3">
                            <label for="texto" class="form-label">
                                Descripción de la Promoción <span class="text-danger">*</span>
                            </label>
                            <textarea 
                                class="form-control @error('texto') is-invalid @enderror" 
                                id="texto" 
                                name="texto" 
                                rows="3" 
                                maxlength="200"
                                placeholder="Ej: 2x1 en productos seleccionados, 50% OFF en segunda unidad..."
                                required>{{ old('texto') }}</textarea>
                            <div class="form-text">
                                <span id="char-count">0</span>/200 caracteres
                            </div>
                            @error('texto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Date Range --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="fecha_desde" class="form-label">
                                    Fecha de Inicio <span class="text-danger">*</span>
                                </label>
                                <input 
                                    type="date" 
                                    class="form-control @error('fecha_desde') is-invalid @enderror" 
                                    id="fecha_desde" 
                                    name="fecha_desde" 
                                    value="{{ old('fecha_desde', date('Y-m-d')) }}"
                                    min="{{ date('Y-m-d') }}"
                                    required>
                                @error('fecha_desde')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="fecha_hasta" class="form-label">
                                    Fecha de Finalización <span class="text-danger">*</span>
                                </label>
                                <input 
                                    type="date" 
                                    class="form-control @error('fecha_hasta') is-invalid @enderror" 
                                    id="fecha_hasta" 
                                    name="fecha_hasta" 
                                    value="{{ old('fecha_hasta') }}"
                                    min="{{ date('Y-m-d') }}"
                                    required>
                                @error('fecha_hasta')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Days of Week --}}
                        <div class="mb-3">
                            <label class="form-label">
                                Días de la Semana Válidos <span class="text-danger">*</span>
                            </label>
                            <div class="form-text mb-2">
                                Seleccioná los días en que la promoción estará disponible
                            </div>
                            <div class="row g-2">
                                @php
                                    $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
                                    $oldDias = old('dias_semana', [1, 1, 1, 1, 1, 1, 1]);
                                @endphp
                                @foreach($dias as $index => $dia)
                                <div class="col-6 col-md-3">
                                    <div class="form-check">
                                        <input 
                                            class="form-check-input day-checkbox @error('dias_semana') is-invalid @enderror" 
                                            type="checkbox" 
                                            name="dias_semana[]" 
                                            value="1"
                                            id="dia_{{ $index }}"
                                            {{ ($oldDias[$index] ?? 0) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="dia_{{ $index }}">
                                            {{ $dia }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @error('dias_semana')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div id="days-error" class="invalid-feedback" style="display: none;">
                                Debes seleccionar al menos un día de la semana
                            </div>
                        </div>

                        {{-- Client Category --}}
                        <div class="mb-3">
                            <label for="categoria_minima" class="form-label">
                                Categoría Mínima de Cliente <span class="text-danger">*</span>
                            </label>
                            <select 
                                class="form-select @error('categoria_minima') is-invalid @enderror" 
                                id="categoria_minima" 
                                name="categoria_minima"
                                required>
                                <option value="">Seleccionar categoría...</option>
                                <option value="Inicial" {{ old('categoria_minima') == 'Inicial' ? 'selected' : '' }}>
                                    Inicial (Todos los clientes)
                                </option>
                                <option value="Medium" {{ old('categoria_minima') == 'Medium' ? 'selected' : '' }}>
                                    Medium (Clientes Medium y Premium)
                                </option>
                                <option value="Premium" {{ old('categoria_minima') == 'Premium' ? 'selected' : '' }}>
                                    Premium (Solo clientes Premium)
                                </option>
                            </select>
                            <div class="form-text">
                                Los clientes podrán acceder a promociones de su categoría o inferiores
                            </div>
                            @error('categoria_minima')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Submit Buttons --}}
                        <div class="d-flex gap-2 justify-content-end mt-4">
                            <a href="{{ route('store.dashboard') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Crear Promoción
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Character counter
    const textoField = document.getElementById('texto');
    const charCount = document.getElementById('char-count');
    
    if (textoField && charCount) {
        textoField.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });
        charCount.textContent = textoField.value.length;
    }

    // Date validation
    const fechaDesde = document.getElementById('fecha_desde');
    const fechaHasta = document.getElementById('fecha_hasta');
    
    if (fechaDesde && fechaHasta) {
        fechaDesde.addEventListener('change', function() {
            fechaHasta.min = this.value;
            if (fechaHasta.value && fechaHasta.value < this.value) {
                fechaHasta.value = this.value;
            }
        });
    }

    // Days of week validation
    const form = document.getElementById('promotion-form');
    const dayCheckboxes = document.querySelectorAll('.day-checkbox');
    const daysError = document.getElementById('days-error');
    
    function validateDays() {
        const anyChecked = Array.from(dayCheckboxes).some(cb => cb.checked);
        if (!anyChecked) {
            daysError.style.display = 'block';
            dayCheckboxes.forEach(cb => cb.classList.add('is-invalid'));
            return false;
        } else {
            daysError.style.display = 'none';
            dayCheckboxes.forEach(cb => cb.classList.remove('is-invalid'));
            return true;
        }
    }

    dayCheckboxes.forEach(cb => {
        cb.addEventListener('change', validateDays);
    });

    // Form validation on submit
    if (form) {
        form.addEventListener('submit', function(e) {
            // Clear previous custom validity
            form.querySelectorAll('input, select, textarea').forEach(field => {
                field.setCustomValidity('');
            });

            // Validate days
            if (!validateDays()) {
                e.preventDefault();
                return false;
            }

            // Bootstrap validation
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            form.classList.add('was-validated');
        });
    }

    // Convert unchecked checkboxes to 0 before submit
    if (form) {
        form.addEventListener('submit', function(e) {
            // Remove existing hidden inputs
            form.querySelectorAll('input[name="dias_semana[]"][type="hidden"]').forEach(el => el.remove());
            
            // Add hidden input for each day (0 or 1)
            dayCheckboxes.forEach((cb, index) => {
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'dias_semana[]';
                hidden.value = cb.checked ? '1' : '0';
                form.appendChild(hidden);
                
                // Disable the checkbox so it doesn't submit
                cb.disabled = true;
            });
        });
    }
});
</script>
@endpush
