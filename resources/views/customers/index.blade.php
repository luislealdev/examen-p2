@extends('layouts.app')

@section('title', 'Customers List')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Customers</h1>
    <a href="{{ route('customers.create') }}" class="btn btn-primary">Add New Customer</a>
</div>

<!-- Search and Filter Section -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('customers.index') }}" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Search</label>
                <input type="text" 
                       class="form-control" 
                       id="search" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Search by name or email...">
            </div>
            
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Customers</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Only</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive Only</option>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="sort" class="form-label">Sort By</label>
                <select class="form-select" id="sort" name="sort">
                    <option value="last_update" {{ request('sort') === 'last_update' ? 'selected' : '' }}>Last Update</option>
                    <option value="first_name" {{ request('sort') === 'first_name' ? 'selected' : '' }}>First Name</option>
                    <option value="last_name" {{ request('sort') === 'last_name' ? 'selected' : '' }}>Last Name</option>
                    <option value="email" {{ request('sort') === 'email' ? 'selected' : '' }}>Email</option>
                    <option value="create_date" {{ request('sort') === 'create_date' ? 'selected' : '' }}>Created Date</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="direction" class="form-label">Order</label>
                <select class="form-select" id="direction" name="direction">
                    <option value="desc" {{ request('direction') === 'desc' ? 'selected' : '' }}>Descending</option>
                    <option value="asc" {{ request('direction') === 'asc' ? 'selected' : '' }}>Ascending</option>
                </select>
            </div>
            
            <div class="col-12">
                <button type="submit" class="btn btn-outline-primary">Apply Filters</button>
                <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>
</div>

<!-- Results Section -->
<div class="card">
    <div class="card-body">
        @if($customers->count() > 0)
            <!-- Results summary -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted">
                    Showing {{ $customers->firstItem() }} to {{ $customers->lastItem() }} of {{ $customers->total() }} customers
                </span>
                <span class="text-muted">
                    Page {{ $customers->currentPage() }} of {{ $customers->lastPage() }}
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Store ID</th>
                            <th>Address ID</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $customer)
                            <tr class="{{ !$customer->active ? 'table-secondary' : '' }}">
                                <td>{{ $customer->customer_id }}</td>
                                <td>
                                    <strong>{{ $customer->full_name }}</strong>
                                </td>
                                <td>{{ $customer->email ?: 'N/A' }}</td>
                                <td>{{ $customer->store_id }}</td>
                                <td>{{ $customer->address_id }}</td>
                                <td>
                                    @if($customer->active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $customer->create_date ? $customer->create_date->format('Y-m-d') : 'N/A' }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('customers.show', $customer->customer_id) }}" class="btn btn-sm btn-info">View</a>
                                        <a href="{{ route('customers.edit', $customer->customer_id) }}" class="btn btn-sm btn-warning">Edit</a>
                                        @if($customer->active)
                                            <form action="{{ route('customers.destroy', $customer->customer_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to deactivate this customer?')">
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
                    {{ $customers->links() }}
                </div>
                <div class="text-muted">
                    Total: {{ $customers->total() }} customers
                </div>
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <p class="text-muted">No customers found.</p>
                @if(request()->hasAny(['search', 'status']))
                    <p class="text-muted">Try adjusting your search or filter criteria.</p>
                    <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">Clear Filters</a>
                @else
                    <a href="{{ route('customers.create') }}" class="btn btn-primary">Add the first customer</a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection