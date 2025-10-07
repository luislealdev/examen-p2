@extends('layouts.app')

@section('title', 'Films')

@section('content')
<div class="container">
    <!-- Header with Title and Add Button -->
    <div class="row align-items-center mb-4">
        <div class="col">
            <h1 class="display-4 fw-bold text-gradient">
                <i class="fas fa-film me-3"></i>Films Collection
            </h1>
            <p class="lead text-muted">Explore our extensive collection of {{ number_format($totalFilms) }} films</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('films.create') }}" class="btn btn-primary btn-lg shadow-lg">
                <i class="fas fa-plus me-2"></i>Add New Film
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card gradient-card-primary">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">Total Films</h6>
                            <h3 class="mb-0">{{ number_format($totalFilms) }}</h3>
                        </div>
                        <div class="opacity-75">
                            <i class="fas fa-film fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card gradient-card-success">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">Recent Films</h6>
                            <h3 class="mb-0">{{ number_format($recentFilms) }}</h3>
                        </div>
                        <div class="opacity-75">
                            <i class="fas fa-calendar-star fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card gradient-card-info">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">Avg Rental Rate</h6>
                            <h3 class="mb-0">${{ number_format($avgRentalRate, 2) }}</h3>
                        </div>
                        <div class="opacity-75">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card gradient-card-warning">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">Avg Length</h6>
                            <h3 class="mb-0">{{ number_format($avgLength) }} min</h3>
                        </div>
                        <div class="opacity-75">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card shadow-lg border-0 mb-4">
        <div class="card-header bg-light border-0">
            <h5 class="mb-0">
                <i class="fas fa-filter me-2"></i>Filters & Search
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('films.index') }}" class="row g-3">
                <!-- Search -->
                <div class="col-md-4">
                    <label class="form-label fw-bold">Search</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Search by title or description..."
                               value="{{ request('search') }}">
                    </div>
                </div>

                <!-- Rating Filter -->
                <div class="col-md-2">
                    <label class="form-label fw-bold">Rating</label>
                    <select name="rating" class="form-select">
                        <option value="">All Ratings</option>
                        @foreach($ratings as $rating)
                            <option value="{{ $rating }}" {{ request('rating') == $rating ? 'selected' : '' }}>
                                {{ $rating }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Language Filter -->
                <div class="col-md-2">
                    <label class="form-label fw-bold">Language</label>
                    <select name="language_id" class="form-select">
                        <option value="">All Languages</option>
                        @foreach($languages as $language)
                            <option value="{{ $language->language_id }}" 
                                    {{ request('language_id') == $language->language_id ? 'selected' : '' }}>
                                {{ $language->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Category Filter -->
                <div class="col-md-2">
                    <label class="form-label fw-bold">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->category_id }}" 
                                    {{ request('category_id') == $category->category_id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Year Filter -->
                <div class="col-md-2">
                    <label class="form-label fw-bold">Release Year</label>
                    <select name="release_year" class="form-select">
                        <option value="">All Years</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}" {{ request('release_year') == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Rental Rate Range -->
                <div class="col-md-3">
                    <label class="form-label fw-bold">Rental Rate Range</label>
                    <div class="row g-1">
                        <div class="col">
                            <input type="number" name="rental_rate_min" class="form-control" 
                                   placeholder="Min" step="0.01" min="0" max="99.99"
                                   value="{{ request('rental_rate_min') }}">
                        </div>
                        <div class="col-auto align-self-center">-</div>
                        <div class="col">
                            <input type="number" name="rental_rate_max" class="form-control" 
                                   placeholder="Max" step="0.01" min="0" max="99.99"
                                   value="{{ request('rental_rate_max') }}">
                        </div>
                    </div>
                </div>

                <!-- Length Range -->
                <div class="col-md-3">
                    <label class="form-label fw-bold">Length Range (minutes)</label>
                    <div class="row g-1">
                        <div class="col">
                            <input type="number" name="length_min" class="form-control" 
                                   placeholder="Min" min="1" max="1000"
                                   value="{{ request('length_min') }}">
                        </div>
                        <div class="col-auto align-self-center">-</div>
                        <div class="col">
                            <input type="number" name="length_max" class="form-control" 
                                   placeholder="Max" min="1" max="1000"
                                   value="{{ request('length_max') }}">
                        </div>
                    </div>
                </div>

                <!-- Special Features -->
                <div class="col-md-2">
                    <label class="form-label fw-bold">Special Features</label>
                    <div class="form-check">
                        <input type="checkbox" name="has_special_features" value="1" class="form-check-input"
                               {{ request('has_special_features') ? 'checked' : '' }}>
                        <label class="form-check-label">Has Special Features</label>
                    </div>
                </div>

                <!-- Sort Options -->
                <div class="col-md-2">
                    <label class="form-label fw-bold">Sort By</label>
                    <select name="sort" class="form-select">
                        <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Title</option>
                        <option value="release_year" {{ request('sort') == 'release_year' ? 'selected' : '' }}>Release Year</option>
                        <option value="rental_rate" {{ request('sort') == 'rental_rate' ? 'selected' : '' }}>Rental Rate</option>
                        <option value="length" {{ request('sort') == 'length' ? 'selected' : '' }}>Length</option>
                        <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Rating</option>
                    </select>
                </div>

                <!-- Sort Direction -->
                <div class="col-md-2">
                    <label class="form-label fw-bold">Direction</label>
                    <select name="direction" class="form-select">
                        <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Ascending</option>
                        <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Descending</option>
                    </select>
                </div>

                <!-- Filter Buttons -->
                <div class="col-12">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i>Apply Filters
                    </button>
                    <a href="{{ route('films.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Clear Filters
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('films.recent') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-calendar-star me-1"></i>Recent Films
                </a>
                <a href="{{ route('films.statistics') }}" class="btn btn-outline-info btn-sm">
                    <i class="fas fa-chart-bar me-1"></i>Statistics
                </a>
                @foreach($ratings as $rating)
                    <a href="{{ route('films.by-rating', $rating) }}" class="btn btn-outline-secondary btn-sm">
                        {{ $rating }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Films Grid -->
    @if($films->count() > 0)
        <div class="row">
            @foreach($films as $film)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 shadow-lg border-0 hover-shadow">
                        <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-start">
                            <div>
                                <span class="badge bg-{{ $film->rating_color }} fs-6">{{ $film->rating }}</span>
                                @if($film->release_year)
                                    <span class="badge bg-secondary ms-1">{{ $film->release_year }}</span>
                                @endif
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                        type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('films.show', $film) }}">
                                        <i class="fas fa-eye me-1"></i>View Details</a></li>
                                    <li><a class="dropdown-item" href="{{ route('films.edit', $film) }}">
                                        <i class="fas fa-edit me-1"></i>Edit Film</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('films.destroy', $film) }}" method="POST" 
                                              onsubmit="return confirm('Are you sure you want to delete this film?')" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fas fa-trash me-1"></i>Delete Film
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title fw-bold">{{ $film->title }}</h5>
                            
                            @if($film->description)
                                <p class="card-text text-muted small">
                                    {{ Str::limit($film->description, 120) }}
                                </p>
                            @endif

                            <!-- Film Details -->
                            <div class="row g-2 text-sm">
                                <div class="col-6">
                                    <strong>Language:</strong><br>
                                    <span class="text-muted">{{ $film->language->name ?? 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <strong>Length:</strong><br>
                                    <span class="text-muted">{{ $film->duration_format }}</span>
                                </div>
                                <div class="col-6">
                                    <strong>Rental Rate:</strong><br>
                                    <span class="text-success fw-bold">${{ number_format($film->rental_rate, 2) }}</span>
                                </div>
                                <div class="col-6">
                                    <strong>Age Category:</strong><br>
                                    <span class="text-muted">{{ $film->age_category }}</span>
                                </div>
                            </div>

                            <!-- Categories -->
                            @if($film->categories->count() > 0)
                                <div class="mt-3">
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($film->categories as $category)
                                            <a href="{{ route('films.by-category', $category) }}" 
                                               class="badge bg-primary text-decoration-none">
                                                {{ $category->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Special Features -->
                            @if($film->special_features && count($film->special_features) > 0)
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-star me-1"></i>
                                        {{ implode(', ', $film->special_features) }}
                                    </small>
                                </div>
                            @endif
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('films.show', $film) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-eye me-1"></i>View Details
                                </a>
                                <a href="{{ route('films.edit', $film) }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $films->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-film fa-4x text-muted mb-3"></i>
            <h3 class="text-muted">No films found</h3>
            <p class="text-muted">Try adjusting your search criteria or add some films to get started.</p>
            <a href="{{ route('films.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add First Film
            </a>
        </div>
    @endif
</div>

<style>
.hover-shadow:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    transition: all 0.3s ease;
}

.text-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.gradient-card-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.gradient-card-success {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.gradient-card-info {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}

.gradient-card-warning {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
}
</style>
@endsection