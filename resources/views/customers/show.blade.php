@extends('layouts.app')

@section('title', 'Customer Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>Customer Details</h3>
                <div>
                    <a href="{{ route('customers.edit', $customer->customer_id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <a href="{{ route('customers.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Basic Information</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Customer ID:</strong></td>
                                <td>{{ $customer->customer_id }}</td>
                            </tr>
                            <tr>
                                <td><strong>Full Name:</strong></td>
                                <td>{{ $customer->full_name }}</td>
                            </tr>
                            <tr>
                                <td><strong>First Name:</strong></td>
                                <td>{{ $customer->first_name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Last Name:</strong></td>
                                <td>{{ $customer->last_name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>{{ $customer->email ?: 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    @if($customer->active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <h5>Location & Dates</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Home Store ID:</strong></td>
                                <td>{{ $customer->store_id }}</td>
                            </tr>
                            <tr>
                                <td><strong>Address ID:</strong></td>
                                <td>{{ $customer->address_id }}</td>
                            </tr>
                            <tr>
                                <td><strong>Created Date:</strong></td>
                                <td>{{ $customer->create_date ? $customer->create_date->format('Y-m-d H:i:s') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Last Update:</strong></td>
                                <td>{{ $customer->last_update ? $customer->last_update->format('Y-m-d H:i:s') : 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($customer->store)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Home Store Information</h5>
                            <div class="alert alert-info">
                                <strong>Store ID:</strong> {{ $customer->store->store_id }}<br>
                                <strong>Manager Staff ID:</strong> {{ $customer->store->manager_staff_id }}<br>
                                <strong>Store Address ID:</strong> {{ $customer->store->address_id }}
                            </div>
                        </div>
                    </div>
                @endif

                {{-- TODO: When Address model is created, uncomment this section
                @if($customer->address)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Address Information</h5>
                            <div class="alert alert-secondary">
                                <strong>Address:</strong> {{ $customer->address->address }}<br>
                                <strong>City:</strong> {{ $customer->address->city }}<br>
                                <strong>District:</strong> {{ $customer->address->district }}<br>
                                <strong>Postal Code:</strong> {{ $customer->address->postal_code }}
                            </div>
                        </div>
                    </div>
                @endif
                --}}

                <div class="mt-4">
                    <h5>Actions</h5>
                    <div class="btn-group" role="group">
                        <a href="{{ route('customers.edit', $customer->customer_id) }}" class="btn btn-warning">Edit Customer</a>
                        @if($customer->active)
                            <form action="{{ route('customers.destroy', $customer->customer_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to deactivate this customer? This will not delete the customer but mark them as inactive.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Deactivate Customer</button>
                            </form>
                        @else
                            <span class="text-muted">Customer is already inactive</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection