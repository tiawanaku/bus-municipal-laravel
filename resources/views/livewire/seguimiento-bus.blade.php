<div>
    <h1 class="text-2xl font-bold">Seguimiento de Bus</h1>

    <!-- Formulario para filtrar por fechas -->
    <form id="trackingForm" wire:submit.prevent="getTrackingData">
        <div>
            <label for="start_date">Fecha de inicio:</label>
            <input type="date" id="startDate" wire:model="startDay"
                class="form-input bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
        </div>
        <div>
            <label for="end_date">Fecha de fin:</label>
            <input type="date" id="endDate" wire:model="endDay"
                class="form-input bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
        </div>
        <button type="submit" class="bg-blue-500 text-white p-2 mt-2">Consultar</button>
    </form>

    <!-- Mensaje de error si no hay datos -->
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <h1>Datos de Seguimiento de Buses</h1>

 

    <!-- Mapa -->
    <h2 class="mt-5">Mapa de Ubicaciones</h2>

    <!-- Cargar estilos y scripts de Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- Contenedor del mapa -->
    <div id="mapId" style="height: 500px;"></div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Inicializar el mapa
        var map = L.map('mapId').setView([-16.5, -68.2], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);
        let testMarker = L.marker([-16.500000, -68.119000]) // Coordenadas válidas
    .addTo(map)
    .bindPopup('Marcador de prueba');
        

        @if(count($trackingData) > 0)
        console.log("Datos de seguimiento:", {!! json_encode($trackingData) !!});
            @foreach($trackingData as $registro)
                let lat = parseFloat("{{ $registro['latitude'] }}");
                let lng = parseFloat("{{ $registro['longitude'] }}");

                if (!isNaN(lat) && !isNaN(lng)) {
                    let marcador = L.marker([lat, lng])
                        .addTo(map)
                        .bindPopup('<b>ID: {{ $registro["id"] }}</b><br>{{ $registro["recorded_at_bolivia"] }}');
                } else {
                    console.error("Latitud o longitud no válidas para el registro con ID: {{ $registro['id'] }}");
                }
            @endforeach
        @else
            console.error("No hay datos de seguimiento disponibles.");
        @endif
    });
</script>


</div>