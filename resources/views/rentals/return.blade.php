@extends('layouts.app')

@section('title', 'Process Return')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-undo me-2"></i>Process Return
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('rentals.index') }}">Rentals</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('rentals.show', $rental) }}">Rental #{{ $rental->rental_id }}</a></li>
                    <li class="breadcrumb-item active">Process Return</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('rentals.show', $rental) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Rental
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <!-- Return Form -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-check-circle me-2"></i>Return Information
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('rentals.process-return', $rental) }}">
                        @csrf
                        
                        <!-- Return Condition -->
                        <div class="mb-4">
                            <label class="form-label fw-medium">Return Condition <span class="text-danger">*</span></label>
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="return_condition" 
                                               id="excellent" value="excellent" {{ old('return_condition') === 'excellent' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="excellent">
                                            <i class="fas fa-star text-success me-2"></i>Excellent
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="return_condition" 
                                               id="good" value="good" {{ old('return_condition') === 'good' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="good">
                                            <i class="fas fa-thumbs-up text-primary me-2"></i>Good
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="return_condition" 
                                               id="fair" value="fair" {{ old('return_condition') === 'fair' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="fair">
                                            <i class="fas fa-minus-circle text-warning me-2"></i>Fair
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="return_condition" 
                                               id="poor" value="poor" {{ old('return_condition') === 'poor' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="poor">
                                            <i class="fas fa-exclamation-triangle text-danger me-2"></i>Poor
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="return_condition" 
                                               id="damaged" value="damaged" {{ old('return_condition') === 'damaged' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="damaged">
                                            <i class="fas fa-times-circle text-danger me-2"></i>Damaged
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Fees -->
                        <div class="mb-4">
                            <label for="additional_fees" class="form-label fw-medium">Additional Fees</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="additional_fees" name="additional_fees" 
                                       step="0.01" min="0" value="{{ old('additional_fees', 0) }}" placeholder="0.00">
                            </div>
                            <div class="form-text">
                                Add any damage fees, cleaning fees, or other charges.
                            </div>
                        </div>

                        <!-- Return Notes -->
                        <div class="mb-4">
                            <label for="notes" class="form-label fw-medium">Return Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="4" 
                                      placeholder="Add any notes about the return condition, damage, or special circumstances">{{ old('notes') }}</textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check me-2"></i>Process Return
                            </button>
                            <a href="{{ route('rentals.show', $rental) }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Rental Summary -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Rental Summary
                    </h6>
                </div>
                <div class="card-body">
                    <h6 class="card-title">{{ $rental->inventory->film->title }}</h6>
                    <p class="text-muted mb-3">Rental #{{ $rental->rental_id }}</p>
                    
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="fw-medium">Customer:</td>
                            <td>{{ $rental->customer->first_name }} {{ $rental->customer->last_name }}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Rental Date:</td>
                            <td>{{ $rental->rental_date->format('M j, Y') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Due Date:</td>
                            <td class="{{ $rental->is_overdue ? 'text-danger fw-bold' : '' }}">
                                {{ $rental->due_date->format('M j, Y') }}
                                @if($rental->is_overdue)
                                    <br><small>{{ $rental->due_date->diffForHumans() }}</small>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-medium">Rental Amount:</td>
                            <td>${{ number_format($rental->rental_amount, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Fee Calculation -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-calculator me-2"></i>Fee Calculation
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Rental Amount:</span>
                        <span>${{ number_format($rental->rental_amount, 2) }}</span>
                    </div>
                    
                    @if($currentLateFee > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-danger">Late Fee:</span>
                            <span class="text-danger fw-bold">${{ number_format($currentLateFee, 2) }}</span>
                        </div>
                    @endif
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Additional Fees:</span>
                        <span id="additionalFeesDisplay">$0.00</span>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between fw-bold">
                        <span>Total Fees:</span>
                        <span id="totalFeesDisplay" class="{{ $currentLateFee > 0 ? 'text-danger' : '' }}">
                            ${{ number_format($rental->rental_amount + $currentLateFee, 2) }}
                        </span>
                    </div>

                    @if($rental->is_overdue)
                        <div class="alert alert-warning mt-3 mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Overdue:</strong> This rental is {{ $rental->due_date->diffForHumans() }}
                        </div>
                    @endif

                    @if($currentLateFee > 0)
                        <div class="alert alert-info mt-3 mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Late Fee:</strong> ${{ number_format($currentLateFee, 2) }} will be automatically applied.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const additionalFeesInput = document.getElementById('additional_fees');
    const additionalFeesDisplay = document.getElementById('additionalFeesDisplay');
    const totalFeesDisplay = document.getElementById('totalFeesDisplay');
    
    const baseAmount = {{ $rental->rental_amount }};
    const lateFee = {{ $currentLateFee }};
    
    function updateTotalFees() {
        const additionalFees = parseFloat(additionalFeesInput.value) || 0;
        const totalFees = baseAmount + lateFee + additionalFees;
        
        additionalFeesDisplay.textContent = `$${additionalFees.toFixed(2)}`;
        totalFeesDisplay.textContent = `$${totalFees.toFixed(2)}`;
        
        // Update color based on total fees
        if (totalFees > baseAmount) {
            totalFeesDisplay.className = 'text-danger fw-bold';
        } else {
            totalFeesDisplay.className = 'fw-bold';
        }
    }
    
    additionalFeesInput.addEventListener('input', updateTotalFees);
    
    // Initialize
    updateTotalFees();
});
</script>
@endpush
@endsection