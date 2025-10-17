@extends('layouts.app')

@section('title', 'Crear Cliente')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Encabezado -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="display-6 fw-bold text-gradient">
                        <i class="fas fa-user-plus me-3"></i>Crear Nuevo Cliente
                    </h1>
                    <p class="lead text-muted">Complete el formulario para registrar un nuevo cliente</p>
                </div>
                <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Volver a Clientes
                </a>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <form action="{{ route('customers.store') }}" method="POST" id="customerForm">
                        @csrf
                        
                        <!-- Información Personal -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-0 bg-light">
                                    <div class="card-header bg-transparent border-bottom-0">
                                        <h5 class="mb-0 text-primary">
                                            <i class="fas fa-user me-2"></i>Información Personal
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="first_name" class="form-label fw-bold">Nombre <span class="text-danger">*</span></label>
                                                    <input type="text" 
                                                           class="form-control @error('first_name') is-invalid @enderror" 
                                                           id="first_name" 
                                                           name="first_name" 
                                                           value="{{ old('first_name') }}" 
                                                           maxlength="45"
                                                           placeholder="Ingrese el nombre"
                                                           required>
                                                    @error('first_name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="last_name" class="form-label fw-bold">Apellido <span class="text-danger">*</span></label>
                                                    <input type="text" 
                                                           class="form-control @error('last_name') is-invalid @enderror" 
                                                           id="last_name" 
                                                           name="last_name" 
                                                           value="{{ old('last_name') }}" 
                                                           maxlength="45"
                                                           placeholder="Ingrese el apellido"
                                                           required>
                                                    @error('last_name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="email" class="form-label fw-bold">Correo Electrónico</label>
                                            <input type="email" 
                                                   class="form-control @error('email') is-invalid @enderror" 
                                                   id="email" 
                                                   name="email" 
                                                   value="{{ old('email') }}" 
                                                   maxlength="50"
                                                   placeholder="correo@ejemplo.com">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Opcional - Correo electrónico del cliente</div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       id="active" 
                                                       name="active" 
                                                       value="1" 
                                                       {{ old('active', true) ? 'checked' : '' }}>
                                                <label class="form-check-label fw-bold" for="active">
                                                    Cliente Activo
                                                </label>
                                            </div>
                                            <div class="form-text">El cliente puede realizar rentas inmediatamente</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tienda Principal -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-0 bg-light">
                                    <div class="card-header bg-transparent border-bottom-0">
                                        <h5 class="mb-0 text-info">
                                            <i class="fas fa-store me-2"></i>Tienda Principal
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="store_search" class="form-label fw-bold">Buscar Tienda <span class="text-danger">*</span></label>
                                            <div class="position-relative">
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="store_search" 
                                                       placeholder="Escriba el nombre de la ciudad, tienda ID o país..."
                                                       autocomplete="off">
                                                <div id="store_results" class="position-absolute w-100 bg-white border rounded shadow-sm d-none" style="z-index: 1000; max-height: 200px; overflow-y: auto;"></div>
                                            </div>
                                            <input type="hidden" name="store_id" id="store_id" required>
                                            @error('store_id')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Tienda seleccionada -->
                                        <div id="selected_store" class="d-none">
                                            <div class="card border border-info">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <h6 class="mb-1 text-info">Tienda Seleccionada:</h6>
                                                            <p class="mb-0" id="selected_store_info"></p>
                                                        </div>
                                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="clearStoreSelection()">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dirección -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-0 bg-light">
                                    <div class="card-header bg-transparent border-bottom-0">
                                        <h5 class="mb-0 text-success">
                                            <i class="fas fa-map-marker-alt me-2"></i>Dirección del Cliente
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="mb-3">
                                                    <label for="address_line1" class="form-label fw-bold">Dirección Principal <span class="text-danger">*</span></label>
                                                    <input type="text" 
                                                           class="form-control @error('address_line1') is-invalid @enderror" 
                                                           id="address_line1" 
                                                           name="address_line1" 
                                                           value="{{ old('address_line1') }}" 
                                                           maxlength="50"
                                                           placeholder="Calle, número, colonia..."
                                                           required>
                                                    @error('address_line1')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="address_line2" class="form-label fw-bold">Dirección Secundaria</label>
                                                    <input type="text" 
                                                           class="form-control @error('address_line2') is-invalid @enderror" 
                                                           id="address_line2" 
                                                           name="address_line2" 
                                                           value="{{ old('address_line2') }}" 
                                                           maxlength="50"
                                                           placeholder="Apartamento, suite, etc.">
                                                    @error('address_line2')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="district" class="form-label fw-bold">Distrito/Provincia <span class="text-danger">*</span></label>
                                                    <input type="text" 
                                                           class="form-control @error('district') is-invalid @enderror" 
                                                           id="district" 
                                                           name="district" 
                                                           value="{{ old('district') }}" 
                                                           maxlength="20"
                                                           placeholder="Distrito o provincia"
                                                           required>
                                                    @error('district')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="postal_code" class="form-label fw-bold">Código Postal <span class="text-danger">*</span></label>
                                                    <input type="text" 
                                                           class="form-control @error('postal_code') is-invalid @enderror" 
                                                           id="postal_code" 
                                                           name="postal_code" 
                                                           value="{{ old('postal_code') }}" 
                                                           maxlength="10"
                                                           placeholder="12345"
                                                           required>
                                                    @error('postal_code')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="phone" class="form-label fw-bold">Teléfono</label>
                                                    <input type="text" 
                                                           class="form-control @error('phone') is-invalid @enderror" 
                                                           id="phone" 
                                                           name="phone" 
                                                           value="{{ old('phone') }}" 
                                                           maxlength="20"
                                                           placeholder="+1234567890">
                                                    @error('phone')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="country_id" class="form-label fw-bold">País <span class="text-danger">*</span></label>
                                                    <select class="form-select @error('country_id') is-invalid @enderror" 
                                                            id="country_id" 
                                                            name="country_id" 
                                                            required>
                                                        <option value="">Seleccionar país...</option>
                                                        @foreach($countries as $country)
                                                            <option value="{{ $country->country_id }}" {{ old('country_id') == $country->country_id ? 'selected' : '' }}>
                                                                {{ $country->country }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('country_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="city_id" class="form-label fw-bold">Ciudad <span class="text-danger">*</span></label>
                                                    <select class="form-select @error('city_id') is-invalid @enderror" 
                                                            id="city_id" 
                                                            name="city_id" 
                                                            required
                                                            disabled>
                                                        <option value="">Primero selecciona un país...</option>
                                                    </select>
                                                    @error('city_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Crear Cliente
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script loaded');
    
    // Elementos del DOM
    const storeSearch = document.getElementById('store_search');
    const storeResults = document.getElementById('store_results');
    const storeIdInput = document.getElementById('store_id');
    const selectedStoreDiv = document.getElementById('selected_store');
    const selectedStoreInfo = document.getElementById('selected_store_info');
    
    const countrySelect = document.getElementById('country_id');
    const citySelect = document.getElementById('city_id');

    console.log('Elements found:', {
        storeSearch: !!storeSearch,
        countrySelect: !!countrySelect,
        citySelect: !!citySelect
    });

    let searchTimeout;

    // Búsqueda de tiendas con autocompletado
    if (storeSearch) {
        storeSearch.addEventListener('input', function() {
            const query = this.value.trim();
            console.log('Store search query:', query);
            
            clearTimeout(searchTimeout);
            
            if (query.length < 2) {
                storeResults.classList.add('d-none');
                return;
            }

            searchTimeout = setTimeout(() => {
                searchStores(query);
            }, 300);
        });

        // Cerrar resultados al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (!storeSearch.contains(e.target) && !storeResults.contains(e.target)) {
                storeResults.classList.add('d-none');
            }
        });
    }

    // Cargar ciudades cuando cambia el país
    if (countrySelect && citySelect) {
        countrySelect.addEventListener('change', function() {
            const countryId = this.value;
            console.log('País seleccionado:', countryId);
            
            if (!countryId) {
                citySelect.innerHTML = '<option value="">Primero selecciona un país...</option>';
                citySelect.disabled = true;
                return;
            }

            // Mostrar estado de carga
            citySelect.innerHTML = '<option value="">Cargando ciudades...</option>';
            citySelect.disabled = true;

            // Hacer la petición AJAX
            const xhr = new XMLHttpRequest();
            xhr.open('GET', `/cities/by-country?country_id=${countryId}`, true);
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    console.log('XHR status:', xhr.status);
                    console.log('XHR response:', xhr.responseText);
                    
                    if (xhr.status === 200) {
                        try {
                            const cities = JSON.parse(xhr.responseText);
                            console.log('Cities parsed:', cities);
                            
                            // Limpiar select
                            citySelect.innerHTML = '<option value="">Seleccionar ciudad...</option>';
                            
                            if (cities.length === 0) {
                                citySelect.innerHTML = '<option value="">No hay ciudades disponibles</option>';
                            } else {
                                cities.forEach(city => {
                                    const option = document.createElement('option');
                                    option.value = city.city_id;
                                    option.textContent = city.city;
                                    citySelect.appendChild(option);
                                });
                            }
                            
                            citySelect.disabled = false;
                        } catch (e) {
                            console.error('Error parsing JSON:', e);
                            citySelect.innerHTML = '<option value="">Error al procesar respuesta</option>';
                            citySelect.disabled = false;
                        }
                    } else {
                        console.error('Error en petición:', xhr.status, xhr.statusText);
                        citySelect.innerHTML = '<option value="">Error al cargar ciudades</option>';
                        citySelect.disabled = false;
                    }
                }
            };
            
            xhr.onerror = function() {
                console.error('Error de red');
                citySelect.innerHTML = '<option value="">Error de conexión</option>';
                citySelect.disabled = false;
            };
            
            xhr.send();
        });
    }

    // Validación del formulario
    const customerForm = document.getElementById('customerForm');
    if (customerForm) {
        customerForm.addEventListener('submit', function(e) {
            if (!storeIdInput.value) {
                e.preventDefault();
                alert('Por favor selecciona una tienda principal.');
                if (storeSearch) storeSearch.focus();
            }
        });
    }
});

