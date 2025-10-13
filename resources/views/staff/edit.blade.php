@extends('layouts.app')

@section('title', 'Edit Staff')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header">
                <h3>Edit Staff: {{ $staff->full_name }}</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('staff.update', $staff->staff_id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Personal Information</h5>
                            
                            <!-- Current picture preview -->
                            @if($staff->picture)
                                <div class="mb-3 text-center">
                                    <img src="{{ route('staff.picture', $staff->staff_id) }}" 
                                         alt="Current Picture" 
                                         class="rounded-circle"
                                         style="width: 100px; height: 100px; object-fit: cover;">
                                    <p class="text-muted small">Current picture</p>
                                </div>
                            @endif
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control @error('first_name') is-invalid @enderror" 
                                               id="first_name" 
                                               name="first_name" 
                                               value="{{ old('first_name', $staff->first_name) }}" 
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
                                               value="{{ old('last_name', $staff->last_name) }}" 
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
                                       value="{{ old('email', $staff->email) }}" 
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
                                       value="{{ old('address_id', $staff->address_id) }}" 
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
                                <div class="form-text">Optional. Upload a new profile picture to replace the current one (max 2MB).</div>
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
                                        <option value="{{ $store->store_id }}" {{ old('store_id', $staff->store_id) == $store->store_id ? 'selected' : '' }}>
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
                                           {{ old('active', $staff->active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="active">
                                        Active Employee
                                    </label>
                                </div>
                                <div class="form-text">Uncheck to deactivate this employee.</div>
                            </div>

                            <h5 class="mt-4">System Access</h5>
                            
                            <div class="mb-3">
                                <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('username') is-invalid @enderror" 
                                       id="username" 
                                       name="username" 
                                       value="{{ old('username', $staff->username) }}" 
                                       maxlength="16"
                                       required>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Username for rental system access (max 16 characters).</div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       minlength="6">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Leave blank to keep current password. Enter new password to change (minimum 6 characters).</div>
                            </div>

                            <!-- Read-only information -->
                            <div class="bg-light p-3 rounded mt-4">
                                <h6>System Information (Read-only)</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <small><strong>Staff ID:</strong> {{ $staff->staff_id }}</small>
                                    </div>
                                    <div class="col-md-6">
                                        <small><strong>Last Update:</strong> {{ $staff->last_update ? $staff->last_update->format('Y-m-d H:i:s') : 'N/A' }}</small>
                                    </div>
                                </div>
                                @if($staff->is_manager)
                                    <div class="mt-2">
                                        <small><strong>Role:</strong> <span class="badge bg-warning">Manager</span></small>
                                        <small class="d-block text-muted">This staff member manages {{ $staff->managedStores->count() }} store(s)</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('staff.show', $staff->staff_id) }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Staff Member</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection