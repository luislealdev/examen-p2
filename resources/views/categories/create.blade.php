@extends('layouts.app')

@section('title', 'Agregar Nueva Categoría')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-custom border-0">
                <div class="card-header bg-gradient-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-plus-circle me-2"></i>
                        Agregar Nueva Categoría
                    </h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('categories.store') }}">
                        @csrf

                        <!-- Campo Nombre -->
                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">
                                Nombre de la Categoría <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   maxlength="25"
                                   placeholder="Ingrese el nombre de la categoría (ej: Acción, Comedia, Drama...)"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Máximo 25 caracteres. El nombre de la categoría debe ser único.
                            </div>
                        </div>

                        <!-- Ejemplos de Categorías Comunes -->
                        <div class="mb-4">
                            <label class="form-label text-muted fw-bold">Categorías Populares de Películas:</label>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="setCategory('Acción')">Acción</button>
                                <button type="button" class="btn btn-outline-success btn-sm" onclick="setCategory('Aventura')">Aventura</button>
                                <button type="button" class="btn btn-outline-warning btn-sm" onclick="setCategory('Comedia')">Comedia</button>
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="setCategory('Drama')">Drama</button>
                                <button type="button" class="btn btn-outline-info btn-sm" onclick="setCategory('Terror')">Terror</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setCategory('Suspenso')">Suspenso</button>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-outline-dark btn-sm" onclick="setCategory('Romance')">Romance</button>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="setCategory('Ciencia Ficción')">Ciencia Ficción</button>
                                <button type="button" class="btn btn-outline-success btn-sm" onclick="setCategory('Fantasía')">Fantasía</button>
                                <button type="button" class="btn btn-outline-warning btn-sm" onclick="setCategory('Animación')">Animación</button>
                                <button type="button" class="btn btn-outline-info btn-sm" onclick="setCategory('Documental')">Documental</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setCategory('Familia')">Familia</button>
                            </div>
                        </div>

                        <!-- Vista Previa de Categoría -->
                        <div class="card bg-light mb-4" id="categoryPreview" style="display: none;">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fas fa-eye me-2"></i>
                                    Vista Previa de Categoría
                                </h6>
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div class="category-icon bg-primary text-white d-flex align-items-center justify-content-center" 
                                             style="width: 50px; height: 50px; border-radius: 8px; font-weight: bold;">
                                            <i class="fas fa-tag"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <strong id="previewName">Nombre de Categoría</strong>
                                        <br>
                                        <small class="text-muted" id="previewSlug">categoria-slug</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>
                                Volver a Categorías
                            </a>
                            <div>
                                <button type="reset" class="btn btn-outline-warning me-2" onclick="resetForm()">
                                    <i class="fas fa-undo me-1"></i>
                                    Restablecer
                                </button>
                                <button type="submit" class="btn btn-gradient-primary">
                                    <i class="fas fa-save me-1"></i>
                                    Guardar Categoría
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
                        Guías para Categorías
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-check-circle text-success me-1"></i> Buenos Ejemplos:</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Acción</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Comedia</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Ciencia Ficción</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Documental</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Infantil y Familia</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-times-circle text-danger me-1"></i> Evitar:</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Nombres vagos (ej: "Buenas Películas")</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Muy específicos (ej: "Acción de los 90s")</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Caracteres especiales</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Nombres muy largos</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Categorías duplicadas</li>
                            </ul>
                        </div>
                    </div>
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-lightbulb me-2"></i>
                        <strong>Consejo:</strong> Piensa en cómo los clientes navegarán las películas. Usa nombres de géneros estándar ampliamente reconocidos.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function setCategory(categoryName) {
    document.getElementById('name').value = categoryName;
    document.getElementById('name').focus();
    updatePreview();
}

function resetForm() {
    document.getElementById('name').value = '';
    document.getElementById('categoryPreview').style.display = 'none';
}

function updatePreview() {
    const nameInput = document.getElementById('name');
    const preview = document.getElementById('categoryPreview');
    const previewName = document.getElementById('previewName');
    const previewSlug = document.getElementById('previewSlug');
    
    if (nameInput.value.trim()) {
        preview.style.display = 'block';
        previewName.textContent = nameInput.value.trim();
        previewSlug.textContent = nameInput.value.trim().toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
    } else {
        preview.style.display = 'none';
    }
}

// Auto-capitalize first letter and update preview
document.getElementById('name').addEventListener('input', function(e) {
    let value = e.target.value;
    if (value.length > 0) {
        e.target.value = value.charAt(0).toUpperCase() + value.slice(1);
    }
    updatePreview();
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const name = document.getElementById('name').value.trim();
    
    if (!name) {
        e.preventDefault();
        alert('Please enter a category name.');
        document.getElementById('name').focus();
        return false;
    }
    
    if (name.length > 25) {
        e.preventDefault();
        alert('Category name cannot exceed 25 characters.');
        document.getElementById('name').focus();
        return false;
    }
    
    if (name.length < 2) {
        e.preventDefault();
        alert('Category name must be at least 2 characters long.');
        document.getElementById('name').focus();
        return false;
    }
});
</script>
@endsection