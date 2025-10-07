@extends('layouts.app')

@section('title', 'Add Inventory Item')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('inventories.index') }}">Inventory</a></li>
                    <li class="breadcrumb-item active">Add Item</li>
                </ol>
            </nav>
            <h1 class="display-5 fw-bold text-gradient">
                <i class="fas fa-plus-circle me-3"></i>Add Inventory Item
            </h1>
            <p class="lead text-muted">Add a new film to store inventory</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card shadow-lg border-0">
        <div class="card-header bg-gradient-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-boxes me-2"></i>Inventory Item Information
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('inventories.store') }}" method="POST">
                @csrf
                
                <!-- Film Selection -->
                <div class="mb-4">
                    <label for="film_id" class="form-label fw-bold">Film <span class="text-danger">*</span></label>
                    <select class="form-select @error('film_id') is-invalid @enderror" 
                            id="film_id" name="film_id" required>
                        <option value="">Select Film</option>
                        @foreach($films as $film)
                            <option value="{{ $film->film_id }}" 
                                    {{ old('film_id') == $film->film_id ? 'selected' : '' }}
                                    data-rating="{{ $film->rating }}"
                                    data-language="{{ $film->language->name ?? 'N/A' }}"
                                    data-rental-rate="{{ $film->rental_rate }}"
                                    data-categories="{{ $film->categories->pluck('name')->implode(', ') }}">
                                {{ $film->title }} 
                                @if($film->release_year)
                                    ({{ $film->release_year }})
                                @endif
                                - {{ $film->rating }} - ${{ number_format($film->rental_rate, 2) }}
                            </option>
                        @endforeach
                    </select>
                    @error('film_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        Select the film to add to inventory
                    </div>
                </div>

                <!-- Store Selection -->
                <div class="mb-4">
                    <label for="store_id" class="form-label fw-bold">Store <span class="text-danger">*</span></label>
                    <select class="form-select @error('store_id') is-invalid @enderror" 
                            id="store_id" name="store_id" required>
                        <option value="">Select Store</option>
                        @foreach($stores as $store)
                            <option value="{{ $store->store_id }}" 
                                    {{ old('store_id') == $store->store_id ? 'selected' : '' }}>
                                Store #{{ $store->store_id }}
                                @if($store->address)
                                    - {{ $store->address }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('store_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        Select the store where this item will be stocked
                    </div>
                </div>

                <!-- Film Details Preview (hidden by default) -->
                <div id="film-details" class="mb-4" style="display: none;">
                    <div class="card bg-light">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>Selected Film Details
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Rating:</strong>
                                    <span id="film-rating" class="badge"></span>
                                </div>
                                <div class="col-md-3">
                                    <strong>Language:</strong>
                                    <span id="film-language"></span>
                                </div>
                                <div class="col-md-3">
                                    <strong>Rental Rate:</strong>
                                    <span id="film-rental-rate" class="text-success fw-bold"></span>
                                </div>
                                <div class="col-md-3">
                                    <strong>Categories:</strong>
                                    <span id="film-categories" class="small"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="d-flex justify-content-between">
                    <div>
                        <a href="{{ route('inventories.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Inventory
                        </a>
                        <a href="{{ route('inventories.bulk-create') }}" class="btn btn-outline-success ms-2">
                            <i class="fas fa-layer-group me-2"></i>Bulk Add Instead
                        </a>
                    </div>
                    <div>
                        <button type="reset" class="btn btn-outline-danger me-2">
                            <i class="fas fa-undo me-2"></i>Reset Form
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Add to Inventory
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card border-0 bg-light">
                <div class="card-body">
                    <h6 class="fw-bold">
                        <i class="fas fa-film me-2"></i>Available Films
                    </h6>
                    <p class="mb-0">{{ $films->count() }} films available to add to inventory</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 bg-light">
                <div class="card-body">
                    <h6 class="fw-bold">
                        <i class="fas fa-store me-2"></i>Available Stores
                    </h6>
                    <p class="mb-0">{{ $stores->count() }} stores available for stocking</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filmSelect = document.getElementById('film_id');
    const filmDetails = document.getElementById('film-details');
    const filmRating = document.getElementById('film-rating');
    const filmLanguage = document.getElementById('film-language');
    const filmRentalRate = document.getElementById('film-rental-rate');
    const filmCategories = document.getElementById('film-categories');

    filmSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (selectedOption.value) {
            // Show film details
            filmDetails.style.display = 'block';
            
            // Update details
            const rating = selectedOption.dataset.rating;
            const language = selectedOption.dataset.language;
            const rentalRate = selectedOption.dataset.rentalRate;
            const categories = selectedOption.dataset.categories;
            
            filmRating.textContent = rating;
            filmRating.className = 'badge bg-' + getRatingColor(rating);
            
            filmLanguage.textContent = language;
            filmRentalRate.textContent = '$' + parseFloat(rentalRate).toFixed(2);
            filmCategories.textContent = categories || 'No categories';
        } else {
            // Hide film details
            filmDetails.style.display = 'none';
        }
    });

    function getRatingColor(rating) {
        const colors = {
            'G': 'success',
            'PG': 'info',
            'PG-13': 'warning',
            'R': 'danger',
            'NC-17': 'dark'
        };
        return colors[rating] || 'secondary';
    }
});
</script>

<style>
.text-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>
@endsection