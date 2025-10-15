@extends('layouts.app')

@section('title', 'Editar Cliente')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header">
                <h3>Editar Cliente: {{ $customer->full_name }}</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('customers.update', $customer->customer_id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="first_name" class="form-label">Nombre <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('first_name') is-invalid @enderror" 
                                       id="first_name" 
                                       name="first_name" 
                                       value="{{ old('first_name', $customer->first_name) }}" 
                                       maxlength="45"
                                       required>
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Apellido <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('last_name') is-invalid @enderror" 
                                       id="last_name" 
                                       name="last_name" 
                                       value="{{ old('last_name', $customer->last_name) }}" 
                                       maxlength="45"
                                       required>
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email', $customer->email) }}" 
                               maxlength="50">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Opcional - Dirección de correo electrónico del cliente.</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="store_id" class="form-label">Tienda Principal <span class="text-danger">*</span></label>
                                <select class="form-select @error('store_id') is-invalid @enderror" 
                                        id="store_id" 
                                        name="store_id" 
                                        required>
                                    <option value="">Seleccionar una tienda...</option>
                                    @foreach($stores as $store)
                                        <option value="{{ $store->store_id }}" {{ old('store_id', $customer->store_id) == $store->store_id ? 'selected' : '' }}>
                                            @if($store->address && $store->address->city)
                                                {{ $store->address->city->city }} - {{ $store->address->address }}
                                                @if($store->manager)
                                                    (Encargado: {{ $store->manager->full_name }})
                                                @endif
                                            @else
                                                Tienda {{ $store->store_id }}
                                                @if($store->manager)
                                                    (Encargado: {{ $store->manager->full_name }})
                                                @endif
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('store_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">La tienda donde este cliente generalmente alquila películas.</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="address_option" class="form-label">Dirección <span class="text-danger">*</span></label>
                                <div class="mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="address_option" id="edit_current" value="edit_current" checked>
                                        <label class="form-check-label" for="edit_current">
                                            Editar dirección actual
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="address_option" id="select_existing" value="select_existing">
                                        <label class="form-check-label" for="select_existing">
                                            Seleccionar dirección existente
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="address_option" id="create_new" value="create_new">
                                        <label class="form-check-label" for="create_new">
                                            Crear nueva dirección
                                        </label>
                                    </div>
                                </div>
                                
                                <!-- Select para dirección existente -->
                                <div id="existing_address_select" style="display: none;">
                                    <select class="form-select @error('address_id') is-invalid @enderror" 
                                            id="address_id_select" 
                                            name="address_id">
                                        <option value="">Seleccionar una dirección...</option>
                                        @foreach($addresses as $address)
                                            <option value="{{ $address->address_id }}" {{ old('address_id', $customer->address_id) == $address->address_id ? 'selected' : '' }}>
                                                {{ $address->address }}, {{ $address->city->city ?? 'Ciudad desconocida' }}, {{ $address->city->country->country ?? 'País desconocido' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                @error('address_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Campos para editar/crear dirección -->
                    <div id="address_fields">
                        <div class="row">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <span id="address_section_title">Editar Dirección Actual</span>
                                </h6>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="address_line1" class="form-label">Dirección Principal <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('address_line1') is-invalid @enderror" 
                                           id="address_line1" 
                                           name="address_line1" 
                                           value="{{ old('address_line1', $customer->address->address ?? '') }}" 
                                           maxlength="50"
                                           required>
                                    @error('address_line1')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="address_line2" class="form-label">Dirección Secundaria</label>
                                    <input type="text" 
                                           class="form-control @error('address_line2') is-invalid @enderror" 
                                           id="address_line2" 
                                           name="address_line2" 
                                           value="{{ old('address_line2', $customer->address->address2 ?? '') }}" 
                                           maxlength="50">
                                    @error('address_line2')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Apartamento, piso, suite, etc. (opcional)</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="district" class="form-label">Distrito/Barrio <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('district') is-invalid @enderror" 
                                           id="district" 
                                           name="district" 
                                           value="{{ old('district', $customer->address->district ?? '') }}" 
                                           maxlength="20"
                                           required>
                                    @error('district')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="postal_code" class="form-label">Código Postal <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('postal_code') is-invalid @enderror" 
                                           id="postal_code" 
                                           name="postal_code" 
                                           value="{{ old('postal_code', $customer->address->postal_code ?? '') }}" 
                                           maxlength="10"
                                           required>
                                    @error('postal_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Teléfono</label>
                                    <input type="text" 
                                           class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" 
                                           name="phone" 
                                           value="{{ old('phone', $customer->address->phone ?? '') }}" 
                                           maxlength="20">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Teléfono de contacto (opcional)</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="country_id" class="form-label">País <span class="text-danger">*</span></label>
                                    <select class="form-select @error('country_id') is-invalid @enderror" 
                                            id="country_id" 
                                            name="country_id" 
                                            required>
                                        <option value="">Seleccionar país...</option>
                                        @php
                                            $countries = \App\Models\Country::all();
                                        @endphp
                                        @foreach($countries as $country)
                                            <option value="{{ $country->country_id }}" 
                                                {{ old('country_id', $customer->address->city->country_id ?? '') == $country->country_id ? 'selected' : '' }}>
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
                                    <label for="city_id" class="form-label">Ciudad <span class="text-danger">*</span></label>
                                    <select class="form-select @error('city_id') is-invalid @enderror" 
                                            id="city_id" 
                                            name="city_id" 
                                            required>
                                        <option value="">Primero selecciona un país...</option>
                                        @if($customer->address && $customer->address->city)
                                            <option value="{{ $customer->address->city->city_id }}" selected>
                                                {{ $customer->address->city->city }}
                                            </option>
                                        @endif
                                    </select>
                                    @error('city_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dirección Actual (información de referencia) -->
                    @if($customer->address)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h6 class="mb-0">Dirección Actual del Cliente</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Dirección Completa:</strong><br>
                                            {{ $customer->address->address }}<br>
                                            @if($customer->address->address2)
                                                {{ $customer->address->address2 }}<br>
                                            @endif
                                            {{ $customer->address->district }}, {{ $customer->address->city->city ?? 'Ciudad desconocida' }}<br>
                                            {{ $customer->address->city->country->country ?? 'País desconocido' }}<br>
                                            CP: {{ $customer->address->postal_code }}
                                        </div>
                                        <div class="col-md-6">
                                            @if($customer->address->phone)
                                                <strong>Teléfono:</strong> {{ $customer->address->phone }}<br>
                                            @endif
                                            <small class="text-muted">
                                                Si necesitas cambiar los detalles de esta dirección, puedes seleccionar una dirección diferente arriba o contactar al administrador.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="active" 
                                   name="active" 
                                   value="1" 
                                   {{ old('active', $customer->active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="active">
                                Cliente Activo
                            </label>
                        </div>
                        <div class="form-text">Desmarca para desactivar este cliente.</div>
                    </div>

                    <!-- Información del Sistema (Solo lectura) -->
                    <div class="bg-light p-3 rounded mb-3">
                        <h6>Información del Sistema (Solo lectura)</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <small><strong>ID del Cliente:</strong> {{ $customer->customer_id }}</small>
                            </div>
                            <div class="col-md-6">
                                <small><strong>Fecha de Registro:</strong> {{ $customer->create_date ? $customer->create_date->format('d/m/Y H:i:s') : 'N/A' }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('customers.show', $customer->customer_id) }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Actualizar Cliente</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript para manejar las opciones de dirección -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const addressOptions = document.querySelectorAll('input[name="address_option"]');
    const addressFields = document.getElementById('address_fields');
    const existingAddressSelect = document.getElementById('existing_address_select');
    const addressSectionTitle = document.getElementById('address_section_title');
    const countrySelect = document.getElementById('country_id');
    const citySelect = document.getElementById('city_id');
    
    // Manejar cambio de opciones de dirección
    addressOptions.forEach(option => {
        option.addEventListener('change', function() {
            switch(this.value) {
                case 'edit_current':
                    addressFields.style.display = 'block';
                    existingAddressSelect.style.display = 'none';
                    addressSectionTitle.textContent = 'Editar Dirección Actual';
                    document.getElementById('address_id_select').removeAttribute('required');
                    setAddressFieldsRequired(true);
                    break;
                case 'select_existing':
                    addressFields.style.display = 'none';
                    existingAddressSelect.style.display = 'block';
                    document.getElementById('address_id_select').setAttribute('required', 'required');
                    setAddressFieldsRequired(false);
                    break;
                case 'create_new':
                    addressFields.style.display = 'block';
                    existingAddressSelect.style.display = 'none';
                    addressSectionTitle.textContent = 'Crear Nueva Dirección';
                    document.getElementById('address_id_select').removeAttribute('required');
                    setAddressFieldsRequired(true);
                    // Limpiar campos para nueva dirección
                    clearAddressFields();
                    break;
            }
        });
    });
    
    // Función para establecer required en campos de dirección
    function setAddressFieldsRequired(required) {
        const requiredFields = ['address_line1', 'district', 'postal_code', 'country_id', 'city_id'];
        requiredFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (required) {
                field.setAttribute('required', 'required');
            } else {
                field.removeAttribute('required');
            }
        });
    }
    
    // Función para limpiar campos de dirección
    function clearAddressFields() {
        document.getElementById('address_line1').value = '';
        document.getElementById('address_line2').value = '';
        document.getElementById('district').value = '';
        document.getElementById('postal_code').value = '';
        document.getElementById('phone').value = '';
        document.getElementById('country_id').value = '';
        document.getElementById('city_id').innerHTML = '<option value="">Primero selecciona un país...</option>';
    }
    
    // Manejar cambio de país para cargar ciudades
    countrySelect.addEventListener('change', function() {
        const countryId = this.value;
        
        // Limpiar ciudades
        citySelect.innerHTML = '<option value="">Cargando ciudades...</option>';
        
        if (countryId) {
            // Hacer petición AJAX para obtener ciudades
            fetch(`/api/countries/${countryId}/cities`)
                .then(response => response.json())
                .then(cities => {
                    citySelect.innerHTML = '<option value="">Seleccionar ciudad...</option>';
                    cities.forEach(city => {
                        const option = document.createElement('option');
                        option.value = city.city_id;
                        option.textContent = city.city;
                        citySelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    citySelect.innerHTML = '<option value="">Error al cargar ciudades</option>';
                });
        } else {
            citySelect.innerHTML = '<option value="">Primero selecciona un país...</option>';
        }
    });
});
</script>
@endsection