function searchStores(query) {
    console.log('Searching stores for:', query);
    
    const xhr = new XMLHttpRequest();
    xhr.open('GET', `/stores/search-for-customers?q=${encodeURIComponent(query)}`, true);
    xhr.setRequestHeader('Accept', 'application/json');
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            const storeResults = document.getElementById('store_results');
            console.log('Store search status:', xhr.status);
            console.log('Store search response:', xhr.responseText);
            
            if (xhr.status === 200) {
                try {
                    const data = JSON.parse(xhr.responseText);
                    console.log('Stores found:', data);
                    
                    if (data.length === 0) {
                        storeResults.innerHTML = '<div class="p-3 text-muted">No se encontraron tiendas</div>';
                    } else {
                        storeResults.innerHTML = data.map(store => `
                            <div class="store-result p-3 border-bottom cursor-pointer hover-bg-light" 
                                 onclick="selectStore(${store.id}, '${escapeHtml(store.text)}', '${escapeHtml(store.manager)}', '${escapeHtml(store.address)}')">
                                <div class="fw-medium">${escapeHtml(store.text)}</div>
                                <small class="text-muted">Gerente: ${escapeHtml(store.manager)}</small>
                                <br>
                                <small class="text-muted">${escapeHtml(store.address)}</small>
                            </div>
                        `).join('');
                    }
                    
                    storeResults.classList.remove('d-none');
                } catch (e) {
                    console.error('Error parsing store JSON:', e);
                    storeResults.innerHTML = '<div class="p-3 text-danger">Error al procesar respuesta</div>';
                    storeResults.classList.remove('d-none');
                }
            } else {
                console.error('Error in store search:', xhr.status, xhr.statusText);
                storeResults.innerHTML = '<div class="p-3 text-danger">Error al buscar tiendas</div>';
                storeResults.classList.remove('d-none');
            }
        }
    };
    
    xhr.onerror = function() {
        console.error('Network error in store search');
        const storeResults = document.getElementById('store_results');
        storeResults.innerHTML = '<div class="p-3 text-danger">Error de conexión</div>';
        storeResults.classList.remove('d-none');
    };
    
    xhr.send();
}

