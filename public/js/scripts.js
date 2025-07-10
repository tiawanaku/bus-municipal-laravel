
let markerBus; 


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





/* Mostrar todos los buses */
// Diccionario para guardar marcadores por ID de dispositivo
const marcadoresBuses = {};

function mostrarUbicacionBuses() {
    fetch('/ubicacion')
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error en la red: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.length > 0) {
                data.forEach(posicion => {
                    const { latitude, longitude, uniqueId, numero_bus, ruta, course } = posicion;

                    if (latitude == null || longitude == null) return;

                    const velocidadMS = posicion.posicion?.speed ?? 0;
                    const velocidadKmh = velocidadMS * 3.6;

                    const rumboGrados = posicion.posicion?.course ?? 0; // rumbo en grados para la rotación
                    const rumboCardinal = obtenerCardinal(rumboGrados); // texto cardinal (N, NE, E, etc.)
                    const sentido = obtenerSentidoEstimado(course, rumboCardinal, ruta);

                    const popupContenido = `
                        <div>
                            <strong>Bus Número:</strong> ${numero_bus}<br>
                            <strong>Ruta:</strong> ${ruta}<br>
                            <strong>Sentido estimado:</strong> ${sentido}<br>
                            <strong>Dirección:</strong> ${rumboCardinal} (${rumboGrados}°)<br>
                            <strong>Velocidad:</strong> ${velocidadKmh.toFixed(1)} km/h
                        </div>
                    `;

                    // Crear icono con flecha rotada
                    const iconoConFlecha = L.divIcon({
                        className: '',
                        html: `
                          <div style="position: relative; width: 40px; height: 40px;">
                            <img src="img/busIcon.png" style="width: 40px; height: 40px;">
                            <div style="
                              position: absolute;
                              top: 0;
                              left: 50%;
                              transform: translateX(-70%) rotate(${rumboGrados}deg);
                              transform-origin: center;
                              width: 0;
                              height: 0;
                              border-left: 6px solid transparent;
                              border-right: 6px solid transparent;
                              border-bottom: 18px solid red;
                            "></div>
                          </div>
                        `,
                        iconSize: [40, 40],
                        iconAnchor: [20, 40]
                    });

                    if (marcadoresBuses[uniqueId]) {
                        // Actualizar posición y icono si ya existe
                        marcadoresBuses[uniqueId].setLatLng([latitude, longitude]);
                        marcadoresBuses[uniqueId].setIcon(iconoConFlecha);
                    } else {
                        // Crear nuevo marcador
                        const marcador = L.marker([latitude, longitude], {
                            icon: iconoConFlecha,
                            info: {
                                numero_bus,
                                ruta,
                                sentido,
                                velocidad_kmh: velocidadKmh
                            }
                        }).addTo(map);
                        marcador.bindPopup(popupContenido);
                        marcadoresBuses[uniqueId] = marcador;
                    }
                });
            } else {
                console.warn("No se encontraron datos de ubicación de los buses.");
            }
        })
        .catch(error => {
            console.error("Error al obtener las ubicaciones:", error);
        });
}





