<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bus Municipal</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.css"  rel="stylesheet" />
</head>
<body class="bg-gray-900 text-gray-300">

  <!-- Navbar -->
  @include('pageInformation.partials.navbar')


  <div class="bg-gray-900 text-white">
    <!-- Sección Principal -->
    <section class="relative bg-cover bg-center h-[60vh]" style="background-image: url('https://via.placeholder.com/1920x1080');">
        <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
            <h1 class="text-5xl font-bold text-center">Ruta Norte</h1>
        </div>
    </section>

    <!-- Sección de Información -->
    <section class="container mx-auto py-16 px-6 lg:px-16">
        <h2 class="text-3xl font-bold mb-6 text-blue-500">Descripción</h2>
        <p class="text-gray-300 leading-relaxed">
            La <span class="font-bold text-white">Ruta Norte</span> conecta las principales áreas del lado Norte de la Ciudad de El Alto con el eje central la Ceja.
            Este servicio está diseñado para garantizar un transporte eficiente y cómodo para todos los usuarios.
        </p>
    </section>

    <!-- Sección de Imagen y Paradas -->
    <section class="bg-gray-800 py-16">
    <div class="container mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        <!-- Video -->
        <div class="lg:col-span-2">
            <div class="rounded-lg overflow-hidden shadow-lg">
                <iframe 
                    src="https://www.facebook.com/plugins/video.php?height=314&href=https%3A%2F%2Fwww.facebook.com%2FElAltoAlcaldia%2Fvideos%2F2012325059154652%2F&show_text=false&width=560&t=0"
                    width="100%" height="450" style="border:none;overflow:hidden" 
                    scrolling="no" frameborder="0" allowfullscreen="true" 
                    allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share">
                </iframe>
            </div>
        </div>

        <!-- Acordeón de Paradas -->
        <div class="bg-gray-900 p-6 rounded-lg shadow-md space-y-4">
            <h3 class="text-2xl font-bold text-blue-500">Paradas</h3>

            <!-- Acordeón -->
            <div id="accordion-paradas" data-accordion="collapse">
                <!-- Ruta de Vuelta -->
                <h2 id="heading-vuelta">
                    <button type="button" 
                        class="flex items-center justify-between w-full p-4 font-medium text-left text-blue-50 bg-gray-800 rounded-lg hover:bg-gray-700" 
                        data-accordion-target="#content-vuelta" 
                        aria-expanded="true" 
                        aria-controls="content-vuelta">
                        Ruta de Vuelta: Ceja → Playa Verde
                        <svg data-accordion-icon class="w-6 h-6 rotate-180 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06 0L10 10.94l3.71-3.72a.75.75 0 111.06 1.06l-4 4a.75.75 0 01-1.06 0l-4-4a.75.75 0 010-1.06z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </h2>
                <div id="content-vuelta" class="hidden" aria-labelledby="heading-vuelta">
                    <ol class="relative border-l border-blue-50 mt-4">
                        <li class="mb-10 ml-4">
                            <div class="absolute w-3 h-3 bg-blue-50 rounded-full -left-1.5 border border-gray-900"></div>
                            <div class="p-4 bg-gray-800 rounded-lg shadow-md">
                                <h3 class="text-lg font-semibold text-white">Ceibo Av. Juan Pablo II - Ceja</h3>
                                <p class="text-sm text-gray-400">Punto de partida</p>
                            </div>
                        </li>
                        <li class="mb-10 ml-4">
                            <div class="absolute w-3 h-3 bg-blue-50 rounded-full -left-1.5 border border-gray-900"></div>
                            <div class="p-4 bg-gray-800 rounded-lg shadow-md">
                                <h3 class="text-lg font-semibold text-white">Cruz Papal Av. Juan Pablo II</h3>
                            </div>
                        </li>
                        <li class="mb-10 ml-4">
                            <div class="absolute w-3 h-3 bg-blue-50 rounded-full -left-1.5 border border-gray-900"></div>
                            <div class="p-4 bg-gray-800 rounded-lg shadow-md">
                                <h3 class="text-lg font-semibold text-white">Av. La Paz Altura Teleférico Azul Plaza La Paz</h3>
                            </div>
                        </li>
                        <li class="mb-10 ml-4">
                            <div class="absolute w-3 h-3 bg-blue-50 rounded-full -left-1.5 border border-gray-900"></div>
                            <div class="p-4 bg-gray-800 rounded-lg shadow-md">
                                <h3 class="text-lg font-semibold text-white">UPEA - Universidad Pública de El Alto</h3>
                            </div>
                        </li>
                        <li class="mb-10 ml-4">
                            <div class="absolute w-3 h-3 bg-blue-50 rounded-full -left-1.5 border border-gray-900"></div>
                            <div class="p-4 bg-gray-800 rounded-lg shadow-md">
                                <h3 class="text-lg font-semibold text-white">Planta de Luz Río Seco</h3>
                            </div>
                        </li>
                        <li class="mb-10 ml-4">
                            <div class="absolute w-3 h-3 bg-blue-50 rounded-full -left-1.5 border border-gray-900"></div>
                            <div class="p-4 bg-gray-800 rounded-lg shadow-md">
                                <h3 class="text-lg font-semibold text-white">Exparada 8</h3>
                            </div>
                        </li>
                        <li class="mb-10 ml-4">
                            <div class="absolute w-3 h-3 bg-blue-50 rounded-full -left-1.5 border border-gray-900"></div>
                            <div class="p-4 bg-gray-800 rounded-lg shadow-md">
                                <h3 class="text-lg font-semibold text-white">Cruce Lagunas</h3>
                            </div>
                        </li>
                        <li class="mb-10 ml-4">
                            <div class="absolute w-3 h-3 bg-blue-50 rounded-full -left-1.5 border border-gray-900"></div>
                            <div class="p-4 bg-gray-800 rounded-lg shadow-md">
                                <h3 class="text-lg font-semibold text-white">Cruce Bella Vista</h3>
                            </div>
                        </li>
                        <li class="mb-10 ml-4">
                            <div class="absolute w-3 h-3 bg-blue-50 rounded-full -left-1.5 border border-gray-900"></div>
                            <div class="p-4 bg-gray-800 rounded-lg shadow-md">
                                <h3 class="text-lg font-semibold text-white">San Roque</h3>
                             
                            </div>
                        </li>
                
                        
                        
                        <li class="ml-4">
                            <div class="absolute w-3 h-3 bg-blue-50 rounded-full -left-1.5 border border-gray-900"></div>
                            <div class="p-4 bg-gray-800 rounded-lg shadow-md">
                                <h3 class="text-lg font-semibold text-white">Playa Verde</h3>
                                <p class="text-sm text-gray-400">Destino final</p>
                            </div>
                        </li>
                    </ol>
                </div>

                <!-- Ruta de Ida -->
                <h2 id="heading-ida">
                    <button type="button" 
                        class="flex items-center justify-between w-full p-4 font-medium text-left text-green-400 bg-gray-800 rounded-lg hover:bg-gray-700" 
                        data-accordion-target="#content-ida" 
                        aria-expanded="false" 
                        aria-controls="content-ida">
                        Ruta de Ida: SAMO → Ceja
                        <svg data-accordion-icon class="w-6 h-6 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06 0L10 10.94l3.71-3.72a.75.75 0 111.06 1.06l-4 4a.75.75 0 01-1.06 0l-4-4a.75.75 0 010-1.06z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </h2>
                <div id="content-ida" class="hidden" aria-labelledby="heading-ida">
                    <ol class="relative border-l border-indigo-600 mt-4">
                        <li class="mb-10 ml-4">
                            <div class="absolute w-3 h-3 bg-gindigo-600 rounded-full -left-1.5 border border-gray-900"></div>
                            <div class="p-4 bg-gray-800 rounded-lg shadow-md">
                                <h3 class="text-lg font-semibold text-white">SAMO</h3>
                                <p class="text-sm text-gray-400">Punto de partida</p>
                            </div>
                        </li>
                        <li class="mb-10 ml-4">
                            <div class="absolute w-3 h-3 bg-gindigo-600 rounded-full -left-1.5 border border-gray-900"></div>
                            <div class="p-4 bg-gray-800 rounded-lg shadow-md">
                                <h3 class="text-lg font-semibold text-white">Cruce Layuri</h3>
                                <p class="text-sm text-gray-400">Parada intermedia</p>
                            </div>
                        </li>
                        <li class="mb-10 ml-4">
                            <div class="absolute w-3 h-3 bg-indigo-600 rounded-full -left-1.5 border border-gray-900"></div>
                            <div class="p-4 bg-gray-800 rounded-lg shadow-md">
                                <h3 class="text-lg font-semibold text-white">Cruce Ventilla</h3>
                                <p class="text-sm text-gray-400">Parada intermedia</p>
                            </div>
                        </li>
                        <li class="mb-10 ml-4">
                            <div class="absolute w-3 h-3 bg-indigo-600 rounded-full -left-1.5 border border-gray-900"></div>
                            <div class="p-4 bg-gray-800 rounded-lg shadow-md">
                                <h3 class="text-lg font-semibold text-white">Puente Vela</h3>
                                <p class="text-sm text-gray-400">Parada intermedia</p>
                            </div>
                        </li>
                        <li class="mb-10 ml-4">
                            <div class="absolute w-3 h-3 bg-indigo-600 rounded-full -left-1.5 border border-gray-900"></div>
                            <div class="p-4 bg-gray-800 rounded-lg shadow-md">
                                <h3 class="text-lg font-semibold text-white">Cruce Senkata</h3>
                                <p class="text-sm text-gray-400">Parada intermedia</p>
                            </div>
                        </li>
                        <li class="mb-10 ml-4">
                            <div class="absolute w-3 h-3 bg-indigo-600 rounded-full -left-1.5 border border-gray-900"></div>
                            <div class="p-4 bg-gray-800 rounded-lg shadow-md">
                                <h3 class="text-lg font-semibold text-white">Rieles Senkata - U.E. José Manuel Pando</h3>
                                <p class="text-sm text-gray-400">Parada intermedia</p>
                            </div>
                        </li>
                        <li class="mb-10 ml-4">
                            <div class="absolute w-3 h-3 bg-indigo-600 rounded-full -left-1.5 border border-gray-900"></div>
                            <div class="p-4 bg-gray-800 rounded-lg shadow-md">
                                <h3 class="text-lg font-semibold text-white">Molino Andino - Cruce Achocalla</h3>
                                <p class="text-sm text-gray-400">Parada intermedia</p>
                            </div>
                        </li>
                        <li class="mb-10 ml-4">
                            <div class="absolute w-3 h-3 bg-indigo-600 rounded-full -left-1.5 border border-gray-900"></div>
                            <div class="p-4 bg-gray-800 rounded-lg shadow-md">
                                <h3 class="text-lg font-semibold text-white">Puente Bolivia Av. 6 de Marzo</h3>
                                <p class="text-sm text-gray-400">Parada intermedia</p>
                            </div>
                        </li>
                        <li class="mb-10 ml-4">
                            <div class="absolute w-3 h-3 bg-indigo-600 rounded-full -left-1.5 border border-gray-900"></div>
                            <div class="p-4 bg-gray-800 rounded-lg shadow-md">
                                <h3 class="text-lg font-semibold text-white">Teleférico Morado Av. 6 de Marzo</h3>
                                <p class="text-sm text-gray-400">Parada intermedia</p>
                            </div>
                        </li>
                        <li class="mb-10 ml-4">
                            <div class="absolute w-3 h-3 bg-indigo-600 rounded-full -left-1.5 border border-gray-900"></div>
                            <div class="p-4 bg-gray-800 rounded-lg shadow-md">
                                <h3 class="text-lg font-semibold text-white">Cruce Viacha Av. 6 de Marzo</h3>
                                <p class="text-sm text-gray-400">Parada intermedia</p>
                            </div>
                        </li>
                        <li class="mb-10 ml-4">
                            <div class="absolute w-3 h-3 bg-indigo-600 rounded-full -left-1.5 border border-gray-900"></div>
                            <div class="p-4 bg-gray-800 rounded-lg shadow-md">
                                <h3 class="text-lg font-semibold text-white">Calle 7 Av. 6 de Marzo</h3>
                                <p class="text-sm text-gray-400">Parada intermedia</p>
                            </div>
                        </li>
                        
                        <li class="ml-4">
                            <div class="absolute w-3 h-3 bg-indigo-600 rounded-full -left-1.5 border border-gray-900"></div>
                            <div class="p-4 bg-gray-800 rounded-lg shadow-md">
                                <h3 class="text-lg font-semibold text-white">Calle 3 Av. 6 de Marzo - Ceja</h3>
                                <p class="text-sm text-gray-400">Destino final</p>
                            </div>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</section>

    <!-- Sección Interactiva o Datos Adicionales -->
    <section class="py-16 px-6 lg:px-16 bg-gray-900">
        <h3 class="text-3xl font-bold text-blue-500 mb-6">Mapa Interactivo</h3>
        <p class="text-gray-300 mb-8">
            Explora el recorrido de esta ruta en el mapa interactivo. Haz clic en las paradas para obtener más información sobre cada punto de interés.
        </p>
        <div class="rounded-lg overflow-hidden shadow-lg">
        <iframe src="https://www.google.com/maps/d/u/0/embed?mid=1rEPHnfLPJGlncfvx7nSNBg7tQXRRqNI&ehbc=2E312F&noprof=1" width="100%" height="480"></iframe>

        </div>
    </section>

    <!-- Botón para Regresar -->
    <section class="py-6 text-center">
        <a href="/bus-municipal" class="px-6 py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition">
            Volver a la Página Principal
        </a>
    </section>
</div>




<!-- Footer -->
@include('pageInformation.partials.footer')
<script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.js"></script>


</body>
</html>
