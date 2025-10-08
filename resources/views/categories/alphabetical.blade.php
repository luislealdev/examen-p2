@extends('layouts.app')

@section('title', 'Vista Alfabética de Categorías')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">
                    <i class="fas fa-sort-alpha-down me-2"></i>
                    Categorías - Vista Alfabética
                </h2>
                <div class="d-flex gap-2">
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-list me-1"></i>
                        Vista de Lista
                    </a>
                    <a href="{{ route('categories.popular') }}" class="btn btn-outline-success">
                        <i class="fas fa-star me-1"></i>
                        Categorías Populares
                    </a>
                    <a href="{{ route('categories.create') }}" class="btn btn-gradient-primary">
                        <i class="fas fa-plus me-1"></i>
                        Agregar Categoría
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if($categoryGroups->count() > 0)
        <!-- Quick Navigation -->
        <div class="card mb-4">
            <div class="card-body">
                <h6 class="card-title">Navegación Rápida</h6>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($categoryGroups->keys()->sort() as $letter)
                        <a href="#letter-{{ $letter }}" class="btn btn-outline-secondary btn-sm">
                            {{ strtoupper($letter) }}
                            <span class="badge bg-secondary ms-1">{{ $categoryGroups[$letter]->count() }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Category Groups -->
        @foreach($categoryGroups->keys()->sort() as $letter)
            <div class="card mb-4" id="letter-{{ $letter }}">
                <div class="card-header">
                    <h4 class="mb-0">
                        <span class="badge bg-primary me-2" style="font-size: 1.2em;">{{ strtoupper($letter) }}</span>
                                                Categorías que empiezan por "{{ strtoupper($letter) }}"
                        <span class="badge bg-secondary ms-2">{{ $categoryGroups[$letter]->count() }}</span>
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($categoryGroups[$letter] as $category)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card h-100 border-0 shadow-sm category-card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <div class="category-icon bg-gradient-{{ ($loop->parent->index * 3 + $loop->index) % 6 + 1 }} text-white d-flex align-items-center justify-content-center" 
                                                     style="width: 50px; height: 50px; border-radius: 8px; font-weight: bold;">
                                                    <i class="fas fa-tag"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="card-title mb-1">{{ $category->formatted_name }}</h6>
                                                <p class="card-text text-muted mb-2">
                                                    <small>
                                                        ID: {{ $category->category_id }}
                                                        @if($category->is_recent)
                                                            <span class="badge bg-success ms-1">Nuevo</span>
                                                        @endif
                                                    </small>
                                                </p>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('categories.show', $category) }}" 
                                                       class="btn btn-outline-info" 
                                                       title="Ver Detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('categories.edit', $category) }}" 
                                                       class="btn btn-outline-warning" 
                                                       title="Editar Categoría">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Back to Top Button -->
        <div class="text-center mb-4">
            <a href="#" class="btn btn-outline-secondary" onclick="window.scrollTo(0,0); return false;">
                <i class="fas fa-arrow-up me-1"></i>
                Volver Arriba
            </a>
        </div>
    @else
        <!-- Empty State -->
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No Hay Categorías Disponibles</h5>
                <p class="text-muted">Comienza agregando tu primera categoría para ver la vista alfabética.</p>
                <a href="{{ route('categories.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>
                    Agregar Primera Categoría
                </a>
            </div>
        </div>
    @endif

    <!-- Summary Card -->
    @if($categoryGroups->count() > 0)
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    Estadísticas de Resumen
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <h4 class="text-primary">{{ $categoryGroups->sum(function($group) { return $group->count(); }) }}</h4>
                        <small class="text-muted">Total de Categorías</small>
                    </div>
                    <div class="col-md-3">
                        <h4 class="text-success">{{ $categoryGroups->count() }}</h4>
                        <small class="text-muted">Grupos de Letras</small>
                    </div>
                    <div class="col-md-3">
                        <h4 class="text-info">{{ $categoryGroups->max(function($group) { return $group->count(); }) }}</h4>
                        <small class="text-muted">Grupo Más Grande</small>
                    </div>
                    <div class="col-md-3">
                        <h4 class="text-warning">{{ round($categoryGroups->avg(function($group) { return $group->count(); }), 1) }}</h4>
                        <small class="text-muted">Promedio por Letra</small>
                    </div>
                </div>
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

/* Card hover effects */
.category-card {
    transition: all 0.3s ease;
}

.category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

/* Category icon hover effects */
.category-icon {
    transition: all 0.3s ease;
}

.category-card:hover .category-icon {
    transform: scale(1.1) rotate(5deg);
}

/* Scroll margin for smooth navigation */
#letter-a, #letter-b, #letter-c, #letter-d, #letter-e, #letter-f, #letter-g, #letter-h, #letter-i, #letter-j, #letter-k, #letter-l, #letter-m, #letter-n, #letter-o, #letter-p, #letter-q, #letter-r, #letter-s, #letter-t, #letter-u, #letter-v, #letter-w, #letter-x, #letter-y, #letter-z {
    scroll-margin-top: 100px;
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
</style>

<script>
// Smooth scrolling for navigation links
document.querySelectorAll('a[href^="#letter-"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Add loading animation when navigating
document.querySelectorAll('.btn-group a').forEach(link => {
    link.addEventListener('click', function() {
        const icon = this.querySelector('i');
        const originalClass = icon.className;
        icon.className = 'fas fa-spinner fa-spin';
        
        setTimeout(() => {
            icon.className = originalClass;
        }, 1000);
    });
});
</script>
@endsection