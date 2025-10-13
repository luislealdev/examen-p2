@extends('layouts.app')

@section('title', 'Gestión de Inventario')

@section('content')
<div class="container">
    <!-- Header with Title and Add Button -->
    <div class="row align-items-center mb-4">
        <div class="col">
            <h1 class="display-4 fw-bold text-primary">
                <i class="fas fa-boxes me-3"></i>Gestión de Inventario
            </h1>
            <p class="lead text-muted">Rastrear y gestionar el inventario de la tienda con {{ number_format($stats['total_items']) }} artículos</p>
        </div>
        <div class="col-auto">
            <div class="btn-group">
                <a href="{{ route('inventories.create') }}" class="btn btn-gradient-primary">
                    <i class="fas fa-plus me-2"></i>Agregar Artículo
                </a>
                <a href="{{ route('inventories.bulk-create') }}" class="btn btn-gradient-success">
                    <i class="fas fa-layer-group me-2"></i>Agregar en Lote
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-custom border-0">
                <div class="card-header bg-gradient-primary text-white border-0">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">Total de Artículos</h6>
                            <h3 class="mb-0">{{ number_format($stats['total_items']) }}</h3>
                        </div>
                        <div class="opacity-75">
                            <i class="fas fa-boxes fa-2x"></i>
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
                            <h6 class="card-title mb-0">Adiciones Recientes</h6>
                            <h3 class="mb-0">{{ number_format($stats['recent_additions']) }}</h3>
                        </div>
                        <div class="opacity-75">
                            <i class="fas fa-calendar-plus fa-2x"></i>
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
                            <h6 class="card-title mb-0">Artículos de Alto Valor</h6>
                            <h3 class="mb-0">{{ number_format($stats['high_value_items']) }}</h3>
                        </div>
                        <div class="opacity-75">
                            <i class="fas fa-gem fa-2x"></i>
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
                            <h3 class="mb-0">${{ number_format($stats['avg_rental_rate'], 2) }}</h3>
                        </div>
                        <div class="opacity-75">
                            <i class="fas fa-dollar-sign fa-2x"></i>
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
            <form method="GET" action="{{ route('inventories.index') }}" class="row g-3">
                <!-- Búsqueda -->
                <div class="col-md-4">
                    <label class="form-label fw-bold">Buscar</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Buscar por título de película..."
                               value="{{ request('search') }}">
                    </div>
                </div>

                <!-- Filtro de Película -->
                <div class="col-md-3">
                    <label class="form-label fw-bold">Película</label>
                    <select name="film_id" class="form-select">
                        <option value="">Todas las Películas</option>
                        @foreach($films as $film)
                            <option value="{{ $film->film_id }}" {{ request('film_id') == $film->film_id ? 'selected' : '' }}>
                                {{ $film->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtro de Tienda -->
                <div class="col-md-2">
                    <label class="form-label fw-bold">Tienda</label>
                    <select name="store_id" class="form-select">
                        <option value="">Todas las Tiendas</option>
                        @foreach($stores as $store)
                            <option value="{{ $store->store_id }}" {{ request('store_id') == $store->store_id ? 'selected' : '' }}>
                                Tienda #{{ $store->store_id }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtro de Clasificación -->
                <div class="col-md-3">
                    <label class="form-label fw-bold">Clasificación de Película</label>
                    <select name="rating" class="form-select">
                        <option value="">Todas las Clasificaciones</option>
                        @foreach($ratings as $rating)
                            <option value="{{ $rating }}" {{ request('rating') == $rating ? 'selected' : '' }}>
                                {{ $rating }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtro de Categoría -->
                <div class="col-md-3">
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

                <!-- Filtro de Idioma -->
                <div class="col-md-3">
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

                <!-- Filtro Reciente -->
                <div class="col-md-2">
                    <label class="form-label fw-bold">Días Recientes</label>
                    <select name="recent_days" class="form-select">
                        <option value="">Todo el Tiempo</option>
                        <option value="7" {{ request('recent_days') == '7' ? 'selected' : '' }}>Últimos 7 días</option>
                        <option value="30" {{ request('recent_days') == '30' ? 'selected' : '' }}>Últimos 30 días</option>
                        <option value="90" {{ request('recent_days') == '90' ? 'selected' : '' }}>Últimos 90 días</option>
                    </select>
                </div>

                <!-- Filtro de Alto Valor -->
                <div class="col-md-2">
                    <label class="form-label fw-bold">Valor</label>
                    <div class="form-check">
                        <input type="checkbox" name="high_value" value="1" class="form-check-input"
                               {{ request('high_value') ? 'checked' : '' }}>
                        <label class="form-check-label">Alto Valor ($4+)</label>
                    </div>
                </div>

                <!-- Opciones de Ordenamiento -->
                <div class="col-md-2">
                    <label class="form-label fw-bold">Ordenar Por</label>
                    <select name="sort" class="form-select">
                        <option value="alphabetical" {{ request('sort') == 'alphabetical' ? 'selected' : '' }}>Título de Película</option>
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Más Recientes</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Más Antiguos</option>
                        <option value="inventory_id" {{ request('sort') == 'inventory_id' ? 'selected' : '' }}>ID de Inventario</option>
                        <option value="store_id" {{ request('sort') == 'store_id' ? 'selected' : '' }}>ID de Tienda</option>
                    </select>
                </div>

                <!-- Botones de Filtro -->
                <div class="col-12">
                    <button type="submit" class="btn btn-gradient-primary me-2">
                        <i class="fas fa-search me-1"></i>Aplicar Filtros
                    </button>
                    <a href="{{ route('inventories.index') }}" class="btn btn-outline-secondary">
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
                <a href="{{ route('inventories.recent') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-calendar-plus me-1"></i>Recent Items
                </a>
                <a href="{{ route('inventories.high-value') }}" class="btn btn-outline-warning btn-sm">
                    <i class="fas fa-gem me-1"></i>High Value
                </a>
                <a href="{{ route('inventories.statistics') }}" class="btn btn-outline-info btn-sm">
                    <i class="fas fa-chart-bar me-1"></i>Statistics
                </a>
                @foreach($stores as $store)
                    <a href="{{ route('inventories.by-store', $store) }}" class="btn btn-outline-secondary btn-sm">
                        Store #{{ $store->store_id }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Inventory Table -->
    @if($inventories->count() > 0)
        <div class="card shadow-lg border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Película</th>
                                <th>Tienda</th>
                                <th>Clasificación</th>
                                <th>Idioma</th>
                                <th>Precio de Alquiler</th>
                                <th>Estado</th>
                                <th>Última Actualización</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inventories as $inventory)
                                <tr>
                                    <td>
                                        <span class="fw-bold text-primary">#{{ $inventory->inventory_id }}</span>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $inventory->film_title }}</strong>
                                            @if($inventory->film && $inventory->film->release_year)
                                                <br><small class="text-muted">({{ $inventory->film->release_year }})</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $inventory->store_location }}</span>
                                    </td>
                                    <td>
                                        @if($inventory->film)
                                            <span class="badge bg-{{ $inventory->film->rating_color }}">
                                                {{ $inventory->film_rating }}
                                            </span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $inventory->film->language->name ?? 'N/A' }}
                                    </td>
                                    <td>
                                        <span class="text-success fw-bold">${{ number_format($inventory->rental_rate, 2) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $inventory->status_color }}">
                                            {{ $inventory->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <span title="{{ $inventory->last_update_format }}">
                                            {{ $inventory->last_update_human }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('inventories.show', $inventory) }}" 
                                               class="btn btn-outline-primary" title="Ver Detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('inventories.edit', $inventory) }}" 
                                               class="btn btn-outline-secondary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('inventories.destroy', $inventory) }}" 
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('¿Estás seguro de que quieres eliminar este elemento del inventario?')">>
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $inventories->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-boxes fa-4x text-muted mb-3"></i>
            <h3 class="text-muted">No se encontraron elementos de inventario</h3>
            <p class="text-muted">Intenta ajustar tus criterios de búsqueda o agrega algunos elementos al inventario para comenzar.</p>
            <div class="mt-3">
                <a href="{{ route('inventories.create') }}" class="btn btn-primary me-2">
                    <i class="fas fa-plus me-2"></i>Agregar Primer Elemento
                </a>
                <a href="{{ route('inventories.bulk-create') }}" class="btn btn-success">
                    <i class="fas fa-layer-group me-2"></i>Agregar Elementos en Lote
                </a>
            </div>
        </div>
    @endif
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