function selectStore(storeId, storeName, manager, address) {
    console.log('Selecting store:', storeId, storeName);
    
    const storeSearch = document.getElementById('store_search');
    const storeIdInput = document.getElementById('store_id');
    const storeResults = document.getElementById('store_results');
    const selectedStoreDiv = document.getElementById('selected_store');
    const selectedStoreInfo = document.getElementById('selected_store_info');

    // Set values
    storeIdInput.value = storeId;
    storeSearch.value = '';
    selectedStoreInfo.innerHTML = `
        <strong>${storeName}</strong><br>
        <small class="text-muted">Gerente: ${manager}</small><br>
        <small class="text-muted">${address}</small>
    `;
    
    // Show selected store and hide search results
    selectedStoreDiv.classList.remove('d-none');
    storeResults.classList.add('d-none');
    storeSearch.style.display = 'none';
}

function clearStoreSelection() {
    const storeSearch = document.getElementById('store_search');
    const storeIdInput = document.getElementById('store_id');
    const selectedStoreDiv = document.getElementById('selected_store');

    storeIdInput.value = '';
    selectedStoreDiv.classList.add('d-none');
    storeSearch.style.display = 'block';
    storeSearch.focus();
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}
</script>

<style>
.cursor-pointer {
    cursor: pointer;
}

.hover-bg-light:hover {
    background-color: #f8f9fa;
}

.store-result:hover {
    background-color: #e3f2fd;
}

.text-gradient {
    background: linear-gradient(45deg, #007bff, #0056b3);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
</style>
@endsection