@extends('layouts.app')

@section('title', 'Editar Tienda')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header">
                <h3>Editar Tienda #{{ $store->store_id }}</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('stores.update', $store->store_id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="manager_staff_id" class="form-label">Gerente <span class="text-danger">*</span></label>
                                <select class="form-select @error('manager_staff_id') is-invalid @enderror" 
                                        id="manager_staff_id" 
                                        name="manager_staff_id" 
                                        required>
                                    <option value="">Seleccionar un gerente...</option>
                                    @foreach($staff as $employee)
                                        <option value="{{ $employee->staff_id }}" {{ old('manager_staff_id', $store->manager_staff_id) == $employee->staff_id ? 'selected' : '' }}>
                                            {{ $employee->full_name }}
                                            @if($employee->email)
                                                ({{ $employee->email }})
                                            @endif
                                            @if(!$employee->active)
                                                - INACTIVO
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('manager_staff_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Selecciona el empleado que será gerente de esta tienda.</div>
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
                                            <option value="{{ $address->address_id }}" {{ old('address_id', $store->address_id) == $address->address_id ? 'selected' : '' }}>
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
                                           value="{{ old('address_line1', $store->address->address ?? '') }}" 
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
                                           value="{{ old('address_line2', $store->address->address2 ?? '') }}" 
                                           maxlength="50">
                                    @error('address_line2')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Piso, local, suite, etc. (opcional)</div>
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
                                           value="{{ old('district', $store->address->district ?? '') }}" 
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
                                           value="{{ old('postal_code', $store->address->postal_code ?? '') }}" 
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
                                           value="{{ old('phone', $store->address->phone ?? '') }}" 
                                           maxlength="20">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Teléfono de la tienda (opcional)</div>
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
                                        @foreach($countries as $country)
                                            <option value="{{ $country->country_id }}" 
                                                {{ old('country_id', $store->address->city->country_id ?? '') == $country->country_id ? 'selected' : '' }}>
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
                                        @if($store->address && $store->address->city)
                                            <option value="{{ $store->address->city->city_id }}" selected>
                                                {{ $store->address->city->city }}
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
                    </div>

                    <!-- Información Actual (información de referencia) -->
                    @if($store->address)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h6 class="mb-0">Dirección Actual de la Tienda</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Dirección Completa:</strong><br>
                                            {{ $store->address->address }}<br>
                                            @if($store->address->address2)
                                                {{ $store->address->address2 }}<br>
                                            @endif
                                            {{ $store->address->district }}, {{ $store->address->city->city ?? 'Ciudad desconocida' }}<br>
                                            {{ $store->address->city->country->country ?? 'País desconocido' }}<br>
                                            CP: {{ $store->address->postal_code }}
                                        </div>
                                        <div class="col-md-6">
                                            @if($store->address->phone)
                                                <strong>Teléfono:</strong> {{ $store->address->phone }}<br>
                                            @endif
                                            <small class="text-muted">
                                                Si necesitas cambiar los detalles de esta dirección, puedes editarla arriba o seleccionar una dirección diferente.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($store->manager)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card bg-info text-white">
                                <div class="card-header">
                                    <h6 class="mb-0 text-white">Gerente Actual</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Nombre:</strong> {{ $store->manager->full_name }}<br>
                                            <strong>Email:</strong> {{ $store->manager->email ?: 'No especificado' }}<br>
                                            <strong>Usuario:</strong> {{ $store->manager->username }}
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Estado:</strong> 
                                            @if($store->manager->active)
                                                <span class="badge bg-success">Activo</span>
                                            @else
                                                <span class="badge bg-warning">Inactivo</span>
                                            @endif
                                            <br>
                                            <small class="text-white-50">
                                                Puedes cambiar el gerente seleccionando otro empleado arriba.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Información del Sistema (Solo lectura) -->
                    <div class="bg-light p-3 rounded mb-3 mt-4">
                        <h6>Información del Sistema (Solo lectura)</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <small><strong>ID de la Tienda:</strong> {{ $store->store_id }}</small>
                            </div>
                            <div class="col-md-6">
                                <small><strong>Última Actualización:</strong> {{ $store->last_update ? $store->last_update->format('d/m/Y H:i:s') : 'N/A' }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('stores.show', $store->store_id) }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Actualizar Tienda</button>
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