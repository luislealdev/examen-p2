@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-file-invoice-dollar me-2"></i>
                        Mis Pagos y Cargos
                    </h4>
                </div>

                <div class="card-body">
                    {{-- Resumen de pagos --}}
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-check-circle me-2"></i>Total Pagado
                                    </h5>
                                    <h3 class="mb-0">${{ number_format($totalPagado, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-warning text-dark">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-exclamation-circle me-2"></i>Cargos Extra
                                    </h5>
                                    <h3 class="mb-0">${{ number_format($totalCargosExtra, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-clock me-2"></i>Pendiente por Pagar
                                    </h5>
                                    <h3 class="mb-0">${{ number_format($totalPendiente, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tabla de rentas y pagos --}}
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Película</th>
                                    <th>Fecha de Renta</th>
                                    <th>Fecha de Devolución</th>
                                    <th>Cargo Base</th>
                                    <th>Cargo Extra</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rentals as $rental)
                                    <tr>
                                        <td>{{ $rental->inventory->film->title }}</td>
                                        <td>{{ $rental->rental_date->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @if($rental->return_date)
                                                {{ $rental->return_date->format('d/m/Y H:i') }}
                                            @else
                                                <span class="badge bg-warning">Pendiente</span>
                                            @endif
                                        </td>
                                        <td>${{ number_format($rental->rental_amount, 2) }}</td>
                                        <td>
                                            @if($rental->late_fees > 0)
                                                <span class="text-danger">${{ number_format($rental->late_fees, 2) }}</span>
                                            @else
                                                $0.00
                                            @endif
                                        </td>
                                        <td>${{ number_format($rental->rental_amount + $rental->late_fees, 2) }}</td>
                                        <td>
                                            @if(!$rental->return_date)
                                                <span class="badge bg-warning">En Renta</span>
                                            @elseif($rental->late_fees > 0)
                                                <span class="badge bg-danger">Con Cargo Extra</span>
                                            @else
                                                <span class="badge bg-success">Completado</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            No hay registros de pagos o rentas.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection