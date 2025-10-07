@extends('layouts.app')

@section('title', 'Edit Customer')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3>Edit Customer: {{ $customer->full_name }}</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('customers.update', $customer->customer_id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
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
                                <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
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
                        <label for="email" class="form-label">Email</label>
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email', $customer->email) }}" 
                               maxlength="50">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Optional - Customer's email address.</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="store_id" class="form-label">Home Store <span class="text-danger">*</span></label>
                                <select class="form-select @error('store_id') is-invalid @enderror" 
                                        id="store_id" 
                                        name="store_id" 
                                        required>
                                    <option value="">Select a store...</option>
                                    @foreach($stores as $store)
                                        <option value="{{ $store->store_id }}" {{ old('store_id', $customer->store_id) == $store->store_id ? 'selected' : '' }}>
                                            Store {{ $store->store_id }} (Manager: {{ $store->manager_staff_id }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('store_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">The store where this customer generally shops.</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="address_id" class="form-label">Address ID <span class="text-danger">*</span></label>
                                <input type="number" 
                                       class="form-control @error('address_id') is-invalid @enderror" 
                                       id="address_id" 
                                       name="address_id" 
                                       value="{{ old('address_id', $customer->address_id) }}" 
                                       min="1"
                                       required>
                                @error('address_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Enter the address ID for this customer.</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="active" 
                                   name="active" 
                                   value="1" 
                                   {{ old('active', $customer->active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="active">
                                Active Customer
                            </label>
                        </div>
                        <div class="form-text">Uncheck to deactivate this customer.</div>
                    </div>

                    <!-- Read-only information -->
                    <div class="bg-light p-3 rounded mb-3">
                        <h6>System Information (Read-only)</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <small><strong>Customer ID:</strong> {{ $customer->customer_id }}</small>
                            </div>
                            <div class="col-md-6">
                                <small><strong>Created:</strong> {{ $customer->create_date ? $customer->create_date->format('Y-m-d H:i:s') : 'N/A' }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('customers.show', $customer->customer_id) }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Customer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection