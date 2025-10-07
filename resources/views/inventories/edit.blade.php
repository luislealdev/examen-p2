@extends('layouts.app')

@section('title', 'Edit Inventory Item')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-primary">
                <i class="fas fa-edit me-2"></i>Edit Inventory Item
            </h1>
            <p class="text-muted">Update inventory item information</p>
        </div>
        <a href="{{ route('inventories.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to List
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Please correct the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-box me-2"></i>Inventory Information
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('inventories.update', $inventory) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="film_id" class="form-label">
                                    <i class="fas fa-film me-1"></i>Film *
                                </label>
                                <select class="form-select @error('film_id') is-invalid @enderror" 
                                        id="film_id" name="film_id" required>
                                    <option value="">Select a film...</option>
                                    @foreach($films as $film)
                                        <option value="{{ $film->film_id }}" 
                                                {{ old('film_id', $inventory->film_id) == $film->film_id ? 'selected' : '' }}
                                                data-title="{{ $film->title }}"
                                                data-description="{{ $film->description }}"
                                                data-category="{{ $film->category->name ?? 'N/A' }}"
                                                data-language="{{ $film->language->name ?? 'N/A' }}"
                                                data-rating="{{ $film->rating }}"
                                                data-length="{{ $film->length }}"
                                                data-rental-rate="{{ $film->rental_rate }}"
                                                data-replacement-cost="{{ $film->replacement_cost }}">
                                            {{ $film->title }} ({{ $film->release_year }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('film_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="store_id" class="form-label">
                                    <i class="fas fa-store me-1"></i>Store *
                                </label>
                                <select class="form-select @error('store_id') is-invalid @enderror" 
                                        id="store_id" name="store_id" required>
                                    <option value="">Select a store...</option>
                                    @foreach($stores as $store)
                                        <option value="{{ $store->store_id }}" 
                                                {{ old('store_id', $inventory->store_id) == $store->store_id ? 'selected' : '' }}>
                                            {{ $store->location }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('store_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-gradient-primary">
                                <i class="fas fa-save me-1"></i>Update Inventory
                            </button>
                            <a href="{{ route('inventories.show', $inventory) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-eye me-1"></i>View Item
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Current Film Preview -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-film me-1"></i>Current Film
                    </h6>
                </div>
                <div class="card-body">
                    <h6 class="text-primary">{{ $inventory->film->title }}</h6>
                    <p class="small text-muted mb-2">{{ Str::limit($inventory->film->description, 100) }}</p>
                    <div class="row g-2">
                        <div class="col-6">
                            <small class="text-muted d-block">Category</small>
                            <strong>{{ $inventory->film->category->name ?? 'N/A' }}</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Language</small>
                            <strong>{{ $inventory->film->language->name ?? 'N/A' }}</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Rating</small>
                            <span class="badge bg-{{ $inventory->film->rating === 'G' ? 'success' : ($inventory->film->rating === 'PG' ? 'primary' : ($inventory->film->rating === 'PG-13' ? 'warning' : 'danger')) }}">
                                {{ $inventory->film->rating }}
                            </span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Length</small>
                            <strong>{{ $inventory->film->length }} min</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Selected Film Preview (will update via JavaScript) -->
            <div class="card shadow-sm" id="filmPreview" style="display: none;">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-eye me-1"></i>New Film Preview
                    </h6>
                </div>
                <div class="card-body">
                    <h6 class="text-primary" id="previewTitle">-</h6>
                    <p class="small text-muted mb-2" id="previewDescription">-</p>
                    <div class="row g-2">
                        <div class="col-6">
                            <small class="text-muted d-block">Category</small>
                            <strong id="previewCategory">-</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Language</small>
                            <strong id="previewLanguage">-</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Rating</small>
                            <span class="badge" id="previewRating">-</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Length</small>
                            <strong id="previewLength">-</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Rental Rate</small>
                            <strong id="previewRentalRate">-</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Replacement Cost</small>
                            <strong id="previewReplacementCost">-</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('film_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const preview = document.getElementById('filmPreview');
    
    if (this.value === '') {
        preview.style.display = 'none';
        return;
    }
    
    // Update preview content
    document.getElementById('previewTitle').textContent = selectedOption.dataset.title || '-';
    document.getElementById('previewDescription').textContent = selectedOption.dataset.description || '-';
    document.getElementById('previewCategory').textContent = selectedOption.dataset.category || '-';
    document.getElementById('previewLanguage').textContent = selectedOption.dataset.language || '-';
    document.getElementById('previewLength').textContent = (selectedOption.dataset.length || '-') + (selectedOption.dataset.length ? ' min' : '');
    document.getElementById('previewRentalRate').textContent = selectedOption.dataset.rentalRate ? '$' + selectedOption.dataset.rentalRate : '-';
    document.getElementById('previewReplacementCost').textContent = selectedOption.dataset.replacementCost ? '$' + selectedOption.dataset.replacementCost : '-';
    
    // Update rating badge
    const rating = selectedOption.dataset.rating || '';
    const ratingElement = document.getElementById('previewRating');
    ratingElement.textContent = rating || '-';
    ratingElement.className = 'badge bg-' + (
        rating === 'G' ? 'success' : 
        rating === 'PG' ? 'primary' : 
        rating === 'PG-13' ? 'warning' : 
        'danger'
    );
    
    preview.style.display = 'block';
});
</script>
@endsection