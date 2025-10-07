@extends('layouts.app')

@section('title', 'Edit Film - ' . $film->title)

@section('content')
<div class="container">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('films.index') }}">Films</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('films.show', $film) }}">{{ $film->title }}</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
            <h1 class="display-5 fw-bold text-gradient">
                <i class="fas fa-edit me-3"></i>Edit Film
            </h1>
            <p class="lead text-muted">Update film information for "{{ $film->title }}"</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card shadow-lg border-0">
        <div class="card-header bg-gradient-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-film me-2"></i>Film Information
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('films.update', $film) }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Basic Information -->
                <div class="row g-3 mb-4">
                    <div class="col-md-8">
                        <label for="title" class="form-label fw-bold">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               id="title" name="title" value="{{ old('title', $film->title) }}" required maxlength="128">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="release_year" class="form-label fw-bold">Release Year</label>
                        <input type="number" class="form-control @error('release_year') is-invalid @enderror" 
                               id="release_year" name="release_year" value="{{ old('release_year', $film->release_year) }}"
                               min="1888" max="{{ now()->year + 5 }}">
                        @error('release_year')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <label for="description" class="form-label fw-bold">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="4" 
                              placeholder="Enter film description...">{{ old('description', $film->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Language Information -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="language_id" class="form-label fw-bold">Language <span class="text-danger">*</span></label>
                        <select class="form-select @error('language_id') is-invalid @enderror" 
                                id="language_id" name="language_id" required>
                            <option value="">Select Language</option>
                            @foreach($languages as $language)
                                <option value="{{ $language->language_id }}" 
                                        {{ old('language_id', $film->language_id) == $language->language_id ? 'selected' : '' }}>
                                    {{ $language->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('language_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="original_language_id" class="form-label fw-bold">Original Language</label>
                        <select class="form-select @error('original_language_id') is-invalid @enderror" 
                                id="original_language_id" name="original_language_id">
                            <option value="">Same as Language</option>
                            @foreach($languages as $language)
                                <option value="{{ $language->language_id }}" 
                                        {{ old('original_language_id', $film->original_language_id) == $language->language_id ? 'selected' : '' }}>
                                    {{ $language->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('original_language_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Rental Information -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label for="rental_duration" class="form-label fw-bold">Rental Duration (days) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('rental_duration') is-invalid @enderror" 
                               id="rental_duration" name="rental_duration" value="{{ old('rental_duration', $film->rental_duration) }}"
                               min="1" max="30" required>
                        @error('rental_duration')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-3">
                        <label for="rental_rate" class="form-label fw-bold">Rental Rate ($) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('rental_rate') is-invalid @enderror" 
                               id="rental_rate" name="rental_rate" value="{{ old('rental_rate', $film->rental_rate) }}"
                               min="0" max="99.99" step="0.01" required>
                        @error('rental_rate')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-3">
                        <label for="length" class="form-label fw-bold">Length (minutes)</label>
                        <input type="number" class="form-control @error('length') is-invalid @enderror" 
                               id="length" name="length" value="{{ old('length', $film->length) }}"
                               min="1" max="1000">
                        @error('length')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-3">
                        <label for="replacement_cost" class="form-label fw-bold">Replacement Cost ($) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('replacement_cost') is-invalid @enderror" 
                               id="replacement_cost" name="replacement_cost" value="{{ old('replacement_cost', $film->replacement_cost) }}"
                               min="0" max="999.99" step="0.01" required>
                        @error('replacement_cost')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Rating and Features -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="rating" class="form-label fw-bold">Rating <span class="text-danger">*</span></label>
                        <select class="form-select @error('rating') is-invalid @enderror" 
                                id="rating" name="rating" required>
                            <option value="">Select Rating</option>
                            @foreach($ratings as $rating)
                                <option value="{{ $rating }}" 
                                        {{ old('rating', $film->rating) == $rating ? 'selected' : '' }}>
                                    {{ $rating }}
                                </option>
                            @endforeach
                        </select>
                        @error('rating')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <small>
                                <strong>G:</strong> General Audiences | 
                                <strong>PG:</strong> Parental Guidance | 
                                <strong>PG-13:</strong> Parents Strongly Cautioned | 
                                <strong>R:</strong> Restricted | 
                                <strong>NC-17:</strong> Adults Only
                            </small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Special Features</label>
                        <div class="row">
                            @foreach($specialFeatures as $feature)
                                <div class="col-12">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" 
                                               id="feature_{{ $loop->index }}" 
                                               name="special_features[]" 
                                               value="{{ $feature }}"
                                               {{ in_array($feature, old('special_features', $film->special_features ?? [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="feature_{{ $loop->index }}">
                                            {{ $feature }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('special_features')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Category -->
                <div class="mb-4">
                    <label for="category_id" class="form-label fw-bold">Category</label>
                    <select class="form-select @error('category_id') is-invalid @enderror" 
                            id="category_id" name="category_id">
                        <option value="">Select a category...</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->category_id }}" 
                                    {{ old('category_id', $film->category_id) == $category->category_id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Form Actions -->
                <div class="d-flex justify-content-between">
                    <div>
                        <a href="{{ route('films.show', $film) }}" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-arrow-left me-2"></i>Back to Film
                        </a>
                        <a href="{{ route('films.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-list me-2"></i>All Films
                        </a>
                    </div>
                    <div>
                        <button type="reset" class="btn btn-outline-danger me-2">
                            <i class="fas fa-undo me-2"></i>Reset Changes
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Film
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Current Film Info -->
    <div class="card shadow-lg border-0 mt-4">
        <div class="card-header bg-gradient-info text-white">
            <h5 class="mb-0">
                <i class="fas fa-info-circle me-2"></i>Current Film Information
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="fw-bold">Basic Details</h6>
                    <table class="table table-sm">
                        <tr>
                            <td class="fw-bold">Title:</td>
                            <td>{{ $film->title }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Rating:</td>
                            <td><span class="badge bg-{{ $film->rating_color }}">{{ $film->rating }}</span></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Release Year:</td>
                            <td>{{ $film->release_year ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Length:</td>
                            <td>{{ $film->duration_format }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold">Rental Information</h6>
                    <table class="table table-sm">
                        <tr>
                            <td class="fw-bold">Rental Rate:</td>
                            <td class="text-success fw-bold">${{ number_format($film->rental_rate, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Rental Duration:</td>
                            <td>{{ $film->rental_duration }} {{ Str::plural('day', $film->rental_duration) }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Replacement Cost:</td>
                            <td class="text-danger fw-bold">${{ number_format($film->replacement_cost, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Language:</td>
                            <td>{{ $film->language->name ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            @if($film->category)
                <h6 class="fw-bold mt-3">Current Category</h6>
                <div class="d-flex flex-wrap gap-1">
                    <span class="badge bg-primary">{{ $film->category->name }}</span>
                </div>
            @endif

            @if($film->special_features && count($film->special_features) > 0)
                <h6 class="fw-bold mt-3">Current Special Features</h6>
                <div class="d-flex flex-wrap gap-1">
                    @foreach($film->special_features as $feature)
                        <span class="badge bg-warning text-dark">{{ $feature }}</span>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

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

.bg-gradient-info {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}
</style>
@endsection