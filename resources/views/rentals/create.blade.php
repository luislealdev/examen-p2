@extends('layouts.app')

@section('title', 'Create New Rental')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-plus me-2"></i>Create New Rental
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('rentals.index') }}">Rentals</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('rentals.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Rentals
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Rental Form -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-film me-2"></i>Rental Information
                    </h6>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('rentals.store') }}" id="rentalForm">
                        @csrf
                        
                        <!-- Store Selection -->
                        <div class="mb-3">
                            <label for="store_id" class="form-label">Store <span class="text-danger">*</span></label>
                            <select class="form-select" id="store_id" name="store_id" required>
                                <option value="">Select a store</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store->store_id }}" {{ old('store_id', $selectedStore?->store_id) == $store->store_id ? 'selected' : '' }}>
                                        Store #{{ $store->store_id }} - {{ $store->address->address ?? 'No address' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Customer Selection -->
                        <div class="mb-3">
                            <label for="customer_id" class="form-label">Customer <span class="text-danger">*</span></label>
                            <select class="form-select" id="customer_id" name="customer_id" required>
                                <option value="">Select a customer</option>
                                @php
                                    $customers = \App\Models\Customer::where('active', true)->get();
                                @endphp
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->customer_id }}" {{ old('customer_id') == $customer->customer_id ? 'selected' : '' }}>
                                        {{ $customer->first_name }} {{ $customer->last_name }} ({{ $customer->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Film Selection -->
                        <div class="mb-3" id="filmSelectionGroup" style="{{ !$selectedStore ? 'display: none;' : '' }}">
                            <label for="film_id" class="form-label">Film <span class="text-danger">*</span></label>
                            <select class="form-select" id="film_id" name="film_id" required>
                                <option value="">Select a film</option>
                                @if($selectedStore && !empty($availableInventory))
                                    @foreach($availableInventory as $filmId => $inventory)
                                        <option value="{{ $filmId }}" data-rate="{{ $inventory['film']->rental_rate }}" data-available="{{ $inventory['available_count'] }}">
                                            {{ $inventory['film']->title }} ({{ $inventory['available_count'] }} available)
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- Rental Amount -->
                        <div class="mb-3">
                            <label for="rental_amount" class="form-label">Rental Amount <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="rental_amount" name="rental_amount" 
                                       step="0.01" min="0" value="{{ old('rental_amount') }}" required>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" 
                                      placeholder="Optional rental notes">{{ old('notes') }}</textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Create Rental
                            </button>
                            <a href="{{ route('rentals.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Customer Info Panel -->
            <div class="card" id="customerInfoCard" style="display: none;">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-user me-2"></i>Customer Information
                    </h6>
                </div>
                <div class="card-body" id="customerInfoContent">
                    <!-- Customer info will be loaded here -->
                </div>
            </div>

            <!-- Film Info Panel -->
            <div class="card mt-3" id="filmInfoCard" style="display: none;">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-film me-2"></i>Film Information
                    </h6>
                </div>
                <div class="card-body" id="filmInfoContent">
                    <!-- Film info will be loaded here -->
                </div>
            </div>

            <!-- Rental Summary -->
            <div class="card mt-3" id="rentalSummary" style="display: none;">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-calculator me-2"></i>Rental Summary
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <span>Rental Rate:</span>
                        <span id="displayRate">$0.00</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Due Date:</span>
                        <span id="displayDueDate">-</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold">
                        <span>Total:</span>
                        <span id="displayTotal">$0.00</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const storeSelect = document.getElementById('store_id');
    const customerSelect = document.getElementById('customer_id');
    const filmSelect = document.getElementById('film_id');
    const rentalAmountInput = document.getElementById('rental_amount');
    const filmSelectionGroup = document.getElementById('filmSelectionGroup');
    const customerInfoCard = document.getElementById('customerInfoCard');
    const filmInfoCard = document.getElementById('filmInfoCard');
    const rentalSummary = document.getElementById('rentalSummary');

    // Customer selection will work with regular select for now

    // Store selection change
    storeSelect.addEventListener('change', function() {
        if (this.value) {
            filmSelectionGroup.style.display = 'block';
            loadAvailableFilms(this.value);
        } else {
            filmSelectionGroup.style.display = 'none';
            filmSelect.innerHTML = '<option value="">Select a film</option>';
        }
    });

    // Customer selection change
    customerSelect.addEventListener('change', function() {
        if (this.value) {
            loadCustomerInfo(this.value);
        } else {
            customerInfoCard.style.display = 'none';
        }
    });

    // Film selection change
    filmSelect.addEventListener('change', function() {
        if (this.value) {
            const selectedOption = this.options[this.selectedIndex];
            const rate = selectedOption.dataset.rate;
            const available = selectedOption.dataset.available;
            
            rentalAmountInput.value = rate;
            loadFilmInfo(this.value);
            updateRentalSummary();
        } else {
            filmInfoCard.style.display = 'none';
            rentalSummary.style.display = 'none';
        }
    });

    // Rental amount change
    rentalAmountInput.addEventListener('input', updateRentalSummary);

    function loadAvailableFilms(storeId) {
        // Reload page with store selection to get available inventory
        const url = new URL(window.location);
        url.searchParams.set('store_id', storeId);
        window.location.href = url.toString();
    }

    function loadCustomerInfo(customerId) {
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

            const canRentBadge = data.canRent 
                ? '<span class="badge bg-success">Can Rent</span>'
                : '<span class="badge bg-danger">Cannot Rent</span>';

            document.getElementById('customerInfoContent').innerHTML = `
                <div class="mb-2">
                    <strong>${data.customer.first_name} ${data.customer.last_name}</strong>
                    ${canRentBadge}
                </div>
                <div class="text-muted small mb-2">${data.customer.email}</div>
                <hr class="my-2">
                <div class="row text-center">
                    <div class="col">
                        <div class="fw-bold">${data.activeRentalsCount}</div>
                        <div class="small text-muted">Active</div>
                    </div>
                    <div class="col">
                        <div class="fw-bold text-danger">${data.overdueRentalsCount}</div>
                        <div class="small text-muted">Overdue</div>
                    </div>
                    <div class="col">
                        <div class="fw-bold text-warning">$${data.totalOutstandingFees}</div>
                        <div class="small text-muted">Fees</div>
                    </div>
                </div>
            `;
            
            customerInfoCard.style.display = 'block';

            if (!data.canRent) {
                document.getElementById('rentalForm').style.display = 'none';
                customerInfoCard.insertAdjacentHTML('beforeend', `
                    <div class="alert alert-warning mt-3 mb-0">
                        <strong>Cannot Process Rental:</strong> Customer has outstanding fees.
                    </div>
                `);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    function loadFilmInfo(filmId) {
        const storeId = storeSelect.value;
        
        fetch('{{ route("ajax.film-availability") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ film_id: filmId, store_id: storeId })
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('filmInfoContent').innerHTML = `
                <div class="mb-2">
                    <strong>${data.film.title}</strong>
                    <span class="badge bg-info ms-2">${data.film.rating}</span>
                </div>
                <div class="text-muted small mb-2">${data.film.release_year}</div>
                <hr class="my-2">
                <div class="d-flex justify-content-between">
                    <span>Available Copies:</span>
                    <span class="fw-bold">${data.available_count}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Rental Rate:</span>
                    <span class="fw-bold">$${data.rental_rate}</span>
                </div>
            `;
            
            filmInfoCard.style.display = 'block';
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    function updateRentalSummary() {
        const amount = parseFloat(rentalAmountInput.value) || 0;
        const dueDate = new Date();
        dueDate.setDate(dueDate.getDate() + 3); // Default 3 days

        document.getElementById('displayRate').textContent = `$${amount.toFixed(2)}`;
        document.getElementById('displayDueDate').textContent = dueDate.toLocaleDateString();
        document.getElementById('displayTotal').textContent = `$${amount.toFixed(2)}`;
        
        rentalSummary.style.display = 'block';
    }
});
</script>
@endpush
@endsection