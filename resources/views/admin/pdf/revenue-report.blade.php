<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Ingresos - Sakila VideoClub</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #dc3545;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #dc3545;
            font-size: 24px;
            margin: 0;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .summary {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 25px;
        }
        .summary h2 {
            color: #dc3545;
            font-size: 16px;
            margin: 0 0 10px 0;
        }
        .summary-stats {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        .stat-item {
            text-align: center;
            margin: 5px;
        }
        .stat-value {
            font-size: 18px;
            font-weight: bold;
            color: #dc3545;
        }
        .stat-label {
            font-size: 11px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        th {
            background-color: #dc3545;
            color: white;
            font-weight: bold;
            font-size: 11px;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .total-row {
            background-color: #dc3545 !important;
            color: white;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
            font-size: 10px;
            color: #666;
        }
        .section-title {
            color: #dc3545;
            font-size: 14px;
            font-weight: bold;
            margin: 20px 0 10px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Ingresos</h1>
        <p>Sakila VideoClub - Sistema de Gestión</p>
        <p>Generado el: {{ now()->format('d/m/Y H:i:s') }}</p>
        @if($period)
            <p>Período: {{ $period }}</p>
        @endif
    </div>

    <div class="summary">
        <h2>Resumen Ejecutivo</h2>
        <div class="summary-stats">
            <div class="stat-item">
                <div class="stat-value">${{ number_format($totalRevenue, 2) }}</div>
                <div class="stat-label">Ingresos Totales</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $totalRentals }}</div>
                <div class="stat-label">Total Rentas</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">${{ number_format($totalLateFees, 2) }}</div>
                <div class="stat-label">Multas Totales</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">${{ number_format($averagePerRental, 2) }}</div>
                <div class="stat-label">Promedio por Renta</div>
            </div>
        </div>
    </div>

    @if($revenueByStore->count() > 0)
    <div class="section-title">Ingresos por Tienda</div>
    <table>
        <thead>
            <tr>
                <th>Tienda</th>
                <th>Manager</th>
                <th>Total Rentas</th>
                <th>Ingresos</th>
                <th>Multas</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($revenueByStore as $store)
            <tr>
                <td>{{ $store->store_name }}</td>
                <td>{{ $store->manager_name }}</td>
                <td>{{ $store->total_rentals }}</td>
                <td>${{ number_format($store->rental_revenue, 2) }}</td>
                <td>${{ number_format($store->late_fees, 2) }}</td>
                <td>${{ number_format($store->total_revenue, 2) }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2"><strong>TOTAL GENERAL</strong></td>
                <td><strong>{{ $revenueByStore->sum('total_rentals') }}</strong></td>
                <td><strong>${{ number_format($revenueByStore->sum('rental_revenue'), 2) }}</strong></td>
                <td><strong>${{ number_format($revenueByStore->sum('late_fees'), 2) }}</strong></td>
                <td><strong>${{ number_format($revenueByStore->sum('total_revenue'), 2) }}</strong></td>
            </tr>
        </tbody>
    </table>
    @endif

    @if($revenueByMonth && $revenueByMonth->count() > 0)
    <div class="section-title">Ingresos por Mes</div>
    <table>
        <thead>
            <tr>
                <th>Mes</th>
                <th>Total Rentas</th>
                <th>Ingresos</th>
                <th>Multas</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($revenueByMonth as $month)
            <tr>
                <td>{{ $month->month_name }}</td>
                <td>{{ $month->total_rentals }}</td>
                <td>${{ number_format($month->rental_revenue, 2) }}</td>
                <td>${{ number_format($month->late_fees, 2) }}</td>
                <td>${{ number_format($month->total_revenue, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="footer">
        <p>Reporte generado automáticamente por el Sistema de Gestión Sakila VideoClub</p>
        <p>Página 1 de 1</p>
    </div>
</body>
</html>