<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Inventario Talonarios</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; margin-bottom: 120px; }
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
            bottom: 10px;
            left: 0;
            width: 100%;
            text-align: center;
        }
        .footer-logo img {
            width: 100%;
            max-width: 600px;
        }
        .filtros-info {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
            border-left: 4px solid #007bff;
        }
        .totales-container {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .totales-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-weight: bold;
        }
        .total-final {
            background-color: #007bff;
            color: white;
            padding: 10px;
            border-radius: 3px;
            text-align: center;
            font-size: 14px;
        }
        .numero-autorizacion {
            font-size: 10px;
            color: #666;
        }
        .firmas-container {
            position: fixed;
            bottom: 150px;
            left: 0;
            width: 100%;
            display: table;
            table-layout: fixed;
            padding: 0 30px;
        }
        .firma-box {
            display: table-cell;
            text-align: center;
            width: 10%;
            vertical-align: top;
        }
        .firma-box:first-child {
            padding-right: 20px;
        }
        .firma-box:last-child {
            padding-left: 20px;
        }
        .firma-linea {
            border-top: 1px solid #000;
            margin-bottom: 5px;
            height: 10px;
            margin-top: 10px;
        }
        .firma-texto {
            font-size: 10px;
            font-weight: bold;
        }
        .fecha-hora {
            text-align: right;
            font-size: 10px;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>

    <div class="logo-container">
        @if(file_exists(public_path('img/Galeria/encabezado.png')))
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('img/Galeria/encabezado.png'))) }}" alt="Encabezado">
        @else
            <div style="border: 2px solid #ddd; padding: 20px; text-align: center; background-color: #f8f9fa;">
                <h3 style="margin: 0; color: #666;">LOGO EMPRESA</h3>
                <p style="margin: 5px 0; color: #999;">Encabezado del Reporte</p>
            </div>
        @endif
    </div>

    <h2>Reporte de Inventario de Talonarios</h2>

    <p style="text-align: right; font-size: 12px;">
        PDF Generado el: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
    </p>

    @if($estadoFiltrado)
        <div class="filtros-info">
            <strong>Filtros aplicados:</strong> Estado: {{ $estadoFiltrado }}
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Custodio</th>
                <th>Núm. Autorización</th>
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
                    <td class="numero-autorizacion">{{ $item->numero_autorizacion ?? 'N/A' }}</td>
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

    {{-- Sección de Totales --}}
    <div class="totales-container">
        <h3 style="margin-top: 0; color: #333;">Resumen de Totales</h3>

        <div class="totales-row">
            <span>Total Cantidad Preferenciales:</span>
            <span>{{ number_format($datos->sum('cantidad_preferenciales')) }}</span>
        </div>

        <div class="totales-row">
            <span>Total Cantidad Regulares:</span>
            <span>{{ number_format($datos->sum('cantidad_regulares')) }}</span>
        </div>

        <div class="totales-row">
            <span>Total Recaudación:</span>
            <span>{{ number_format($datos->sum('total_recaudacion_bolivianos'), 2) }} Bs.</span>
        </div>

        <div class="totales-row">
            <span>Total Registros:</span>
            <span>{{ $datos->count() }}</span>
        </div>

        @if($estadoFiltrado)
            <div class="total-final">
                Registros {{ $estadoFiltrado }}: {{ $datos->count() }} |
                Total: {{ number_format($datos->sum('total_recaudacion_bolivianos'), 2) }} Bs.
            </div>
        @else
            <div class="total-final">
                Total General: {{ number_format($datos->sum('total_recaudacion_bolivianos'), 2) }} Bs.
            </div>
        @endif
    </div>

    <div class="footer-logo">
        @if(file_exists(public_path('img/Galeria/pie de pagina.png')))
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('img/Galeria/pie de pagina.png'))) }}" alt="Pie de página">
        @else
            <div style="border-top: 2px solid #ddd; padding: 10px; text-align: center; background-color: #f8f9fa;">
                <p style="margin: 0; color: #666; font-size: 10px;">Pie de página del reporte</p>
            </div>
        @endif
    </div>

    {{-- Sección de Firmas --}}
    <div class="firmas-container">
        <div class="firma-box">
            <div class="firma-linea"></div>
            <div class="firma-texto">
                RESPONSABLE DE INVENTARIO<br>
                Nombre: _________<br>
                C.I.: _________
            </div>
        </div>

        <div class="firma-box">
            <div class="firma-linea"></div>
            <div class="firma-texto">
                SUPERVISOR GENERAL<br>
                Nombre: _________<br>
                C.I.: _________
            </div>
        </div>
    </div>

</body>
</html>
