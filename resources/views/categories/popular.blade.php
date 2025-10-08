@extends('layouts.app')

@section('title', 'Categorías Populares')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">
                    <i class="fas fa-star me-2"></i>
                    Categorías Populares
                </h2>
                <div class="d-flex gap-2">
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-list me-1"></i>
                        Vista de Lista
                    </a>
                    <a href="{{ route('categories.alphabetical') }}" class="btn btn-outline-info">
                        <i class="fas fa-sort-alpha-down me-1"></i>
                        Vista Alfabética
                    </a>
                    <a href="{{ route('categories.create') }}" class="btn btn-gradient-primary">
                        <i class="fas fa-plus me-1"></i>
                        Agregar Categoría
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Alert -->
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Próximamente:</strong> Las clasificaciones de popularidad de categorías estarán disponibles cuando se implemente el modelo de Películas. 
        Actualmente mostrando todas las categorías en orden alfabético.
    </div>

    @if($categories->count() > 0)
        <!-- Categories Grid -->
        <div class="row">
            @foreach($categories as $category)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm popular-category-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <div class="category-icon bg-gradient-{{ $loop->index % 6 + 1 }} text-white d-flex align-items-center justify-content-center" 
                                         style="width: 60px; height: 60px; border-radius: 12px; font-size: 20px; font-weight: bold;">
                                        <i class="fas fa-tag"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-1">{{ $category->formatted_name }}</h5>
                                    <p class="text-muted mb-0">
                                        <small>ID de Categoría: {{ $category->category_id }}</small>
                                        @if($category->is_recent)
                                            <span class="badge bg-success ms-1">Nuevo</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="text-end">
                                    <!-- Future: Show popularity rank -->
                                    <div class="rank-badge bg-primary text-white d-flex align-items-center justify-content-center" 
                                         style="width: 30px; height: 30px; border-radius: 50%; font-size: 12px; font-weight: bold;">
                                        {{ $loop->iteration }}
                                    </div>
                                </div>
                            </div>

                            <!-- Category Details -->
                            <div class="category-details">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="stat-item">
                                            <h6 class="text-primary mb-0">
                                                {{-- {{ $category->films_count ?? 0 }} --}}
                                                <span class="text-muted">0</span>
                                            </h6>
                                            <small class="text-muted">Películas</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="stat-item">
                                            <h6 class="text-success mb-0">
                                                {{-- {{ $category->popularity_score ?? 'N/A' }} --}}
                                                <span class="text-muted">N/A</span>
                                            </h6>
                                            <small class="text-muted">Puntuación</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Category Meta Info -->
                            <div class="mt-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">Slug:</span>
                                    <code class="small">{{ $category->slug }}</code>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Actualizado:</span>
                                    <small class="text-muted">{{ $category->last_update?->diffForHumans() ?? 'N/A' }}</small>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="mt-3 d-flex gap-2">
                                <a href="{{ route('categories.show', $category) }}" 
                                   class="btn btn-outline-info btn-sm flex-fill">
                                    <i class="fas fa-eye me-1"></i>
                                    Ver
                                </a>
                                <a href="{{ route('categories.edit', $category) }}" 
                                   class="btn btn-outline-warning btn-sm flex-fill">
                                    <i class="fas fa-edit me-1"></i>
                                    Editar
                                </a>
                                {{-- Future: View films in category
                                <a href="{{ route('films.index', ['category' => $category->category_id]) }}" 
                                   class="btn btn-outline-primary btn-sm flex-fill">
                                    <i class="fas fa-film me-1"></i>
                                    Films
                                </a>
                                --}}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($categories->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $categories->links() }}
            </div>
        @endif

        <!-- Future Features Info -->
        <div class="card mt-4 border-info">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="fas fa-rocket me-2"></i>
                    Próximas Funcionalidades
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-chart-line text-primary me-2"></i>Clasificación por Cantidad de Películas</h6>
                        <p class="text-muted">Las categorías se clasificarán por el número de películas en cada categoría.</p>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-thumbs-up text-success me-2"></i>Puntuación de Popularidad</h6>
                        <p class="text-muted">Puntuación avanzada basada en alquileres, calificaciones y preferencias del usuario.</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-fire text-danger me-2"></i>Categorías en Tendencia</h6>
                        <p class="text-muted">Categorías con la mayor actividad y participación reciente.</p>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-filter text-warning me-2"></i>Filtros Avanzados</h6>
                        <p class="text-muted">Filtrar por cantidad de películas, calificación, rangos de fechas de lanzamiento y más.</p>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-star fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No Hay Categorías Disponibles</h5>
                <p class="text-muted">Comienza agregando tu primera categoría para ver las clasificaciones de popularidad.</p>
                <a href="{{ route('categories.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>
                    Agregar Primera Categoría
                </a>
            </div>
        </div>
    @endif
</div>

<style>
/* Gradient backgrounds for category icons */
.bg-gradient-1 { background: linear-gradient(45deg, #007bff, #0056b3); }
.bg-gradient-2 { background: linear-gradient(45deg, #28a745, #1e7e34); }
.bg-gradient-3 { background: linear-gradient(45deg, #dc3545, #bd2130); }
.bg-gradient-4 { background: linear-gradient(45deg, #ffc107, #d39e00); }
.bg-gradient-5 { background: linear-gradient(45deg, #17a2b8, #117a8b); }
.bg-gradient-6 { background: linear-gradient(45deg, #6f42c1, #59359a); }

/* Popular category card hover effects */
.popular-category-card {
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.popular-category-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
    border-color: rgba(0,123,255,0.3);
}

/* Category icon animations */
.category-icon {
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.category-icon::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: rgba(255,255,255,0.1);
    transform: rotate(45deg) translateX(-100%);
    transition: transform 0.5s ease;
}

.popular-category-card:hover .category-icon::before {
    transform: rotate(45deg) translateX(100%);
}

.popular-category-card:hover .category-icon {
    transform: scale(1.1);
}

/* Rank badge */
.rank-badge {
    font-family: 'Arial Black', sans-serif;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
}

.popular-category-card:hover .rank-badge {
    transform: scale(1.2);
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
}

/* Stat items */
.stat-item {
    transition: all 0.3s ease;
}

.popular-category-card:hover .stat-item {
    transform: scale(1.05);
}

/* Animation for new badges */
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

.badge.bg-success {
    animation: pulse 2s infinite;
}

/* Button hover effects */
.btn {
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}
</style>

<script>
// Add loading animation when clicking action buttons
document.querySelectorAll('.btn').forEach(button => {
    button.addEventListener('click', function(e) {
        const icon = this.querySelector('i');
        if (icon && !icon.classList.contains('fa-spinner')) {
            const originalClass = icon.className;
            icon.className = 'fas fa-spinner fa-spin me-1';
            
            setTimeout(() => {
                icon.className = originalClass;
            }, 1000);
        }
    });
});

// Add entrance animation for cards
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.popular-category-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>
@endsection