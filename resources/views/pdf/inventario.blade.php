<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Inventario Talonarios</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; margin-bottom: 80px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { text-align: center; margin-top: 10px; }
        .logo-container {
            text-align: center;
            margin-top: 10px;
        }
        .logo-container img {
            width: 100%;
            max-width: 600px;
        }

        .footer-logo {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            text-align: center;
        }

        .footer-logo img {
            width: 100%;
            max-width: 600px;
        }
    </style>
</head>
<body>

    <div class="logo-container">
        <img src="{{ public_path('img/Galeria/encabezado.png') }}" alt="Encabezado">
    </div>

    <h2>Reporte de Inventario de Talonarios</h2>

    <p style="text-align: right; font-size: 12px;">
        PDF Generado el: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
    </p>

    <table>
    <thead>
        <tr>
            <th>Custodio</th>
            <th>Pref. Rango Inicial</th>
            <th>Pref. Rango Final</th>
            <th>Cantidad Preferenciales</th>
            <th>Reg. Rango Inicial</th>
            <th>Reg. Rango Final</th>
            <th>Cantidad Regulares</th>
            <th>Total Bs.</th>
            <th>Fecha Entrega</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($datos as $index => $item)
            <tr>
                <td>{{ $item->cajero->nombre_completo ?? 'N/A' }}</td>
                <td>{{ $item->rango_inicial_preferencial }}</td>
                <td>{{ $item->rango_final_preferencial }}</td>
                <td>{{ $item->cantidad_preferenciales }}</td>
                <td>{{ $item->rango_inicial_regular }}</td>
                <td>{{ $item->rango_final_regular }}</td>
                <td>{{ $item->cantidad_regulares }}</td>
                <td>{{ number_format($item->total_recaudacion_bolivianos, 2) }}</td>
                <td>{{ \Carbon\Carbon::parse($item->fecha_entrega)->format('d/m/Y') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

    <div class="footer-logo">
        <img src="{{ public_path('img/Galeria/pie de pagina.png') }}" alt="Pie de página">
    </div>

</body>
</html>
