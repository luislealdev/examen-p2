@extends('layouts.app')

@section('title', 'Inventory Item #' . $inventory->inventory_id)

@section('content')
<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('inventories.index') }}">Inventory</a></li>
            <li class="breadcrumb-item active">Item #{{ $inventory->inventory_id }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col">
            <div class="d-flex align-items-center mb-2">
                <h1 class="display-5 fw-bold text-gradient me-3">
                    Inventory Item #{{ $inventory->inventory_id }}
                </h1>
                <span class="badge bg-{{ $inventory->status_color }} fs-6">{{ $inventory->status }}</span>
            </div>
            <p class="lead text-muted">
                <i class="fas fa-boxes me-2"></i>{{ $inventory->film_title }} at {{ $inventory->store_location }}
            </p>
        </div>
        <div class="col-auto">
            <div class="btn-group">
                <a href="{{ route('inventories.edit', $inventory) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i>Edit Item
                </a>
                <button type="button" class="btn btn-outline-danger" 
                        data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="fas fa-trash me-2"></i>Delete
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Inventory Details Card -->
            <div class="card shadow-lg border-0 mb-4">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Inventory Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="fw-bold">Basic Information</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td class="fw-bold">Inventory ID:</td>
                                    <td><span class="text-primary fw-bold">#{{ $inventory->inventory_id }}</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Store:</td>
                                    <td>
                                        <a href="{{ route('inventories.by-store', $inventory->store) }}" class="text-decoration-none">
                                            {{ $inventory->store_location }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Status:</td>
                                    <td>
                                        <span class="badge bg-{{ $inventory->status_color }}">{{ $inventory->status }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Last Update:</td>
                                    <td>
                                        <span title="{{ $inventory->last_update_format }}">
                                            {{ $inventory->last_update_human }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h6 class="fw-bold">Rental Information</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td class="fw-bold">Rental Rate:</td>
                                    <td><span class="text-success fw-bold">${{ number_format($inventory->rental_rate, 2) }}</span></td>
                                </tr>
                                @if($inventory->film)
                                <tr>
                                    <td class="fw-bold">Rental Duration:</td>
                                    <td>{{ $inventory->film->rental_duration }} {{ Str::plural('day', $inventory->film->rental_duration) }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Replacement Cost:</td>
                                    <td><span class="text-danger fw-bold">${{ number_format($inventory->film->replacement_cost, 2) }}</span></td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="fw-bold">Value Ratio:</td>
                                    <td>
                                        @if($inventory->film && $inventory->film->replacement_cost > 0)
                                            {{ number_format($inventory->film->replacement_cost / $inventory->rental_rate, 1) }}x
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Film Information Card -->
            @if($inventory->film)
            <div class="card shadow-lg border-0 mb-4">
                <div class="card-header bg-gradient-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-film me-2"></i>Film Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-start">
                        <div class="col-md-8">
                            <h4 class="fw-bold">{{ $inventory->film->title }}</h4>
                            @if($inventory->film->description)
                                <p class="text-muted">{{ $inventory->film->description }}</p>
                            @endif
                            
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <strong>Language:</strong> {{ $inventory->film->language->name ?? 'N/A' }}
                                </div>
                                <div class="col-sm-6">
                                    <strong>Length:</strong> {{ $inventory->film->duration_format }}
                                </div>
                                @if($inventory->film->release_year)
                                <div class="col-sm-6">
                                    <strong>Release Year:</strong> {{ $inventory->film->release_year }}
                                </div>
                                @endif
                                <div class="col-sm-6">
                                    <strong>Age Category:</strong> {{ $inventory->film->age_category }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="badge bg-{{ $inventory->film->rating_color }} fs-5 mb-2">
                                {{ $inventory->film->rating }}
                            </span>
                            <br>
                            @if($inventory->film->release_year)
                                <span class="badge bg-secondary fs-6">{{ $inventory->film->release_year }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Category -->
                    @if($inventory->film->category)
                        <div class="mb-2">
                            <strong>Category:</strong><br>
                            <span class="badge bg-secondary">{{ $inventory->film->category->name }}</span>
                        </div>
                    @endif                    <!-- Special Features -->
                    @if($inventory->film->special_features && count($inventory->film->special_features) > 0)
                        <div class="mt-3">
                            <strong>Special Features:</strong><br>
                            <div class="d-flex flex-wrap gap-1 mt-2">
                                @foreach($inventory->film->special_features as $feature)
                                    <span class="badge bg-warning text-dark">{{ $feature }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Actions Card -->
            <div class="card shadow-lg border-0 mb-4">
                <div class="card-header bg-gradient-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('inventories.edit', $inventory) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>Edit This Item
                        </a>
                        
                        @if($inventory->film)
                            <a href="{{ route('inventories.by-film', $inventory->film) }}" class="btn btn-outline-info">
                                <i class="fas fa-film me-2"></i>All Copies of This Film
                            </a>
                            <a href="{{ route('films.show', $inventory->film) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-info-circle me-2"></i>Film Details
                            </a>
                        @endif
                        
                        @if($inventory->store)
                            <a href="{{ route('inventories.by-store', $inventory->store) }}" class="btn btn-outline-warning">
                                <i class="fas fa-store me-2"></i>Store Inventory
                            </a>
                            <a href="{{ route('stores.show', $inventory->store) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-building me-2"></i>Store Details
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Related Inventory Card -->
            @if($inventory->film)
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-warning text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-copy me-2"></i>Other Copies
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $otherCopies = \App\Models\Inventory::where('film_id', $inventory->film_id)
                            ->where('inventory_id', '!=', $inventory->inventory_id)
                            ->with('store')
                            ->take(5)
                            ->get();
                    @endphp
                    
                    @if($otherCopies->count() > 0)
                        @foreach($otherCopies as $copy)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <small class="text-muted">#{{ $copy->inventory_id }}</small><br>
                                    <strong>{{ $copy->store_location }}</strong>
                                </div>
                                <div>
                                    <span class="badge bg-{{ $copy->status_color }}">{{ $copy->status }}</span>
                                </div>
                            </div>
                        @endforeach
                        
                        @php
                            $totalCopies = \App\Models\Inventory::where('film_id', $inventory->film_id)->count();
                        @endphp
                        
                        @if($totalCopies > 5)
                            <div class="text-center mt-3">
                                <a href="{{ route('inventories.by-film', $inventory->film) }}" class="btn btn-sm btn-outline-primary">
                                    View All {{ $totalCopies }} Copies
                                </a>
                            </div>
                        @endif
                    @else
                        <p class="text-muted text-center">This is the only copy of this film in inventory.</p>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete inventory item <strong>#{{ $inventory->inventory_id }}</strong>?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    This action cannot be undone. The inventory item will be permanently removed.
                </div>
                <div class="card bg-light">
                    <div class="card-body">
                        <strong>Item Details:</strong><br>
                        Film: {{ $inventory->film_title }}<br>
                        Store: {{ $inventory->store_location }}<br>
                        Status: {{ $inventory->status }}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('inventories.destroy', $inventory) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Delete Item
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.text-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
}
</style>
@endsection