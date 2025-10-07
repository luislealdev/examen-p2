@extends('layouts.app')

@section('title', 'Film Statistics')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('films.index') }}">Films</a></li>
                    <li class="breadcrumb-item active">Statistics</li>
                </ol>
            </nav>
            <h1 class="display-4 fw-bold text-gradient">
                <i class="fas fa-chart-bar me-3"></i>Film Statistics
            </h1>
            <p class="lead text-muted">Comprehensive analysis of film collection data</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('films.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left me-2"></i>Back to Films
            </a>
        </div>
    </div>

    <!-- Overview Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card gradient-card-primary">
                <div class="card-body text-white text-center">
                    <div class="display-6 fw-bold">{{ number_format($stats['total_films']) }}</div>
                    <div class="small">Total Films</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card gradient-card-success">
                <div class="card-body text-white text-center">
                    <div class="display-6 fw-bold">{{ number_format($stats['recent_films']) }}</div>
                    <div class="small">Recent Films</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card gradient-card-warning">
                <div class="card-body text-white text-center">
                    <div class="display-6 fw-bold">{{ number_format($stats['with_special_features']) }}</div>
                    <div class="small">With Special Features</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card gradient-card-info">
                <div class="card-body text-white text-center">
                    <div class="display-6 fw-bold">${{ number_format($stats['avg_rental_rate'], 2) }}</div>
                    <div class="small">Avg Rental Rate</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Films by Rating -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-star me-2"></i>Films by Rating
                    </h5>
                </div>
                <div class="card-body">
                    @if($stats['by_rating']->count() > 0)
                        @foreach($stats['by_rating'] as $rating => $count)
                            @php
                                $percentage = ($count / $stats['total_films']) * 100;
                                $ratingColors = [
                                    'G' => 'success',
                                    'PG' => 'info', 
                                    'PG-13' => 'warning',
                                    'R' => 'danger',
                                    'NC-17' => 'dark'
                                ];
                                $color = $ratingColors[$rating] ?? 'secondary';
                            @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold">
                                        <span class="badge bg-{{ $color }}">{{ $rating }}</span>
                                    </span>
                                    <span class="text-muted">{{ $count }} films ({{ number_format($percentage, 1) }}%)</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-{{ $color }}" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center">No rating data available</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Films by Language -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-language me-2"></i>Films by Language
                    </h5>
                </div>
                <div class="card-body">
                    @if($stats['by_language']->count() > 0)
                        @foreach($stats['by_language']->take(10) as $language => $count)
                            @php
                                $percentage = ($count / $stats['total_films']) * 100;
                            @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold">{{ $language }}</span>
                                    <span class="text-muted">{{ $count }} films ({{ number_format($percentage, 1) }}%)</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-info" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                        @if($stats['by_language']->count() > 10)
                            <p class="text-muted text-center small">
                                <i class="fas fa-info-circle me-1"></i>
                                Showing top 10 languages only
                            </p>
                        @endif
                    @else
                        <p class="text-muted text-center">No language data available</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Films by Decade -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-warning text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar me-2"></i>Films by Decade
                    </h5>
                </div>
                <div class="card-body">
                    @if($stats['by_decade']->count() > 0)
                        @foreach($stats['by_decade'] as $decade => $count)
                            @php
                                $percentage = ($count / $stats['total_films']) * 100;
                            @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold">{{ $decade }}s</span>
                                    <span class="text-muted">{{ $count }} films ({{ number_format($percentage, 1) }}%)</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-warning" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center">No decade data available</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Average Statistics -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-calculator me-2"></i>Average Statistics
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                                <div>
                                    <h6 class="fw-bold mb-0">Average Rental Rate</h6>
                                    <small class="text-muted">Per film rental</small>
                                </div>
                                <div class="text-end">
                                    <div class="h5 mb-0 text-success">${{ number_format($stats['avg_rental_rate'], 2) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                                <div>
                                    <h6 class="fw-bold mb-0">Average Length</h6>
                                    <small class="text-muted">Film duration</small>
                                </div>
                                <div class="text-end">
                                    <div class="h5 mb-0 text-info">{{ number_format($stats['avg_length']) }} min</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                                <div>
                                    <h6 class="fw-bold mb-0">Average Replacement Cost</h6>
                                    <small class="text-muted">If film is lost</small>
                                </div>
                                <div class="text-end">
                                    <div class="h5 mb-0 text-danger">${{ number_format($stats['avg_replacement_cost'], 2) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="card shadow-lg border-0 mt-4">
        <div class="card-header bg-gradient-secondary text-white">
            <h5 class="mb-0">
                <i class="fas fa-link me-2"></i>Quick Navigation
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-3">
                    <a href="{{ route('films.recent') }}" class="btn btn-outline-primary w-100">
                        <i class="fas fa-calendar-star me-1"></i>Recent Films
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('films.index') }}?has_special_features=1" class="btn btn-outline-warning w-100">
                        <i class="fas fa-star me-1"></i>Special Features
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-info w-100">
                        <i class="fas fa-tags me-1"></i>Browse Categories
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('languages.index') }}" class="btn btn-outline-success w-100">
                        <i class="fas fa-language me-1"></i>Browse Languages
                    </a>
                </div>
            </div>
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

.gradient-card-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.gradient-card-success {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}

.gradient-card-info {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.gradient-card-warning {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}

.bg-gradient-secondary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>
@endsection