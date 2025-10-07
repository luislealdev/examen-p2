@extends('layouts.app')

@section('title', 'Categories')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">
                    <i class="fas fa-tags me-2"></i>
                    Categories Management
                </h2>
                <div class="d-flex gap-2">
                    <a href="{{ route('categories.alphabetical') }}" class="btn btn-outline-primary">
                        <i class="fas fa-sort-alpha-down me-1"></i>
                        Alphabetical View
                    </a>
                    <a href="{{ route('categories.popular') }}" class="btn btn-outline-success">
                        <i class="fas fa-star me-1"></i>
                        Popular Categories
                    </a>
                    <a href="{{ route('categories.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>
                        Add Category
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-0">Total Categories</h6>
                            <h3 class="mt-2">{{ $totalCategories }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-tags fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-0">Recently Updated</h6>
                            <h3 class="mt-2">{{ $recentlyAdded }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('categories.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search Category</label>
                    <div class="input-group">
                        <input type="text" 
                               class="form-control" 
                               id="search" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Enter category name...">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="sort" class="form-label">Sort By</label>
                    <select class="form-select" id="sort" name="sort">
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name</option>
                        <option value="category_id" {{ request('sort') == 'category_id' ? 'selected' : '' }}>ID</option>
                        <option value="last_update" {{ request('sort') == 'last_update' ? 'selected' : '' }}>Last Update</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="direction" class="form-label">Direction</label>
                    <select class="form-select" id="direction" name="direction">
                        <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Ascending</option>
                        <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Descending</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-1"></i>
                        Filter
                    </button>
                </div>
            </form>
            @if(request()->hasAny(['search', 'sort', 'direction']))
                <div class="mt-3">
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-times me-1"></i>
                        Clear Filters
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Results Summary -->
    @if(request('search') || request()->hasAny(['sort', 'direction']))
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Showing {{ $categories->count() }} of {{ $categories->total() }} categories
            @if(request('search'))
                matching "<strong>{{ request('search') }}</strong>"
            @endif
        </div>
    @endif

    <!-- Categories Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>
                Categories List
                <span class="badge bg-secondary ms-2">{{ $categories->total() }}</span>
            </h5>
        </div>
        <div class="card-body p-0">
            @if($categories->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'category_id', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                       class="text-decoration-none text-dark">
                                        ID
                                        @if(request('sort') == 'category_id')
                                            <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="fas fa-sort text-muted"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                       class="text-decoration-none text-dark">
                                        Category Name
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
                                        Last Update
                                        @if(request('sort') == 'last_update')
                                            <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="fas fa-sort text-muted"></i>
                                        @endif
                                    </a>
                                </th>
                                <th width="200" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $category)
                                <tr>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $category->category_id }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <div class="category-icon bg-gradient-{{ $loop->index % 6 + 1 }} text-white d-flex align-items-center justify-content-center" 
                                                     style="width: 40px; height: 40px; border-radius: 8px; font-weight: bold;">
                                                    <i class="fas fa-tag"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <strong>{{ $category->formatted_name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $category->slug }}</small>
                                                @if($category->is_recent)
                                                    <span class="badge bg-success ms-1">New</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $category->last_update?->format('M d, Y H:i') ?? 'N/A' }}</span>
                                        @if($category->last_update && $category->last_update->isToday())
                                            <span class="badge bg-success ms-1">Today</span>
                                        @elseif($category->last_update && $category->last_update->isYesterday())
                                            <span class="badge bg-warning ms-1">Yesterday</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('categories.show', $category) }}" 
                                               class="btn btn-sm btn-outline-info" 
                                               title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('categories.edit', $category) }}" 
                                               class="btn btn-sm btn-outline-warning" 
                                               title="Edit Category">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" 
                                                  action="{{ route('categories.destroy', $category) }}" 
                                                  class="d-inline" 
                                                  onsubmit="return confirm('Are you sure you want to delete the category \'{{ $category->name }}\'?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-danger" 
                                                        title="Delete Category">
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
                    <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No categories found</h5>
                    <p class="text-muted">
                        @if(request('search'))
                            No categories match your search criteria.
                        @else
                            Start by adding your first category.
                        @endif
                    </p>
                    @if(request('search'))
                        <a href="{{ route('categories.index') }}" class="btn btn-outline-primary">Clear Search</a>
                    @else
                        <a href="{{ route('categories.create') }}" class="btn btn-primary">Add First Category</a>
                    @endif
                </div>
            @endif
        </div>
        
        @if($categories->hasPages())
            <div class="card-footer">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <span class="text-muted">
                            Showing {{ $categories->firstItem() }} to {{ $categories->lastItem() }} of {{ $categories->total() }} results
                        </span>
                    </div>
                    <div class="col-md-6">
                        {{ $categories->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
.bg-gradient-1 { background: linear-gradient(45deg, #007bff, #0056b3); }
.bg-gradient-2 { background: linear-gradient(45deg, #28a745, #1e7e34); }
.bg-gradient-3 { background: linear-gradient(45deg, #dc3545, #bd2130); }
.bg-gradient-4 { background: linear-gradient(45deg, #ffc107, #d39e00); }
.bg-gradient-5 { background: linear-gradient(45deg, #17a2b8, #117a8b); }
.bg-gradient-6 { background: linear-gradient(45deg, #6f42c1, #59359a); }

.category-icon {
    transition: transform 0.2s ease;
}

.category-icon:hover {
    transform: scale(1.1);
}
</style>

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