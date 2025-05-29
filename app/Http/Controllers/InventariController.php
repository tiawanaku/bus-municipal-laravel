<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\InventarioTalonarios;

class InventariController extends Controller
{
    public function exportarPDF()
    {
        $datos = InventarioTalonarios::with('cajero')->get();

        $pdf = Pdf::loadView('pdf.inventario', compact('datos'))
            ->setPaper('Carta', 'landscape'); // 👈 Esto activa el modo horizontal

        return $pdf->stream('reporte_inventario.pdf');
    }
}