@extends('layouts.app')

@section('title', 'Overdue Rentals Report')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-clock me-2"></i>Overdue Rentals Report
            </h1>
            <p class="text-muted mb-0">Track and manage overdue rentals across all stores</p>
        </div>
        <div>
            <a href="{{ route('rentals.index') }}" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>Back to Rentals
            </a>
            <button type="button" class="btn btn-warning" id="updateOverdueBtn">
                <i class="fas fa-refresh me-2"></i>Update Late Fees
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center border-danger">
                <div class="card-body">
                    <i class="fas fa-clock fa-2x text-danger mb-2"></i>
                    <h4 class="text-danger fw-bold">{{ $overdueRentals->count() }}</h4>
                    <p class="mb-0 small">Total Overdue</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-warning">
                <div class="card-body">
                    <i class="fas fa-dollar-sign fa-2x text-warning mb-2"></i>
                    <h4 class="text-warning fw-bold">${{ number_format($overdueRentals->sum('late_fee'), 2) }}</h4>
                    <p class="mb-0 small">Total Late Fees</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-info">
                <div class="card-body">
                    <i class="fas fa-calendar-alt fa-2x text-info mb-2"></i>
                    <h4 class="text-info fw-bold">
                        {{ $overdueRentals->where('due_date', '<', now()->subDays(7))->count() }}
                    </h4>
                    <p class="mb-0 small">7+ Days Overdue</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-primary">
                <div class="card-body">
                    <i class="fas fa-users fa-2x text-primary mb-2"></i>
                    <h4 class="text-primary fw-bold">
                        {{ $overdueRentals->pluck('customer_id')->unique()->count() }}
                    </h4>
                    <p class="mb-0 small">Affected Customers</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Overdue Rentals Table -->
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="fas fa-list me-2"></i>Overdue Rentals Details
            </h6>
        </div>
        <div class="card-body p-0">
            @if($overdueRentals->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="overdueTable">
                        <thead class="table-danger">
                            <tr>
                                <th>Rental ID</th>
                                <th>Customer</th>
                                <th>Film</th>
                                <th>Store</th>
                                <th>Due Date</th>
                                <th>Days Overdue</th>
                                <th>Late Fee</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($overdueRentals->sortBy('due_date') as $rental)
                                <tr class="{{ $rental->due_date->lt(now()->subDays(7)) ? 'table-danger' : 'table-warning' }}">
                                    <td>
                                        <a href="{{ route('rentals.show', $rental) }}" class="text-primary fw-bold">
                                            #{{ $rental->rental_id }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="fw-medium">{{ $rental->customer->first_name }} {{ $rental->customer->last_name }}</div>
                                        <small class="text-muted">{{ $rental->customer->email }}</small>
                                        <br>
                                        @if(!$rental->customer->canRent())
                                            <span class="badge bg-danger">Blocked</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-medium">{{ $rental->inventory->film->title }}</div>
                                        <small class="text-muted">{{ $rental->inventory->film->release_year }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">Store #{{ $rental->inventory->store_id }}</span>
                                    </td>
                                    <td>
                                        <div class="text-danger fw-bold">{{ $rental->due_date->format('M j, Y') }}</div>
                                        <small class="text-muted">{{ $rental->due_date->format('g:i A') }}</small>
                                    </td>
                                    <td>
                                        @php
                                            $daysOverdue = $rental->due_date->diffInDays(now());
                                            $severity = $daysOverdue > 7 ? 'danger' : ($daysOverdue > 3 ? 'warning' : 'info');
                                        @endphp
                                        <span class="badge bg-{{ $severity }} fs-6">
                                            {{ $daysOverdue }} {{ Str::plural('day', $daysOverdue) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($rental->late_fee > 0)
                                            <span class="text-danger fw-bold">${{ number_format($rental->late_fee, 2) }}</span>
                                        @else
                                            <span class="text-warning fw-bold">${{ number_format($rental->getCurrentLateFee(), 2) }}</span>
                                            <br><small class="text-muted">Pending</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('rentals.show', $rental) }}" class="btn btn-outline-primary" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('rentals.return-form', $rental) }}" class="btn btn-outline-success" title="Process Return">
                                                <i class="fas fa-undo"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-info contact-customer-btn" 
                                                    title="Contact Customer" data-customer-id="{{ $rental->customer_id }}">
                                                <i class="fas fa-phone"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h5 class="text-success">No Overdue Rentals</h5>
                    <p class="text-muted">All rentals are returned or within the due date.</p>
                </div>
            @endif
        </div>
    </div>

    @if($overdueRentals->count() > 0)
        <!-- Actions Section -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-tools me-2"></i>Bulk Actions
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Generate Reports</h6>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary" onclick="generateOverdueReport()">
                                <i class="fas fa-file-pdf me-2"></i>Export PDF Report
                            </button>
                            <button type="button" class="btn btn-outline-success" onclick="generateCustomerList()">
                                <i class="fas fa-file-excel me-2"></i>Export Customer List
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>Communication</h6>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-warning" onclick="sendReminders()">
                                <i class="fas fa-envelope me-2"></i>Send Email Reminders
                            </button>
                            <button type="button" class="btn btn-outline-info" onclick="generatePhoneList()">
                                <i class="fas fa-phone me-2"></i>Generate Call List
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Contact Customer Modal -->
<div class="modal fade" id="contactCustomerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Contact Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="customerContactInfo">
                    <!-- Customer contact info will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update overdue status and late fees
    document.getElementById('updateOverdueBtn').addEventListener('click', function() {
        const btn = this;
        const originalText = btn.innerHTML;
        
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
        btn.disabled = true;
        
        fetch('{{ route("ajax.update-overdue") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.updated > 0) {
                alert(`Updated ${data.updated} overdue rentals with late fees.`);
                location.reload();
            } else {
                alert('No rentals needed updating.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating overdue rentals.');
        })
        .finally(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    });

    // Contact customer buttons
    document.querySelectorAll('.contact-customer-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const customerId = this.dataset.customerId;
            loadCustomerContactInfo(customerId);
        });
    });

    function loadCustomerContactInfo(customerId) {
        fetch('{{ route("ajax.customer-info") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ customer_id: customerId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }

            document.getElementById('customerContactInfo').innerHTML = `
                <div class="text-center mb-3">
                    <h6>${data.customer.first_name} ${data.customer.last_name}</h6>
                    <p class="text-muted">${data.customer.email}</p>
                </div>
                <div class="row">
                    <div class="col-6">
                        <strong>Active Rentals:</strong><br>
                        <span class="badge bg-primary">${data.activeRentalsCount}</span>
                    </div>
                    <div class="col-6">
                        <strong>Outstanding Fees:</strong><br>
                        <span class="badge bg-warning">$${data.totalOutstandingFees}</span>
                    </div>
                </div>
                <hr>
                <div class="d-grid gap-2">
                    <a href="mailto:${data.customer.email}" class="btn btn-primary">
                        <i class="fas fa-envelope me-2"></i>Send Email
                    </a>
                    <button class="btn btn-outline-secondary" onclick="copyToClipboard('${data.customer.email}')">
                        <i class="fas fa-copy me-2"></i>Copy Email
                    </button>
                </div>
            `;
            
            new bootstrap.Modal(document.getElementById('contactCustomerModal')).show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading customer information.');
        });
    }

    // Initialize DataTable for better sorting and filtering
    if (document.getElementById('overdueTable')) {
        // Simple table enhancements
        const table = document.getElementById('overdueTable');
        
        // Add row hover effects
        table.addEventListener('mouseover', function(e) {
            if (e.target.closest('tr')) {
                e.target.closest('tr').style.cursor = 'pointer';
            }
        });
    }
});

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('Email copied to clipboard!');
    });
}

function generateOverdueReport() {
    alert('PDF report generation would be implemented here.');
}

function generateCustomerList() {
    alert('Excel export would be implemented here.');
}

function sendReminders() {
    alert('Email reminder system would be implemented here.');
}

function generatePhoneList() {
    alert('Phone list generation would be implemented here.');
}
</script>
@endpush
@endsection