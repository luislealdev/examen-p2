@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Tiendas Disponibles') }}</div>

                <div class="card-body">
                    <div class="row">
                        @foreach($stores as $store)
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">Tienda #{{ $store->id }}</h5>
                                        <p class="card-text">
                                            <strong>Dirección:</strong> {{ $store->address->address }}
                                        </p>
                                        <a href="{{ route('client.stores.inventory', $store->id) }}" 
                                           class="btn btn-primary">
                                            Ver Inventario
                                        </a>
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
@endsection