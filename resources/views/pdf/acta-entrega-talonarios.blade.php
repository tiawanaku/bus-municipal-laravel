{{-- resources/views/pdf/acta-entrega-talonarios.blade.php --}}
<html>
<head>
    <style>
        @page { size: letter; margin: 1in; }
        body { 
            font-family: DejaVu Sans, sans-serif; 
            font-size: 12px; 
            margin: 0; padding: 0; 
            position: relative; 
            min-height: 100vh; 
            padding-bottom: 120px; 
        }
        .encabezado { text-align: center; margin-bottom: 20px; }
        .encabezado img { max-width: 100%; height: auto; }
        h2 { text-align: center; text-decoration: underline; margin-bottom: 20px; }
        table { 
            width: 70%; border-collapse: collapse; margin: 15px auto; font-size: 10px; 
        }
        td, th { 
            border: 1px solid #000; padding: 4px; text-align: center; 
            vertical-align: middle; 
        }
        th { background-color: #f0f0f0; font-weight: bold; }
        .no-border { border: none; background-color: transparent; }
        .firmas-finales { 
            margin-top: 60px; width: 60%; margin-left: auto; 
            margin-right: auto; clear: both; 
        }
        .firma-izquierda, .firma-derecha { 
            width: 45%; text-align: center; 
        }
        .firma-izquierda { float: left; }
        .firma-derecha { float: right; }
        .firma-izquierda p, .firma-derecha p { 
            margin: 3px 0; line-height: 1.2; 
        }
        .pie-pagina { 
            margin-top: 80px; text-align: center; clear: both; 
            position: absolute; bottom: 0; left: 0; right: 0; 
        }
        .pie-pagina img { max-width: 100%; height: auto; }
        .fecha-generacion { 
            position: absolute; top: -20px; right: 10px; 
            font-size: 8px; color: #333; font-weight: bold; 
            background-color: rgba(255, 255, 255, 0.9); 
            padding: 3px 8px; border-radius: 3px; border: 1px solid #ccc; 
        }
        p { text-align: justify; line-height: 1.4; margin: 15px 0; }
    </style>
</head>
<body>
    @php
        $encabezadoPath = public_path('img/Galeria/encabezado.png');
        $piePath = public_path('img/Galeria/pie de pagina.png');
        $encabezadoBase64 = file_exists($encabezadoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($encabezadoPath)) : '';
        $pieBase64 = file_exists($piePath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($piePath)) : '';
    @endphp

    <div class="encabezado">
        @if($encabezadoBase64)
            <img src="{{ $encabezadoBase64 }}" alt="Encabezado">
        @else
            <h3>EMPRESA - ENCABEZADO</h3>
        @endif
    </div>

    <h2>ACTA DE ENTREGA DE TALONARIOS</h2>

    <p>
        Mediante la presente Acta, se efectúa la entrega de talonarios 
        <strong>{{ strtoupper($tipo_talonario) }}</strong> a la siguiente persona:
    </p>

    <table>
        <tr>
            <th>N°</th>
            <th>CAJERO (A)</th>
            <th>C.I.</th>
        </tr>
        <tr>
            <td>1</td>
            <td>{{ $nombreCompleto }}</td>
            <td>{{ $ci }}</td>
        </tr>
    </table>

    <p>
        Al respecto, se aclara que los mismos se harán responsables por la asignación y 
        recaudo de las FACTURAS PRE VALORADAS, siendo el rango de las facturas de acuerdo 
        al siguiente detalle:
    </p>

    <table>
        <tr>
            <th>TIPO DE TICKET</th>
            <th colspan="2">TALONARIO</th>
            <th>RANGO DE FACTURAS</th>
        </tr>
        <tr>
            <th class="no-border"></th>
            <th>DE</th>
            <th>A</th>
            <th></th>
        </tr>
        {!! $filasTabla !!}
    </table>

    <p>
        El incumplimiento, si corresponde, será pasivo a sanciones administrativas. 
        Dando el asentimiento al contenido de la presente Acta de Corresponsabilidad, 
        es firmado en la ciudad de El Alto, a los {{ $fechaActual }}.
    </p>
    
    <div class="firmas-finales">
        <div class="firma-izquierda">
            <br><br><br>
            <p>_________________________</p>
            <p><strong>Firma del Cajero</strong></p>
            <p>{{ $nombreCompleto }}</p>
            <p>C.I.: {{ $ci }}</p>
        </div>
        <div class="firma-derecha">
            <br><br><br>
            <p>_________________________</p>
            <p><strong>Encargado de Cajeros</strong></p>
            <p><strong>Firma y Sello</strong></p>
        </div>
    </div>

    <div class="pie-pagina">
        @if($pieBase64)
            <img src="{{ $pieBase64 }}" alt="Pie de página">
        @else
            <p><strong>Dirección de la empresa | Teléfono | Email</strong></p>
        @endif
        <div class="fecha-generacion">
            PDF generado el: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}
        </div>
    </div>
</body>
</html>