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
                    <div class="row">
                        @foreach($inventory as $item)
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
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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