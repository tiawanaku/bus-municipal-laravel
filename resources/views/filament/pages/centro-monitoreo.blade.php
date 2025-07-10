<x-filament-panels::page>
    <div x-data="{ tab: 'tiempo_real', busSeleccionado: '', mostrarIframe: false }" class="space-y-6">
        <!-- TABS SUPERIORES PERSONALIZADOS -->
        <div class="flex space-x-4 border-b border-gray-200">
            <button :class="tab === 'tiempo_real' ? 'border-b-2 border-primary-600 text-primary-600' : 'text-gray-500'"
                class="px-4 py-2 focus:outline-none" @click="tab = 'tiempo_real'; mostrarIframe = false">
                Mapa en Tiempo Real
            </button>
            <button :class="tab === 'recorrido' ? 'border-b-2 border-primary-600 text-primary-600' : 'text-gray-500'"
                class="px-4 py-2 focus:outline-none" @click="tab = 'recorrido'; mostrarIframe = false">
                Mapa de Recorrido
            </button>
        </div>

        <!-- TAB: MAPA EN TIEMPO REAL -->
        <div x-show="tab === 'tiempo_real'" class="mt-4 z-0" wire:ignore>
            <div id="map" style="height: 400px; z-index: 0;"></div>
        </div>

        <!-- TAB: MAPA DE RECORRIDO -->
        <div x-show="tab === 'recorrido'" class="mt-4 space-y-4">
            <div>
                <label for="bus" class="block mb-2 text-sm font-medium text-gray-700 dark:text-white">Selecciona un
                    Bus:</label>
                <select x-model="busSeleccionado" id="bus" class="filament-input form-select bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
           focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 
           dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 w-full">
                    <option value="">-- Selecciona un bus --</option>
                    @foreach(\App\Models\Bus::all() as $bus)
                        <option value="{{ $bus->id }}">{{ $bus->numero_placa }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <button class="px-4 py-2 bg-primary-600 text-white rounded hover:bg-primary-700 disabled:opacity-50"
                    :disabled="!busSeleccionado" @click="mostrarIframe = true">
                    Ver Recorrido
                </button>


            </div>

            <template x-if="mostrarIframe">
                <iframe :src="`http://127.0.0.3:8000/tracking-data/`" width="100%" height="400" frameborder="0"
                    class="rounded border"></iframe>
            </template>
        </div>
    </div>



    <!-- LEAFLET -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <!-- OTRAS TABS DE FILAMENT (INFERIORES) -->
    <div class="filament-card p-6 shadow rounded-lg space-y-6">
        <form wire:submit.prevent="generarReporte" class="space-y-6">
            {{ $this->form }}
            <x-filament::button type="submit">Generar Reporte PDF</x-filament::button>
        </form>
        {{-- Este es el nuevo iframe, independiente del resto --}}
        @if ($iframePDF)
            <div class="mt-6 border rounded-lg overflow-hidden">
                <iframe src="{{ $iframePDF }}" style="width: 100%; height: 900px; border: none;" frameborder="0">
                </iframe>
            </div>
        @endif
    </div>
</x-filament-panels::page>
<!-- MAPA -->
<script>
    window.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            const mapElement = document.getElementById('map');
            if (!mapElement) return;

            const map = L.map('map').setView([-16.5, -68.15], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            //  Capa de tráfico TomTom
            L.tileLayer('https://api.tomtom.com/traffic/map/4/tile/flow/absolute/{z}/{x}/{y}.png?key=LCkpmtZVvEiscHf11xo3wNSu3REAQf7I', {
                attribution: '&copy; <a href="https://developer.tomtom.com">TomTom</a>'
            }).addTo(map);

            const busIcon = L.icon({
                iconUrl: '/img/busIcon.png',
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -32]
            });

            fetch('/ubicacion')
                .then(res => {
                    if (!res.ok) throw new Error(`Error en la red: ${res.statusText}`);
                    return res.json();
                })
                .then(data => {
                    if (!Array.isArray(data) || data.length === 0) {
                        console.warn('No se encontraron datos de ubicación.');
                        return;
                    }

                    data.forEach(posicion => {
                        const { latitude, longitude, numero_bus, ruta, conductor, anfitrion, posicion: detalles } = posicion;

                        if (!latitude || !longitude) return;

                        const rumbo = obtenerCardinal(detalles?.course ?? 0);

                        const popupContenido = `
        <div>
            <strong>Bus:</strong> ${numero_bus}<br>
            <strong>Ruta:</strong> ${ruta}<br>
            <strong>Conductor:</strong> ${conductor}<br>
            <strong>Anfitrión:</strong> ${anfitrion}<br>
            <strong>Dirección:</strong> ${rumbo} (${detalles?.course ?? 0}°)
        </div>
    `;

                        L.marker([latitude, longitude], { icon: busIcon })
                            .addTo(map)
                            .bindPopup(popupContenido);
                    });
                })
                .catch(error => {
                    console.error('Error cargando ubicación:', error);
                });

            //  Forzar a Leaflet a redibujar bien el mapa
            setTimeout(() => {
                map.invalidateSize();
            }, 100);
        }, 300);
    });
    function obtenerCardinal(grados) {
        const direcciones = ['N', 'NE', 'E', 'SE', 'S', 'SO', 'O', 'NO'];
        const index = Math.round(grados / 45) % 8;
        return direcciones[index];
    }
</script>