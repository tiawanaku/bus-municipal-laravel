<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\InventarioTalonarios;

class InventariController extends Controller
{
    public function exportarPDF(Request $request)
    {
        // Construir la consulta base
        $query = InventarioTalonarios::with('cajero');

        // Aplicar filtro de estado si existe
        $estadoFiltrado = null;
        if ($request->has('estado_preferencial') && $request->estado_preferencial !== null && $request->estado_preferencial !== '') {
            $estadoValue = (int) $request->estado_preferencial;
            $query->where('estado_preferencial', $estadoValue);

            // Mapear el valor numérico al texto correspondiente
            switch ($estadoValue) {
                case 0:
                    $estadoFiltrado = 'Asignado';
                    break;
                case 1:
                    $estadoFiltrado = 'Asignable';
                    break;
                case 2:
                    $estadoFiltrado = 'En Espera';
                    break;
                default:
                    $estadoFiltrado = 'Estado desconocido';
                    break;
            }
        }

        // Obtener los datos filtrados
        $datos = $query->get();

        // Verificar si hay datos
        if ($datos->isEmpty()) {
            // Opcional: puedes redirigir con un mensaje o mostrar un PDF vacío
            // return redirect()->back()->with('warning', 'No se encontraron datos con los filtros aplicados.');
        }

        // Generar el PDF
        $pdf = Pdf::loadView('pdf.inventario', compact('datos', 'estadoFiltrado'))
            ->setPaper('Carta', 'landscape')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'sans-serif'
            ]);

        // Configurar el nombre del archivo según si hay filtros aplicados
        $filename = 'reporte_inventario';
        if ($estadoFiltrado) {
            $filename .= '_' . strtolower(str_replace(' ', '_', $estadoFiltrado));
        }
        $filename .= '.pdf';

        return $pdf->stream($filename);
    }
}