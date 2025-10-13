@extends('layouts.app')

@section('title', 'Films in ' . $language->name)

@section('content')
<div class="container">
    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('films.index') }}">Films</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('languages.index') }}">Languages</a></li>
                    <li class="breadcrumb-item active">{{ $language->name }}</li>
                </ol>
            </nav>
            <h1 class="display-4 fw-bold text-gradient">
                <i class="fas fa-language me-3"></i>{{ $language->name }} Films
            </h1>
            <p class="lead text-muted">{{ $films->total() }} {{ Str::plural('film', $films->total()) }} in {{ $language->name }}</p>
        </div>
        <div class="col-auto">
            <div class="btn-group">
                <a href="{{ route('languages.show', $language) }}" class="btn btn-outline-primary">
                    <i class="fas fa-info-circle me-2"></i>Language Details
                </a>
                <a href="{{ route('films.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add New Film
                </a>
            </div>
        </div>
    </div>

    <!-- Language Info Card -->
    <div class="card shadow-lg border-0 mb-4">
        <div class="card-body bg-gradient-info text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="fw-bold mb-0">{{ $language->name }}</h3>
                    <small class="opacity-90">Language Code: {{ strtoupper(substr($language->name, 0, 2)) }}</small>
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
                                @if($film->original_language_id && $film->original_language_id !== $film->language_id)
                                    <span class="badge bg-warning text-dark ms-1" title="Dubbed from {{ $film->originalLanguage->name ?? 'Unknown' }}">
                                        <i class="fas fa-globe"></i>
                                    </span>
                                @endif
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
                                    <span class="text-primary fw-bold">{{ $film->language->name ?? 'N/A' }}</span>
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

                            <!-- Original Language Info -->
                            @if($film->original_language_id && $film->original_language_id !== $film->language_id)
                                <div class="mt-3">
                                    <small class="text-warning">
                                        <i class="fas fa-globe me-1"></i>
                                        Originally in {{ $film->originalLanguage->name ?? 'Unknown' }}
                                    </small>
                                </div>
                            @endif

                            <!-- Categories -->
                            @if($film->categories->count() > 0)
                                <div class="mt-3">
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($film->categories->take(3) as $category)
                                            <a href="{{ route('films.by-category', $category) }}" 
                                               class="badge bg-primary text-decoration-none">
                                                {{ $category->name }}
                                            </a>
                                        @endforeach
                                        @if($film->categories->count() > 3)
                                            <span class="badge bg-light text-dark">+{{ $film->categories->count() - 3 }} more</span>
                                        @endif
                                    </div>
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
            <i class="fas fa-language fa-4x text-muted mb-3"></i>
            <h3 class="text-muted">No films found</h3>
            <p class="text-muted">There are no films in {{ $language->name }} yet.</p>
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

.bg-gradient-info {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}
</style>
@endsection