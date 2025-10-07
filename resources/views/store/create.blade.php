@extends('layouts.app')

@section('title', 'Create Store')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3>Create New Store</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('stores.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="manager_staff_id" class="form-label">Manager Staff ID <span class="text-danger">*</span></label>
                        <input type="number" 
                               class="form-control @error('manager_staff_id') is-invalid @enderror" 
                               id="manager_staff_id" 
                               name="manager_staff_id" 
                               value="{{ old('manager_staff_id') }}" 
                               min="1"
                               required>
                        @error('manager_staff_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Enter the staff ID of the manager for this store.</div>
                    </div>

                    <div class="mb-3">
                        <label for="address_id" class="form-label">Address ID <span class="text-danger">*</span></label>
                        <input type="number" 
                               class="form-control @error('address_id') is-invalid @enderror" 
                               id="address_id" 
                               name="address_id" 
                               value="{{ old('address_id') }}" 
                               min="1"
                               required>
                        @error('address_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Enter the address ID for this store location.</div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('stores.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create Store</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection