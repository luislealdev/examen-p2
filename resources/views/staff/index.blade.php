@extends('layouts.app')

@section('title', 'Staff List')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Staff</h1>
    <a href="{{ route('staff.create') }}" class="btn btn-primary">Add New Staff</a>
</div>

<!-- Advanced Search and Filter Section -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('staff.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="search" class="form-label">Search</label>
                <input type="text" 
                       class="form-control" 
                       id="search" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Name, email or username...">
            </div>
            
            <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Staff</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Only</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive Only</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="store_id" class="form-label">Store</label>
                <select class="form-select" id="store_id" name="store_id">
                    <option value="">All Stores</option>
                    @foreach($stores as $store)
                        <option value="{{ $store->store_id }}" {{ request('store_id') == $store->store_id ? 'selected' : '' }}>
                            Store {{ $store->store_id }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="is_manager" class="form-label">Manager</label>
                <select class="form-select" id="is_manager" name="is_manager">
                    <option value="">All Staff</option>
                    <option value="yes" {{ request('is_manager') === 'yes' ? 'selected' : '' }}>Managers Only</option>
                    <option value="no" {{ request('is_manager') === 'no' ? 'selected' : '' }}>Non-Managers</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="sort" class="form-label">Sort By</label>
                <select class="form-select" id="sort" name="sort">
                    <option value="last_update" {{ request('sort') === 'last_update' ? 'selected' : '' }}>Last Update</option>
                    <option value="first_name" {{ request('sort') === 'first_name' ? 'selected' : '' }}>First Name</option>
                    <option value="last_name" {{ request('sort') === 'last_name' ? 'selected' : '' }}>Last Name</option>
                    <option value="email" {{ request('sort') === 'email' ? 'selected' : '' }}>Email</option>
                    <option value="username" {{ request('sort') === 'username' ? 'selected' : '' }}>Username</option>
                    <option value="store_id" {{ request('sort') === 'store_id' ? 'selected' : '' }}>Store</option>
                </select>
            </div>
            
            <div class="col-md-1">
                <label for="direction" class="form-label">Order</label>
                <select class="form-select" id="direction" name="direction">
                    <option value="desc" {{ request('direction') === 'desc' ? 'selected' : '' }}>↓</option>
                    <option value="asc" {{ request('direction') === 'asc' ? 'selected' : '' }}>↑</option>
                </select>
            </div>
            
            <div class="col-12">
                <button type="submit" class="btn btn-outline-primary">Apply Filters</button>
                <a href="{{ route('staff.index') }}" class="btn btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>
</div>

<!-- Results Section -->
<div class="card">
    <div class="card-body">
        @if($staff->count() > 0)
            <!-- Results summary -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted">
                    Showing {{ $staff->firstItem() }} to {{ $staff->lastItem() }} of {{ $staff->total() }} staff members
                </span>
                <span class="text-muted">
                    Page {{ $staff->currentPage() }} of {{ $staff->lastPage() }}
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Store</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($staff as $member)
                            <tr class="{{ !$member->active ? 'table-secondary' : '' }}">
                                <td>{{ $member->staff_id }}</td>
                                <td>
                                    @if($member->picture)
                                        <img src="{{ route('staff.picture', $member->staff_id) }}" 
                                             alt="Photo" 
                                             class="rounded-circle"
                                             style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white" 
                                             style="width: 40px; height: 40px;">
                                            {{ $member->initials }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $member->full_name }}</strong>
                                </td>
                                <td>
                                    <code>{{ $member->username }}</code>
                                </td>
                                <td>{{ $member->email ?: 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-info">Store {{ $member->store_id }}</span>
                                </td>
                                <td>
                                    @if($member->is_manager)
                                        <span class="badge bg-warning">Manager</span>
                                    @else
                                        <span class="badge bg-light text-dark">Staff</span>
                                    @endif
                                </td>
                                <td>
                                    @if($member->active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('staff.show', $member->staff_id) }}" class="btn btn-sm btn-info">View</a>
                                        <a href="{{ route('staff.edit', $member->staff_id) }}" class="btn btn-sm btn-warning">Edit</a>
                                        @if($member->active)
                                            <form action="{{ route('staff.destroy', $member->staff_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to deactivate this staff member?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Deactivate</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Enhanced Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div>
                    {{ $staff->links() }}
                </div>
                <div class="text-muted">
                    Total: {{ $staff->total() }} staff members
                </div>
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <p class="text-muted">No staff members found.</p>
                @if(request()->hasAny(['search', 'status', 'store_id', 'is_manager']))
                    <p class="text-muted">Try adjusting your search or filter criteria.</p>
                    <a href="{{ route('staff.index') }}" class="btn btn-outline-secondary">Clear Filters</a>
                @else
                    <a href="{{ route('staff.create') }}" class="btn btn-primary">Add the first staff member</a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection