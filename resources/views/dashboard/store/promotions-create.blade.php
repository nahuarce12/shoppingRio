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

                    <form method="POST" action="{{ route('store.promotions.store') }}" id="promotion-form" enctype="multipart/form-data" novalidate>
                        @csrf

                        {{-- Store Selection (hidden if user has only one store) --}}
                        <input type="hidden" name="store_id" value="{{ $store->id }}">

                        {{-- Promotion Title --}}
                        <div class="mb-3">
                            <label for="title" class="form-label">
                                Título de la Promoción <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="text" 
                                class="form-control @error('title') is-invalid @enderror" 
                                id="title" 
                                name="title" 
                                value="{{ old('title') }}"
                                maxlength="100"
                                placeholder="Ej: 2x1 en todos los productos, 50% OFF en segunda unidad..."
                                required>
                            <div class="form-text">
                                Este es el título principal que verán los clientes
                            </div>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Promotion Description --}}
                        <div class="mb-3">
                            <label for="description" class="form-label">
                                Descripción de la Promoción <span class="text-danger">*</span>
                            </label>
                            <textarea 
                                class="form-control @error('description') is-invalid @enderror" 
                                id="description" 
                                name="description" 
                                rows="3" 
                                maxlength="200"
                                placeholder="Ej: Válido para todos los productos de la tienda. No acumulable con otras promociones..."
                                required>{{ old('description') }}</textarea>
                            <div class="form-text">
                                <span id="char-count">0</span>/200 caracteres - Agrega detalles adicionales sobre la promoción
                            </div>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Date Range --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">
                                    Fecha de Inicio <span class="text-danger">*</span>
                                </label>
                                <input 
                                    type="date" 
                                    class="form-control @error('start_date') is-invalid @enderror" 
                                    id="start_date" 
                                    name="start_date" 
                                    value="{{ old('start_date', date('Y-m-d')) }}"
                                    min="{{ date('Y-m-d') }}"
                                    required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="end_date" class="form-label">
                                    Fecha de Finalización <span class="text-danger">*</span>
                                </label>
                                <input 
                                    type="date" 
                                    class="form-control @error('end_date') is-invalid @enderror" 
                                    id="end_date" 
                                    name="end_date" 
                                    value="{{ old('end_date') }}"
                                    min="{{ date('Y-m-d') }}"
                                    required>
                                @error('end_date')
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
                                    $oldDias = old('weekdays', [1, 1, 1, 1, 1, 1, 1]);
                                @endphp
                                @foreach($dias as $index => $dia)
                                <div class="col-6 col-md-3">
                                    <div class="form-check">
                                        <input type="hidden" name="dias_semana[{{ $index }}]" value="0">
                                        <input 
                                            class="form-check-input day-checkbox @error('weekdays') is-invalid @enderror" 
                                            type="checkbox" 
                                            name="dias_semana[{{ $index }}]" 
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
                            @error('weekdays')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div id="days-error" class="invalid-feedback" style="display: none;">
                                Debes seleccionar al menos un día de la semana
                            </div>
                        </div>

                        {{-- Client Category --}}
                        <div class="mb-3">
                            <label for="minimum_category" class="form-label">
                                Categoría Mínima de Cliente <span class="text-danger">*</span>
                            </label>
                            <select 
                                class="form-select @error('minimum_category') is-invalid @enderror" 
                                id="minimum_category" 
                                name="minimum_category"
                                required>
                                <option value="">Seleccionar categoría...</option>
                                <option value="Inicial" {{ old('minimum_category') == 'Inicial' ? 'selected' : '' }}>
                                    Inicial (Todos los clientes)
                                </option>
                                <option value="Medium" {{ old('minimum_category') == 'Medium' ? 'selected' : '' }}>
                                    Medium (Clientes Medium y Premium)
                                </option>
                                <option value="Premium" {{ old('minimum_category') == 'Premium' ? 'selected' : '' }}>
                                    Premium (Solo clientes Premium)
                                </option>
                            </select>
                            <div class="form-text">
                                Los clientes podrán acceder a promociones de su categoría o inferiores
                            </div>
                            @error('minimum_category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Promotion Image --}}
                        <div class="mb-3">
                            <label for="imagen" class="form-label">
                                Imagen de la Promoción <span class="text-muted">(Opcional)</span>
                            </label>
                            <input 
                                type="file" 
                                class="form-control @error('imagen') is-invalid @enderror" 
                                id="imagen" 
                                name="imagen"
                                accept="image/*">
                            <div class="form-text">
                                Formatos aceptados: JPG, PNG, GIF. Tamaño máximo: 2MB
                            </div>
                            @error('imagen')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="imagen-preview" class="mt-2" style="display: none;">
                                <img src="" alt="Vista previa" class="img-thumbnail" style="max-width: 300px;">
                            </div>
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
    const textoField = document.getElementById('description');
    const charCount = document.getElementById('char-count');
    
    if (textoField && charCount) {
        textoField.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });
        charCount.textContent = textoField.value.length;
    }

    // Date validation
    const fechaDesde = document.getElementById('start_date');
    const fechaHasta = document.getElementById('end_date');
    
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

    // Image preview
    const imagenInput = document.getElementById('imagen');
    if (imagenInput) {
        imagenInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('imagen-preview');
            const previewImg = preview.querySelector('img');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
                previewImg.src = '';
            }
        });
    }

});
</script>
@endpush
