@extends('layouts.app')

@section('title', 'Crear Tienda')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-custom border-0">
            <div class="card-header bg-gradient-primary text-white">
                <h3 class="mb-0">
                    <i class="fas fa-store me-2"></i>Crear Nueva Tienda
                </h3>
            </div>
            <div class="card-body">
                <form action="{{ route('stores.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="manager_staff_id" class="form-label fw-bold">Gerente de la Tienda <span class="text-danger">*</span></label>
                        <select class="form-select @error('manager_staff_id') is-invalid @enderror" 
                                id="manager_staff_id" 
                                name="manager_staff_id" 
                                required>
                            <option value="">Seleccione un gerente...</option>
                            @foreach($staff as $employee)
                                <option value="{{ $employee->staff_id }}" {{ old('manager_staff_id') == $employee->staff_id ? 'selected' : '' }}>
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
                        <label for="address_id" class="form-label fw-bold">Dirección de la Tienda <span class="text-danger">*</span></label>
                        <select class="form-select @error('address_id') is-invalid @enderror" 
                                id="address_id" 
                                name="address_id" 
                                required>
                            <option value="">Seleccione una dirección...</option>
                            @foreach($addresses as $address)
                                <option value="{{ $address->address_id }}" {{ old('address_id') == $address->address_id ? 'selected' : '' }}>
                                    {{ $address->address }} - {{ $address->city ?? 'Ciudad N/A' }} (ID: {{ $address->address_id }})
                                </option>
                            @endforeach
                        </select>
                        @error('address_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Seleccione la dirección para la ubicación de esta tienda.</div>
                    </div>

                    <!-- New Address Section -->
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="create_new_address">
                            <label class="form-check-label fw-bold" for="create_new_address">
                                Crear nueva dirección
                            </label>
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
                                        <input type="text" class="form-control" id="new_address" name="new_address" placeholder="Ingrese la dirección completa">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="new_district" class="form-label">Distrito</label>
                                        <input type="text" class="form-control" id="new_district" name="new_district" placeholder="Distrito">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="new_postal_code" class="form-label">Código Postal</label>
                                        <input type="text" class="form-control" id="new_postal_code" name="new_postal_code" placeholder="Código postal">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="new_phone" class="form-label">Teléfono</label>
                                        <input type="text" class="form-control" id="new_phone" name="new_phone" placeholder="Número de teléfono">
                                    </div>
                                </div>
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
    const createNewAddressCheckbox = document.getElementById('create_new_address');
    const newAddressFields = document.getElementById('new_address_fields');
    const addressSelect = document.getElementById('address_id');
    
    createNewAddressCheckbox.addEventListener('change', function() {
        if (this.checked) {
            newAddressFields.style.display = 'block';
            addressSelect.disabled = true;
            addressSelect.required = false;
            
            // Make new address fields required
            document.getElementById('new_address').required = true;
            document.getElementById('new_district').required = true;
            document.getElementById('new_postal_code').required = true;
            document.getElementById('new_phone').required = true;
        } else {
            newAddressFields.style.display = 'none';
            addressSelect.disabled = false;
            addressSelect.required = true;
            
            // Remove required from new address fields
            document.getElementById('new_address').required = false;
            document.getElementById('new_district').required = false;
            document.getElementById('new_postal_code').required = false;
            document.getElementById('new_phone').required = false;
        }
    });
});
</script>
@endsection