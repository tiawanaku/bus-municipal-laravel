
let markerBus; 
const velocidadBus = 300; // Velocidad promedio del bus en metros por minuto


/* Buscador */
const searchInput = document.getElementById("search");
const suggestionsDiv = document.getElementById("suggestions");

let debounceTimer = null;


    searchInput.addEventListener("input", function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const query = this.value.trim().toLowerCase();
            suggestionsDiv.innerHTML = "";
    
            if (query.length > 0) {
                fetch(`/buscar?query=${encodeURIComponent(query)}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        const uniqueItems = new Map();
    
                        data.forEach(item => {
                            const key = `${item.nombre_parada}-${item.sentido}`;
                            if (!uniqueItems.has(key)) {
                                uniqueItems.set(key, item);
                            }
                        });
    
                        if (uniqueItems.size > 0) {
                            uniqueItems.forEach(item => {
                                const suggestionItem = document.createElement("div");
                                suggestionItem.className = "suggestion-item flex items-center gap-2 p-2 border-b hover:bg-gray-100 cursor-pointer";
    
                                suggestionItem.innerHTML = `
                                    <div class="flex-1">
                                        <strong class="text-gray-800">${item.nombre_parada}</strong><br>
                                        <span class="text-sm text-gray-500">${item.sentido}</span>
                                    </div>
                                   
                                `;
    
                                suggestionItem.addEventListener("click", function () {
                                    if (item.id_paradas) {
                                        centrarEnParada(item.id_paradas);
                                        suggestionsDiv.classList.add("hidden"); 
                                        searchInput.value = ''; 
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
        }, 300); // 300 ms de espera
    }); 



// Cerrar sugerencias si hace click fuera
document.addEventListener("click", function (event) {
    if (!searchInput.contains(event.target) && !suggestionsDiv.contains(event.target)) {
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



const btn = L.control({ position: 'bottomright' });

btn.onAdd = function (map) {
    const div = L.DomUtil.create('div', 'leaflet-bar leaflet-control');
    div.style.backgroundColor = 'white';
    div.style.padding = '5px';
    div.style.cursor = 'pointer';
    div.title = 'Mostrar mi ubicación';
    div.innerHTML = '<img src="https://cdn-icons-png.flaticon.com/512/684/684908.png" width="20" />';

    div.onclick = function () {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    L.marker([lat, lng]).addTo(map)
                        .bindPopup("Estás aquí")
                        .openPopup();
                    map.setView([lat, lng], 16);
                },
                (err) => {
                    alert("No se pudo obtener la ubicación.");
                },
                {
                    enableHighAccuracy: true
                }
            );
        } else {
            alert("Geolocalización no es soportada por este navegador.");
        }
    };

    return div;
};

btn.addTo(map);


/* Seguimiento del Bus */

var busIcon = L.icon({
    iconUrl: '/img/busIcon.png',


    iconSize: [40, 40], 

    iconAnchor: [22, 94], 

    popupAnchor: [-3, -76] 
});




 /* Agregando las rutas al mapa con los colores de la base de datos*/

const recorridosValidos = window.rutasData.filter(r => r.recorrido != null);

recorridosValidos.forEach(function (ruta) {
    const recorrido = ruta.recorrido;

    if (recorrido && recorrido.geojson && recorrido.geojson.features) {

        // Si ruta.color no existe o es null, se usará gris por defecto
        const color = ruta.color || '#101218';

        recorrido.geojson.features.forEach(function (feature) {
            const latlngs = feature.geometry.coordinates.map(coord => [coord[1], coord[0]]);

            L.polyline(latlngs, {
                color: color,
                weight: 4,
                opacity: 0.7,
                smoothFactor: 1
            })
            .bindPopup(`<strong>${ruta.nombre}</strong>`)
            .addTo(map);
        });
    }
});




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
    window.locationsData.forEach(location => {
        const distancia = calcularDistancia(busLat, busLng, location.latitud, location.longitud);
        const tiempoEstimado = Math.round(distancia / velocidadBus);

       
        let bgColorStyle = 'background-color: #6c757d;'; // Por defecto gris
        if (location.ruta && location.ruta.color) {
            bgColorStyle = `background-color: ${location.ruta.color};`;
        }

        const popupContent = `
            <div class="rounded-lg overflow-hidden shadow-lg bg-white">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr>
                            <th colspan="3" style="${bgColorStyle}" class="p-3 text-white font-semibold text-center">
                                Buses próximos a llegar en <br><span class="text-lg">${location.nombre_parada}</span>
                            </th>
                        </tr>
                        <tr class="bg-gray-200 text-gray-700 text-center">
                            <th class="p-2">Nombre de la Parada</th>
                            <th class="p-2">Minutos para llegar</th>
                            <th class="p-2">Sentido</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="text-center border-t hover:bg-gray-100">
                            <td class="p-2 font-medium">${location.nombre_parada}</td>
                            <td class="p-2 text-green-600 font-bold">${tiempoEstimado} min</td>
                            <td class="p-2">${location.sentido}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        `;

        actualizarTiempoEnTabs(location.nombre_parada, location.sentido, tiempoEstimado);

        const paradaIcon = L.icon({
            iconUrl: 'img/ParadaIcon.png',
            iconSize: [38, 55],
            iconAnchor: [22, 94],
            popupAnchor: [-3, -76]
        });

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
    /* console.log("Centrando en la parada con ID:", id);  */
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
            window.scrollTo(0, 0); 

        })
        .catch(error => console.error("Error al centrar la parada:", error));
}



/* Js para Tabs Estado del servicio */
document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('.tab-btn');
    const contents = document.querySelectorAll('.tab-content');
    
    buttons.forEach(button => {
        button.addEventListener('click', function () {
            const tab = this.getAttribute('data-tab');
      
            buttons.forEach(btn => {
                btn.classList.replace('bg-gradient-to-r', 'bg-gray-900');
                btn.classList.remove('border');  
                btn.classList.add('text-blue-600');  
            });

            this.classList.replace('bg-gray-900', 'bg-gradient-to-r');
            this.classList.add('from-red-800', 'to-red-900', 'shadow-md', 'text-white'); 
            contents.forEach(content => content.classList.add('hidden'));
            document.getElementById(tab).classList.remove('hidden');
        });
    });
});
  
/* Funcion para activar los sub tabs */
 document.addEventListener('DOMContentLoaded', function () {
        const buttons = document.querySelectorAll('[data-tabs-target]');
        const contents = document.querySelectorAll('#subTabContent > div');

        buttons.forEach(button => {
            button.addEventListener('click', () => {
                // Quitar clases activas de botones
                buttons.forEach(btn => btn.classList.remove('active', 'text-blue-600', 'border-b-2', 'border-blue-600'));

                // Ocultar todos los contenidos
                contents.forEach(c => c.classList.add('hidden'));

                // Activar el botón actual
                button.classList.add('active', 'text-blue-600', 'border-b-2', 'border-blue-600');

                // Mostrar el contenido asociado
                const targetId = button.getAttribute('data-tabs-target');
                document.querySelector(targetId).classList.remove('hidden');
            });
        });
    });
    /* Fin */

/* Función para actualizar el tiempo de los tabs */
  function actualizarTiempoEnTabs(nombreParada, sentido, tiempo) {
   
  /*   let elementos = document.querySelectorAll(`[data-parada="${nombreParada}"] .tiempo-llegada`); */
  let elementos = document.querySelectorAll(`[data-parada="${nombreParada}"][data-sentido="${sentido}"] .tiempo-llegada`);
    
    elementos.forEach(el => el.textContent = `${tiempo} min`);
}
/* Fin */

