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
                                        <h5 class="card-title">Tienda #{{ $store->store_id }}</h5>
                                        <p class="card-text">
                                            <strong>Dirección:</strong> {{ $store->address->address }}<br>
                                            <strong>Distrito:</strong> {{ $store->address->district }}<br>
                                            <strong>Ciudad:</strong> 
                                            @if($store->address && $store->address->city)
                                                {{ $store->address->city->city }}
                                            @else
                                                No disponible
                                            @endif
                                            <br>
                                            <strong>País:</strong> 
                                            @if($store->address && $store->address->city && $store->address->city->country)
                                                {{ $store->address->city->country->country }}
                                            @else
                                                No disponible
                                            @endif
                                            <br>
                                            <strong>Encargado:</strong> {{ $store->manager->first_name }} {{ $store->manager->last_name }}
                                        </p>
                                        <a href="{{ route('client.stores.inventory', $store->store_id) }}" 
                                           class="btn btn-primary">
                                            <i class="fas fa-film me-1"></i>Ver Inventario
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