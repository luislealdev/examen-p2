@extends('layouts.app')

@section('title', 'Agregar Artículo al Inventario')

@section('content')
<div class="container">
    <!-- Encabezado -->
    <div class="row mb-4">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('inventories.index') }}">Inventario</a></li>
                    <li class="breadcrumb-item active">Agregar Artículo</li>
                </ol>
            </nav>
            <h1 class="display-5 fw-bold text-gradient">
                <i class="fas fa-plus-circle me-3"></i>Agregar Artículo al Inventario
            </h1>
            <p class="lead text-muted">Agregar una nueva película al inventario de la tienda</p>
        </div>
    </div>

    <!-- Tarjeta del Formulario -->
    <div class="card shadow-custom border-0">
        <div class="card-header bg-gradient-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-boxes me-2"></i>Información del Artículo de Inventario
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('inventories.store') }}" method="POST">
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
                    <div class="form-text">
                        Busca y selecciona la película a agregar al inventario
                    </div>
                </div>

                <!-- Selección de Tienda -->
                <div class="mb-4">
                    <label for="store_id" class="form-label fw-bold">Tienda <span class="text-danger">*</span></label>
                    <div class="position-relative">
                        <input type="text" 
                               class="form-control @error('store_id') is-invalid @enderror" 
                               id="store_search" 
                               placeholder="Buscar tienda..."
                               autocomplete="off">
                        <input type="hidden" id="store_id" name="store_id" value="{{ old('store_id') }}">
                        
                        <!-- Dropdown de resultados -->
                        <div class="dropdown-menu w-100" id="store_dropdown" style="display: none; max-height: 300px; overflow-y: auto;">
                        </div>
                        
                        <!-- Tienda seleccionada -->
                        <div id="selected_store" class="mt-2" style="display: none;">
                            <div class="border rounded p-2 bg-light">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong id="selected_store_name"></strong>
                                        <div class="small text-muted">
                                            <span id="selected_store_address"></span>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearStoreSelection()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @error('store_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        Busca y selecciona la tienda donde se almacenará este artículo
                    </div>
                </div>

                <!-- Acciones del Formulario -->
                <div class="d-flex justify-content-between">
                    <div>
                        <a href="{{ route('inventories.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver al Inventario
                        </a>
                        <a href="{{ route('inventories.bulk-create') }}" class="btn btn-outline-success ms-2">
                            <i class="fas fa-layer-group me-2"></i>Agregar en Masa
                        </a>
                    </div>
                    <div>
                        <button type="reset" class="btn btn-outline-danger me-2">
                            <i class="fas fa-undo me-2"></i>Restablecer Formulario
                        </button>
                        <button type="submit" class="btn btn-gradient-primary">
                            <i class="fas fa-save me-2"></i>Agregar al Inventario
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Estadísticas Rápidas -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card border-0 bg-light">
                <div class="card-body">
                    <h6 class="fw-bold">
                        <i class="fas fa-film me-2"></i>Búsqueda de Películas
                    </h6>
                    <p class="mb-0">Utiliza el buscador para encontrar películas rápidamente</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 bg-light">
                <div class="card-body">
                    <h6 class="fw-bold">
                        <i class="fas fa-store me-2"></i>Búsqueda de Tiendas
                    </h6>
                    <p class="mb-0">Busca tiendas por ID o dirección para asignar inventario</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let filmTimeout;
    let storeTimeout;
    
    // Autocompletado para películas
    const filmSearch = document.getElementById('film_search');
    const filmDropdown = document.getElementById('film_dropdown');
    const filmIdInput = document.getElementById('film_id');
    
    filmSearch.addEventListener('input', function() {
        clearTimeout(filmTimeout);
        const query = this.value.trim();
        
        if (query.length >= 2) {
            filmTimeout = setTimeout(() => searchFilms(query), 300);
        } else {
            filmDropdown.style.display = 'none';
        }
    });
    
    // Autocompletado para tiendas
    const storeSearch = document.getElementById('store_search');
    const storeDropdown = document.getElementById('store_dropdown');
    const storeIdInput = document.getElementById('store_id');
    
    storeSearch.addEventListener('input', function() {
        clearTimeout(storeTimeout);
        const query = this.value.trim();
        
        if (query.length >= 1) {
            storeTimeout = setTimeout(() => searchStores(query), 300);
        } else {
            storeDropdown.style.display = 'none';
        }
    });
    
    // Funciones de búsqueda
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
    
    function searchStores(query) {
        fetch(`{{ route('stores.search') }}?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                displayStoreResults(data);
            })
            .catch(error => {
                console.error('Error searching stores:', error);
            });
    }
    
    // Mostrar resultados de películas
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
    
    // Mostrar resultados de tiendas
    function displayStoreResults(stores) {
        storeDropdown.innerHTML = '';
        
        if (stores.length === 0) {
            storeDropdown.innerHTML = '<div class="dropdown-item-text">No se encontraron tiendas</div>';
        } else {
            stores.forEach(store => {
                const item = document.createElement('a');
                item.className = 'dropdown-item';
                item.href = '#';
                item.innerHTML = `
                    <div>
                        <strong>Tienda #${store.store_id}</strong>
                    </div>
                    <div class="small text-muted">
                        ${store.address}${store.district ? ` - ${store.district}` : ''}
                    </div>
                `;
                
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    selectStore(store);
                });
                
                storeDropdown.appendChild(item);
            });
        }
        
        storeDropdown.style.display = 'block';
    }
    
    // Seleccionar película
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
    };
    
    // Seleccionar tienda
    window.selectStore = function(store) {
        storeIdInput.value = store.store_id;
        storeSearch.value = '';
        storeDropdown.style.display = 'none';
        
        document.getElementById('selected_store_name').textContent = `Tienda #${store.store_id}`;
        document.getElementById('selected_store_address').textContent = `${store.address}${store.district ? ` - ${store.district}` : ''}`;
        
        document.getElementById('selected_store').style.display = 'block';
    };
    
    // Limpiar selecciones
    window.clearFilmSelection = function() {
        filmIdInput.value = '';
        filmSearch.value = '';
        document.getElementById('selected_film').style.display = 'none';
    };
    
    window.clearStoreSelection = function() {
        storeIdInput.value = '';
        storeSearch.value = '';
        document.getElementById('selected_store').style.display = 'none';
    };
    
    // Cerrar dropdowns al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!filmSearch.contains(e.target) && !filmDropdown.contains(e.target)) {
            filmDropdown.style.display = 'none';
        }
        if (!storeSearch.contains(e.target) && !storeDropdown.contains(e.target)) {
            storeDropdown.style.display = 'none';
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

<style>
.text-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>
@endsection