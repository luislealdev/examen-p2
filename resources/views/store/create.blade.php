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
                        <label for="manager_staff_id" class="form-label fw-bold">ID del Personal Gerente <span class="text-danger">*</span></label>
                        <input type="number" 
                               class="form-control @error('manager_staff_id') is-invalid @enderror" 
                               id="manager_staff_id" 
                               name="manager_staff_id" 
                               value="{{ old('manager_staff_id') }}" 
                               min="1"
                               placeholder="ID del personal gerente"
                               required>
                        @error('manager_staff_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Ingrese el ID del personal que será gerente de esta tienda.</div>
                    </div>

                    <div class="mb-3">
                        <label for="address_id" class="form-label fw-bold">ID de Dirección <span class="text-danger">*</span></label>
                        <input type="number" 
                               class="form-control @error('address_id') is-invalid @enderror" 
                               id="address_id" 
                               name="address_id" 
                               value="{{ old('address_id') }}" 
                               min="1"
                               placeholder="ID de dirección de la tienda"
                               required>
                        @error('address_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Ingrese el ID de dirección para la ubicación de esta tienda.</div>
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
@endsection