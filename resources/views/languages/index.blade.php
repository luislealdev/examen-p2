@extends('layouts.app')

@section('title', 'Idiomas')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0 text-primary">
                    <i class="fas fa-language me-2"></i>
                    Gestión de Idiomas
                </h2>
                <div class="d-flex gap-2">
                    <a href="{{ route('languages.alphabetical') }}" class="btn btn-outline-primary">
                        <i class="fas fa-sort-alpha-down me-1"></i>
                        Vista Alfabética
                    </a>
                    <a href="{{ route('languages.create') }}" class="btn btn-gradient-primary">
                        <i class="fas fa-plus me-1"></i>
                        Agregar Idioma
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-custom border-0">
                <div class="card-header bg-gradient-primary text-white border-0">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-0">Total de Idiomas</h6>
                            <h3 class="mt-2">{{ $totalLanguages }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-globe fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-custom border-0">
                <div class="card-header bg-gradient-success text-white border-0">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-0">Agregados Este Mes</h6>
                            <h3 class="mt-2">{{ $recentlyAdded }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-plus-circle fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('languages.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Buscar Idioma</label>
                    <div class="input-group">
                        <input type="text" 
                               class="form-control" 
                               id="search" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Ingresa el nombre del idioma...">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="sort" class="form-label">Ordenar Por</label>
                    <select class="form-select" id="sort" name="sort">
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nombre</option>
                        <option value="language_id" {{ request('sort') == 'language_id' ? 'selected' : '' }}>ID</option>
                        <option value="last_update" {{ request('sort') == 'last_update' ? 'selected' : '' }}>Última Actualización</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="direction" class="form-label">Dirección</label>
                    <select class="form-select" id="direction" name="direction">
                        <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Ascendente</option>
                        <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Descendente</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-gradient-primary w-100">
                        <i class="fas fa-filter me-1"></i>
                        Filtrar
                    </button>
                </div>
            </form>
            @if(request()->hasAny(['search', 'sort', 'direction']))
                <div class="mt-3">
                    <a href="{{ route('languages.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-times me-1"></i>
                        Limpiar Filtros
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Results Summary -->
    @if(request('search') || request()->hasAny(['sort', 'direction']))
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Mostrando {{ $languages->count() }} de {{ $languages->total() }} idiomas
            @if(request('search'))
                que coinciden con "<strong>{{ request('search') }}</strong>"
            @endif
        </div>
    @endif

    <!-- Languages Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>
                Lista de Idiomas
                <span class="badge bg-secondary ms-2">{{ $languages->total() }}</span>
            </h5>
        </div>
        <div class="card-body p-0">
            @if($languages->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'language_id', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                       class="text-decoration-none text-dark">
                                        ID
                                        @if(request('sort') == 'language_id')
                                            <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="fas fa-sort text-muted"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                       class="text-decoration-none text-dark">
                                        Nombre
                                        @if(request('sort') == 'name' || !request('sort'))
                                            <i class="fas fa-sort-{{ request('direction') === 'desc' ? 'down' : 'up' }}"></i>
                                        @else
                                            <i class="fas fa-sort text-muted"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'last_update', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                       class="text-decoration-none text-dark">
                                        Última Actualización
                                        @if(request('sort') == 'last_update')
                                            <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="fas fa-sort text-muted"></i>
                                        @endif
                                    </a>
                                </th>
                                <th width="200" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($languages as $language)
                                <tr>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $language->language_id }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <div class="avatar-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                                                     style="width: 40px; height: 40px; border-radius: 50%; font-weight: bold;">
                                                    {{ strtoupper(substr($language->name, 0, 2)) }}
                                                </div>
                                            </div>
                                            <div>
                                                <strong>{{ $language->formatted_name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $language->name }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $language->last_update?->format('M d, Y H:i') ?? 'N/A' }}</span>
                                        @if($language->last_update && $language->last_update->isToday())
                                            <span class="badge bg-success ms-1">Hoy</span>
                                        @elseif($language->last_update && $language->last_update->isYesterday())
                                            <span class="badge bg-warning ms-1">Ayer</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('languages.show', $language) }}" 
                                               class="btn btn-sm btn-outline-info" 
                                               title="Ver Detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('languages.edit', $language) }}" 
                                               class="btn btn-sm btn-outline-warning" 
                                               title="Editar Idioma">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" 
                                                  action="{{ route('languages.destroy', $language) }}" 
                                                  class="d-inline" 
                                                  onsubmit="return confirm('¿Estás seguro de que quieres eliminar el idioma \'{{ $language->name }}\'?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-danger" 
                                                        title="Eliminar Idioma">
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
            @else
                <div class="text-center py-5">
                    <i class="fas fa-language fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No se encontraron idiomas</h5>
                    <p class="text-muted">
                        @if(request('search'))
                            No hay idiomas que coincidan con tus criterios de búsqueda.
                        @else
                            Comienza agregando tu primer idioma.
                        @endif
                    </p>
                    @if(request('search'))
                        <a href="{{ route('languages.index') }}" class="btn btn-outline-primary">Limpiar Búsqueda</a>
                    @else
                        <a href="{{ route('languages.create') }}" class="btn btn-gradient-primary">Agregar Primer Idioma</a>
                    @endif
                </div>
            @endif
        </div>
        
        @if($languages->hasPages())
            <div class="card-footer">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <span class="text-muted">
                            Mostrando {{ $languages->firstItem() }} a {{ $languages->lastItem() }} de {{ $languages->total() }} resultados
                        </span>
                    </div>
                    <div class="col-md-6">
                        {{ $languages->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Simple alert for success message
            alert('{{ session('success') }}');
        });
    </script>
@endif

@if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Simple alert for error message
            alert('{{ session('error') }}');
        });
    </script>
@endif
@endsection