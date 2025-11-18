@extends('layouts.dashboard')

@section('title', 'Editar Local - Panel Administrador')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-pencil-square"></i> Editar Local: {{ $store->name }}
                    </h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.stores.update', $store) }}" enctype="multipart/form-data" novalidate>
                        @csrf
                        @method('PUT')

                        {{-- C\u00f3digo (read-only) --}}
                        <div class="mb-3">
                            <label for="code" class="form-label">C\u00f3digo del Local</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="code" 
                                value="{{ $store->code }}"
                                disabled>
                            <div class="form-text">El c\u00f3digo no se puede modificar</div>
                        </div>

                        {{-- Nombre del Local --}}
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                Nombre del Local <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="text" 
                                class="form-control @error('name') is-invalid @enderror" 
                                id="name" 
                                name="name" 
                                value="{{ old('name', $store->name) }}"
                                maxlength="100"
                                required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Rubro --}}
                        <div class="mb-3">
                            <label for="category" class="form-label">
                                Rubro <span class="text-danger">*</span>
                            </label>
                            <select 
                                class="form-select @error('category') is-invalid @enderror" 
                                id="category" 
                                name="category"
                                required>
                                <option value="">Seleccionar rubro...</option>
                                @foreach($rubros as $rubro)
                                    <option value="{{ $rubro }}" {{ old('category', $store->category) == $rubro ? 'selected' : '' }}>
                                        {{ ucfirst($rubro) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Ubicación --}}
                        <div class="mb-3">
                            <label for="location" class="form-label">
                                Ubicaci\u00f3n <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="text" 
                                class="form-control @error('location') is-invalid @enderror" 
                                id="location" 
                                name="location" 
                                value="{{ old('location', $store->location) }}"
                                maxlength="50"
                                required>
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Descripción --}}
                        <div class="mb-3">
                            <label for="description" class="form-label">
                                Descripci\u00f3n del Local <span class="text-muted">(Opcional)</span>
                            </label>
                            <textarea 
                                class="form-control @error('description') is-invalid @enderror" 
                                id="description" 
                                name="description" 
                                rows="4"
                                maxlength="500"
                                placeholder="Ej: Somos una tienda especializada en electrodomésticos de última generación...">{{ old('description', $store->description) }}</textarea>
                            <div class="form-text">
                                Esta descripci\u00f3n aparecer\u00e1 en la secci\u00f3n "Sobre el local" en la p\u00e1gina de detalle
                            </div>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Logo --}}
                        <div class="mb-3">
                            <label for="logo" class="form-label">
                                Logo del Local <span class="text-muted">(Opcional)</span>
                            </label>
                            
                            @if($store->logo)
                                <div class="mb-2">
                                    <label class="form-label">Logo actual:</label>
                                    <div>
                                        <img src="{{ asset('storage/' . $store->logo) }}" alt="Logo actual" class="img-thumbnail" style="max-width: 200px;">
                                    </div>
                                </div>
                            @endif

                            <input 
                                type="file" 
                                class="form-control @error('logo') is-invalid @enderror" 
                                id="logo" 
                                name="logo"
                                accept="image/*">
                            <div class="form-text">
                                Dej\u00e1 vac\u00edo para mantener el logo actual. Formatos: JPG, PNG, GIF. M\u00e1ximo: 2MB
                            </div>
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="logo-preview" class="mt-2" style="display: none;">
                                <label class="form-label">Vista previa del nuevo logo:</label>
                                <div>
                                    <img src="" alt="Vista previa" class="img-thumbnail" style="max-width: 200px;">
                                </div>
                            </div>
                        </div>

                        {{-- Submit Buttons --}}
                        <div class="d-flex gap-2 justify-content-end mt-4">
                            <a href="{{ route('admin.stores.show', $store) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Guardar Cambios
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
    // Image preview
    const logoInput = document.getElementById('logo');
    const logoPreview = document.getElementById('logo-preview');
    
    if (logoInput && logoPreview) {
        logoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    logoPreview.querySelector('img').src = e.target.result;
                    logoPreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                logoPreview.style.display = 'none';
            }
        });
    }
});
</script>
@endpush