/* Funciión para mostrar las paradas */
function mostrarParadas() {
    
  if (!window.locationsData) return;

  window.locationsData.forEach(location => {
    const paradaIcon = L.icon({
      iconUrl: 'img/ParadaIcon.png',
      iconSize: [38, 55],
      iconAnchor: [22, 94],
      popupAnchor: [-3, -76]
    });

    const marcador = L.marker([location.latitud, location.longitud], {
      icon: paradaIcon
    }).addTo(map);

    // Inicializar popup vacío
    marcador.bindPopup('Cargando...');

    marcador.on('click', () => {
      const busesEnRuta = obtenerBusesDeMismaRuta(location);
      const bgColor = location.ruta?.color || '#6c757d';

      let filas = `
        <tr><td colspan="3" class="text-center text-gray-500">No hay buses en esta ruta</td></tr>
      `;

      if (busesEnRuta.length > 0) {
        filas = busesEnRuta.map(bus => `
          <tr class="text-center border-t hover:bg-gray-100">
            <td class="p-2 font-medium">${bus.numero_bus}</td>
            <td class="p-2">${bus.sentido}</td>
            <td class="p-2">${bus.tiempo} min</td>
          </tr>
        `).join('');
      }

      const popupContent = `
        <div class="rounded-lg overflow-hidden shadow-lg bg-white">
          <table class="min-w-full text-sm">
            <thead>
              <tr>
                <th colspan="3" style="background-color:${bgColor}" class="p-3 text-white font-semibold text-center">
                  Buses en ruta a:<br><span class="text-lg">${location.nombre_parada}</span><br>
                  Sentido:<span class="text-base">${location.sentido}</span>
                </th>
              </tr>
              <tr class="bg-gray-200 text-gray-700 text-center">
                <th class="p-2">Bus</th>
                <th class="p-2">Sentido</th>
                <th class="p-2">Tiempo</th>
              </tr>
            </thead>
            <tbody>${filas}</tbody>
          </table>
        </div>
      `;

      marcador.getPopup().setContent(popupContent);
      marcador.openPopup();
    });
  });
}
/* Fin */
/* Funcion para mostrar los avisos */
function mostrarAvisosConUbicacion() {
  if (!window.avisosData) return;

  window.avisosData.forEach(aviso => {
    if (!aviso.ubicacion) return; // Si no hay ubicación, lo ignoramos

    const { lat, lng } = aviso.ubicacion;

    // Ícono para avisos
    const avisoIcon = L.icon({
      iconUrl: 'img/AvisoIcon.svg', 
      iconSize: [38, 55],
      iconAnchor: [22, 94],
      popupAnchor: [-3, -76]
    });

    // Crear marcador y añadirlo al mapa
    const marker = L.marker([lat, lng], {
      icon: avisoIcon
    }).addTo(map);

    // Contenido del popup
    const popupContent = `
      <div class="rounded-lg overflow-hidden shadow-lg bg-white p-3">
        <h4 class="text-lg font-bold mb-1 text-red-600 text-center">🚧 Aviso 🚧</h4>
        <p><strong>Noticia:</strong> ${aviso.noticia}</p>
        <p><strong>Razón:</strong> ${aviso.razon}</p>
        <p><strong>De:</strong> ${aviso.inicio_periodo ?? 'N/D'} </p>
         <p><strong>Hasta:</strong> ${aviso.fin_periodo ?? 'N/D'}</p>
        <p><strong>Paradas Afectadas:</strong> ${aviso.paradas_afectadas}</p>
      </div>
    `;

    marker.bindPopup(popupContent);
  });
}
/* Fin */

setInterval(mostrarUbicacionBuses, 60000);
mostrarUbicacionBuses();
mostrarParadas();

mostrarAvisosConUbicacion();
/*Fin Mapa leaflet */



// Función para centrar el mapa en la ubicación de la parada
function centrarEnParada(id) {
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
      setTimeout(() => {
        actualizarTodosLosTiempos();
    }, 3000);

    
    setInterval(actualizarTodosLosTiempos, 30000);
});
  
/* Funcion para activar los sub tabs */
 document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('[data-tabs-target]');
    const contents = document.querySelectorAll('#subTabContent > div');

    buttons.forEach(button => {
        button.addEventListener('click', () => {
            // Quitar clases activas de botones y poner clases inactivas
            buttons.forEach(btn => {
                btn.classList.remove('active', 'text-gray-400', 'border-b-2', 'border-red-600');
                btn.classList.add('text-gray-400');
            });

            // Ocultar todos los contenidos
            contents.forEach(c => c.classList.add('hidden'));

            // Activar el botón actual
            button.classList.add('active', 'text-white', 'border-b-2', 'border-red-600');
            button.classList.remove('text-gray-400');

            // Mostrar el contenido asociado
            const targetId = button.getAttribute('data-tabs-target');
            document.querySelector(targetId).classList.remove('hidden');
        });
    });
});
    /* Fin */

