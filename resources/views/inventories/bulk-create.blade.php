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
                    <div class="position-relative">
                        <input type="text" 
                               class="form-control @error('film_id') is-invalid @enderror" 
                               id="film_search" 
                               placeholder="Buscar película..."
                               autocomplete="off">
                        <input type="hidden" id="film_id" name="film_id" value="{{ old('film_id') }}">
                        
                        <!-- Dropdown de resultados -->
                        <div class="dropdown-menu w-100" id="film_dropdown" style="display: none; max-height: 300px; overflow-y: auto;">
                        </div>
                        
                        <!-- Película seleccionada -->
                        <div id="selected_film" class="mt-2" style="display: none;">
                            <div class="border rounded p-2 bg-light">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong id="selected_film_title"></strong>
                                        <div class="small text-muted">
                                            <span id="selected_film_details"></span>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearFilmSelection()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @error('film_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Busca y selecciona la película para agregar al inventario</div>
                </div>

                <!-- Selección de Tiendas -->
                <div class="mb-4">
                    <label for="store_ids" class="form-label fw-bold">Tiendas <span class="text-danger">*</span></label>
                    
                    <!-- Filtro de búsqueda para tiendas -->
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" 
                                   class="form-control" 
                                   id="store_filter" 
                                   placeholder="Buscar tienda por ID o dirección...">
                        </div>
                        <div class="form-text">Filtrar tiendas para facilitar la selección</div>
                    </div>
                    
                    <!-- Botones de selección rápida -->
                    <div class="mb-3">
                        <button type="button" class="btn btn-sm btn-outline-primary me-2" onclick="selectAllStores()">
                            <i class="fas fa-check-double me-1"></i>Seleccionar Todas
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearAllStores()">
                            <i class="fas fa-times me-1"></i>Limpiar Selección
                        </button>
                    </div>
                    
                    <div class="row" id="stores-container">
                        @foreach($stores as $store)
                            <div class="col-md-6 col-lg-4 mb-2 store-item" 
                                 data-store-id="{{ $store->store_id }}"
                                 data-store-address="{{ $store->address->address ?? '' }}"
                                 data-store-district="{{ $store->address->district ?? '' }}">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="store_ids[]" 
                                           value="{{ $store->store_id }}" 
                                           id="store_{{ $store->store_id }}"
                                           {{ in_array($store->store_id, old('store_ids', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="store_{{ $store->store_id }}">
                                        <strong>Tienda #{{ $store->store_id }}</strong>
                                        @if($store->address)
                                            <br><small class="text-muted">{{ $store->address->address }}</small>
                                            @if($store->address->district)
                                                <br><small class="text-muted">{{ $store->address->district }}</small>
                                            @endif
                                        @endif
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
    let filmTimeout;
    
    // Elementos del DOM
    const filmSearch = document.getElementById('film_search');
    const filmDropdown = document.getElementById('film_dropdown');
    const filmIdInput = document.getElementById('film_id');
    const filmDetails = document.getElementById('film-details');
    const storeCheckboxes = document.querySelectorAll('input[name="store_ids[]"]');
    const quantityInput = document.getElementById('quantity');
    const operationSummary = document.getElementById('operation-summary');
    const storeFilter = document.getElementById('store_filter');
    const storeItems = document.querySelectorAll('.store-item');

    // === AUTOCOMPLETADO DE PELÍCULAS ===
    filmSearch.addEventListener('input', function() {
        clearTimeout(filmTimeout);
        const query = this.value.trim();
        
        if (query.length >= 2) {
            filmTimeout = setTimeout(() => searchFilms(query), 300);
        } else {
            filmDropdown.style.display = 'none';
        }
    });

    function searchFilms(query) {
        fetch(`{{ route('films.search') }}?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                displayFilmResults(data);
            })
            .catch(error => {
                console.error('Error searching films:', error);
            });
    }

    function displayFilmResults(films) {
        filmDropdown.innerHTML = '';
        
        if (films.length === 0) {
            filmDropdown.innerHTML = '<div class="dropdown-item-text">No se encontraron películas</div>';
        } else {
            films.forEach(film => {
                const item = document.createElement('a');
                item.className = 'dropdown-item';
                item.href = '#';
                item.innerHTML = `
                    <div>
                        <strong>${film.title}</strong>
                        ${film.release_year ? `<span class="text-muted">(${film.release_year})</span>` : ''}
                        <span class="badge bg-${getRatingColor(film.rating)} ms-2">${film.rating}</span>
                    </div>
                    <div class="small text-muted">
                        ${film.category} • $${parseFloat(film.rental_rate).toFixed(2)}
                    </div>
                `;
                
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    selectFilm(film);
                });
                
                filmDropdown.appendChild(item);
            });
        }
        
        filmDropdown.style.display = 'block';
    }

    window.selectFilm = function(film) {
        filmIdInput.value = film.film_id;
        filmSearch.value = '';
        filmDropdown.style.display = 'none';
        
        document.getElementById('selected_film_title').textContent = film.title;
        document.getElementById('selected_film_details').innerHTML = `
            ${film.release_year ? `${film.release_year} • ` : ''}
            <span class="badge bg-${getRatingColor(film.rating)}">${film.rating}</span> • 
            ${film.category} • $${parseFloat(film.rental_rate).toFixed(2)}
        `;
        
        document.getElementById('selected_film').style.display = 'block';
        
        // Actualizar detalles antiguos si existen
        if (filmDetails) {
            document.getElementById('film-rating').textContent = film.rating;
            document.getElementById('film-language').textContent = film.language || 'N/A';
            document.getElementById('film-category').textContent = film.category;
            document.getElementById('film-rental-rate').textContent = film.rental_rate;
            filmDetails.classList.remove('d-none');
        }
        
        updateSummary();
    };

    window.clearFilmSelection = function() {
        filmIdInput.value = '';
        filmSearch.value = '';
        document.getElementById('selected_film').style.display = 'none';
        if (filmDetails) {
            filmDetails.classList.add('d-none');
        }
        updateSummary();
    };

    // === FILTRADO DE TIENDAS ===
    storeFilter.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        
        storeItems.forEach(item => {
            const storeId = item.dataset.storeId.toLowerCase();
            const storeAddress = item.dataset.storeAddress.toLowerCase();
            const storeDistrict = item.dataset.storeDistrict.toLowerCase();
            
            const matches = storeId.includes(query) || 
                          storeAddress.includes(query) || 
                          storeDistrict.includes(query);
            
            item.style.display = matches ? 'block' : 'none';
        });
    });

    // === FUNCIONES DE SELECCIÓN DE TIENDAS ===
    window.selectAllStores = function() {
        storeItems.forEach(item => {
            if (item.style.display !== 'none') {
                const checkbox = item.querySelector('input[type="checkbox"]');
                checkbox.checked = true;
            }
        });
        updateSummary();
    };

    window.clearAllStores = function() {
        storeCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        updateSummary();
    };

    // === ACTUALIZACIÓN DE RESUMEN ===
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

    // === UTILIDADES ===
    // Cerrar dropdowns al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!filmSearch.contains(e.target) && !filmDropdown.contains(e.target)) {
            filmDropdown.style.display = 'none';
        }
    });

    function getRatingColor(rating) {
        const colors = {
            'G': 'success',
            'PG': 'info',
            'PG-13': 'warning',
            'R': 'danger',
            'NC-17': 'dark'
        };
        return colors[rating] || 'secondary';
    }
});
</script>
@endsection