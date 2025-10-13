@extends('layouts.app')

@section('title', "Categoría: {$category->name}")

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-tag me-2"></i>
                        Detalles de la Categoría
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Category Header -->
                    <div class="row mb-4">
                        <div class="col-md-3 text-center">
                            <div class="category-icon bg-primary text-white d-flex align-items-center justify-content-center mx-auto" 
                                 style="width: 80px; height: 80px; border-radius: 12px; font-size: 24px; font-weight: bold;">
                                <i class="fas fa-tag"></i>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <h2 class="mb-2">{{ $category->formatted_name }}</h2>
                            <p class="text-muted mb-2">
                                <i class="fas fa-id-badge me-1"></i>
                                Category ID: <strong>{{ $category->category_id }}</strong>
                            </p>
                            <p class="text-muted mb-2">
                                <i class="fas fa-link me-1"></i>
                                Slug: <strong>{{ $category->slug }}</strong>
                            </p>
                            <p class="text-muted mb-0">
                                <i class="fas fa-clock me-1"></i>
                                Última Actualización: <strong>{{ $category->last_update?->format('F j, Y \a\t g:i A') ?? 'No disponible' }}</strong>
                                @if($category->is_recent)
                                    <span class="badge bg-success ms-2">Recently Added</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Category Information Table -->
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <td width="30%" class="fw-bold text-muted">Category ID:</td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $category->category_id }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Nombre de Categoría:</td>
                                    <td>
                                        <strong>{{ $category->name }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Nombre Formateado:</td>
                                    <td>{{ $category->formatted_name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">URL Slug:</td>
                                    <td>
                                        <code>{{ $category->slug }}</code>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">First Letter:</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $category->first_letter }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Nombre en Mayúsculas:</td>
                                    <td>
                                        <span class="fw-bold">{{ $category->upper_name }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Última Actualización:</td>
                                    <td>
                                        @if($category->last_update)
                                            {{ $category->last_update->format('F j, Y \a\t g:i A') }}
                                            <small class="text-muted">
                                                ({{ $category->last_update->diffForHumans() }})
                                            </small>
                                        @else
                                            <em class="text-muted">Not available</em>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Short Description:</td>
                                    <td>{{ $category->short_description }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Film Statistics (Future implementation) -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Films in Category</h5>
                                    <h3 class="text-primary">
                                        {{-- {{ $category->films_count ?? 0 }} --}}
                                        <span class="text-muted">Coming Soon</span>
                                    </h3>
                                    <small class="text-muted">When Film model is implemented</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Popularity Rank</h5>
                                    <h3 class="text-success">
                                        {{-- {{ $category->popularity_rank ?? 'N/A' }} --}}
                                        <span class="text-muted">Coming Soon</span>
                                    </h3>
                                    <small class="text-muted">Based on film count</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>
                            Volver a Categorías
                        </a>
                        <div>
                            <a href="{{ route('categories.edit', $category) }}" class="btn btn-warning me-2">
                                <i class="fas fa-edit me-1"></i>
                                Editar Categoría
                            </a>
                            <form method="POST" 
                                  action="{{ route('categories.destroy', $category) }}" 
                                  class="d-inline" 
                                  onsubmit="return confirm('¿Está seguro de que desea eliminar la categoría \'{{ $category->name }}\'? Esta acción no se puede deshacer.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash me-1"></i>
                                    Eliminar Categoría
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar with Additional Information -->
        <div class="col-md-4">
            <!-- Category Properties Card -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Category Properties
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Character Count:</span>
                        <span class="badge bg-info">{{ strlen($category->name) }} / 25</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Word Count:</span>
                        <span class="badge bg-secondary">{{ str_word_count($category->name) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Starts With:</span>
                        <span class="badge bg-primary">{{ $category->first_letter }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Recent:</span>
                        @if($category->is_recent)
                            <span class="badge bg-success">Yes</span>
                        @else
                            <span class="badge bg-secondary">No</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Activity Timeline Card -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-history me-2"></i>
                        Activity Timeline
                    </h6>
                </div>
                <div class="card-body">
                    @if($category->last_update)
                        <div class="timeline-item">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="timeline-icon bg-primary">
                                        <i class="fas fa-clock text-white"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">Last Updated</h6>
                                    <p class="text-muted mb-0">{{ $category->last_update->diffForHumans() }}</p>
                                    <small class="text-muted">{{ $category->last_update->format('M j, Y \a\t g:i A') }}</small>
                                </div>
                            </div>
                        </div>
                        
                        @if($category->is_recent)
                            <div class="timeline-item mt-3">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="timeline-icon bg-success">
                                            <i class="fas fa-plus text-white"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">Recently Added</h6>
                                        <p class="text-muted mb-0">This category was added recently</p>
                                        <small class="text-muted">Within the last 30 days</small>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        <p class="text-muted text-center">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            No activity data available
                        </p>
                    @endif
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>
                        Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('categories.edit', $category) }}" class="btn btn-outline-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>
                            Edit This Category
                        </a>
                        <a href="{{ route('categories.create') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>
                            Add New Category
                        </a>
                        <a href="{{ route('categories.alphabetical') }}" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-sort-alpha-down me-1"></i>
                            Alphabetical View
                        </a>
                        <a href="{{ route('categories.popular') }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-star me-1"></i>
                            Popular Categories
                        </a>
                        {{-- 
                        <a href="{{ route('films.index', ['category' => $category->category_id]) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-film me-1"></i>
                            View Films in {{ $category->name }}
                        </a>
                        --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline-icon {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.category-icon {
    transition: transform 0.3s ease;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.category-icon:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 12px rgba(0,0,0,0.15);
}
</style>
@endsection