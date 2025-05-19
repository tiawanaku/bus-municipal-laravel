@extends('layouts.app')
@section('content')
  <!-- Header Section -->
  <header class="bg-gray-900 bg-cover bg-center h-screen"
    style="background-image: url('{{ asset('storage/' . $header->imagen) }}');">
    <div class="bg-black bg-opacity-60 h-full flex flex-col justify-center items-center text-center">
    <h1 class="text-5xl text-white font-bold mb-4">
      {{ $header->titulo ?? 'Bus Municipal' }}
    </h1>
    <div class="text-lg text-white italic mb-6">
      {!! $header->descripcion ?? '<em>"El bus del corazón de los alteños."</em>' !!}
    </div>
    <a href="/"
      class="text-blue-600 hover:text-white border border-blue-600 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg px-6 py-3.5 text-center mb-2 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800">
      Seguimiento en Vivo
    </a>
    </div>
  </header>
  <!-- About Section -->
  <section id="about" class="py-16 bg-gray-900 bg-fixed bg-center bg-cover relative text-gray-300"
    style="background-image: url('img/bus.jpg');">
    <div class="absolute inset-0 bg-black bg-opacity-90"></div>

    <div class="container mx-auto px-6 md:px-12 relative text-center">
    <h2 class="text-4xl font-bold text-blue-500 mb-12">
      {{ $about->titulo ?? 'Sobre Nosotros' }}
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">

      <div class="text-lg text-white  mb-6">
      {!! $about->descripcion ?? '<p class="text-lg text-gray-300 md:text-center">
      El Servicio Municipal de Transporte Público Masivo (Bus Municipal) de la ciudad de El Alto inició sus
      operaciones el 04 de septiembre de 2023.<br><br>
      Con el objetivo de brindar un servicio público de transporte municipal, que contribuya a mejorar las condiciones
      de vida de la población beneficiaria con escasos servicios de transporte y ser una alternativa al transporte
      público convencional.
      </p>' !!}
      </div>
      <figure class="md:ml-auto md:max-w-md w-full">
      @if (!empty($about->imagen))
      <img class="rounded-lg shadow-lg transition-transform duration-300 hover:scale-105"
      src="{{ asset('storage/' . $about->imagen) }}" alt="Sobre Nosotros">
    @endif
      </figure>


    </div>
    <!-- Misión y Visión -->

    <!-- Msisión -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center mt-12 overflow-x-hidden">
      <div data-aos="fade-right" data-aos-duration="1000" data-aos-easing="ease-in-out">
      <h2 class="text-3xl font-bold text-blue-500 mb-6">{{ $mision->titulo ?? 'Misión' }}</h2>
      <div class="text-lg text-white mb-4">
        {!! $mision->descripcion ?? '<p>"Nuestra misión es..."</p>' !!}
      </div>
      </div>
      <div class="flex justify-center" data-aos="fade-left" data-aos-duration="1000" data-aos-easing="ease-in-out">
      @if (!empty($vision->imagen))
      <img class="rounded-lg shadow-lg mx-auto object-cover h-64 w-full"
      src="{{ asset('storage/' . $mision->imagen) }}" alt="Misión">

    @endif
      </div>
    </div>
    <!-- Visión -->
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center mt-12 overflow-x-hidden">
      <div class="order-2 md:order-1" data-aos="fade-right" data-aos-duration="1000" data-aos-easing="ease-in-out">
      @if (!empty($vision->imagen))
      <img class="rounded-lg shadow-lg mx-auto object-cover h-64 w-full max-w-full md:max-w-md"
      src="{{ asset('storage/' . $vision->imagen) }}" alt="Visión">
    @endif
      </div>
      <div class="order-1 md:order-2" data-aos="fade-left" data-aos-duration="1000" data-aos-easing="ease-in-out">
      <h2 class="text-3xl font-bold text-blue-500 mb-6">{{ $vision->titulo ?? 'Visión' }}</h2>
      <div class="text-lg text-white mb-4 max-w-full md:max-w-xl">
        {!! $vision->descripcion ?? '<p>"Nuestra visión es..."</p>' !!}
      </div>
      </div>
    </div>

    </div>
  </section>
  <!-- Sección de Pasajes y Horarios -->
  <section id="pasajes" class="py-20 bg-gray-900 px-8">
    <h2 class="text-4xl font-bold text-white mb-12 text-center">
    Conoce Nuestros Pasajes
    </h2>
    <div class="flex justify-center">
    <div class="grid gap-4 max-w-screen-xl w-full justify-center"
      style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); max-width: 1000px;">
      @foreach ($pasajes as $pasaje)
      <div
      class="rounded-xl shadow-xl p-6 transition-transform transform hover:scale-105 bg-gray-800 text-white w-[250px] border-l-2"
      style="border-left-color: {{ $pasaje->color }}" data-aos="fade-up" data-aos-easing="ease-in-out">
      <h2 class="text-2xl font-bold">{{ $pasaje->titulo }}</h2>
      <p class="mb-2">{{ $pasaje->descripcion }}</p>
      <p class="font-semibold text-4xl">Bs {{ number_format($pasaje->precio, 2) }}</p>

      <div class="mt-4 relative h-[150px] overflow-hidden">
      <!--       <img src="{{ asset('storage/' . $pasaje->imagen) }}" alt="." class="w-full h-full object-contain"> -->
      </div>
      </div>
    @endforeach
    </div>
    </div>
  </section>
  <!-- Horarios del Servicio -->
  <section id="horarios" class="py-20 px-8 bg-gray-900 text-white bg-fixed"
    style="background-image: radial-gradient(circle, rgba(255, 255, 255, 0.09) 1px, transparent 1px); background-size: 30px 30px;">
    <h2 class="text-4xl font-bold mb-12 text-center">Horarios de Servicio</h2>

    <div class="flex justify-center">
    <div class="grid gap-8 max-w-screen-lg w-full justify-center"
      style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));">

      @foreach ($horarios as $horario)
      <div
      class="bg-[#2D3748] rounded-lg shadow-xl shadow-blue-500/50 p-6 transition transform hover:scale-105 hover:bg-[#3b4861] border-b-4"
      style="border-bottom-color: #3B82F6;">
      <h3 class="text-2xl font-semibold text-[#F6A5B0] mb-4">
      {{ ucfirst($horario->turno) }}
      </h3>

      <ul class="list-none space-y-3">
      <li class="flex flex-col">
      <span class="font-semibold text-lg">
        {{ \Carbon\Carbon::parse($horario->desde)->format('g:i A') }} —
        {{ \Carbon\Carbon::parse($horario->hasta)->format('g:i A') }}
      </span>
      </li>
      @if ($horario->descripcion)
      <li class="flex items-center">
      <span class="text-gray-300">
      {{ $horario->descripcion }}
      </span>
      </li>
      @endif
      </ul>
      </div>
    @endforeach

    </div>
    </div>
  </section>





  <!-- Sección Galeria -->
  <section class="py-12 bg-gray-900">
    <div class="container mx-auto px-4">
    <h2 class="text-3xl font-bold text-center text-white mb-8">Galería</h2>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      @foreach ($galerias as $index => $imagen)
      <div class="relative group rounded overflow-hidden shadow hover:shadow-lg transition">
      <a href="javascript:void(0);" onclick="openModal({{ $index }})">
      <img src="{{ asset('storage/' . $imagen->imagen) }}" alt="Imagen galería"
      class="w-full h-48 object-cover cursor-pointer">
      <div
      class="absolute inset-0 bg-black bg-opacity-60 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
      <p class="text-white text-sm px-4 text-center">{{ $imagen->descripcion }}</p>
      </div>
      </a>
      </div>
    @endforeach
    </div>

    <!-- Modal con carrusel -->
    <div id="modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-80 hidden"
      onclick="closeModal(event)">
      <div class="relative w-full max-w-3xl mx-auto" onclick="event.stopPropagation()">
      <!-- Evita cerrar cuando se hace clic dentro del modal -->
      <!-- Botón cerrar -->
      <button onclick="closeModal()"
        class="absolute top-2 right-2 text-white text-4xl p-2 bg-gray-800 rounded-full hover:bg-blue-500 transition">
        &times;
      </button>

      <!-- Imagen actual -->
      <div class="text-center p-4">
        <img id="modal-image" src="" alt="Imagen grande" class="max-h-[80vh] mx-auto rounded">
        <p id="modal-description" class="text-white mt-4 text-sm"></p>

        <!-- Controles -->
        <div class="flex justify-between mt-4">
        <!-- Flecha izquierda -->
        <button onclick="changeImage(-1)"
          class="absolute left-4 top-1/2 transform -translate-y-1/2 text-white text-2xl px-4 py-2 rounded-full bg-gray-800 hover:bg-blue-500 transition">
          &#10094;
        </button>

        <!-- Flecha derecha -->
        <button onclick="changeImage(1)"
          class="absolute right-4 top-1/2 transform -translate-y-1/2 text-white text-2xl px-4 py-2 rounded-full bg-gray-800 hover:bg-blue-500 transition">
          &#10095;
        </button>
        </div>
      </div>
      </div>
    </div>
    </div>
  </section>


  <script>
    const images = @json($galerias->values()); 
  </script>





@endsection
@section('scripts')
  <!-- Scripts específicos para esta vista -->
  <script src="{{ asset('js/scriptsPrincipal.js') }}"></script>
@endsection