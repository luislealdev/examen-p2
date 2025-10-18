@extends('layouts.app')

@section('title', 'Agregar Nueva Película')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('films.index') }}">Películas</a></li>
                    <li class="breadcrumb-item active">Agregar Nueva Película</li>
                </ol>
            </nav>
            <h1 class="display-5 fw-bold text-primary">
                <i class="fas fa-plus-circle me-3"></i>Agregar Nueva Película
            </h1>
            <p class="lead text-muted">Crear una nueva entrada de película en la colección</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card shadow-custom border-0">
        <div class="card-header bg-gradient-primary text-white border-0">
            <h5 class="mb-0">
                <i class="fas fa-film me-2"></i>Información de la Película
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('films.store') }}" method="POST">
                @csrf
                
                <!-- Basic Information -->
                <div class="row g-3 mb-4">
                    <div class="col-md-8">
                        <label for="title" class="form-label fw-bold">Título <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               id="title" name="title" value="{{ old('title') }}" required maxlength="128">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="release_year" class="form-label fw-bold">Año de Lanzamiento</label>
                        <input type="number" class="form-control @error('release_year') is-invalid @enderror" 
                               id="release_year" name="release_year" value="{{ old('release_year') }}"
                               min="1888" max="{{ now()->year + 5 }}">
                        @error('release_year')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <label for="description" class="form-label fw-bold">Descripción</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="4" 
                              placeholder="Ingresa la descripción de la película...">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Poster URL -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <label for="poster_url" class="form-label fw-bold">URL del Poster</label>
                        <input type="url" class="form-control @error('poster_url') is-invalid @enderror" 
                               id="poster_url" name="poster_url" value="{{ old('poster_url') }}"
                               placeholder="https://ejemplo.com/poster.jpg">
                        @error('poster_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">URL de la imagen del poster de la película</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Vista Previa</label>
                        <div class="text-center">
                            <img id="poster-preview" src="/placeholder-movie.svg" 
                                 alt="Imagen no disponible" 
                                 class="img-fluid rounded"
                                 style="max-height: 120px;">
                        </div>
                    </div>
                </div>

                <!-- Language Information -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="language_id" class="form-label fw-bold">Idioma <span class="text-danger">*</span></label>
                        <select class="form-select @error('language_id') is-invalid @enderror" 
                                id="language_id" name="language_id" required>
                            <option value="">Seleccionar Idioma</option>
                            @foreach($languages as $language)
                                <option value="{{ $language->language_id }}" 
                                        {{ old('language_id') == $language->language_id ? 'selected' : '' }}>
                                    {{ $language->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('language_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="original_language_id" class="form-label fw-bold">Idioma Original</label>
                        <select class="form-select @error('original_language_id') is-invalid @enderror" 
                                id="original_language_id" name="original_language_id">
                            <option value="">Mismo que el Idioma</option>
                            @foreach($languages as $language)
                                <option value="{{ $language->language_id }}" 
                                        {{ old('original_language_id') == $language->language_id ? 'selected' : '' }}>
                                    {{ $language->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('original_language_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Rental Information -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label for="rental_duration" class="form-label fw-bold">Duración del Alquiler (días) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('rental_duration') is-invalid @enderror" 
                               id="rental_duration" name="rental_duration" value="{{ old('rental_duration', 3) }}"
                               min="1" max="30" required>
                        @error('rental_duration')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-3">
                        <label for="rental_rate" class="form-label fw-bold">Precio de Alquiler ($) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('rental_rate') is-invalid @enderror" 
                               id="rental_rate" name="rental_rate" value="{{ old('rental_rate', '4.99') }}"
                               min="0" max="99.99" step="0.01" required>
                        @error('rental_rate')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-3">
                        <label for="length" class="form-label fw-bold">Duración (minutos)</label>
                        <input type="number" class="form-control @error('length') is-invalid @enderror" 
                               id="length" name="length" value="{{ old('length') }}"
                               min="1" max="1000">
                        @error('length')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-3">
                        <label for="replacement_cost" class="form-label fw-bold">Costo de Reposición ($) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('replacement_cost') is-invalid @enderror" 
                               id="replacement_cost" name="replacement_cost" value="{{ old('replacement_cost', '19.99') }}"
                               min="0" max="999.99" step="0.01" required>
                        @error('replacement_cost')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Rating and Features -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="rating" class="form-label fw-bold">Clasificación <span class="text-danger">*</span></label>
                        <select class="form-select @error('rating') is-invalid @enderror" 
                                id="rating" name="rating" required>
                            <option value="">Seleccionar Clasificación</option>
                            @foreach($ratings as $rating)
                                <option value="{{ $rating }}" 
                                        {{ old('rating') == $rating ? 'selected' : '' }}>
                                    {{ $rating }}
                                </option>
                            @endforeach
                        </select>
                        @error('rating')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <small>
                                <strong>G:</strong> Audiencias Generales | 
                                <strong>PG:</strong> Orientación Parental | 
                                <strong>PG-13:</strong> Padres Fuertemente Aconsejados | 
                                <strong>R:</strong> Restringida | 
                                <strong>NC-17:</strong> Solo Adultos
                            </small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Características Especiales</label>
                        <div class="row">
                            @foreach($specialFeatures as $feature)
                                <div class="col-12">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" 
                                               id="feature_{{ $loop->index }}" 
                                               name="special_features[]" 
                                               value="{{ $feature }}"
                                               {{ in_array($feature, old('special_features', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="feature_{{ $loop->index }}">
                                            {{ $feature }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('special_features')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Category -->
                <div class="mb-4">
                    <label for="category_id" class="form-label fw-bold">Categoría</label>
                    <select class="form-select @error('category_id') is-invalid @enderror" 
                            id="category_id" name="category_id">
                        <option value="">Seleccionar una categoría...</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->category_id }}" 
                                    {{ old('category_id') == $category->category_id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Form Actions -->
                <div class="d-flex justify-content-between">
                    <a href="{{ route('films.index') }}" class="btn btn-gradient-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Volver a Películas
                    </a>
                    <div>
                        <button type="reset" class="btn btn-outline-danger me-2">
                            <i class="fas fa-undo me-2"></i>Reiniciar Formulario
                        </button>
                        <button type="submit" class="btn btn-gradient-primary">
                            <i class="fas fa-save me-2"></i>Crear Película
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const posterUrlInput = document.getElementById('poster_url');
    const posterPreview = document.getElementById('poster-preview');
    
    if (posterUrlInput && posterPreview) {
        posterUrlInput.addEventListener('input', function() {
            const url = this.value.trim();
            if (url) {
                posterPreview.src = url;
                posterPreview.alt = 'Vista previa del poster';
            } else {
                posterPreview.src = '/placeholder-movie.svg';
                posterPreview.alt = 'Imagen no disponible';
            }
        });
    }
});
</script>
@endsection