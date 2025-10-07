@extends('layouts.app')

@section('title', 'Store Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>Store Details</h3>
                <div>
                    <a href="{{ route('stores.edit', $store->store_id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <a href="{{ route('stores.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Store ID:</strong></td>
                                <td>{{ $store->store_id }}</td>
                            </tr>
                            <tr>
                                <td><strong>Manager Staff ID:</strong></td>
                                <td>{{ $store->manager_staff_id }}</td>
                            </tr>
                            <tr>
                                <td><strong>Address ID:</strong></td>
                                <td>{{ $store->address_id }}</td>
                            </tr>
                            <tr>
                                <td><strong>Last Update:</strong></td>
                                <td>{{ $store->last_update ? $store->last_update->format('Y-m-d H:i:s') : 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="mt-4">
                    <h5>Actions</h5>
                    <div class="btn-group" role="group">
                        <a href="{{ route('stores.edit', $store->store_id) }}" class="btn btn-warning">Edit Store</a>
                        <form action="{{ route('stores.destroy', $store->store_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this store? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete Store</button>
                        </form>
                    </div>
                </div>

                {{-- TODO: When Staff and Address models are created, uncomment these sections
                <div class="mt-4">
                    <h5>Manager Information</h5>
                    @if($store->manager)
                        <p><strong>Name:</strong> {{ $store->manager->first_name }} {{ $store->manager->last_name }}</p>
                        <p><strong>Email:</strong> {{ $store->manager->email }}</p>
                    @else
                        <p class="text-muted">Manager information not available</p>
                    @endif
                </div>

                <div class="mt-4">
                    <h5>Address Information</h5>
                    @if($store->address)
                        <p><strong>Address:</strong> {{ $store->address->address }}</p>
                        <p><strong>City:</strong> {{ $store->address->city }}</p>
                        <p><strong>District:</strong> {{ $store->address->district }}</p>
                        <p><strong>Postal Code:</strong> {{ $store->address->postal_code }}</p>
                    @else
                        <p class="text-muted">Address information not available</p>
                    @endif
                </div>
                --}}
            </div>
        </div>
    </div>
</div>
@endsection