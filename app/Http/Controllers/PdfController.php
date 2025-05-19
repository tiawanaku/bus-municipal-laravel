<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Mantenimiento;

class PdfController extends Controller
{
    //
    public function MantenimientosRecords(){

        if (!auth()->check()) {
            abort(404); 
        }

        $mantenimientos=Mantenimiento::all();
        $pdf=Pdf::loadView('pdf.example',['mantenimientos'=>$mantenimientos]);
        return $pdf->stream();
        }
}
