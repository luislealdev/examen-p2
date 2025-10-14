@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>{{ __('Inventario de Películas - Tienda #') }}{{ $store->store_id }}</span>
                        <a href="{{ route('client.stores.index') }}" class="btn btn-sm btn-secondary">
                            Volver a Tiendas
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    {{-- Formulario de búsqueda --}}
                    <form action="{{ route('client.stores.inventory', $store->store_id) }}" method="GET" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" name="search" class="form-control" placeholder="Buscar por título..." value="{{ request('search') }}">
                                </div>
                            </div>
                            
                            <div class="col-md-2">
                                <select name="category" class="form-select">
                                    <option value="">Todas las categorías</option>
                                    @foreach(\App\Models\Category::orderBy('name')->get() as $category)
                                        <option value="{{ $category->category_id }}" {{ request('category') == $category->category_id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" name="actor" class="form-control" placeholder="Buscar por actor..." value="{{ request('actor') }}">
                                </div>
                            </div>
                            
                            <div class="col-md-2">
                                <select name="language" class="form-select">
                                    <option value="">Todos los idiomas</option>
                                    @foreach(\App\Models\Language::orderBy('name')->get() as $language)
                                        <option value="{{ $language->language_id }}" {{ request('language') == $language->language_id ? 'selected' : '' }}>
                                            <i class="fas fa-globe me-1"></i>{{ $language->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-1"></i>Buscar
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- Resultados --}}
                    {{-- Mostrar número de resultados si hay una búsqueda --}}
                    @if(request()->hasAny(['search', 'category', 'actor', 'language']))
                        <div class="alert alert-info mb-4">
                            Se encontraron {{ $inventory->count() }} resultados
                            @if(request('search'))
                                para la búsqueda "{{ request('search') }}"
                            @endif
                        </div>
                    @endif

                    <div class="row">
                        @forelse($inventory as $item)
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="row g-0">
                                        <div class="col-md-4">
                                            @if(isset($item['omdb_data']['Poster']) && $item['omdb_data']['Poster'] != 'N/A')
                                                <img src="{{ $item['omdb_data']['Poster'] }}" 
                                                     class="img-fluid rounded-start" 
                                                     alt="{{ $item['film']->title }}">
                                            @else
                                                <div class="text-center p-3">
                                                    <i class="fas fa-film fa-4x"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-8">
                                            <div class="card-body">
                                                <h5 class="card-title">{{ $item['film']->title }}</h5>
                                                <p class="card-text">
                                                    <small class="text-muted">
                                                        Año: {{ $item['omdb_data']['Year'] ?? 'N/A' }}
                                                    </small>
                                                </p>
                                                <p class="card-text">
                                                    {{ Str::limit($item['film']->description, 100) }}
                                                </p>
                                                <p class="card-text">
                                                    <strong>Precio de renta:</strong> ${{ $item['film']->rental_rate }}
                                                </p>
                                                <button class="btn btn-primary rent-movie" 
                                                        data-inventory-id="{{ $item['inventory_id'] }}">
                                                    Rentar Película
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    No se encontraron películas que coincidan con los criterios de búsqueda.
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar Select2 para mejorar los selectores
    $('select').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });
});
</script>
@endpush

@push('scripts')
<script>
document.querySelectorAll('.rent-movie').forEach(button => {
    button.addEventListener('click', function() {
        const inventoryId = this.dataset.inventoryId;
        
        const button = this;
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
        
        fetch(`/client/inventory/${inventoryId}/rent`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: data.message,
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.href = '{{ route('client.rentals.index') }}';
            });
        })
        .catch(error => {
            console.error('Error:', error);
            button.disabled = false;
            button.innerHTML = 'Rentar Película';
            
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al intentar rentar la película. Por favor intente más tarde.'
            });
        });
    });
});
</script>
@endpush
@endsection