<div style="font-family: Arial, sans-serif; padding: 20px;">
    <h1 style="text-align: center; color: #0B4A9B; margin-bottom: 30px;">Reporte de Mantenimientos</h1>

    <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
        <thead>
            <tr style="background-color: #0B4A9B; color: white;">
                <th style="padding: 8px; border: 1px solid #ddd;">N° Bus</th>
                <th style="padding: 8px; border: 1px solid #ddd;">Placa</th>
                <th style="padding: 8px; border: 1px solid #ddd;">Fecha Mantenimiento</th>
                <th style="padding: 8px; border: 1px solid #ddd;">Km Actual</th>
                <th style="padding: 8px; border: 1px solid #ddd;">Km Anterior</th>
                <th style="padding: 8px; border: 1px solid #ddd;">Km Recorrido</th>
                <th style="padding: 8px; border: 1px solid #ddd;">Tipo</th>
                <th style="padding: 8px; border: 1px solid #ddd;">Observaciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($mantenimientos as $item)
                <tr>
                    <td style="padding: 6px; border: 1px solid #ddd;">{{ $item->bus->numero_bus }}</td>
                    <td style="padding: 6px; border: 1px solid #ddd;">{{ $item->bus->numero_placa }}</td>
                    <td style="padding: 6px; border: 1px solid #ddd;">{{ $item->fecha_mantenimiento }}</td>
                    <td style="padding: 6px; border: 1px solid #ddd;">{{ $item->km_actual }}</td>
                    <td style="padding: 6px; border: 1px solid #ddd;">{{ $item->km_anterior }}</td>
                    <td style="padding: 6px; border: 1px solid #ddd;">{{ $item->km_actual_recorrido }}</td>
                    <td style="padding: 6px; border: 1px solid #ddd;">{{ $item->tipo_mantenimiento }}</td>
                    <td style="padding: 6px; border: 1px solid #ddd;">{{ $item->observaciones }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
