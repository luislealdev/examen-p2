@extends('layouts.app')

@section('title', 'Stores List')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Stores</h1>
    <a href="{{ route('stores.create') }}" class="btn btn-primary">Add New Store</a>
</div>

<div class="card">
    <div class="card-body">
        @if($stores->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Store ID</th>
                            <th>Manager Staff ID</th>
                            <th>Address ID</th>
                            <th>Last Update</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stores as $store)
                            <tr>
                                <td>{{ $store->store_id }}</td>
                                <td>{{ $store->manager_staff_id }}</td>
                                <td>{{ $store->address_id }}</td>
                                <td>{{ $store->last_update ? $store->last_update->format('Y-m-d H:i:s') : 'N/A' }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('stores.show', $store->store_id) }}" class="btn btn-sm btn-info">View</a>
                                        <a href="{{ route('stores.edit', $store->store_id) }}" class="btn btn-sm btn-warning">Edit</a>
                                        <form action="{{ route('stores.destroy', $store->store_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this store?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $stores->links() }}
            </div>
        @else
            <div class="text-center py-4">
                <p class="text-muted">No stores found.</p>
                <a href="{{ route('stores.create') }}" class="btn btn-primary">Add the first store</a>
            </div>
        @endif
    </div>
</div>
@endsection