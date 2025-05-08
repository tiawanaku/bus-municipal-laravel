@extends('layouts.app')
@section('content')
  <!-- Header Section -->
  <header class="bg-gray-900 bg-cover bg-center h-screen " style="background-image: url('img/background.jpg');">
    <div class="bg-black bg-opacity-60 h-full flex flex-col justify-center items-center text-center">
    <h1 class="text-5xl text-white font-bold mb-4">Bus Municipal</h1>
    <p class=" italic text-lg text-gray-300 mb-6">"El bus del corazón de los alteños".</p>
    <button type="button"
      class="text-blue-600 hover:text-white border border-blue-600 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg  px-6 py-3.5 text-center me-2 mb-2 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800"><a
      href="/">Seguimiento en Vivo</a></button>
    </div>
  </header>
  <!-- About Section -->
  <section id="about" class="py-16 bg-gray-900 bg-fixed bg-center bg-cover relative text-gray-300"
    style="background-image: url('img/bus.jpg');">
    <div class="absolute inset-0 bg-black bg-opacity-90"></div>

    <div class="container mx-auto px-6 md:px-12 relative text-center">
    <h2 class="text-4xl font-bold text-blue-500 mb-12">Sobre nosotros</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
      <p class="text-lg text-gray-300 md:text-left">
      El Servicio Municipal de Transporte Público Masivo (Bus Municipal) de la ciudad de El Alto inició sus
      operaciones el 04 de septiembre de 2023.<br><br>
      Con el objetivo de brindar un servicio público de transporte municipal, que contribuya a mejorar las condiciones
      de vida de la población beneficiaria con escasos servicios de transporte y ser una alternativa al transporte
      público convencional.
      </p>
      <figure
      class="relative max-w-sm ml-auto transition-all duration-300 cursor-pointer filter grayscale hover:grayscale-0">

      <img class="rounded-lg" src="img/about_bus.jpg" alt="image description">

      <figcaption class="absolute px-4 text-lg text-white bottom-6">
        <p></p>
      </figcaption>
      </figure>
      <!-- Misión y Visión -->

    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center mt-12">
      <div>
      <h2 class="text-3xl font-bold text-blue-500 mb-12">Misión</h2>
      <p>Hola mundo</p>
      </div>
      <div>
      <h2 class="text-3xl font-bold text-blue-500 mb-12">Visión</h2>
      <p>Hola mundo</p>
      </div>

    </div>

    </div>
  </section>
  <!-- Sección de Pasajes y Horarios -->
  <section id="pasajes" class="py-20 bg-gray-900 px-8">
    <div class="text-center mb-10">
    <h2 class="text-3xl font-bold text-blue-500">Pasajes y Horarios</h2>

    </div>
    <div class="grid md:grid-cols-2 gap-8 ">
    <div class=" bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">

      <img class="rounded-t-lg " src="/img/informacion/pasajesBus.png" alt="" />


    </div>
    <div class=" bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">

      <img class="rounded-t-lg " src="/img/informacion/horariosBus.png" alt="" />


    </div>

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
      <div class="absolute inset-0 bg-blue-900 opacity-10 group-hover:opacity-20 transition duration-300"></div>
      <div
        class="absolute inset-0 bg-gradient-to-t from-black via-black/70 to-transparent opacity-80 group-hover:opacity-100 transition duration-300">
      </div>
      <div class="absolute bottom-4 left-4">
        <h3 class="text-4xl font-bold text-white">Ruta Norte</h3>
        <p class="text-gray-200">Recorrido desde Ceibo/Ceja hasta llegar a Playa Verde.</p>

        <button type="button" onclick="window.location='{{ route('ruta-norte') }}'"
        class="text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80 font-medium rounded-lg text-base px-5 py-2.5 text-center me-2 mb-2 ">Ver
        más</button>

      </div>
      </div>
      <!-- Ruta Sur -->
      <div class="relative group overflow-hidden rounded-lg shadow-lg">
      <img src="img/rutaSUR.jpg" alt="Ruta Sur" class="w-full h-48 object-cover">
      <div
        class="absolute inset-0 bg-gradient-to-t from-black via-black/70 to-transparent opacity-80 group-hover:opacity-100 transition duration-300">
      </div>

      <div
        class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent opacity-80 group-hover:opacity-100 transition duration-300">
      </div>
      <div class="absolute bottom-4 left-4">
        <h3 class="text-4xl font-bold text-white">Ruta Sur</h3>
        <p class="text-gray-200">Recorrido desde Calle 3/Ceja hasta llegar a Samo.</p>
        <button type="button" onclick="window.location='{{ route('ruta-sur') }}'"
        class="text-white bg-gradient-to-r from-purple-500 via-purple-600 to-purple-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-purple-300 dark:focus:ring-purple-800 shadow-lg shadow-purple-500/50 dark:shadow-lg dark:shadow-purple-800/80 font-medium rounded-lg text-base px-5 py-2.5 text-center me-2 mb-2">Ver
        más</button>

      </div>
      </div>



    </div>







  </section>

  <section id="avisos" class="py-20 bg-gray-900 px-8">
    <div class="container mx-auto px-6 md:px-12 relative text-center">
    <h2 class="text-3xl font-bold text-blue-500 mb-10">Galería</h2>
    </div>
    <div id="gallery" class="grid grid-cols-2 md:grid-cols-4 gap-4"></div>

    <!-- Modal del Carrusel -->
    <div id="carouselModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center hidden"
    onclick="handleBackdropClick(event)">
    <div class="relative">
      <button class="absolute top-2 right-2 text-white text-3xl" onclick="closeCarousel()">&times;</button>
      <img id="carouselImage" src="" alt="Carrusel" class="max-h-screen rounded shadow-lg">
      <div class="flex justify-between mt-4">



      <!-- Slider controls -->
      <button onclick="prevImage()" type="button"
        class="absolute top-0 start-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none"
        data-carousel-prev>
        <span
        class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
        <svg class="w-4 h-4 text-gray-800 dark:text-gray-800 rtl:rotate-180" aria-hidden="true"
          xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M5 1 1 5l4 4" />
        </svg>
        <span class="sr-only">Previous</span>
        </span>
      </button>
      <button onclick="nextImage()" type="button"
        class="absolute top-0 end-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none"
        data-carousel-next>
        <span
        class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
        <svg class="w-4 h-4 text-gray-800 dark:text-gray-800 rtl:rotate-180" aria-hidden="true"
          xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="m1 9 4-4-4-4" />
        </svg>
        <span class="sr-only">Next</span>
        </span>
      </button>
      </div>
    </div>
    </div>


  </section>
@endsection
@section('scripts')
  <!-- Scripts específicos para esta vista -->
  <script src="{{ asset('js/scripts2.js') }}"></script>
@endsection