@extends('layouts.app')

@section('title', 'Agregar Nuevo Idioma')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-custom border-0">
                <div class="card-header bg-gradient-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-plus-circle me-2"></i>
                        Agregar Nuevo Idioma
                    </h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('languages.store') }}">
                        @csrf

                        <!-- Campo Nombre -->
                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">
                                Nombre del Idioma <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   maxlength="20"
                                   placeholder="Ingrese el nombre del idioma (ej: Inglés, Español, Francés...)"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Máximo 20 caracteres. El nombre del idioma debe ser único.
                            </div>
                        </div>

                        <!-- Ejemplos de Idiomas Comunes -->
                        <div class="mb-4">
                            <label class="form-label text-muted fw-bold">Ejemplos de Idiomas Comunes:</label>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setLanguage('Inglés')">Inglés</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setLanguage('Español')">Español</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setLanguage('Francés')">Francés</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setLanguage('Alemán')">Alemán</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setLanguage('Italiano')">Italiano</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setLanguage('Japonés')">Japonés</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setLanguage('Mandarín')">Mandarín</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setLanguage('Portugués')">Portugués</button>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('languages.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>
                                Volver a Idiomas
                            </a>
                            <div>
                                <button type="reset" class="btn btn-outline-warning me-2">
                                    <i class="fas fa-undo me-1"></i>
                                    Restablecer
                                </button>
                                <button type="submit" class="btn btn-gradient-primary">
                                    <i class="fas fa-save me-1"></i>
                                    Guardar Idioma
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tarjeta de Ayuda -->
            <div class="card shadow-custom border-0 mt-4">
                <div class="card-header bg-gradient-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-question-circle me-2"></i>
                        Ayuda y Guías
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-check-circle text-success me-1"></i> Hacer:</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Usar nombres propios de idiomas</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Verificar la precisión ortográfica</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Usar nombres estándar de idiomas</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Mantener nombres concisos</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-times-circle text-danger me-1"></i> No Hacer:</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Use abbreviations (unless standard)</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Include dialect specifications</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Use special characters</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Exceed 20 characters</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function setLanguage(languageName) {
    document.getElementById('name').value = languageName;
    document.getElementById('name').focus();
}

// Auto-capitalize first letter
document.getElementById('name').addEventListener('input', function(e) {
    let value = e.target.value;
    if (value.length > 0) {
        e.target.value = value.charAt(0).toUpperCase() + value.slice(1);
    }
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const name = document.getElementById('name').value.trim();
    
    if (!name) {
        e.preventDefault();
        alert('Please enter a language name.');
        document.getElementById('name').focus();
        return false;
    }
    
    if (name.length > 20) {
        e.preventDefault();
        alert('Language name cannot exceed 20 characters.');
        document.getElementById('name').focus();
        return false;
    }
});
</script>
@endsection