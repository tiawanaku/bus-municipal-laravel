<x-filament-panels::page>
<h1>Enviar Texto al Controlador</h1>

<form action="{{ route('enviar.texto') }}" method="POST">
    @csrf
    <button type="submit">Enviar Texto</button>
</form>

@isset($texto)
    <p><strong>Texto recibido desde el controlador:</strong> {{ $texto }}</p>
@endisset
</x-filament-panels::page>
