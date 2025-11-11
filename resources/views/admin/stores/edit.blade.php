@extends('layouts.dashboard')

@section('title', 'Editar Local - Panel Administrador')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-pencil-square"></i> Editar Local: {{ $store->nombre }}
                    </h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.stores.update', $store) }}" enctype="multipart/form-data" novalidate>
                        @csrf
                        @method('PUT')

                        {{-- C\u00f3digo (read-only) --}}
                        <div class="mb-3">
                            <label for="codigo" class="form-label">C\u00f3digo del Local</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="codigo" 
                                value="{{ $store->codigo }}"
                                disabled>
                            <div class="form-text">El c\u00f3digo no se puede modificar</div>
                        </div>

                        {{-- Nombre del Local --}}
                        <div class="mb-3">
                            <label for="nombre" class="form-label">
                                Nombre del Local <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="text" 
                                class="form-control @error('nombre') is-invalid @enderror" 
                                id="nombre" 
                                name="nombre" 
                                value="{{ old('nombre', $store->nombre) }}"
                                maxlength="100"
                                required>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Rubro --}}
                        <div class="mb-3">
                            <label for="rubro" class="form-label">
                                Rubro <span class="text-danger">*</span>
                            </label>
                            <select 
                                class="form-select @error('rubro') is-invalid @enderror" 
                                id="rubro" 
                                name="rubro"
                                required>
                                <option value="">Seleccionar rubro...</option>
                                @foreach($rubros as $rubro)
                                    <option value="{{ $rubro }}" {{ old('rubro', $store->rubro) == $rubro ? 'selected' : '' }}>
                                        {{ ucfirst($rubro) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('rubro')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Ubicación --}}
                        <div class="mb-3">
                            <label for="ubicacion" class="form-label">
                                Ubicaci\u00f3n <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="text" 
                                class="form-control @error('ubicacion') is-invalid @enderror" 
                                id="ubicacion" 
                                name="ubicacion" 
                                value="{{ old('ubicacion', $store->ubicacion) }}"
                                maxlength="50"
                                required>
                            @error('ubicacion')
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
