@extends('layouts.app')

@section('title', 'Películas')

@section('content')
<div class="container">
    <!-- Header with Title and Add Button -->
    <div class="row align-items-center mb-4">
        <div class="col">
            <h1 class="display-4 fw-bold text-primary">
                <i class="fas fa-film me-3"></i>Colección de Películas
            </h1>
            <p class="lead text-muted">Explora nuestra extensa colección de {{ number_format($totalFilms) }} películas</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('films.create') }}" class="btn btn-gradient-primary btn-lg shadow-custom">
                <i class="fas fa-plus me-2"></i>Agregar Nueva Película
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-custom border-0">
                <div class="card-header bg-gradient-primary text-white border-0">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">Total de Películas</h6>
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
            <div class="card shadow-custom border-0">
                <div class="card-header bg-gradient-success text-white border-0">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">Películas Recientes</h6>
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
            <div class="card shadow-custom border-0">
                <div class="card-header bg-gradient-info text-white border-0">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">Precio Promedio</h6>
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
            <div class="card shadow-custom border-0">
                <div class="card-header bg-gradient-warning text-white border-0">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">Duración Promedio</h6>
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
    <div class="card shadow-custom border-0 mb-4">
        <div class="card-header bg-gradient-light text-dark border-0">
            <h5 class="mb-0">
                <i class="fas fa-filter me-2"></i>Filtros y Búsqueda
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('films.index') }}" class="row g-3">
                <!-- Search -->
                <div class="col-md-4">
                    <label class="form-label fw-bold">Buscar</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Buscar por título o descripción..."
                               value="{{ request('search') }}">
                    </div>
                </div>

                <!-- Rating Filter -->
                <div class="col-md-2">
                    <label class="form-label fw-bold">Clasificación</label>
                    <select name="rating" class="form-select">
                        <option value="">Todas las Clasificaciones</option>
                        @foreach($ratings as $rating)
                            <option value="{{ $rating }}" {{ request('rating') == $rating ? 'selected' : '' }}>
                                {{ $rating }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Language Filter -->
                <div class="col-md-2">
                    <label class="form-label fw-bold">Idioma</label>
                    <select name="language_id" class="form-select">
                        <option value="">Todos los Idiomas</option>
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
                    <label class="form-label fw-bold">Categoría</label>
                    <select name="category_id" class="form-select">
                        <option value="">Todas las Categorías</option>
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
                    <label class="form-label fw-bold">Año de Lanzamiento</label>
                    <select name="release_year" class="form-select">
                        <option value="">Todos los Años</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}" {{ request('release_year') == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Rental Rate Range -->
                <div class="col-md-3">
                    <label class="form-label fw-bold">Rango de Precio de Alquiler</label>
                    <div class="row g-1">
                        <div class="col">
                            <input type="number" name="rental_rate_min" class="form-control" 
                                   placeholder="Mín" step="0.01" min="0" max="99.99"
                                   value="{{ request('rental_rate_min') }}">
                        </div>
                        <div class="col-auto align-self-center">-</div>
                        <div class="col">
                            <input type="number" name="rental_rate_max" class="form-control" 
                                   placeholder="Máx" step="0.01" min="0" max="99.99"
                                   value="{{ request('rental_rate_max') }}">
                        </div>
                    </div>
                </div>

                <!-- Length Range -->
                <div class="col-md-3">
                    <label class="form-label fw-bold">Rango de Duración (minutos)</label>
                    <div class="row g-1">
                        <div class="col">
                            <input type="number" name="length_min" class="form-control" 
                                   placeholder="Mín" min="1" max="1000"
                                   value="{{ request('length_min') }}">
                        </div>
                        <div class="col-auto align-self-center">-</div>
                        <div class="col">
                            <input type="number" name="length_max" class="form-control" 
                                   placeholder="Máx" min="1" max="1000"
                                   value="{{ request('length_max') }}">
                        </div>
                    </div>
                </div>

                <!-- Special Features -->
                <div class="col-md-2">
                    <label class="form-label fw-bold">Características Especiales</label>
                    <div class="form-check">
                        <input type="checkbox" name="has_special_features" value="1" class="form-check-input"
                               {{ request('has_special_features') ? 'checked' : '' }}>
                        <label class="form-check-label">Tiene Características Especiales</label>
                    </div>
                </div>

                <!-- Sort Options -->
                <div class="col-md-2">
                    <label class="form-label fw-bold">Ordenar Por</label>
                    <select name="sort" class="form-select">
                        <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Título</option>
                        <option value="release_year" {{ request('sort') == 'release_year' ? 'selected' : '' }}>Año de Lanzamiento</option>
                        <option value="rental_rate" {{ request('sort') == 'rental_rate' ? 'selected' : '' }}>Precio de Alquiler</option>
                        <option value="length" {{ request('sort') == 'length' ? 'selected' : '' }}>Duración</option>
                        <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Clasificación</option>
                    </select>
                </div>

                <!-- Sort Direction -->
                <div class="col-md-2">
                    <label class="form-label fw-bold">Dirección</label>
                    <select name="direction" class="form-select">
                        <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Ascendente</option>
                        <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Descendente</option>
                    </select>
                </div>

                <!-- Filter Buttons -->
                <div class="col-12">
                    <button type="submit" class="btn btn-gradient-primary me-2">
                        <i class="fas fa-search me-1"></i>Aplicar Filtros
                    </button>
                    <a href="{{ route('films.index') }}" class="btn btn-gradient-secondary">
                        <i class="fas fa-times me-1"></i>Limpiar Filtros
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
                    <i class="fas fa-calendar-star me-1"></i>Películas Recientes
                </a>
                <a href="{{ route('films.statistics') }}" class="btn btn-outline-info btn-sm">
                    <i class="fas fa-chart-bar me-1"></i>Estadísticas
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
                    <div class="card h-100 shadow-custom border-0 hover-shadow">
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
                                        <i class="fas fa-eye me-1"></i>Ver Detalles</a></li>
                                    <li><a class="dropdown-item" href="{{ route('films.edit', $film) }}">
                                        <i class="fas fa-edit me-1"></i>Editar Película</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('films.destroy', $film) }}" method="POST" 
                                              onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta película?')" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fas fa-trash me-1"></i>Eliminar Película
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
                                    <strong>Idioma:</strong><br>
                                    <span class="text-muted">{{ $film->language->name ?? 'N/A' }}</span>
                                </div>
                                <div class="col-6">
                                    <strong>Duración:</strong><br>
                                    <span class="text-muted">{{ $film->duration_format }}</span>
                                </div>
                                <div class="col-6">
                                    <strong>Precio de Alquiler:</strong><br>
                                    <span class="text-success fw-bold">${{ number_format($film->rental_rate, 2) }}</span>
                                </div>
                                <div class="col-6">
                                    <strong>Categoría de Edad:</strong><br>
                                    <span class="text-muted">{{ $film->age_category }}</span>
                                </div>
                            </div>

                            <!-- Category -->
                            @if($film->category)
                                <div class="mt-3">
                                    <div class="d-flex flex-wrap gap-1">
                                        <a href="{{ route('films.by-category', $film->category) }}" 
                                           class="badge bg-primary text-decoration-none">
                                            {{ $film->category->name }}
                                        </a>
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
                                <a href="{{ route('films.show', $film) }}" class="btn btn-gradient-primary btn-sm">
                                    <i class="fas fa-eye me-1"></i>Ver Detalles
                                </a>
                                <a href="{{ route('films.edit', $film) }}" class="btn btn-gradient-secondary btn-sm">
                                    <i class="fas fa-edit me-1"></i>Editar
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
            <h3 class="text-muted">No se encontraron películas</h3>
            <p class="text-muted">Intenta ajustar tus criterios de búsqueda o agrega algunas películas para comenzar.</p>
            <a href="{{ route('films.create') }}" class="btn btn-gradient-primary">
                <i class="fas fa-plus me-2"></i>Agregar Primera Película
            </a>
        </div>
    @endif
</div>
@endsection