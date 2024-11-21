 <!-- Header Section -->
 <header class="bg-gray-900 bg-cover bg-center h-screen " style="background-image: url('img/background.jpg');">
    <div class="bg-black bg-opacity-60 h-full flex flex-col justify-center items-center text-center">
      <h1 class="text-5xl text-white font-bold mb-4">Bus Municipal</h1>
      <p class=" italic text-lg text-gray-300 mb-6">"El bus del corazón de los alteños".</p>
      <button type="button" class="text-blue-600 hover:text-white border border-blue-600 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg  px-6 py-3.5 text-center me-2 mb-2 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800"><a href="/">Seguimiento en Vivo</a></button>
    </div>
  </header>
 <!-- About Section -->
 <section id="about" class="py-16 bg-gray-900 bg-cover bg-center relative text-gray-300" style="background-image: url('img/bus.jpg');">
  <div class="absolute inset-0 bg-black bg-opacity-90"></div>

  <div class="container mx-auto px-6 md:px-12 relative text-center">
    <h2 class="text-4xl font-bold text-blue-500 mb-12">Sobre nosotros</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
      <p class="text-lg text-gray-300 md:text-left">
        El Servicio Municipal de Transporte Público Masivo (Bus Municipal) de la ciudad de El Alto inició sus operaciones el 04 de septiembre de 2023.<br><br>
        Con el objetivo de brindar un servicio público de transporte municipal, que contribuya a mejorar las condiciones de vida de la población beneficiaria con escasos servicios de transporte y ser una alternativa al transporte público convencional.
      </p>
      <figure class="relative max-w-sm ml-auto transition-all duration-300 cursor-pointer filter grayscale hover:grayscale-0">
        
          <img class="rounded-lg" src="img/about_bus.jpg" alt="image description">
       
        <figcaption class="absolute px-4 text-lg text-white bottom-6">
         <p></p>
        </figcaption>
      </figure>
    </div>
  </div>
</section>
  <!-- Sección de Avisos -->
  <section id="avisos" class="container mx-auto px-4 py-12">
    <h2 class="text-3xl font-bold text-center mb-8">Avisos y Comunicados</h2>
    <div id="avisos-carousel" class="relative w-full">
      <div class="overflow-hidden rounded-lg">
        <!-- Slide 1 -->
        <div class="hidden duration-700 ease-in-out" data-carousel-item>
          <div class="p-8 bg-white bg-opacity-20 rounded-lg">
            <h3 class="text-2xl font-bold text-blue-400">Nuevo horario de la Ruta Norte</h3>
            <p class="text-white mt-4">A partir del 1 de diciembre, los horarios serán ampliados para mayor comodidad.</p>
          </div>
        </div>
        <!-- Slide 2 -->
        <div class="hidden duration-700 ease-in-out" data-carousel-item>
          <div class="p-8 bg-white bg-opacity-20 rounded-lg">
            <h3 class="text-2xl font-bold text-purple-400">Mantenimiento en la Ruta Sur</h3>
            <p class="text-white mt-4">Algunas paradas estarán temporalmente cerradas por mejoras en el servicio.</p>
          </div>
        </div>
      </div>
      <!-- Controles -->
      <button class="absolute top-0 left-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-prev>
        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-gray-800 bg-opacity-50 group-hover:bg-gray-700">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
          </svg>
        </span>
      </button>
      <button class="absolute top-0 right-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-next>
        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-gray-800 bg-opacity-50 group-hover:bg-gray-700">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
          </svg>
        </span>
      </button>
    </div>
  </section>
<!-- Ruta  -->
<section id="rutas" class="py-20 bg-gray-900">
  <div class="container mx-auto">
    <div class="text-center mb-10">
      <h2 class="text-3xl font-bold text-blue-500">Nuestras Rutas</h2>
      <p class="text-gray-400 mt-3">Explora las rutas de los buses municipales disponibles.</p>
    </div>
    <div class="grid md:grid-cols-2 gap-8">
      <!-- Ruta Norte -->
      <div class="relative group overflow-hidden rounded-lg shadow-lg">
        <img src="img/rutaNORTE.jpg" alt="Ruta Norte" class="w-full h-48 object-cover">
        <div class="absolute inset-0 bg-blue-500 opacity-20 group-hover:opacity-50 transition duration-300"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent opacity-80 group-hover:opacity-100 transition duration-300"></div>
        <div class="absolute bottom-4 left-4">
          <h3 class="text-4xl font-bold text-white">Ruta Norte</h3>
          <p class="text-gray-300">Recorrido desde Ceibo/Ceja hasta llegar a Playa Verde.</p>
          <a href="{{ route('ruta-norte') }}" class="mt-2 inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Ver más
            </a>
        </div>
      </div>
      <!-- Ruta Sur -->
      <div class="relative group overflow-hidden rounded-lg shadow-lg">
        <img src="img/rutaSUR.jpg" alt="Ruta Sur" class="w-full h-48 object-cover">
        <div class="absolute inset-0 bg-purple-500 opacity-30 group-hover:opacity-50 transition duration-300"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent opacity-80 group-hover:opacity-100 transition duration-300"></div>
        <div class="absolute bottom-4 left-4">
          <h3 class="text-4xl font-bold text-white">Ruta Sur</h3>
          <p class="text-gray-300">Recorrido desde Calle 3/Ceja hasta llegar a Samo.</p>
          <a href="{{ route('ruta-sur') }}" class="mt-2 inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Ver más
            </a>
        </div>
      </div>
    </div>
  </div>
</section>