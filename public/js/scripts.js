
/* Menu hambuerguesa */
const hamburger = document.getElementById('hamburger');
const sidebar = document.getElementById('sidebar');

hamburger.addEventListener('click', function () {
    sidebar.classList.toggle('-translate-x-full');
});
/* Fin Menu Hamburguesa */


/* Buscador */
const searchInput = document.getElementById("search");
const suggestionsDiv = document.getElementById("suggestions");

searchInput.addEventListener("input", function () {
    const query = this.value.toLowerCase();
    suggestionsDiv.innerHTML = "";

    if (query.length > 0) {
        fetch(`/buscar?query=${query}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.length > 0) {
                    data.forEach(item => {
                        const suggestionItem = document.createElement("div");
                        suggestionItem.classList.add("suggestion-item", "p-2", "hover:bg-gray-200");
                        suggestionItem.textContent = item.nombre_parada; // Nombre de la parada

                        
                        suggestionItem.addEventListener("click", function () {
                            if (item.id_paradas) {
                                centrarEnParada(item.id_paradas);
                            } else {
                                console.error("ID de parada no encontrado:", item);
                            }
                        });

                        suggestionsDiv.appendChild(suggestionItem);
                    });
                    suggestionsDiv.classList.remove("hidden");
                } else {
                    suggestionsDiv.classList.add("hidden");
                }
            })
            .catch(error => console.error("Error fetching suggestions:", error));
    } else {
        suggestionsDiv.classList.add("hidden");
    }
});
document.addEventListener("click", function (event) {
    if (!searchInput.contains(event.target)) {
        suggestionsDiv.classList.add("hidden");
    }
});




/* Fin Buscador */


/* Mapa leaflet */

let map = L.map("mapa_bus", { zoomControl: false }).setView([-16.5051, -68.1635], 13);
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

L.control.zoom({
    position: 'bottomright'
}).addTo(map);
/* Seguimiento del Bus */

var busIcon = L.icon({
    iconUrl: '/img/busIcon.png',


    iconSize: [40, 40], 

    iconAnchor: [22, 94], 

    popupAnchor: [-3, -76] 
});

let markerBus; 
const velocidadBus = 300; // Velocidad promedio del bus en metros por minuto

// Función para calcular la distancia entre dos coordenadas usando la fórmula de Haversine
function calcularDistancia(lat1, lon1, lat2, lon2) {
    const R = 6371e3; // Radio de la Tierra en metros
    const phi1 = lat1 * Math.PI / 180;
    const phi2 = lat2 * Math.PI / 180;
    const deltaPhi = (lat2 - lat1) * Math.PI / 180;
    const deltaLambda = (lon2 - lon1) * Math.PI / 180;

    const a = Math.sin(deltaPhi / 2) * Math.sin(deltaPhi / 2) +
              Math.cos(phi1) * Math.cos(phi2) *
              Math.sin(deltaLambda / 2) * Math.sin(deltaLambda / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

    return R * c; 
}

// Función para mostrar la ubicación del bus en el mapa
function mostrarUbicacionBus() {
    fetch('/ubicacion')
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error en la red: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.length > 0) {
                const { latitude, longitude } = data[0];

                // Actualizar la posición del marcador del bus
                if (markerBus) {
                    markerBus.setLatLng([latitude, longitude]);
                } else {
                    markerBus = L.marker([latitude, longitude], { icon: busIcon }).addTo(map);
                }

                
                mostrarTiempoEstimadoDeLlegada(latitude, longitude);
            } else {
                console.warn("No se encontraron datos de ubicación del bus.");
            }
        })
        .catch(error => {
            console.error("Error al obtener la ubicación:", error);
        });
}

// Función para mostrar el tiempo estimado de llegada en cada parada
function mostrarTiempoEstimadoDeLlegada(busLat, busLng) {
    locations.forEach(location => {
        const distancia = calcularDistancia(busLat, busLng, location.latitud, location.longitud);
        const tiempoEstimado = Math.round(distancia / velocidadBus);

        
        const popupContent = `
            <table class="min-w-full border-collapse">
                <thead>
                    <tr>
                        <th class="bg-gray-600 p-2 text-white font-bold" colspan="3">Buses próximos a llegar en ${location.nombre_parada}</th>
                    </tr>
                    <tr>
                        <th class="bg-gray-600 p-2 text-white font-bold">Nombre de la Parada</th>
                        <th class="bg-gray-600 p-2 text-white font-bold">Minutos para llegar</th>
                        <th class="bg-gray-600 p-2 text-white font-bold">Sentido</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="bg-gray-100 border">
                        <td class="p-2">${location.nombre_parada}</td>
                        <td class="p-2">${tiempoEstimado} min</td>
                        <td class="p-2">Vuelta</td> 
                </tbody>
            </table>
        `;
        var paradaIcon = L.icon({
            iconUrl: 'img/ParadaIcon.png',


            iconSize: [38, 85], 

            iconAnchor: [22, 94], 

            popupAnchor: [-3, -76] 
        });
        console.log(locations);
        
        const marker = L.marker([location.latitud, location.longitud], { icon: paradaIcon }).addTo(map);
        marker.bindPopup(popupContent);
        marker.on('click', function () {
            marker.openPopup();
        });
    });
}


setInterval(mostrarUbicacionBus, 60000);
mostrarUbicacionBus();
/*Fin Mapa leaflet */



// Función para centrar el mapa en la ubicación de la parada
function centrarEnParada(id) {
    console.log("Centrando en la parada con ID:", id); 
    fetch(`/ubicacionparada?id_paradas=${id}`) 
        .then(response => {
            if (!response.ok) {
                throw new Error('Error al obtener la ubicación de la parada');
            }
            return response.json();
        })
        .then(parada => {
            const { latitud, longitud } = parada; 
            map.setView([latitud, longitud], 18); 
        })
        .catch(error => console.error("Error al centrar la parada:", error));
}

/* Para las grids de paradas */
const paradaImages = document.querySelectorAll('.parada-image');


paradaImages.forEach(image => {
    image.addEventListener('click', function () {
        const idParada = this.getAttribute('data-id'); 
        centrarEnParada(idParada); 
    });
});
/* Fin Grids */

/* Radio Button SENTIDO IDA - VUELTA */
document.addEventListener("DOMContentLoaded", function() {
    
    var containerNorte = document.getElementById("ruta-norte");
    var containerSur = document.getElementById("ruta-sur");

    if (containerNorte && containerSur) {
       
        var imagenesNorte = Array.from(containerNorte.querySelectorAll("img"));
        var imagenesSur = Array.from(containerSur.querySelectorAll("img"));

        
        function actualizarImagenes(container, imagenes, orden) {
            
            var imagenesOrdenadas = [...imagenes];

            
            if (orden === "ida") {
                imagenesOrdenadas.reverse();
            }

           
            container.innerHTML = '';

            
            imagenesOrdenadas.forEach(function(imagen) {
                var div = document.createElement('div');
                div.appendChild(imagen); 
                container.appendChild(div); 
            });
        }

        
        document.querySelectorAll('input[name="ruta"]').forEach(function(radio) {
            radio.addEventListener("change", function() {
               
                actualizarImagenes(containerNorte, imagenesNorte, this.value);
                actualizarImagenes(containerSur, imagenesSur, this.value);
            });
        });

        
        actualizarImagenes(containerNorte, imagenesNorte, 'vuelta');
        actualizarImagenes(containerSur, imagenesSur, 'vuelta');
    }
});

/* Fin Radio Button */