/* Función para actualizar el tiempo de los tabs */
  function actualizarTiempoEnTabs(nombreParada, sentido, tiempo) {
 let elementos = document.querySelectorAll(`[data-parada="${nombreParada}"][data-sentido="${sentido}"] .tiempo-llegada`);
  
  

  elementos.forEach(el => {
    const tiempoActualTexto = el.textContent.replace(' min', '').trim();
    const tiempoActual = parseInt(tiempoActualTexto);

    if (isNaN(tiempoActual) || tiempo < tiempoActual) {
      el.textContent = `${tiempo} min`;
    }
  });
}
/* Fin */

function obtenerCardinal(course) {
    if (course === null || course === undefined) return 'Desconocido';

    if (course >= 337.5 || course < 22.5) return 'Norte';
    if (course >= 22.5 && course < 67.5) return 'Noreste';
    if (course >= 67.5 && course < 112.5) return 'Este';
    if (course >= 112.5 && course < 157.5) return 'Sureste';
    if (course >= 157.5 && course < 202.5) return 'Sur';
    if (course >= 202.5 && course < 247.5) return 'Suroeste';
    if (course >= 247.5 && course < 292.5) return 'Oeste';
    if (course >= 292.5 && course < 337.5) return 'Noroeste';

    return 'Desconocido';
}
/* Funcion para obtener el sentido estimado */
function obtenerSentidoEstimado(course,direccion, rutaNombre) {
    const cardinal = obtenerCardinal(course);

    if (!cardinal || !rutaNombre) return 'Desconocido';

    // Limpiar y convertir todo a minúsculas
    const ruta = rutaNombre.toLowerCase().replace(/\s+/g, '');
    const direccionLower = direccion.toLowerCase().replace(/\s+/g, '');

    

    if (ruta.includes('rutasur')) {
        if (['norte', 'noreste', 'noroeste'].includes(direccionLower)) {
            return 'Ida';
        } else if (['sur', 'sureste', 'suroeste'].includes(direccionLower)) {
            return 'Vuelta';
        }
    }

    if (ruta.includes('rutanorte')) {
        if (['este', 'noreste', 'sureste'].includes(direccionLower)) {
            return 'Ida';
        } else if (['oeste', 'noroeste', 'suroeste'].includes(direccionLower)) {
            return 'Vuelta';
        }
    }

    return 'Desconocido';
}


function obtenerBusesDeMismaRuta(location) {
 const busesCoincidentes = [];

Object.values(marcadoresBuses).forEach(marcadorBus => {
  const busInfo = marcadorBus.options.info;
  if (!busInfo) return;

  const mismaRuta = busInfo.ruta === location.ruta?.nombre;
  const mismoSentido = busInfo.sentido === location.sentido;

  if (mismaRuta && mismoSentido) {
    const latBus = marcadorBus.getLatLng().lat;
    const lonBus = marcadorBus.getLatLng().lng;
    const latParada = location.latitud;
    const lonParada = location.longitud;

    const distanciaMetros = calcularDistancia(latBus, lonBus, latParada, lonParada);
    const distanciaKm = distanciaMetros / 1000;

    // Usar velocidad mínima si el bus está detenido o sin velocidad
    const velocidadKmH = (busInfo.velocidad_kmh && busInfo.velocidad_kmh > 1) ? busInfo.velocidad_kmh : 8;

    // Evitar divisiones por cero y redondear
    const tiempoMin = Math.round((distanciaKm / velocidadKmH) * 60);

    busesCoincidentes.push({
      numero_bus: busInfo.numero_bus,
      sentido: busInfo.sentido,
      tiempo: tiempoMin
    });
  }
});

return busesCoincidentes;
}

/* Actualizar todos los tiempos, en todos los tabs */
function actualizarTodosLosTiempos() {
  if (!window.locationsData) return;

  window.locationsData.forEach(location => {
    const busesEnRuta = obtenerBusesDeMismaRuta(location);

    if (busesEnRuta.length > 0) {
      const menorTiempo = Math.min(...busesEnRuta.map(bus => bus.tiempo));
      actualizarTiempoEnTabs(location.nombre_parada, location.sentido, menorTiempo);
    } else {
      // Opcional: Si no hay buses, poner -- min o vacío
      actualizarTiempoEnTabs(location.nombre_parada, location.sentido, '--');
    }
  });
}