<div>  <!-- Contenedor raíz único -->
@if($texto)
        <p>Texto recibido: <strong>{{ $texto }}</strong></p>
    @else
        <p>No se ha recibido texto aún.</p>
    @endif
</div>
