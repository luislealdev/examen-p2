@extends('layouts.app')

@section('title', 'Create Staff')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header">
                <h3>Create New Staff Member</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('staff.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Personal Information</h5>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control @error('first_name') is-invalid @enderror" 
                                               id="first_name" 
                                               name="first_name" 
                                               value="{{ old('first_name') }}" 
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
                                               value="{{ old('last_name') }}" 
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
                                       value="{{ old('email') }}" 
                                       maxlength="50">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Optional staff email address.</div>
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
                                <div class="form-text">Enter the address ID for this staff member.</div>
                            </div>

                            <div class="mb-3">
                                <label for="picture" class="form-label">Profile Picture</label>
                                <input type="file" 
                                       class="form-control @error('picture') is-invalid @enderror" 
                                       id="picture" 
                                       name="picture" 
                                       accept="image/*">
                                @error('picture')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Optional. Upload a profile picture (max 2MB).</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>Work Information</h5>
                            
                            <div class="mb-3">
                                <label for="store_id" class="form-label">Home Store <span class="text-danger">*</span></label>
                                <select class="form-select @error('store_id') is-invalid @enderror" 
                                        id="store_id" 
                                        name="store_id" 
                                        required>
                                    <option value="">Select a store...</option>
                                    @foreach($stores as $store)
                                        <option value="{{ $store->store_id }}" {{ old('store_id') == $store->store_id ? 'selected' : '' }}>
                                            Store {{ $store->store_id }} (Manager: {{ $store->manager_staff_id }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('store_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">The store where this staff member is assigned.</div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="active" 
                                           name="active" 
                                           value="1" 
                                           {{ old('active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="active">
                                        Active Employee
                                    </label>
                                </div>
                                <div class="form-text">Uncheck to create an inactive employee.</div>
                            </div>

                            <h5 class="mt-4">System Access</h5>
                            
                            <div class="mb-3">
                                <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('username') is-invalid @enderror" 
                                       id="username" 
                                       name="username" 
                                       value="{{ old('username') }}" 
                                       maxlength="16"
                                       required>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Username for rental system access (max 16 characters).</div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       minlength="6"
                                       required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Password for rental system access (minimum 6 characters).</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('staff.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create Staff Member</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection