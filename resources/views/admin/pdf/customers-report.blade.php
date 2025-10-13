<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Top Clientes - Sakila VideoClub</title>
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
        .rank-1 { background-color: #ffd700 !important; }
        .rank-2 { background-color: #c0c0c0 !important; }
        .rank-3 { background-color: #cd7f32 !important; }
        .rank-medal {
            font-weight: bold;
            text-align: center;
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
        <h1>Top Clientes</h1>
        <p>Sakila VideoClub - Clientes con Mayor Número de Rentas</p>
        <p>Generado el: {{ now()->format('d/m/Y H:i:s') }}</p>
        @if($period)
            <p>Período: {{ $period }}</p>
        @endif
    </div>

    <div class="summary">
        <h2>Resumen de Clientes</h2>
        <p><strong>Total de clientes analizados:</strong> {{ $customers->count() }}</p>
        <p><strong>Cliente más activo:</strong> {{ $customers->first()->full_name ?? 'N/A' }} 
           ({{ $customers->first()->total_rentals ?? 0 }} rentas)</p>
    </div>

    <div class="section-title">Ranking de Clientes por Número de Rentas</div>
    <table>
        <thead>
            <tr>
                <th>Posición</th>
                <th>Cliente</th>
                <th>Email</th>
                <th>Tienda</th>
                <th>Total Rentas</th>
                <th>Monto Total</th>
                <th>Última Renta</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customers as $index => $customer)
            <tr class="@if($index === 0) rank-1 @elseif($index === 1) rank-2 @elseif($index === 2) rank-3 @endif">
                <td class="rank-medal">
                    @if($index === 0) 🥇
                    @elseif($index === 1) 🥈
                    @elseif($index === 2) 🥉
                    @else {{ $index + 1 }}
                    @endif
                </td>
                <td>{{ $customer->full_name }}</td>
                <td>{{ $customer->email }}</td>
                <td>Tienda {{ $customer->store_id }}</td>
                <td>{{ $customer->total_rentals }}</td>
                <td>${{ number_format($customer->total_amount, 2) }}</td>
                <td>{{ $customer->last_rental ? \Carbon\Carbon::parse($customer->last_rental)->format('d/m/Y') : 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Reporte generado automáticamente por el Sistema de Gestión Sakila VideoClub</p>
        <p>Página 1 de 1</p>
    </div>
</body>
</html>