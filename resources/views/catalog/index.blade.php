@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Catálogo de Películas</h2>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
        @foreach($films as $film)
        <div class="col">
            <div class="card h-100 shadow-sm">
                @if($film->poster_url)
                    <img src="{{ $film->poster_url }}" class="card-img-top" alt="{{ $film->title }}" style="height: 300px; object-fit: cover;">
                @else
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 300px;">
                        <i class="fas fa-film fa-3x text-muted"></i>
                    </div>
                @endif
                
                <div class="card-body">
                    <h5 class="card-title" title="{{ $film->title }}">
                        {{ Str::limit($film->title, 30) }}
                    </h5>
                    <p class="card-text small text-muted mb-2">
                        {{ $film->release_year }} • {{ $film->category->name ?? 'Sin categoría' }}
                    </p>
                    <p class="card-text" style="height: 4.5em; overflow: hidden;">
                        {{ Str::limit($film->description, 100) }}
                    </p>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="ratings">
                            @if($film->imdb_rating)
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-star"></i> {{ $film->imdb_rating }}/10
                                </span>
                            @endif
                        </div>
                        <span class="text-primary fw-bold">${{ number_format($film->rental_rate, 2) }}</span>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <div class="d-grid">
                        <button class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-shopping-cart me-1"></i> Alquilar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $films->links() }}
    </div>
</div>
@endsection

@section('styles')
<style>
.card:hover {
    transform: translateY(-5px);
    transition: transform 0.2s ease-in-out;
}
</style>
@endsection