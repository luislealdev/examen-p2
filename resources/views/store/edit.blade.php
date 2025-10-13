@extends('layouts.app')

@section('title', 'Editar Tienda')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-custom border-0">
            <div class="card-header bg-gradient-primary text-white">
                <h3 class="mb-0">
                    <i class="fas fa-edit me-2"></i>Editar Tienda #{{ $store->store_id }}
                </h3>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('stores.update', $store->store_id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="manager_staff_id" class="form-label">Gerente de la Tienda <span class="text-danger">*</span></label>
                        <select class="form-select @error('manager_staff_id') is-invalid @enderror" 
                                id="manager_staff_id" 
                                name="manager_staff_id" 
                                required>
                            <option value="">Seleccione un gerente...</option>
                            @foreach($staff as $employee)
                                <option value="{{ $employee->staff_id }}" 
                                        {{ old('manager_staff_id', $store->manager_staff_id) == $employee->staff_id ? 'selected' : '' }}>
                                    {{ $employee->first_name }} {{ $employee->last_name }} (ID: {{ $employee->staff_id }})
                                </option>
                            @endforeach
                        </select>
                        @error('manager_staff_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Seleccione el personal que será gerente de esta tienda.</div>
                    </div>

                    <div class="mb-3">
                        <label for="address_id" class="form-label">Dirección de la Tienda <span class="text-danger">*</span></label>
                        <select class="form-select @error('address_id') is-invalid @enderror" 
                                id="address_id" 
                                name="address_id" 
                                required>
                            <option value="">Seleccione una dirección...</option>
                            @foreach($addresses as $address)
                                <option value="{{ $address->address_id }}"
                                        {{ old('address_id', $store->address_id) == $address->address_id ? 'selected' : '' }}>
                                    {{ $address->address }} - {{ $address->city ?? 'Ciudad N/A' }} (ID: {{ $address->address_id }})
                                </option>
                            @endforeach
                        </select>
                        @error('address_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Seleccione la dirección para la ubicación de esta tienda.</div>
                    </div>

                    <!-- Current Address Information -->
                    @if($store->address)
                    <div class="mb-3">
                        <label class="form-label">Dirección Actual</label>
                        <div class="card">
                            <div class="card-body">
                                <p class="mb-1"><strong>Dirección:</strong> {{ $store->address->address }}</p>
                                <p class="mb-1"><strong>Distrito:</strong> {{ $store->address->district }}</p>
                                <p class="mb-1"><strong>Ciudad:</strong> {{ $store->address->city ?? 'N/A' }}</p>
                                <p class="mb-1"><strong>Código Postal:</strong> {{ $store->address->postal_code }}</p>
                                <p class="mb-0"><strong>Teléfono:</strong> {{ $store->address->phone }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Address options -->
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="edit_address">
                                    <label class="form-check-label" for="edit_address">
                                        Editar dirección actual
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="create_new_address">
                                    <label class="form-check-label" for="create_new_address">
                                        Crear nueva dirección
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="edit_address_fields" style="display: none;" class="mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Editar Dirección Actual</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="edit_address_line" class="form-label">Dirección</label>
                                        <input type="text" class="form-control" id="edit_address_line" name="edit_address_line" 
                                               value="{{ $store->address->address ?? '' }}" placeholder="Ingrese la dirección completa">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="edit_district" class="form-label">Distrito</label>
                                        <input type="text" class="form-control" id="edit_district" name="edit_district" 
                                               value="{{ $store->address->district ?? '' }}" placeholder="Distrito">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="edit_postal_code" class="form-label">Código Postal</label>
                                        <input type="text" class="form-control" id="edit_postal_code" name="edit_postal_code" 
                                               value="{{ $store->address->postal_code ?? '' }}" placeholder="Código postal">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="edit_phone" class="form-label">Teléfono</label>
                                        <input type="text" class="form-control" id="edit_phone" name="edit_phone" 
                                               value="{{ $store->address->phone ?? '' }}" placeholder="Número de teléfono">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="new_address_fields" style="display: none;" class="mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Nueva Dirección</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="new_address" class="form-label">Dirección</label>
                                        <input type="text" class="form-control" id="new_address" name="new_address" 
                                               placeholder="Ingrese la dirección completa">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="new_district" class="form-label">Distrito</label>
                                        <input type="text" class="form-control" id="new_district" name="new_district" 
                                               placeholder="Distrito">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="new_postal_code" class="form-label">Código Postal</label>
                                        <input type="text" class="form-control" id="new_postal_code" name="new_postal_code" 
                                               placeholder="Código postal">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="new_phone" class="form-label">Teléfono</label>
                                        <input type="text" class="form-control" id="new_phone" name="new_phone" 
                                               placeholder="Número de teléfono">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Última Actualización</label>
                        <input type="text" class="form-control" value="{{ $store->last_update ? $store->last_update->format('Y-m-d H:i:s') : 'N/A' }}" readonly>
                        <div class="form-text">Este campo se actualiza automáticamente cuando guardas los cambios.</div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="{{ route('stores.show', $store->store_id) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <a href="{{ route('stores.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-list me-2"></i>Volver a Lista
                            </a>
                        </div>
                        <button type="submit" class="btn btn-gradient-primary">
                            <i class="fas fa-save me-2"></i>Actualizar Tienda
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editAddressCheckbox = document.getElementById('edit_address');
    const editAddressFields = document.getElementById('edit_address_fields');
    const createNewAddressCheckbox = document.getElementById('create_new_address');
    const newAddressFields = document.getElementById('new_address_fields');
    const addressSelect = document.getElementById('address_id');
    
    function resetFormState() {
        // Hide all fields
        if (editAddressFields) editAddressFields.style.display = 'none';
        if (newAddressFields) newAddressFields.style.display = 'none';
        
        // Enable address select
        if (addressSelect) {
            addressSelect.disabled = false;
            addressSelect.required = false; // Changed to false to allow other options
        }
        
        // Remove required from all edit/new fields
        const allFields = ['edit_address_line', 'edit_district', 'edit_postal_code', 'edit_phone',
                          'new_address', 'new_district', 'new_postal_code', 'new_phone'];
        allFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) field.required = false;
        });
    }
    
    if (editAddressCheckbox) {
        editAddressCheckbox.addEventListener('change', function() {
            if (this.checked) {
                // Uncheck the other option
                if (createNewAddressCheckbox) createNewAddressCheckbox.checked = false;
                resetFormState();
                
                // Show edit fields
                if (editAddressFields) editAddressFields.style.display = 'block';
                if (addressSelect) addressSelect.disabled = true;
                
                // Make edit address required
                const field = document.getElementById('edit_address_line');
                if (field) field.required = true;
            } else {
                resetFormState();
            }
        });
    }
    
    if (createNewAddressCheckbox) {
        createNewAddressCheckbox.addEventListener('change', function() {
            if (this.checked) {
                // Uncheck the other option
                if (editAddressCheckbox) editAddressCheckbox.checked = false;
                resetFormState();
                
                // Show new address fields
                if (newAddressFields) newAddressFields.style.display = 'block';
                if (addressSelect) addressSelect.disabled = true;
                
                // Make new address required
                const field = document.getElementById('new_address');
                if (field) field.required = true;
            } else {
                resetFormState();
            }
        });
    }
});
</script>
@endsection