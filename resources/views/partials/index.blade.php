<div class="flex flex-col h-full mb-16">
    <div class="bg-cover bg-center h-24 flex items-center justify-between px-4">
        <form class="max-w-md ml-0 mt-4">
            <label for="search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Buscar</label>
            <div class="relative">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                    </svg>
                </div>
                <input type="search" id="search"
                    class="block w-full md:w-1/4 lg:w-16 p-4 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Buscar Paradas" required />
                <div id="suggestions"
                    class="absolute z-50 bg-white border border-gray-200 rounded-lg shadow-md max-h-52 overflow-y-auto w-full md:w-1/2 p-2 hidden transition-all ease-in-out duration-200 transform scale-95 hover:scale-100">
                </div>
            </div>
        </form>
        <a href="/admin">
            <button type="button"
                class="hidden md:block text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-xl px-5 py-2.5 ml-4 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800">
                Iniciar Sesión
            </button>
        </a>
    </div>
    <section class="flex flex-row h-screen mt-4">
        <!-- Mapa -->
        <div class="flex-grow mr-4 z-0 h-96">
            <div id="mapa_bus" class="w-full h-full rounded"></div>
        </div>


    </section>

    <!-- Tabs de Estado del Servicio -->
    <div class="w-full max-w-4xl mx-auto">
        <div class="flex border-b border-gray-300">
            <button class="tab-btn px-4 py-2 text-white bg-blue-600 rounded-t-md" data-tab="bus">Estado del servicio
                Bus</button>
            <button class="tab-btn px-4 py-2 text-blue-700 bg-gray-200 rounded-t-md" data-tab="metro">Ruta
                Norte</button>
            <button class="tab-btn px-4 py-2 text-gray-700 bg-gray-200 rounded-t-md" data-tab="metrotren">Ruta
                Sur</button>
        </div>

        <!-- Contenido de cada tab -->
        <div class="tab-content bg-white p-6 border border-gray-300" id="bus">
            <h2 class="text-xl font-bold text-blue-600">Estado del servicio Bus</h2>
            <p class="text-gray-700">Información sobre el estado de los buses...</p>
            <div class="w-full  mt-3">
            <div>
            <h3 class="text-gray-700 font-medium">Avisos</h3>
            <ul class="bg-blue-100 p-3 rounded-lg shadow">
                @foreach ($avisos as $aviso)
                    <li class="border-b py-2">
                        <h5 class="font-semibold text-lg text-gray-700">Noticia: {{ $aviso->noticia }}</h5>
                        <p class="font-sans tracking-tight text-gray-600">Duración: {{ $aviso->inicio_periodo }} ~ {{ $aviso->fin_periodo }}</p>
                        <p class="font-sans tracking-tight text-gray-600">Razón: {{ $aviso->razon }}</p>
                        <p class="font-sans tracking-tight text-gray-600">Paradas afectadas: {{ $aviso->paradas_afectadas }}</p>
                        <p class="font-sans tracking-tight text-gray-600">Detalle: {{ $aviso->detalle }}</p>
                        <small class="text-gray-800">Actualizado hace {{ $aviso->created_at->diffForHumans() }}</small>
                    </li>
                @endforeach
            </ul>
        </div>
            </div>
        </div>
        <div class="tab-content hidden bg-white p-6 border border-gray-300" id="metro">
            <h2 class="text-xl font-bold text-blue-600">Próximos buses en RUTA NORTE</h2>
            <p class="text-gray-700">Información sobre llegada de próximos buses a las paradas...</p>
            <div class="grid grid-cols-2 gap-4 mt-3">
                <!-- Sentido Ida -->
                <div>
                    <h3 class="text-gray-700 font-medium">Sentido Ida Playa Verde - Ceja</h3>
                    <ul class="bg-blue-50 p-3 rounded-lg shadow">
                    <li class="flex justify-between border-b py-2" data-parada="Playa Verde" data-sentido="Ida">
                            <span>Playa Verde</span>
                            <span class="tiempo-llegada text-green-600">-- min</span>
                        </li>
                        <li class="flex justify-between border-b py-2" data-parada="San Roque" data-sentido="Ida">
                            <span>San Roque</span>
                            <span class="tiempo-llegada text-green-600">-- min</span>
                        </li>
                       
                        <li class="flex justify-between border-b py-2" data-parada="Cruce Bella Vista" data-sentido="Ida">
                            <span>Cruce Bella Vista</span>
                            <span class="tiempo-llegada text-green-600">-- min</span>
                        </li>
                        <li class="flex justify-between border-b py-2" data-parada="Cruce Lagunas" data-sentido="Ida">
                            <span>Cruce Lagunas</span>
                            <span class="tiempo-llegada text-green-600">-- min</span>
                        </li>
                        <li class="flex justify-between border-b py-2" data-parada="Ex Parada 8" data-sentido="Ida">
                            <span>Ex Parada 8</span>
                            <span class="tiempo-llegada text-green-600">-- min</span>
                        </li>
                        <li class="flex justify-between border-b py-2" data-parada="Planta de Luz Rio Seco" data-sentido="Ida">
                            <span>Planta de Luz Río Seco</span>
                            <span class="tiempo-llegada text-green-600">-- min</span>
                        </li>
                        <li class="flex justify-between border-b py-2" data-parada="UPEA" data-sentido="Ida">
                            <span>Universidad Pública de El Alto</span>
                            <span class="tiempo-llegada text-green-600">-- min</span>
                        </li>
                        <li class="flex justify-between border-b py-2" data-parada="Av. La Paz (Estacion Teleferico Azul Plaza La Paz)" data-sentido="Ida">
                            <span>Av. La Paz</span>
                            <span class="tiempo-llegada text-green-600">-- min</span>
                        </li>
                        <li class="flex justify-between border-b py-2" data-parada="CRUZ PAPAL" data-sentido="Vuelta">
                            <span>Cruz Papal Av. Juan Pablo II</span>
                            <span class="tiempo-llegada text-green-600">-- min</span>
                        </li>
                        <li class="flex justify-between border-b py-2" data-parada="EX TRANSITO" data-sentido="Ida">
                            <span>Ex Transito</span>
                            <span class="tiempo-llegada text-green-600">-- min</span>
                        </li>
                    </ul>
                </div>
                <!-- Sentido Vuelta -->
                <div>
                    <h3 class="text-gray-700 font-medium">Sentido Vuelta Ceja - Playa Verde</h3>
                    <ul class="bg-blue-50 p-3 rounded-lg shadow">
                    <li class="flex justify-between border-b py-2" data-parada="CEIBO Ceja" data-sentido="Vuelta">
                            <span>CEIBO Ceja</span>
                            <span class="tiempo-llegada text-green-600">-- min</span>
                        </li>
                       
                        <li class="flex justify-between border-b py-2" data-parada="CRUZ PAPAL" data-sentido="Vuelta">
                            <span>Cruz Papal Av. Juan Pablo II</span>
                            <span class="tiempo-llegada text-green-600">-- min</span>
                        </li>
                        <li class="flex justify-between border-b py-2" data-parada="Av. La Paz (Estacion Teleferico Azul Plaza La Paz)" data-sentido="Vuelta">
                            <span>Av. La Paz</span>
                            <span class="tiempo-llegada text-green-600">-- min</span>
                        </li>
                        <li class="flex justify-between border-b py-2" data-parada="UPEA" data-sentido="Vuelta">
                            <span>Universidad Pública de El Alto</span>
                            <span class="tiempo-llegada text-green-600">-- min</span>
                        </li>
                        <li class="flex justify-between border-b py-2" data-parada="Planta de Luz Rio Seco" data-sentido="Vuelta">
                            <span>Planta de Luz Río Seco</span>
                            <span class="tiempo-llegada text-green-600">-- min</span>
                        </li>
                        <li class="flex justify-between border-b py-2" data-parada="Ex Parada 8" data-sentido="Vuelta">
                            <span>Ex Parada 8</span>
                            <span class="tiempo-llegada text-green-600">-- min</span>
                        </li>
                        <li class="flex justify-between border-b py-2" data-parada="Cruce Lagunas" data-sentido="Vuelta">
                            <span>Cruce Lagunas</span>
                            <span class="tiempo-llegada text-green-600">-- min</span>
                        </li>
                        <li class="flex justify-between border-b py-2" data-parada="Cruce Bella Vista" data-sentido="Vuelta">
                            <span>Cruce Bella Vista</span>
                            <span class="tiempo-llegada text-green-600">-- min</span>
                        </li>
                        <li class="flex justify-between border-b py-2" data-parada="San Roque" data-sentido="Vuelta">
                            <span>San Roque</span>
                            <span class="tiempo-llegada text-green-600">-- min</span>
                        </li>
                        <li class="flex justify-between border-b py-2" data-parada="Playa Verde" data-sentido="Vuelta">
                            <span>Playa Verde</span>
                            <span class="tiempo-llegada text-green-600">-- min</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="tab-content hidden bg-white p-6 border border-gray-300" id="metrotren">
            <h2 class="text-xl font-bold text-blue-600">Próximos buses en RUTA SUR</h2>
            <p class="text-gray-700">Información de llegada de próximos buses a las paradas...</p>
            <div class="grid grid-cols-2 gap-4 mt-3">
                <!-- Sentido Ida -->
                <div>
                    <h3 class="text-gray-700 font-medium">Sentido Ida SAMO CEJA</h3>
                    <ul class="bg-blue-50 p-3 rounded-lg shadow">
                        <li class="flex justify-between border-b py-2" data-parada="SAMO" data-sentido="Ida">
                            <span>SAMO</span>
                            <span class="tiempo-llegada text-green-600">-- min</span>
                        </li>
                        <li class="flex justify-between border-b py-2" data-parada="CRUCE LAYURI" data-sentido="Ida">
                            <span>Cruce Layuri</span>
                            <span class="tiempo-llegada text-green-600">-- min</span>
                        </li>
                        <li class="flex justify-between border-b py-2" data-parada="CRUCE VENTILLA" data-sentido="Ida">
                            <span>Cruce Ventilla</span>
                            <span class="tiempo-llegada text-green-600">-- min</span>
                        </li>
                        <li class="flex justify-between border-b py-2" data-parada="PUENTE VELA" data-sentido="Ida">
                            <span>Puente Vela</span>
                            <span class="tiempo-llegada text-green-600">-- min</span>
                        </li>
                        <li class="flex justify-between border-b py-2" data-parada="CRUCE SENKATA" data-sentido="Ida">
                            <span>Cruce Senkata</span>
                            <span class="tiempo-llegada text-green-600">-- min</span>
                        </li>

                        <li class="flex justify-between border-b py-2" data-parada="ESCUELA JOSE MANUEL PANDO"
                            data-sentido="Ida">
                            <span>U.E. José Manuel Pando</span>
                            <span class="tiempo-llegada text-green-600">-- min</span>
                        </li>

                        <li class="flex justify-between border-b py-2" data-parada="MOLINO ANDINO" data-sentido="Ida">
                            <span>Molino Andino 6 de Marzo</span>
                            <span class="tiempo-llegada text-green-600">-- min</span>
                        </li>

                        <li class="flex justify-between border-b py-2" data-parada="PUENTE BOLIVIA" data-sentido="Ida">
                            <span>Puente Bolivia Av. 6 de Marzo</span>
                            <span class="tiempo-llegada text-green-600">-- min</span>
                        </li>

                        <li class="flex justify-between border-b py-2" data-parada="ESTACION TELEFERICO MORADO"
                            data-sentido="Ida">
                            <span>Teleferico Morado 6 de Marzo</span>
                            <span class="tiempo-llegada text-green-600">-- min</span>
                        </li>

                        <li class="flex justify-between border-b py-2" data-parada="CRUCE VIACHA" data-sentido="Ida">
                            <span>Cruce Viacha</span>
                            <span class="tiempo-llegada text-green-600">-- min</span>
                        </li>

                        <li class="flex justify-between border-b py-2" data-parada="CALLE 7" data-sentido="Ida">
                            <span>Calle 7</span>
                            <span class="tiempo-llegada text-green-600">-- min</span>
                        </li>
                    </ul>
                </div>
                <!-- Sentido Vuelta -->
                <div>
                    <h3 class="text-gray-700 font-medium">Sentido Vuelta CEJA - SAMO</h3>
                    <ul class="bg-blue-50 p-3 rounded-lg shadow">
                        <li class="flex justify-between border-b py-2" data-parada="CALLE 3" data-sentido="Vuelta">
                            <span>Calle 3</span>
                            <span class="tiempo-llegada text-green-600">-- min</span>
                        </li>
                        <li class="flex justify-between border-b py-2" data-parada="CALLE 7" data-sentido="Vuelta">
                            <span>Calle 7</span>
                            <span class="tiempo-llegada text-green-600">-- min</span>
                        </li>
                        <li class="flex justify-between border-b py-2" data-parada="CRUCE VIACHA" data-sentido="Vuelta">
                            <span>Cruce Viacha</span>
                            <span class="tiempo-llegada text-green-600">-- min</span>
                        </li>
                        <li class="flex justify-between border-b py-2" data-parada="ESTACION TELEFERICO MORADO"
                            data-sentido="Vuelta">
                            <span>Teleferico Morado 6 de Marzo</span>
                            <span class="tiempo-llegada text-green-600">-- min</span>
                        </li>
                        <li class="flex justify-between border-b py-2" data-parada="PUENTE BOLIVIA"
                            data-sentido="Vuelta">
                            <span>Puente Bolivia Av. 6 de Marzo</span>
                            <span class="tiempo-llegada text-green-600">-- min</span>
                        </li>
                        <li class="flex justify-between border-b py-2" data-parada="MOLINO ANDINO"
                            data-sentido="Vuelta">
                            <span>Molino Andino 6 de Marzo</span>
                            <span class="tiempo-llegada text-green-600">-- min</span>
                        </li>
                        <li class="flex justify-between border-b py-2" data-parada="RIELES SENKATA"
                            data-sentido="Vuelta">
                            <span>U.E. José Manuel Pando</span>
                            <span class="tiempo-llegada text-green-600">-- min</span>
                        </li>
                        <li class="flex justify-between border-b py-2" data-parada="CRUCE SENKATA" data-sentido="Vuelta">
                            <span>Cruce Senkata</span>
                            <span class="tiempo-llegada text-green-600">-- min</span>
                        </li>
                        <li class="flex justify-between border-b py-2" data-parada="PUENTE VELA" data-sentido="Vuelta">
                            <span>Puente Vela</span>
                            <span class="tiempo-llegada text-green-600">-- min</span>
                        </li>
                        <li class="flex justify-between border-b py-2" data-parada="CRUCE VENTILLA" data-sentido="Vuelta">
                            <span>Cruce Ventilla</span>
                            <span class="tiempo-llegada text-green-600">-- min</span>
                        </li>
                        <li class="flex justify-between border-b py-2" data-parada="CRUCE LAYURI" data-sentido="Vuelta">
                            <span>Cruce Layuri</span>
                            <span class="tiempo-llegada text-green-600">-- min</span>
                        </li>
                        <li class="flex justify-between border-b py-2" data-parada="SAMO" data-sentido="Ida">
                            <span>SAMO</span>
                            <span class="tiempo-llegada text-green-600">-- min</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>


</div>


<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    var locations = @json($locations);
</script>
