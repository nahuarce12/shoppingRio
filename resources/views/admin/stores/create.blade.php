@extends('layouts.dashboard')

@section('title', 'Crear Local - Panel Administrador')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-shop"></i> Crear Nuevo Local
                    </h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.stores.store') }}" enctype="multipart/form-data" novalidate>
                        @csrf

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
                                value="{{ old('name') }}"
                                maxlength="100"
                                placeholder="Ej: Tienda de Electrodom\u00e9sticos XYZ"
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
                                    <option value="{{ $rubro }}" {{ old('category') == $rubro ? 'selected' : '' }}>
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
                                value="{{ old('location') }}"
                                maxlength="50"
                                placeholder="Ej: Primer Piso - Local 205"
                                required>
                            <div class="form-text">
                                Indic\u00e1 el piso y n\u00famero de local dentro del shopping
                            </div>
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
                                placeholder="Ej: Somos una tienda especializada en electrodomésticos de última generación...">{{ old('description') }}</textarea>
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
                            <input 
                                type="file" 
                                class="form-control @error('logo') is-invalid @enderror" 
                                id="logo" 
                                name="logo"
                                accept="image/*">
                            <div class="form-text">
                                Formatos aceptados: JPG, PNG, GIF. Tama\u00f1o m\u00e1ximo: 2MB
                            </div>
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="logo-preview" class="mt-2" style="display: none;">
                                <img src="" alt="Vista previa" class="img-thumbnail" style="max-width: 200px;">
                            </div>
                        </div>

                        {{-- Submit Buttons --}}
                        <div class="d-flex gap-2 justify-content-end mt-4">
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Crear Local
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
