@extends('layouts.app')

@section('title', 'Mis Alquileres')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-gradient-primary text-white">
        <h4 class="mb-0">
            <i class="fas fa-ticket-alt me-2"></i>Mis Alquileres
        </h4>
    </div>
    <div class="card-body">
        @if($rentals->isEmpty())
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>No tienes alquileres registrados.
                <a href="{{ route('films.index') }}" class="alert-link">¡Explora nuestro catálogo de películas!</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Película</th>
                            <th>Fecha de Alquiler</th>
                            <th>Fecha de Devolución</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rentals as $rental)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $rental->inventory->film->poster_url ?? asset('images/default-poster.jpg') }}" 
                                             alt="Poster" class="me-3" style="width: 50px; height: 75px; object-fit: cover;">
                                        <div>
                                            <h6 class="mb-0">{{ $rental->inventory->film->title }}</h6>
                                            <small class="text-muted">{{ $rental->inventory->film->release_year }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $rental->rental_date->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($rental->return_date)
                                        {{ $rental->return_date->format('d/m/Y H:i') }}
                                    @else
                                        <span class="badge bg-warning">Pendiente</span>
                                    @endif
                                </td>
                                <td>
                                    @if($rental->return_date)
                                        <span class="badge bg-success">Devuelto</span>
                                    @else
                                        <span class="badge bg-primary">En préstamo</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $rentals->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Sección de películas recomendadas --}}
<div class="card shadow-sm mt-4">
    <div class="card-header bg-gradient-info text-white">
        <h5 class="mb-0">
            <i class="fas fa-star me-2"></i>Películas Recomendadas
        </h5>
    </div>
    <div class="card-body">
        <div class="alert alert-primary">
            <i class="fas fa-lightbulb me-2"></i>
            ¡Basado en tus alquileres anteriores, estas películas podrían interesarte!
        </div>
        {{-- TODO: Implementar recomendaciones basadas en categorías preferidas --}}
        <div class="text-center">
            <a href="{{ route('films.index') }}" class="btn btn-gradient-primary">
                <i class="fas fa-film me-2"></i>Explorar Catálogo
            </a>
        </div>
    </div>
</div>
@endsection