@extends('layouts.app')

@section('title', 'Films in ' . $category->name)

@section('content')
<div class="container">
    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('films.index') }}">Films</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('categories.index') }}">Categories</a></li>
                    <li class="breadcrumb-item active">{{ $category->name }}</li>
                </ol>
            </nav>
            <h1 class="display-4 fw-bold text-gradient">
                <i class="fas fa-tag me-3"></i>{{ $category->name }} Films
            </h1>
            <p class="lead text-muted">{{ $films->total() }} {{ Str::plural('film', $films->total()) }} in this category</p>
        </div>
        <div class="col-auto">
            <div class="btn-group">
                <a href="{{ route('categories.show', $category) }}" class="btn btn-outline-primary">
                    <i class="fas fa-info-circle me-2"></i>Category Details
                </a>
                <a href="{{ route('films.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add New Film
                </a>
            </div>
        </div>
    </div>

    <!-- Category Info Card -->
    <div class="card shadow-lg border-0 mb-4">
        <div class="card-body bg-gradient-primary text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="fw-bold mb-2">{{ $category->name }}</h3>
                    @if($category->description)
                        <p class="mb-0 opacity-90">{{ $category->description }}</p>
                    @endif
                </div>
                <div class="col-auto">
                    <div class="text-center">
                        <div class="display-6 fw-bold">{{ $films->total() }}</div>
                        <div class="small">{{ Str::plural('Film', $films->total()) }}</div>
                    </div>
                </div>
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

                            <!-- Other Categories -->
                            <!-- @php
                                $otherCategories = $film->categories->filter(fn($cat) => $cat->category_id !== $category->category_id);
                            @endphp -->
                            @if($otherCategories->count() > 0)
                                <div class="mt-3">
                                    <small class="text-muted">Also in:</small>
                                    <div class="d-flex flex-wrap gap-1 mt-1">
                                        @foreach($otherCategories as $otherCategory)
                                            <a href="{{ route('films.by-category', $otherCategory) }}" 
                                               class="badge bg-outline-primary text-decoration-none">
                                                {{ $otherCategory->name }}
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
                                        {{ implode(', ', array_slice($film->special_features, 0, 2)) }}
                                        @if(count($film->special_features) > 2)
                                            <span class="text-primary">+{{ count($film->special_features) - 2 }} more</span>
                                        @endif
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
            <p class="text-muted">There are no films in the {{ $category->name }} category yet.</p>
            <div class="mt-3">
                <a href="{{ route('films.create') }}" class="btn btn-primary me-2">
                    <i class="fas fa-plus me-2"></i>Add New Film
                </a>
                <a href="{{ route('films.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Browse All Films
                </a>
            </div>
        </div>
    @endif

    <!-- Related Categories -->
    <div class="mt-5">
        <h4 class="fw-bold mb-3">
            <i class="fas fa-tags me-2"></i>Explore Other Categories
        </h4>
        <div class="row">
            @php
                $relatedCategories = \App\Models\Category::where('category_id', '!=', $category->category_id)
                    ->withCount('films')
                    ->orderBy('films_count', 'desc')
                    ->limit(6)
                    ->get();
            @endphp
            @foreach($relatedCategories as $relatedCategory)
                <div class="col-md-4 col-sm-6 mb-3">
                    <div class="card border-0 bg-light">
                        <div class="card-body text-center">
                            <h6 class="card-title fw-bold">{{ $relatedCategory->name }}</h6>
                            <p class="card-text text-muted small">{{ $relatedCategory->films_count }} {{ Str::plural('film', $relatedCategory->films_count) }}</p>
                            <a href="{{ route('films.by-category', $relatedCategory) }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-arrow-right me-1"></i>View Films
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
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

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>
@endsection