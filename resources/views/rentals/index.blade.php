@extends('layouts.app')

@section('title', 'Rental Management')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-film me-2"></i>Rental Management
            </h1>
            <p class="text-muted mb-0">Manage movie rentals and returns</p>
        </div>
        <div>
            <a href="{{ route('rentals.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>New Rental
            </a>
            <a href="{{ route('rentals.overdue') }}" class="btn btn-warning">
                <i class="fas fa-clock me-2"></i>Overdue Report
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="fas fa-filter me-2"></i>Filters
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('rentals.index') }}">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Statuses</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>Returned</option>
                            <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="store_id" class="form-label">Store</label>
                        <select class="form-select" id="store_id" name="store_id">
                            <option value="">All Stores</option>
                            @foreach($stores as $store)
                                <option value="{{ $store->store_id }}" {{ request('store_id') == $store->store_id ? 'selected' : '' }}>
                                    Store #{{ $store->store_id }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="customer_search" class="form-label">Customer</label>
                        <input type="text" class="form-control" id="customer_search" name="customer_search" 
                               placeholder="Search by name or email" value="{{ request('customer_search') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="film_search" class="form-label">Film</label>
                        <input type="text" class="form-control" id="film_search" name="film_search" 
                               placeholder="Search by film title" value="{{ request('film_search') }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="w-100">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-2"></i>Filter
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Rentals Table -->
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="fas fa-list me-2"></i>Rentals ({{ $rentals->total() }} total)
            </h6>
        </div>
        <div class="card-body p-0">
            @if($rentals->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Rental ID</th>
                                <th>Customer</th>
                                <th>Film</th>
                                <th>Store</th>
                                <th>Rental Date</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th>Late Fee</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rentals as $rental)
                                <tr>
                                    <td>
                                        <a href="{{ route('rentals.show', $rental) }}" class="text-primary fw-bold">
                                            #{{ $rental->rental_id }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="fw-medium">{{ $rental->customer->first_name }} {{ $rental->customer->last_name }}</div>
                                        <small class="text-muted">{{ $rental->customer->email }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-medium">{{ $rental->inventory->film->title }}</div>
                                        <small class="text-muted">{{ $rental->inventory->film->release_year }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">Store #{{ $rental->inventory->store_id }}</span>
                                    </td>
                                    <td>
                                        <div>{{ $rental->rental_date->format('M j, Y') }}</div>
                                        <small class="text-muted">{{ $rental->rental_date->format('g:i A') }}</small>
                                    </td>
                                    <td>
                                        <div class="{{ $rental->is_overdue ? 'text-danger fw-bold' : '' }}">
                                            {{ $rental->due_date->format('M j, Y') }}
                                        </div>
                                        @if($rental->is_overdue)
                                            <small class="text-danger">
                                                {{ $rental->due_date->diffForHumans() }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($rental->status === 'active')
                                            @if($rental->is_overdue)
                                                <span class="badge bg-danger">Overdue</span>
                                            @else
                                                <span class="badge bg-success">Active</span>
                                            @endif
                                        @elseif($rental->status === 'returned')
                                            <span class="badge bg-secondary">Returned</span>
                                        @else
                                            <span class="badge bg-warning">{{ ucfirst($rental->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($rental->late_fee > 0)
                                            <span class="text-danger fw-bold">${{ number_format($rental->late_fee, 2) }}</span>
                                        @elseif($rental->is_overdue && $rental->status === 'active')
                                            <span class="text-warning fw-bold">${{ number_format($rental->getCurrentLateFee(), 2) }}</span>
                                        @else
                                            <span class="text-muted">$0.00</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('rentals.show', $rental) }}" class="btn btn-outline-primary" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($rental->status === 'active')
                                                <a href="{{ route('rentals.return-form', $rental) }}" class="btn btn-outline-success" title="Process Return">
                                                    <i class="fas fa-undo"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-film fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No rentals found</h5>
                    <p class="text-muted">Try adjusting your filters or create a new rental.</p>
                </div>
            @endif
        </div>
        @if($rentals->hasPages())
            <div class="card-footer">
                {{ $rentals->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when filters change
    const filterSelects = document.querySelectorAll('#status, #store_id');
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });
});
</script>
@endpush
@endsection