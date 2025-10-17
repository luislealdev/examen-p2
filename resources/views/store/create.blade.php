@extends('layouts.app')

@section('title', 'Crear Tienda')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-custom border-0">
            <div class="card-header bg-gradient-primary text-white">
                <h3 class="mb-0">
                    <i class="fas fa-store me-2"></i>Crear Nueva Tienda
                </h3>
            </div>
            <div class="card-body">
                <form action="{{ route('stores.store') }}" method="POST" id="storeForm">
                    @csrf
                    
                    <!-- Selección de Manager -->
                    <div class="mb-4">
                        <label for="manager_staff_id" class="form-label fw-bold">
                            <i class="fas fa-user-tie me-2"></i>Manager de la Tienda <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('manager_staff_id') is-invalid @enderror" 
                                id="manager_staff_id" 
                                name="manager_staff_id" 
                                required>
                            <option value="">Seleccione un manager</option>
                            @foreach($staff as $employee)
                                <option value="{{ $employee->staff_id }}" 
                                        {{ old('manager_staff_id') == $employee->staff_id ? 'selected' : '' }}>
                                    {{ $employee->first_name }} {{ $employee->last_name }}
                                    @if($employee->email)
                                        ({{ $employee->email }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('manager_staff_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Seleccione el empleado que será manager de esta tienda.</div>
                    </div>

                    <!-- Opción de Dirección -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="fas fa-map-marker-alt me-2"></i>Dirección de la Tienda <span class="text-danger">*</span>
                        </label>
                        
                        <!-- Radio buttons para seleccionar opción -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="address_option" 
                                       id="select_existing" value="select_existing" 
                                       {{ old('address_option', 'create_new') == 'select_existing' ? 'checked' : '' }}>
                                <label class="form-check-label" for="select_existing">
                                    <i class="fas fa-list me-2"></i>Seleccionar dirección existente
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="address_option" 
                                       id="create_new" value="create_new" 
                                       {{ old('address_option', 'create_new') == 'create_new' ? 'checked' : '' }}>
                                <label class="form-check-label" for="create_new">
                                    <i class="fas fa-plus me-2"></i>Crear nueva dirección
                                </label>
                            </div>
                        </div>

                        <!-- Selección de dirección existente -->
                        <div id="existing_address_section" class="mb-3" style="display: none;">
                            <select class="form-select @error('address_id') is-invalid @enderror" 
                                    id="address_id" 
                                    name="address_id">
                                <option value="">Seleccione una dirección</option>
                                @foreach($addresses as $address)
                                    <option value="{{ $address->address_id }}" 
                                            {{ old('address_id') == $address->address_id ? 'selected' : '' }}>
                                        {{ $address->address }}
                                        @if($address->address2), {{ $address->address2 }}@endif
                                        - {{ $address->city->city ?? 'Ciudad N/A' }}, 
                                        {{ $address->city->country->country ?? 'País N/A' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('address_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Formulario para nueva dirección -->
                        <div id="new_address_section">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="address_line1" class="form-label">Dirección <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('address_line1') is-invalid @enderror" 
                                           id="address_line1" 
                                           name="address_line1" 
                                           value="{{ old('address_line1') }}" 
                                           maxlength="50"
                                           placeholder="Calle y número">
                                    @error('address_line1')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="address_line2" class="form-label">Dirección 2 (Opcional)</label>
                                    <input type="text" 
                                           class="form-control @error('address_line2') is-invalid @enderror" 
                                           id="address_line2" 
                                           name="address_line2" 
                                           value="{{ old('address_line2') }}" 
                                           maxlength="50"
                                           placeholder="Apartamento, oficina, etc.">
                                    @error('address_line2')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="district" class="form-label">Distrito <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('district') is-invalid @enderror" 
                                           id="district" 
                                           name="district" 
                                           value="{{ old('district') }}" 
                                           maxlength="20"
                                           placeholder="Distrito o zona">
                                    @error('district')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="postal_code" class="form-label">Código Postal <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('postal_code') is-invalid @enderror" 
                                           id="postal_code" 
                                           name="postal_code" 
                                           value="{{ old('postal_code') }}" 
                                           maxlength="10"
                                           placeholder="Código postal">
                                    @error('postal_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="country_id" class="form-label">País <span class="text-danger">*</span></label>
                                    <select class="form-select @error('country_id') is-invalid @enderror" 
                                            id="country_id" 
                                            name="country_id">
                                        <option value="">Seleccione un país</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country->country_id }}" 
                                                    {{ old('country_id') == $country->country_id ? 'selected' : '' }}>
                                                {{ $country->country }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('country_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="city_id" class="form-label">Ciudad <span class="text-danger">*</span></label>
                                    <select class="form-select @error('city_id') is-invalid @enderror" 
                                            id="city_id" 
                                            name="city_id">
                                        <option value="">Primero seleccione un país</option>
                                    </select>
                                    @error('city_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Teléfono (Opcional)</label>
                                <input type="text" 
                                       class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" 
                                       name="phone" 
                                       value="{{ old('phone') }}" 
                                       maxlength="20"
                                       placeholder="Número de teléfono">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('stores.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-gradient-primary">
                            <i class="fas fa-save me-2"></i>Crear Tienda
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const addressOptions = document.querySelectorAll('input[name="address_option"]');
    const existingSection = document.getElementById('existing_address_section');
    const newSection = document.getElementById('new_address_section');
    const countrySelect = document.getElementById('country_id');
    const citySelect = document.getElementById('city_id');

    // Manejar cambio de opción de dirección
    addressOptions.forEach(option => {
        option.addEventListener('change', function() {
            if (this.value === 'select_existing') {
                existingSection.style.display = 'block';
                newSection.style.display = 'none';
                // Remover required de campos de nueva dirección
                toggleRequiredNewAddress(false);
            } else {
                existingSection.style.display = 'none';
                newSection.style.display = 'block';
                // Agregar required a campos de nueva dirección
                toggleRequiredNewAddress(true);
            }
        });
    });

    // Inicializar vista según opción seleccionada
    const selectedOption = document.querySelector('input[name="address_option"]:checked');
    if (selectedOption) {
        selectedOption.dispatchEvent(new Event('change'));
    }

    // Función para toggle required en campos de nueva dirección
    function toggleRequiredNewAddress(required) {
        const requiredFields = ['address_line1', 'district', 'postal_code', 'country_id', 'city_id'];
        requiredFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                if (required) {
                    field.setAttribute('required', 'required');
                } else {
                    field.removeAttribute('required');
                }
            }
        });
    }

    // Cargar ciudades cuando cambie el país
    countrySelect.addEventListener('change', function() {
        const countryId = this.value;
        citySelect.innerHTML = '<option value="">Cargando ciudades...</option>';
        
        if (countryId) {
            fetch(`{{ route('cities.by-country') }}?country_id=${countryId}`)
                .then(response => response.json())
                .then(data => {
                    citySelect.innerHTML = '<option value="">Seleccione una ciudad</option>';
                    data.forEach(city => {
                        const option = document.createElement('option');
                        option.value = city.city_id;
                        option.textContent = city.city;
                        if ('{{ old("city_id") }}' == city.city_id) {
                            option.selected = true;
                        }
                        citySelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error loading cities:', error);
                    citySelect.innerHTML = '<option value="">Error cargando ciudades</option>';
                });
        } else {
            citySelect.innerHTML = '<option value="">Primero seleccione un país</option>';
        }
    });

    // Si hay un país preseleccionado, cargar sus ciudades
    if (countrySelect.value) {
        countrySelect.dispatchEvent(new Event('change'));
    }
});
</script>

@endsection