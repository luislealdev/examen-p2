@extends('layouts.app')

@section('title', 'Inventory Management')

@section('content')
<div class="container">
    <!-- Header with Title and Add Button -->
    <div class="row align-items-center mb-4">
        <div class="col">
            <h1 class="display-4 fw-bold text-gradient">
                <i class="fas fa-boxes me-3"></i>Inventory Management
            </h1>
            <p class="lead text-muted">Track and manage store inventory with {{ number_format($stats['total_items']) }} items</p>
        </div>
        <div class="col-auto">
            <div class="btn-group">
                <a href="{{ route('inventories.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add Item
                </a>
                <a href="{{ route('inventories.bulk-create') }}" class="btn btn-success">
                    <i class="fas fa-layer-group me-2"></i>Bulk Add
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card gradient-card-primary">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">Total Items</h6>
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
            <div class="card gradient-card-success">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">Recent Additions</h6>
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
            <div class="card gradient-card-warning">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">High Value Items</h6>
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
            <div class="card gradient-card-info">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">Avg Rental Rate</h6>
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
    <div class="card shadow-lg border-0 mb-4">
        <div class="card-header bg-light border-0">
            <h5 class="mb-0">
                <i class="fas fa-filter me-2"></i>Filters & Search
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('inventories.index') }}" class="row g-3">
                <!-- Search -->
                <div class="col-md-4">
                    <label class="form-label fw-bold">Search</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Search by film title..."
                               value="{{ request('search') }}">
                    </div>
                </div>

                <!-- Film Filter -->
                <div class="col-md-3">
                    <label class="form-label fw-bold">Film</label>
                    <select name="film_id" class="form-select">
                        <option value="">All Films</option>
                        @foreach($films as $film)
                            <option value="{{ $film->film_id }}" {{ request('film_id') == $film->film_id ? 'selected' : '' }}>
                                {{ $film->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Store Filter -->
                <div class="col-md-2">
                    <label class="form-label fw-bold">Store</label>
                    <select name="store_id" class="form-select">
                        <option value="">All Stores</option>
                        @foreach($stores as $store)
                            <option value="{{ $store->store_id }}" {{ request('store_id') == $store->store_id ? 'selected' : '' }}>
                                Store #{{ $store->store_id }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Rating Filter -->
                <div class="col-md-3">
                    <label class="form-label fw-bold">Film Rating</label>
                    <select name="rating" class="form-select">
                        <option value="">All Ratings</option>
                        @foreach($ratings as $rating)
                            <option value="{{ $rating }}" {{ request('rating') == $rating ? 'selected' : '' }}>
                                {{ $rating }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Category Filter -->
                <div class="col-md-3">
                    <label class="form-label fw-bold">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->category_id }}" 
                                    {{ request('category_id') == $category->category_id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Language Filter -->
                <div class="col-md-3">
                    <label class="form-label fw-bold">Language</label>
                    <select name="language_id" class="form-select">
                        <option value="">All Languages</option>
                        @foreach($languages as $language)
                            <option value="{{ $language->language_id }}" 
                                    {{ request('language_id') == $language->language_id ? 'selected' : '' }}>
                                {{ $language->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Recent Filter -->
                <div class="col-md-2">
                    <label class="form-label fw-bold">Recent Days</label>
                    <select name="recent_days" class="form-select">
                        <option value="">All Time</option>
                        <option value="7" {{ request('recent_days') == '7' ? 'selected' : '' }}>Last 7 days</option>
                        <option value="30" {{ request('recent_days') == '30' ? 'selected' : '' }}>Last 30 days</option>
                        <option value="90" {{ request('recent_days') == '90' ? 'selected' : '' }}>Last 90 days</option>
                    </select>
                </div>

                <!-- High Value Filter -->
                <div class="col-md-2">
                    <label class="form-label fw-bold">Value</label>
                    <div class="form-check">
                        <input type="checkbox" name="high_value" value="1" class="form-check-input"
                               {{ request('high_value') ? 'checked' : '' }}>
                        <label class="form-check-label">High Value ($4+)</label>
                    </div>
                </div>

                <!-- Sort Options -->
                <div class="col-md-2">
                    <label class="form-label fw-bold">Sort By</label>
                    <select name="sort" class="form-select">
                        <option value="alphabetical" {{ request('sort') == 'alphabetical' ? 'selected' : '' }}>Film Title</option>
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                        <option value="inventory_id" {{ request('sort') == 'inventory_id' ? 'selected' : '' }}>Inventory ID</option>
                        <option value="store_id" {{ request('sort') == 'store_id' ? 'selected' : '' }}>Store ID</option>
                    </select>
                </div>

                <!-- Filter Buttons -->
                <div class="col-12">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i>Apply Filters
                    </button>
                    <a href="{{ route('inventories.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Clear Filters
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
                                <th>Film</th>
                                <th>Store</th>
                                <th>Rating</th>
                                <th>Language</th>
                                <th>Rental Rate</th>
                                <th>Status</th>
                                <th>Last Update</th>
                                <th>Actions</th>
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
                                               class="btn btn-outline-primary" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('inventories.edit', $inventory) }}" 
                                               class="btn btn-outline-secondary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('inventories.destroy', $inventory) }}" 
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this inventory item?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Delete">
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
            <h3 class="text-muted">No inventory items found</h3>
            <p class="text-muted">Try adjusting your search criteria or add some inventory items to get started.</p>
            <div class="mt-3">
                <a href="{{ route('inventories.create') }}" class="btn btn-primary me-2">
                    <i class="fas fa-plus me-2"></i>Add First Item
                </a>
                <a href="{{ route('inventories.bulk-create') }}" class="btn btn-success">
                    <i class="fas fa-layer-group me-2"></i>Bulk Add Items
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