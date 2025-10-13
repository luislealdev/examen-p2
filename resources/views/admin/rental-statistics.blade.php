@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 fw-bold text-dark mb-0">
                    <i class="fas fa-chart-bar me-2 text-primary"></i>
                    {{ $title }}
                </h1>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Volver al Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Filters and Report Options -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-filter me-2"></i>Filtros y Opciones de Reporte
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.rental-statistics') }}" class="row g-3" id="reportForm">
                        <div class="col-md-3">
                            <label for="type" class="form-label fw-semibold">Tipo de Reporte</label>
                            <select name="type" id="type" class="form-select">
                                <optgroup label="Estadísticas Básicas">
                                    <option value="store" {{ $type === 'store' ? 'selected' : '' }}>Por Sucursal</option>
                                    <option value="category" {{ $type === 'category' ? 'selected' : '' }}>Por Categoría</option>
                                    <option value="actor" {{ $type === 'actor' ? 'selected' : '' }}>Por Actor</option>
                                </optgroup>
                                <optgroup label="Reportes Avanzados">
                                    <option value="revenue" {{ $type === 'revenue' ? 'selected' : '' }}>💰 Ingresos por Tienda</option>
                                    <option value="top-customers" {{ $type === 'top-customers' ? 'selected' : '' }}>👥 Top Clientes</option>
                                </optgroup>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="timeframe" class="form-label fw-semibold">Período de Tiempo</label>
                            <select name="timeframe" id="timeframe" class="form-select">
                                <option value="7" {{ $timeframe == '7' ? 'selected' : '' }}>Últimos 7 días</option>
                                <option value="30" {{ $timeframe == '30' ? 'selected' : '' }}>Últimos 30 días</option>
                                <option value="90" {{ $timeframe == '90' ? 'selected' : '' }}>Últimos 90 días</option>
                                <option value="180" {{ $timeframe == '180' ? 'selected' : '' }}>Últimos 6 meses</option>
                                <option value="365" {{ $timeframe == '365' ? 'selected' : '' }}>Último año</option>
                            </select>
                        </div>
                        <div class="col-md-3" id="storeFilterGroup" style="{{ in_array($type, ['revenue', 'top-customers']) ? '' : 'display: none;' }}">
                            <label for="store_id" class="form-label fw-semibold">Filtrar por Tienda</label>
                            <select name="store_id" id="store_id" class="form-select">
                                <option value="">Todas las Tiendas (Global)</option>
                                @if(isset($stores))
                                    @foreach($stores as $store)
                                        <option value="{{ $store->store_id }}" {{ $storeId == $store->store_id ? 'selected' : '' }}>
                                            Tienda #{{ $store->store_id }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <div class="btn-group w-100">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i>Ver Reporte
                                </button>
                                <button type="button" class="btn btn-outline-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">
                                    <span class="visually-hidden">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" onclick="exportReport('csv')">
                                        <i class="fas fa-file-csv me-2"></i>Exportar CSV
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" onclick="exportReport('pdf')">
                                        <i class="fas fa-file-pdf me-2"></i>Exportar PDF
                                    </a></li>
                                </ul>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- General Statistics -->
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="h5 fw-bold text-dark mb-3">
                <i class="fas fa-info-circle me-2"></i>Estadísticas Generales
                <small class="text-muted">(Últimos {{ $timeframe }} días)</small>
            </h3>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-primary bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-handshake text-white fa-2x"></i>
                    </div>
                    <h4 class="fw-bold text-primary">{{ number_format($generalStats['total_rentals']) }}</h4>
                    <p class="text-muted mb-0">Total Rentas</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-success bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-dollar-sign text-white fa-2x"></i>
                    </div>
                    <h4 class="fw-bold text-success">${{ number_format($generalStats['total_revenue'], 2) }}</h4>
                    <p class="text-muted mb-0">Ingresos Totales</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-info bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-play-circle text-white fa-2x"></i>
                    </div>
                    <h4 class="fw-bold text-info">{{ number_format($generalStats['active_rentals']) }}</h4>
                    <p class="text-muted mb-0">Rentas Activas</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-warning bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-exclamation-triangle text-white fa-2x"></i>
                    </div>
                    <h4 class="fw-bold text-warning">{{ number_format($generalStats['overdue_rentals']) }}</h4>
                    <p class="text-muted mb-0">Rentas Vencidas</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats Row -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-chart-line text-secondary fa-2x mb-2"></i>
                    <h6 class="text-muted mb-1">Promedio por Renta</h6>
                    <span class="fw-bold h5">${{ number_format($generalStats['avg_rental_amount'], 2) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-user-crown text-secondary fa-2x mb-2"></i>
                    <h6 class="text-muted mb-1">Mejor Cliente</h6>
                    <span class="fw-bold small">{{ $generalStats['top_customer'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-day text-secondary fa-2x mb-2"></i>
                    <h6 class="text-muted mb-1">Día Más Activo</h6>
                    <span class="fw-bold small">{{ $generalStats['busiest_day'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        {{ $title }} - Gráfico
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="rentalChart" style="max-height: 400px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Statistics Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-gradient-secondary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-table me-2"></i>
                        Detalle de Estadísticas
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($type === 'revenue')
                        <!-- Revenue Report -->
                        @if(isset($data['byStore']) && $data['byStore']->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-success">
                                        <tr>
                                            <th><i class="fas fa-store me-2"></i>Tienda</th>
                                            <th class="text-center"><i class="fas fa-film me-2"></i>Total Rentas</th>
                                            <th class="text-center"><i class="fas fa-dollar-sign me-2"></i>Ingresos Rentas</th>
                                            <th class="text-center"><i class="fas fa-exclamation-triangle me-2"></i>Multas</th>
                                            <th class="text-center"><i class="fas fa-calculator me-2"></i>Total General</th>
                                            <th class="text-center"><i class="fas fa-chart-line me-2"></i>Promedio</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $totalRevenue = 0; $totalRentals = 0; $totalFees = 0; @endphp
                                        @foreach($data['byStore'] as $item)
                                            @php 
                                                $totalRevenue += $item->rental_revenue;
                                                $totalRentals += $item->total_rentals;
                                                $totalFees += $item->late_fees;
                                            @endphp
                                            <tr>
                                                <td class="fw-semibold">
                                                    <span class="badge bg-info me-2">#{{ $item->store_id }}</span>
                                                    Tienda {{ $item->store_id }}
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-primary">{{ number_format($item->total_rentals) }}</span>
                                                </td>
                                                <td class="text-center text-success fw-bold">
                                                    ${{ number_format($item->rental_revenue, 2) }}
                                                </td>
                                                <td class="text-center text-warning fw-bold">
                                                    ${{ number_format($item->late_fees, 2) }}
                                                </td>
                                                <td class="text-center text-primary fw-bold fs-6">
                                                    ${{ number_format($item->total_revenue, 2) }}
                                                </td>
                                                <td class="text-center">
                                                    @php
                                                        $avgPerRental = $item->total_rentals > 0 ? $item->rental_revenue / $item->total_rentals : 0;
                                                    @endphp
                                                    ${{ number_format($avgPerRental, 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-dark">
                                        <tr>
                                            <th>TOTALES</th>
                                            <th class="text-center">{{ number_format($totalRentals) }}</th>
                                            <th class="text-center">${{ number_format($totalRevenue, 2) }}</th>
                                            <th class="text-center">${{ number_format($totalFees, 2) }}</th>
                                            <th class="text-center">${{ number_format($totalRevenue + $totalFees, 2) }}</th>
                                            <th class="text-center">${{ $totalRentals > 0 ? number_format($totalRevenue / $totalRentals, 2) : '0.00' }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-dollar-sign fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No hay ingresos registrados</h5>
                                <p class="text-muted">No se encontraron ingresos para el período seleccionado.</p>
                            </div>
                        @endif
                        
                    @elseif($type === 'top-customers')
                        <!-- Top Customers Report -->
                        @if($data->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-warning">
                                        <tr>
                                            <th><i class="fas fa-trophy me-2"></i>Ranking</th>
                                            <th><i class="fas fa-user me-2"></i>Cliente</th>
                                            <th><i class="fas fa-envelope me-2"></i>Email</th>
                                            <th class="text-center"><i class="fas fa-store me-2"></i>Tienda</th>
                                            <th class="text-center"><i class="fas fa-film me-2"></i>Total Rentas</th>
                                            <th class="text-center"><i class="fas fa-dollar-sign me-2"></i>Total Gastado</th>
                                            <th class="text-center"><i class="fas fa-exclamation-triangle me-2"></i>Multas</th>
                                            <th class="text-center"><i class="fas fa-calendar me-2"></i>Última Renta</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data as $index => $customer)
                                            <tr>
                                                <td class="text-center">
                                                    @if($index < 3)
                                                        @php
                                                            $medals = ['🥇', '🥈', '🥉'];
                                                        @endphp
                                                        <span class="fs-4">{{ $medals[$index] }}</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ $index + 1 }}</span>
                                                    @endif
                                                </td>
                                                <td class="fw-semibold">
                                                    {{ $customer->first_name }} {{ $customer->last_name }}
                                                </td>
                                                <td class="text-muted">{{ $customer->email }}</td>
                                                <td class="text-center">
                                                    <span class="badge bg-info">#{{ $customer->customer_store_id }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-primary fs-6">{{ $customer->total_rentals }}</span>
                                                </td>
                                                <td class="text-center text-success fw-bold">
                                                    ${{ number_format($customer->total_spent, 2) }}
                                                </td>
                                                <td class="text-center text-warning fw-bold">
                                                    ${{ number_format($customer->total_late_fees, 2) }}
                                                </td>
                                                <td class="text-center">
                                                    {{ \Carbon\Carbon::parse($customer->last_rental_date)->format('d/m/Y') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No hay clientes registrados</h5>
                                <p class="text-muted">No se encontraron clientes con rentas en el período seleccionado.</p>
                            </div>
                        @endif
                        
                    @else
                        <!-- Default Reports (store, category, actor) -->
                        @if($data->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>
                                                @if($type === 'store')
                                                    Sucursal
                                                @elseif($type === 'category')
                                                    Categoría
                                                @else
                                                    Actor
                                                @endif
                                            </th>
                                            @if($type === 'store')
                                                <th>Ubicación</th>
                                            @endif
                                            <th class="text-center">Total Rentas</th>
                                            <th class="text-center">Ingresos Totales</th>
                                            <th class="text-center">Promedio por Renta</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data as $item)
                                            <tr>
                                                <td class="fw-semibold">{{ $item['name'] }}</td>
                                                @if($type === 'store')
                                                    <td class="text-muted">{{ $item['location'] }}</td>
                                                @endif
                                                <td class="text-center">
                                                    <span class="badge bg-primary">{{ number_format($item['total_rentals']) }}</span>
                                                </td>
                                                <td class="text-center text-success fw-bold">
                                                    ${{ $item['total_revenue'] }}
                                                </td>
                                                <td class="text-center">
                                                    ${{ $item['avg_per_rental'] }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No hay datos disponibles</h5>
                                <p class="text-muted">No se encontraron rentas para el período seleccionado.</p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const storeFilterGroup = document.getElementById('storeFilterGroup');
    const reportForm = document.getElementById('reportForm');

    // Show/hide store filter based on report type
    typeSelect.addEventListener('change', function() {
        const showStoreFilter = ['revenue', 'top-customers'].includes(this.value);
        storeFilterGroup.style.display = showStoreFilter ? '' : 'none';
        
        // Reset store selection when hiding
        if (!showStoreFilter) {
            document.getElementById('store_id').value = '';
        }
    });

    // Initialize chart only for compatible report types
    const reportType = '{{ $type }}';
    @if($type === 'revenue')
        const hasChartData = @json(isset($data['byStore']) && $data['byStore']->isNotEmpty());
    @elseif($type === 'top-customers')
        const hasChartData = @json($data->isNotEmpty());
    @else
        const hasChartData = @json($data->isNotEmpty());
    @endif
    
    if (['store', 'category', 'actor'].includes(reportType) && hasChartData) {
        initializeChart();
    }
});

function initializeChart() {
    // Preparar datos para el gráfico (solo para reportes básicos)
    const chartData = @json($data);
    
    if (!chartData || chartData.length === 0) return;
    
    const labels = chartData.map(item => item.name && item.name.length > 20 ? item.name.substring(0, 20) + '...' : (item.name || 'N/A'));
    const rentalCounts = chartData.map(item => item.total_rentals || 0);
    const revenues = chartData.map(item => {
        const revenue = item.total_revenue || '0';
        return parseFloat(revenue.toString().replace(/[,$]/g, ''));
    });

    const ctx = document.getElementById('rentalChart');
    if (!ctx) return;
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Rentas',
                data: rentalCounts,
                backgroundColor: 'rgba(54, 162, 235, 0.8)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                yAxisID: 'y'
            }, {
                label: 'Ingresos ($)',
                data: revenues,
                backgroundColor: 'rgba(255, 99, 132, 0.8)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1,
                type: 'line',
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Estadísticas de Rentas - Últimos {{ $timeframe }} días'
                },
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Número de Rentas'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Ingresos ($)'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            }
        }
    });
}

// Export functions
function exportReport(format) {
    const form = document.getElementById('reportForm');
    const formData = new FormData(form);
    
    // Add format parameter
    formData.set('format', format);
    
    // Create URL with parameters
    const params = new URLSearchParams(formData);
    const url = '{{ route("admin.rental-statistics") }}?' + params.toString();
    
    // Show loading message
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generando...';
    button.disabled = true;
    
    // Create hidden iframe for download
    const iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    iframe.src = url;
    document.body.appendChild(iframe);
    
    // Reset button after delay
    setTimeout(() => {
        button.innerHTML = originalText;
        button.disabled = false;
        document.body.removeChild(iframe);
    }, 3000);
}

// Auto-submit form when filters change (except format)
document.getElementById('type').addEventListener('change', function() {
    setTimeout(() => document.getElementById('reportForm').submit(), 100);
});

document.getElementById('timeframe').addEventListener('change', function() {
    document.getElementById('reportForm').submit();
});

document.getElementById('store_id').addEventListener('change', function() {
    document.getElementById('reportForm').submit();
});
</script>
@endpush
@endsection