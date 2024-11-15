<div>
  


 
<div x-data="{ open: @entangle('isOpen') }">
    <!-- Botón para abrir el modal -->
    <button @click="open = true" class="bg-blue-500 text-white p-2 rounded">
        Ver Mapa de Seguimiento
    </button>

    <!-- Modal -->
    <div x-show="open" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
        <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full relative">
            <!-- Botón para cerrar el modal -->
            <button @click="open = false" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">
                &times;
            </button>

            <h2 class="text-2xl font-semibold mb-4">Mapa de Seguimiento</h2>
            
            <!-- Contenedor del mapa -->
            <div id="map" style="height: 300px; width: 100%;"></div>
        </div>
    </div>

    <!-- Leaflet CSS y JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <!-- Inicialización del mapa -->
    <script>
        document.addEventListener('livewire:load', () => {
            // Crear el mapa dentro del modal
            const map = L.map('map').setView([0, 0], 2);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            
        });
    </script>
</div>



</div>
