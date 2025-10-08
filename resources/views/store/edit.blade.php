@extends('layouts.app')

@section('title', 'Editar Tienda')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3>Editar Tienda #{{ $store->store_id }}</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('stores.update', $store->store_id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="manager_staff_id" class="form-label">ID del Personal Gerente <span class="text-danger">*</span></label>
                        <input type="number" 
                               class="form-control @error('manager_staff_id') is-invalid @enderror" 
                               id="manager_staff_id" 
                               name="manager_staff_id" 
                               value="{{ old('manager_staff_id', $store->manager_staff_id) }}" 
                               min="1"
                               required>
                        @error('manager_staff_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Ingresa el ID del personal que será gerente de esta tienda.</div>
                    </div>

                    <div class="mb-3">
                        <label for="address_id" class="form-label">ID de Dirección <span class="text-danger">*</span></label>
                        <input type="number" 
                               class="form-control @error('address_id') is-invalid @enderror" 
                               id="address_id" 
                               name="address_id" 
                               value="{{ old('address_id', $store->address_id) }}" 
                               min="1"
                               required>
                        @error('address_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Ingresa el ID de dirección para la ubicación de esta tienda.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Última Actualización</label>
                        <input type="text" class="form-control" value="{{ $store->last_update ? $store->last_update->format('Y-m-d H:i:s') : 'N/A' }}" readonly>
                        <div class="form-text">Este campo se actualiza automáticamente cuando guardas los cambios.</div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="{{ route('stores.show', $store->store_id) }}" class="btn btn-secondary">Cancel</a>
                            <a href="{{ route('stores.index') }}" class="btn btn-outline-secondary">Back to List</a>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Store</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection