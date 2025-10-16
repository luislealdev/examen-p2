@extends('layouts.app')

@section('title', 'Agregar Múltiples Artículos al Inventario')

@section('content')
<div class="container">
    <!-- Encabezado -->
    <div class="row mb-4">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('inventories.index') }}">Inventario</a></li>
                    <li class="breadcrumb-item active">Agregar Múltiples Artículos</li>
                </ol>
            </nav>
            <h1 class="display-5 fw-bold text-gradient">
                <i class="fas fa-layer-group me-3"></i>Agregar Múltiples Artículos al Inventario
            </h1>
            <p class="lead text-muted">Agregar múltiples copias de películas al inventario de manera eficiente</p>
        </div>
    </div>

    <!-- Tarjeta del Formulario -->
    <div class="card shadow-custom border-0">
        <div class="card-header bg-gradient-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-boxes me-2"></i>Información para Múltiples Artículos
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('inventories.bulk-store') }}" method="POST">
                @csrf
                
                <!-- Selección de Película -->
                <div class="mb-4">
                    <label for="film_id" class="form-label fw-bold">Película <span class="text-danger">*</span></label>
                    <select class="form-select @error('film_id') is-invalid @enderror" 
                            id="film_id" name="film_id" required>
                        <option value="">Seleccionar Película</option>
                        @foreach($films as $film)
                            <option value="{{ $film->film_id }}" 
                                    {{ old('film_id') == $film->film_id ? 'selected' : '' }}
                                    data-rating="{{ $film->rating }}"
                                    data-language="{{ $film->language->name ?? 'N/A' }}"
                                    data-rental-rate="{{ $film->rental_rate }}"
                                    data-category="{{ $film->category->name ?? 'N/A' }}">
                                {{ $film->title }} 
                                @if($film->release_year)
                                    ({{ $film->release_year }})
                                @endif
                                - {{ $film->category->name ?? 'Sin categoría' }}
                            </option>
                        @endforeach
                    </select>
                    @error('film_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Selecciona la película para agregar al inventario</div>
                </div>

                <!-- Selección de Tiendas -->
                <div class="mb-4">
                    <label for="store_ids" class="form-label fw-bold">Tiendas <span class="text-danger">*</span></label>
                    <div class="row">
                        @foreach($stores as $store)
                            <div class="col-md-6 col-lg-4 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="store_ids[]" 
                                           value="{{ $store->store_id }}" 
                                           id="store_{{ $store->store_id }}"
                                           {{ in_array($store->store_id, old('store_ids', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="store_{{ $store->store_id }}">
                                        <strong>Tienda #{{ $store->store_id }}</strong>
                                        @if($store->manager)
                                            <br><small class="text-muted">Gerente: {{ $store->manager->full_name }}</small>
                                        @endif
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('store_ids')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Selecciona las tiendas donde agregar la película</div>
                </div>

                <!-- Cantidad por Tienda -->
                <div class="mb-4">
                    <label for="quantity" class="form-label fw-bold">Cantidad por Tienda <span class="text-danger">*</span></label>
                    <input type="number" 
                           class="form-control @error('quantity') is-invalid @enderror" 
                           id="quantity" 
                           name="quantity" 
                           value="{{ old('quantity', 1) }}" 
                           min="1" 
                           max="20"
                           required>
                    @error('quantity')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Número de copias a agregar en cada tienda seleccionada</div>
                </div>

                <!-- Vista Previa de Detalles de Película -->
                <div id="film-details" class="alert alert-info d-none">
                    <h6><i class="fas fa-film me-2"></i>Detalles de la Película</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <small><strong>Clasificación:</strong> <span id="film-rating">-</span></small><br>
                            <small><strong>Idioma:</strong> <span id="film-language">-</span></small>
                        </div>
                        <div class="col-md-6">
                            <small><strong>Categoría:</strong> <span id="film-category">-</span></small><br>
                            <small><strong>Tarifa de Renta:</strong> $<span id="film-rental-rate">-</span></small>
                        </div>
                    </div>
                </div>

                <!-- Resumen de la Operación -->
                <div id="operation-summary" class="alert alert-success d-none">
                    <h6><i class="fas fa-calculator me-2"></i>Resumen de la Operación</h6>
                    <p class="mb-1">
                        <strong>Total de artículos a crear:</strong> 
                        <span id="total-items" class="badge bg-primary">0</span>
                    </p>
                    <p class="mb-0">
                        <strong>Tiendas seleccionadas:</strong> 
                        <span id="selected-stores-count" class="badge bg-info">0</span>
                    </p>
                </div>

                <!-- Botones de Acción -->
                <div class="d-flex justify-content-between">
                    <a href="{{ route('inventories.index') }}" class="btn btn-gradient-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Cancelar
                    </a>
                    <button type="submit" class="btn btn-gradient-success">
                        <i class="fas fa-layer-group me-2"></i>Crear Artículos de Inventario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filmSelect = document.getElementById('film_id');
    const filmDetails = document.getElementById('film-details');
    const storeCheckboxes = document.querySelectorAll('input[name="store_ids[]"]');
    const quantityInput = document.getElementById('quantity');
    const operationSummary = document.getElementById('operation-summary');

    // Mostrar detalles de película
    filmSelect.addEventListener('change', function() {
        if (this.value) {
            const selectedOption = this.options[this.selectedIndex];
            document.getElementById('film-rating').textContent = selectedOption.dataset.rating || 'N/A';
            document.getElementById('film-language').textContent = selectedOption.dataset.language || 'N/A';
            document.getElementById('film-category').textContent = selectedOption.dataset.category || 'N/A';
            document.getElementById('film-rental-rate').textContent = selectedOption.dataset.rentalRate || 'N/A';
            filmDetails.classList.remove('d-none');
        } else {
            filmDetails.classList.add('d-none');
        }
        updateSummary();
    });

    // Actualizar resumen cuando cambian las tiendas o cantidad
    storeCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSummary);
    });
    quantityInput.addEventListener('input', updateSummary);

    function updateSummary() {
        const selectedStores = document.querySelectorAll('input[name="store_ids[]"]:checked');
        const quantity = parseInt(quantityInput.value) || 0;
        const totalItems = selectedStores.length * quantity;

        document.getElementById('selected-stores-count').textContent = selectedStores.length;
        document.getElementById('total-items').textContent = totalItems;

        if (selectedStores.length > 0 && quantity > 0) {
            operationSummary.classList.remove('d-none');
        } else {
            operationSummary.classList.add('d-none');
        }
    }
});
</script>
@endsection