@extends('layouts.app')

@section('title', 'Staff Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>Staff Details: {{ $staff->full_name }}</h3>
                <div>
                    <a href="{{ route('staff.edit', $staff->staff_id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <a href="{{ route('staff.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <!-- Profile Picture Section -->
                        <div class="text-center mb-4">
                            @if($staff->picture)
                                <img src="{{ route('staff.picture', $staff->staff_id) }}" 
                                     alt="Profile Picture" 
                                     class="rounded-circle mb-3"
                                     style="width: 150px; height: 150px; object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white mx-auto mb-3" 
                                     style="width: 150px; height: 150px; font-size: 3rem;">
                                    {{ $staff->initials }}
                                </div>
                            @endif
                            <h4>{{ $staff->full_name }}</h4>
                            <p class="text-muted">
                                @if($staff->is_manager)
                                    <span class="badge bg-warning">Manager</span>
                                @else
                                    <span class="badge bg-light text-dark">Staff</span>
                                @endif
                                @if($staff->active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Personal Information</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Staff ID:</strong></td>
                                        <td>{{ $staff->staff_id }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>First Name:</strong></td>
                                        <td>{{ $staff->first_name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Last Name:</strong></td>
                                        <td>{{ $staff->last_name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email:</strong></td>
                                        <td>{{ $staff->email ?: 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Address ID:</strong></td>
                                        <td>{{ $staff->address_id }}</td>
                                    </tr>
                                </table>
                            </div>
                            
                            <div class="col-md-6">
                                <h5>Work Information</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Home Store ID:</strong></td>
                                        <td>{{ $staff->store_id }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Username:</strong></td>
                                        <td><code>{{ $staff->username }}</code></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            @if($staff->active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Last Update:</strong></td>
                                        <td>{{ $staff->last_update ? $staff->last_update->format('Y-m-d H:i:s') : 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                @if($staff->store)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Home Store Information</h5>
                            <div class="alert alert-info">
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Store ID:</strong> {{ $staff->store->store_id }}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Manager Staff ID:</strong> {{ $staff->store->manager_staff_id }}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Store Address ID:</strong> {{ $staff->store->address_id }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if($staff->managedStores->count() > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Stores Managed</h5>
                            <div class="alert alert-warning">
                                <p><strong>This staff member is a manager of the following stores:</strong></p>
                                <ul class="mb-0">
                                    @foreach($staff->managedStores as $managedStore)
                                        <li>Store {{ $managedStore->store_id }} (Address ID: {{ $managedStore->address_id }})</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- TODO: When Address model is created, uncomment this section
                @if($staff->address)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Address Information</h5>
                            <div class="alert alert-secondary">
                                <strong>Address:</strong> {{ $staff->address->address }}<br>
                                <strong>City:</strong> {{ $staff->address->city }}<br>
                                <strong>District:</strong> {{ $staff->address->district }}<br>
                                <strong>Postal Code:</strong> {{ $staff->address->postal_code }}
                            </div>
                        </div>
                    </div>
                @endif
                --}}

                <div class="mt-4">
                    <h5>Actions</h5>
                    <div class="btn-group" role="group">
                        <a href="{{ route('staff.edit', $staff->staff_id) }}" class="btn btn-warning">Edit Staff</a>
                        @if($staff->active)
                            <form action="{{ route('staff.destroy', $staff->staff_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to deactivate this staff member? This will not delete the record but mark them as inactive.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Deactivate Staff</button>
                            </form>
                        @else
                            <span class="text-muted">Staff member is already inactive</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection