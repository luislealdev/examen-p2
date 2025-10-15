@extends('layouts.app')

@section('title', 'Agregar Inventario en Lote')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('inventories.index') }}">Inventario</a></li>
                    <li class="breadcrumb-item active">Agregar en Lote</li>
                </ol>
            </nav>
            <h1 class="display-5 fw-bold text-success mb-2">
                <i class="fas fa-layer-group me-3"></i>Agregar Inventario en Lote
            </h1>
            <p class="lead text-muted">Agregar múltiples copias de una película a varias tiendas simultáneamente</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('inventories.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver al Inventario
            </a>
        </div>
    </div>

    <!-- Instructions -->
    <div class="alert alert-info border-0 shadow-custom mb-4">
        <div class="d-flex align-items-start">
            <i class="fas fa-info-circle fa-2x me-3 mt-1"></i>
            <div>
                <h6 class="alert-heading mb-2">Instrucciones de Uso</h6>
                <ul class="mb-0">
                    <li>Seleccione la película que desea agregar al inventario</li>
                    <li>Elija las tiendas donde desea crear copias</li>
                    <li>Especifique la cantidad de copias por tienda</li>
                    <li>El sistema creará automáticamente todos los artículos de inventario</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Bulk Creation Form -->
    <div class="card shadow-custom border-0">
        <div class="card-header bg-gradient-success text-white">
            <h5 class="mb-0">
                <i class="fas fa-plus-circle me-2"></i>Formulario de Creación en Lote
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('inventories.bulk-store') }}" method="POST" id="bulkCreateForm">
                @csrf
                
                <!-- Film Selection -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <label for="film_id" class="form-label fw-bold">
                            <i class="fas fa-film me-2 text-primary"></i>Seleccionar Película *
                        </label>
                        <select name="film_id" id="film_id" class="form-select @error('film_id') is-invalid @enderror" required>
                            <option value="">-- Seleccione una película --</option>
                            @foreach($films as $film)
                                <option value="{{ $film->film_id }}" 
                                        data-title="{{ $film->title }}"
                                        data-year="{{ $film->release_year }}"
                                        data-rating="{{ $film->rating }}"
                                        data-rate="{{ $film->rental_rate }}"
                                        data-poster="{{ $film->poster_url }}"
                                        {{ old('film_id') == $film->film_id ? 'selected' : '' }}>
                                    {{ $film->title }} ({{ $film->release_year }}) - {{ $film->rating }} - ${{ number_format($film->rental_rate, 2) }}
                                </option>
                            @endforeach
                        </select>
                        @error('film_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Film Preview -->
                <div id="filmPreview" class="card border-primary mb-4" style="display: none;">
                    <div class="card-header bg-light border-primary">
                        <h6 class="mb-0 text-primary">
                            <i class="fas fa-eye me-2"></i>Vista Previa de la Película
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div id="filmPosterContainer" class="text-center">
                                    <img id="filmPoster" src="" alt="" class="img-thumbnail" style="max-height: 200px; display: none;">
                                    <div id="filmPosterPlaceholder" class="bg-light p-4 rounded" style="height: 200px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-film fa-3x text-muted"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <h5 id="filmTitle" class="text-primary"></h5>
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <span id="filmRating" class="badge bg-primary"></span>
                                    <span id="filmYear" class="badge bg-secondary"></span>
                                    <span id="filmRate" class="badge bg-success"></span>
                                </div>
                                <p id="filmDescription" class="text-muted"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Store Selection -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <label class="form-label fw-bold">
                            <i class="fas fa-store me-2 text-info"></i>Seleccionar Tiendas *
                        </label>
                        <div class="card border-info">
                            <div class="card-header bg-light border-info">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-info">Tiendas Disponibles</span>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-info" onclick="selectAllStores()">
                                            <i class="fas fa-check-double me-1"></i>Todas
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" onclick="deselectAllStores()">
                                            <i class="fas fa-times me-1"></i>Ninguna
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($stores as $store)
                                        <div class="col-md-6 mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input store-checkbox" 
                                                       type="checkbox" 
                                                       name="stores[]" 
                                                       value="{{ $store->store_id }}" 
                                                       id="store_{{ $store->store_id }}"
                                                       {{ is_array(old('stores')) && in_array($store->store_id, old('stores')) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="store_{{ $store->store_id }}">
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-info rounded-circle text-white text-center me-2"
                                                             style="width: 30px; height: 30px; line-height: 30px; font-size: 12px;">
                                                            {{ $store->store_id }}
                                                        </div>
                                                        <div>
                                                            <strong>Tienda #{{ $store->store_id }}</strong>
                                                            @if($store->manager)
                                                                <br><small class="text-muted">{{ $store->manager->first_name }} {{ $store->manager->last_name }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('stores')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quantity Selection -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="quantity" class="form-label fw-bold">
                            <i class="fas fa-hashtag me-2 text-warning"></i>Cantidad por Tienda *
                        </label>
                        <div class="input-group">
                            <button type="button" class="btn btn-outline-secondary" onclick="decreaseQuantity()">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" 
                                   name="quantity" 
                                   id="quantity" 
                                   class="form-control text-center fw-bold @error('quantity') is-invalid @enderror" 
                                   min="1" 
                                   max="50" 
                                   value="{{ old('quantity', 1) }}" 
                                   required>
                            <button type="button" class="btn btn-outline-secondary" onclick="increaseQuantity()">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <small class="form-text text-muted">Número de copias a crear en cada tienda seleccionada (máximo 50)</small>
                        @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted">
                            <i class="fas fa-calculator me-2"></i>Resumen de Creación
                        </label>
                        <div class="card bg-light">
                            <div class="card-body py-3">
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="text-primary fw-bold" id="selectedStores">0</div>
                                        <small class="text-muted">Tiendas</small>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-warning fw-bold" id="selectedQuantity">1</div>
                                        <small class="text-muted">Por Tienda</small>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-success fw-bold" id="totalItems">0</div>
                                        <small class="text-muted">Total</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-between">
                    <a href="{{ route('inventories.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </a>
                    <button type="submit" class="btn btn-gradient-success" id="submitBtn" disabled>
                        <i class="fas fa-plus-circle me-2"></i>Crear Artículos en Lote
                        <span id="submitCount" class="badge bg-white text-success ms-2">0</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.btn-gradient-success {
    background: linear-gradient(45deg, #28a745, #1e7e34);
    border: none;
    color: white;
}

.btn-gradient-success:hover {
    background: linear-gradient(45deg, #1e7e34, #155724);
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
}

.shadow-custom {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.form-check-input:checked {
    background-color: #17a2b8;
    border-color: #17a2b8;
}

.card-header.bg-gradient-success {
    background: linear-gradient(45deg, #28a745, #1e7e34) !important;
}

.store-checkbox:checked + label {
    background-color: rgba(23, 162, 184, 0.1);
    border-radius: 5px;
    padding: 5px;
}

#submitBtn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filmSelect = document.getElementById('film_id');
    const filmPreview = document.getElementById('filmPreview');
    const storeCheckboxes = document.querySelectorAll('.store-checkbox');
    const quantityInput = document.getElementById('quantity');
    const submitBtn = document.getElementById('submitBtn');
    
    // Film selection handler
    filmSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (this.value) {
            // Show film preview
            document.getElementById('filmTitle').textContent = selectedOption.dataset.title;
            document.getElementById('filmYear').textContent = selectedOption.dataset.year;
            document.getElementById('filmRating').textContent = selectedOption.dataset.rating;
            document.getElementById('filmRate').textContent = '$' + parseFloat(selectedOption.dataset.rate).toFixed(2) + '/día';
            
            const poster = document.getElementById('filmPoster');
            const placeholder = document.getElementById('filmPosterPlaceholder');
            
            if (selectedOption.dataset.poster) {
                poster.src = selectedOption.dataset.poster;
                poster.alt = selectedOption.dataset.title;
                poster.style.display = 'block';
                placeholder.style.display = 'none';
            } else {
                poster.style.display = 'none';
                placeholder.style.display = 'flex';
            }
            
            filmPreview.style.display = 'block';
        } else {
            filmPreview.style.display = 'none';
        }
        
        updateSummary();
    });
    
    // Store selection handlers
    storeCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSummary);
    });
    
    // Quantity input handler
    quantityInput.addEventListener('input', updateSummary);
    
    // Update summary function
    function updateSummary() {
        const selectedStores = document.querySelectorAll('.store-checkbox:checked').length;
        const quantity = parseInt(quantityInput.value) || 0;
        const total = selectedStores * quantity;
        const hasFilm = filmSelect.value !== '';
        
        document.getElementById('selectedStores').textContent = selectedStores;
        document.getElementById('selectedQuantity').textContent = quantity;
        document.getElementById('totalItems').textContent = total;
        document.getElementById('submitCount').textContent = total;
        
        // Enable/disable submit button
        if (hasFilm && selectedStores > 0 && quantity > 0) {
            submitBtn.disabled = false;
            submitBtn.classList.remove('btn-outline-secondary');
            submitBtn.classList.add('btn-gradient-success');
        } else {
            submitBtn.disabled = true;
            submitBtn.classList.remove('btn-gradient-success');
            submitBtn.classList.add('btn-outline-secondary');
        }
    }
    
    // Initial summary update
    updateSummary();
});

function selectAllStores() {
    document.querySelectorAll('.store-checkbox').forEach(checkbox => {
        checkbox.checked = true;
    });
    updateSummary();
}

function deselectAllStores() {
    document.querySelectorAll('.store-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    updateSummary();
}

function increaseQuantity() {
    const input = document.getElementById('quantity');
    const currentValue = parseInt(input.value) || 0;
    if (currentValue < 50) {
        input.value = currentValue + 1;
        input.dispatchEvent(new Event('input'));
    }
}

function decreaseQuantity() {
    const input = document.getElementById('quantity');
    const currentValue = parseInt(input.value) || 0;
    if (currentValue > 1) {
        input.value = currentValue - 1;
        input.dispatchEvent(new Event('input'));
    }
}

function updateSummary() {
    const selectedStores = document.querySelectorAll('.store-checkbox:checked').length;
    const quantity = parseInt(document.getElementById('quantity').value) || 0;
    const total = selectedStores * quantity;
    const hasFilm = document.getElementById('film_id').value !== '';
    
    document.getElementById('selectedStores').textContent = selectedStores;
    document.getElementById('selectedQuantity').textContent = quantity;
    document.getElementById('totalItems').textContent = total;
    document.getElementById('submitCount').textContent = total;
    
    const submitBtn = document.getElementById('submitBtn');
    
    if (hasFilm && selectedStores > 0 && quantity > 0) {
        submitBtn.disabled = false;
        submitBtn.classList.remove('btn-outline-secondary');
        submitBtn.classList.add('btn-gradient-success');
    } else {
        submitBtn.disabled = true;
        submitBtn.classList.remove('btn-gradient-success');
        submitBtn.classList.add('btn-outline-secondary');
    }
}
</script>
@endpush