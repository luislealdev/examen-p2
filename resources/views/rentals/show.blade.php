@extends('layouts.app')

@section('title', 'Rental Details')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-film me-2"></i>Rental #{{ $rental->rental_id }}
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('rentals.index') }}">Rentals</a></li>
                    <li class="breadcrumb-item active">Rental #{{ $rental->rental_id }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('rentals.index') }}" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>Back to Rentals
            </a>
            @if($rental->status === 'active')
                <a href="{{ route('rentals.return-form', $rental) }}" class="btn btn-success">
                    <i class="fas fa-undo me-2"></i>Process Return
                </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <!-- Rental Details -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Rental Information
                    </h6>
                    <div>
                        @if($rental->status === 'active')
                            @if($rental->is_overdue)
                                <span class="badge bg-danger fs-6">OVERDUE</span>
                            @else
                                <span class="badge bg-success fs-6">ACTIVE</span>
                            @endif
                        @elseif($rental->status === 'returned')
                            <span class="badge bg-secondary fs-6">RETURNED</span>
                        @else
                            <span class="badge bg-warning fs-6">{{ strtoupper($rental->status) }}</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-medium">Rental ID:</td>
                                    <td>#{{ $rental->rental_id }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">Rental Date:</td>
                                    <td>{{ $rental->rental_date->format('F j, Y g:i A') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">Due Date:</td>
                                    <td class="{{ $rental->is_overdue ? 'text-danger fw-bold' : '' }}">
                                        {{ $rental->due_date->format('F j, Y g:i A') }}
                                        @if($rental->is_overdue)
                                            <br><small class="text-danger">{{ $rental->due_date->diffForHumans() }}</small>
                                        @endif
                                    </td>
                                </tr>
                                @if($rental->return_date)
                                    <tr>
                                        <td class="fw-medium">Return Date:</td>
                                        <td>{{ $rental->return_date->format('F j, Y g:i A') }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td class="fw-medium">Rental Amount:</td>
                                    <td class="fw-bold">${{ number_format($rental->rental_amount, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-medium">Store:</td>
                                    <td>
                                        <a href="{{ route('stores.show', $rental->store) }}" class="text-decoration-none">
                                            Store #{{ $rental->store->store_id }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">Staff:</td>
                                    <td>{{ $rental->staff->first_name }} {{ $rental->staff->last_name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">Status:</td>
                                    <td>
                                        <span class="text-capitalize">{{ $rental->status }}</span>
                                    </td>
                                </tr>
                                @if($rental->late_fee > 0)
                                    <tr>
                                        <td class="fw-medium">Late Fee:</td>
                                        <td class="text-danger fw-bold">${{ number_format($rental->late_fee, 2) }}</td>
                                    </tr>
                                @elseif($rental->is_overdue && $rental->status === 'active')
                                    <tr>
                                        <td class="fw-medium">Current Late Fee:</td>
                                        <td class="text-warning fw-bold">${{ number_format($rental->getCurrentLateFee(), 2) }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    @if($rental->notes)
                        <hr>
                        <div>
                            <h6 class="fw-medium">Notes:</h6>
                            <div class="bg-light p-3 rounded">
                                {!! nl2br(e($rental->notes)) !!}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Film Details -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-film me-2"></i>Film Details
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="card-title">{{ $rental->inventory->film->title }}</h5>
                            <p class="text-muted mb-2">{{ $rental->inventory->film->description }}</p>
                            
                            <div class="row">
                                <div class="col-sm-6">
                                    <table class="table table-borderless table-sm">
                                        <tr>
                                            <td class="fw-medium">Release Year:</td>
                                            <td>{{ $rental->inventory->film->release_year }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-medium">Length:</td>
                                            <td>{{ $rental->inventory->film->length }} minutes</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-medium">Rating:</td>
                                            <td><span class="badge bg-info">{{ $rental->inventory->film->rating }}</span></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-sm-6">
                                    <table class="table table-borderless table-sm">
                                        <tr>
                                            <td class="fw-medium">Language:</td>
                                            <td>{{ $rental->inventory->film->language->name ?? 'Unknown' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-medium">Rental Duration:</td>
                                            <td>{{ $rental->inventory->film->rental_duration }} days</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-medium">Rental Rate:</td>
                                            <td>${{ number_format($rental->inventory->film->rental_rate, 2) }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <!-- Placeholder for film poster -->
                            <div class="bg-light rounded p-4 mb-2" style="height: 200px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-film fa-3x text-muted"></i>
                            </div>
                            <small class="text-muted">Inventory ID: {{ $rental->inventory_id }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Customer Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-user me-2"></i>Customer Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" 
                             style="width: 60px; height: 60px;">
                            <i class="fas fa-user fa-2x text-white"></i>
                        </div>
                    </div>
                    
                    <h6 class="text-center">{{ $rental->customer->first_name }} {{ $rental->customer->last_name }}</h6>
                    <p class="text-muted text-center mb-3">{{ $rental->customer->email }}</p>
                    
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="fw-medium">Customer ID:</td>
                            <td>
                                <a href="{{ route('customers.show', $rental->customer) }}" class="text-decoration-none">
                                    #{{ $rental->customer->customer_id }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Active Since:</td>
                            <td>{{ $rental->customer->create_date->format('M Y') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Status:</td>
                            <td>
                                @if($rental->customer->active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                        </tr>
                    </table>

                    <div class="d-grid">
                        <a href="{{ route('customers.show', $rental->customer) }}" class="btn btn-outline-primary btn-sm">
                            View Customer Profile
                        </a>
                    </div>
                </div>
            </div>

            <!-- Rental Timeline -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-clock me-2"></i>Rental Timeline
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Rental Created</h6>
                                <small class="text-muted">{{ $rental->rental_date->format('M j, Y g:i A') }}</small>
                            </div>
                        </div>
                        
                        @if($rental->late_fee_applied && $rental->late_fee > 0)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-warning"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Late Fee Applied</h6>
                                    <small class="text-muted">${{ number_format($rental->late_fee, 2) }} charged</small>
                                </div>
                            </div>
                        @endif
                        
                        @if($rental->return_date)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Film Returned</h6>
                                    <small class="text-muted">{{ $rental->return_date->format('M j, Y g:i A') }}</small>
                                </div>
                            </div>
                        @else
                            <div class="timeline-item">
                                <div class="timeline-marker bg-{{ $rental->is_overdue ? 'danger' : 'secondary' }}"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Due Date</h6>
                                    <small class="text-muted">{{ $rental->due_date->format('M j, Y g:i A') }}</small>
                                    @if($rental->is_overdue)
                                        <div class="text-danger small">{{ $rental->due_date->diffForHumans() }}</div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    padding-bottom: 20px;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -22px;
    top: 20px;
    width: 2px;
    height: calc(100% - 10px);
    background-color: #e9ecef;
}

.timeline-marker {
    position: absolute;
    left: -27px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.timeline-content h6 {
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}
</style>
@endpush
@endsection