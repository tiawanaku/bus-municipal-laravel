<!-- Mapa -->
<div id="map" style="height: 400px; width: 100%;"></div>

<!-- Campo oculto para almacenar el punto -->
<input type="hidden" name="lat_long" value="">

@push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Obtener referencia al campo para el punto
            var pointField = document.querySelector('input[name="lat_long"]');

            if (!pointField) {
                console.error('El campo lat_long no se encuentra en el DOM');
                return; // Salir si el campo no se encuentra
            }

            // Inicializar el mapa
            var map = L.map('map').setView([-16.51740, -68.20364], 13); // Coordenadas iniciales

            // Cargar las imágenes del mapa de OpenStreetMap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Inicializar el marcador
            var fixedMarker = null; // Este marcador se fijará en la ubicación cuando se haga clic

            // Evento para fijar el marcador cuando se haga clic en el mapa
            map.on('click', function (e) {
                var lat = e.latlng.lat;
                var lng = e.latlng.lng;

                // Si ya existe un marcador fijo, eliminarlo antes de agregar uno nuevo
                if (fixedMarker) {
                    map.removeLayer(fixedMarker);
                }

                // Fijar el marcador en la ubicación clickeada
                fixedMarker = L.marker([lat, lng]).addTo(map);

                // Actualizar el campo con el punto en formato "POINT(lng lat)"
                pointField.value = `POINT(${lng} ${lat})`; // Almacena las coordenadas como un punto
            });
        });
    </script>
@endpush
