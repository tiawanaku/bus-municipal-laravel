@extends('layouts.app')
@section('content')
    <section>
        <div class="bg-gray-900 text-white">
            <!-- Seccion principal -->
            <section
                class="relative min-h-screen flex items-center justify-center px-6 lg:px-16 bg-cover bg-center bg-no-repeat"
                @if ($rutas && $rutas->imagen) style="background-image: url('{{ asset('storage/' . $rutas->imagen) }}');"
                @endif>
                <div class="absolute inset-0 bg-black bg-opacity-60"></div>
                <div class="relative z-10 text-center text-white max-w-3xl">
                    <h1 class="text-5xl font-bold mb-6 text-shadow-lg/30" style="color: '#ffff'">
                        {{ strtoupper($rutas->nombre ?? 'Ruta no encontrada') }}
                    </h1>
                    <p class="text-gray-200 leading-relaxed text-lg">
                        {!! nl2br(e($rutas->descripcion ?? 'Sin descripción disponible')) !!}
                    </p>
                </div>
            </section>

             <!-- Paradas de la ruta -->
            <section class="bg-gray-900 py-16">
                   <div class="w-full px-8 overflow-visible">

                    <!-- Título Ida -->
                    <h2 class="hidden sm:block text-white text-xl font-semibold mb-6">Sentido Ida</h2>


                    <ol id="lista-ida" class="hidden sm:flex w-full flex-col sm:flex-row items-start gap-4 mb-12">
                        @foreach ($paradasIda as $parada)
                            <li class="relative flex-1">
                                <div class="flex items-start justify-start sm:items-center sm:justify-center">
                                    <div class="relative flex items-center justify-center">
                                        <div data-popover-target="popover-ida-{{ $loop->index }}"
                                            class="z-10 flex items-center justify-center w-8 h-8 border border-blue-500 rounded-full text-white font-bold cursor-pointer">
                                            {{ $loop->index + 1 }}
                                        </div>
                                        <p class="absolute top-10 text-xs text-white text-center w-24">
                                            {{ $parada->nombre_parada }}
                                        </p>
                                    </div>

                                    @if (!$loop->last)
                                        <div class="hidden sm:flex flex-1 h-0.5 bg-gray-300 min-w-[30px]"></div>
                                    @endif
                                </div>

                                <!-- Popover -->
                                <div data-popover id="popover-ida-{{ $loop->index }}" role="tooltip"
                                    class="absolute z-10 invisible inline-block w-54 text-sm transition-opacity duration-300 border rounded-lg shadow-lg opacity-0 text-gray-400 border-gray-600 bg-gray-800">
                                    <div class="px-3 py-2">
                                        <img src="{{ asset('storage/' . $parada->imagen) }}" alt="{{ $parada->nombre_parada }}"
                                            class="w-full h-48 object-cover transform transition-transform duration-300 group-hover:scale-110">
                                    </div>
                                    <div data-popper-arrow></div>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                    <!-- Acordeón SOLO visible en pantallas chicas -->
                    <div id="accordion-sentido-ida" data-accordion="collapse" class="sm:hidden mb-6">
                        <h2 id="accordion-ida-heading">
                            <button type="button"
                                class="flex items-center justify-between w-full p-4 font-medium text-left border border-blue-500 bg-gray-900 text-white rounded-lg focus:ring-2 focus:ring-blue-500 "
                                data-accordion-target="#accordion-ida-body" aria-expanded="false"
                                aria-controls="accordion-ida-body">
                                <span>Sentido Ida</span>
                                <svg data-accordion-icon class="w-4 h-4 rotate-0 shrink-0 transition-transform" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                        </h2>
                        <div id="accordion-ida-body" class="hidden" aria-labelledby="accordion-ida-heading">
                            <ol class="w-full flex flex-col gap-4 p-4 bg-gray-800 rounded-b-lg">
                                @foreach ($paradasIda as $parada)
                                    <li class="relative flex items-center gap-2">
                                        <div data-popover-target="popover-chico-ida-{{ $loop->index }}"
                                            class="z-10 flex items-center justify-center w-8 h-8 border border-blue-500  rounded-full text-white font-bold cursor-pointer">
                                            {{ $loop->index + 1 }}
                                        </div>
                                        <p class="text-sm text-white">
                                            {{ $parada->nombre_parada }}
                                        </p>

                                        <!-- Popover para versión móvil -->
                                        <div data-popover id="popover-chico-ida-{{ $loop->index }}" role="tooltip"
                                            class="absolute z-10 invisible inline-block w-54 text-sm transition-opacity duration-300 border rounded-lg shadow-lg opacity-0 text-gray-400 border-gray-600 bg-gray-800">
                                            <div class="px-3 py-2">
                                                <img src="{{ asset('storage/' . $parada->imagen) }}"
                                                    alt="{{ $parada->nombre_parada }}"
                                                    class="w-full h-48 object-cover transform transition-transform duration-300 group-hover:scale-110">
                                            </div>
                                            <div data-popper-arrow></div>
                                        </div>
                                    </li>
                                @endforeach
                            </ol>
                        </div>
                    </div>
                    <!-- Línea divisoria -->
                    <hr class="my-10 border-t border-gray-600">

                    <!-- Título Vuelta -->
                    <h2 class="hidden sm:block text-white text-xl font-semibold mb-6">Sentido Vuelta</h2>

                    <ol id="lista-vuelta" class="hidden sm:flex w-full flex-col sm:flex-row items-start gap-4 mb-12">
                        @foreach ($paradasVuelta as $parada)
                            <li class="relative flex-1">
                                <div class="flex items-center justify-center">
                                    <div class="relative flex items-center justify-center">
                                        <div data-popover-target="popover-vuelta-{{ $loop->index }}"
                                            class="z-10 flex items-center justify-center w-8 h-8 border border-red-500 rounded-full text-white font-bold cursor-pointer">
                                            {{ $loop->index + 1 }}
                                        </div>
                                        <p class="absolute top-10 text-xs text-white text-center w-24">
                                            {{ $parada->nombre_parada }}
                                        </p>
                                    </div>

                                    @if (!$loop->last)
                                        <div class="hidden sm:flex flex-1 h-0.5 bg-gray-300 min-w-[30px]"></div>
                                    @endif
                                </div>

                                <!-- Popover -->
                                <div data-popover id="popover-vuelta-{{ $loop->index }}" role="tooltip"
                                    class="absolute z-10 invisible inline-block w-54 text-sm transition-opacity duration-300 border rounded-lg shadow-lg opacity-0 text-gray-400 border-gray-600 bg-gray-800">
                                    <div class="px-3 py-2">
                                        <img src="{{ asset('storage/' . $parada->imagen) }}" alt="{{ $parada->nombre_parada }}"
                                            class="w-full h-48 object-cover transform transition-transform duration-300 group-hover:scale-110">
                                    </div>
                                    <div data-popper-arrow></div>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                    <!-- Acordeón para pantallas pequeñas (vuelta) -->
                    <div id="accordion-sentido-vuelta" data-accordion="collapse" class="sm:hidden mb-6">
                        <h2 id="accordion-vuelta-heading">
                            <button type="button"
                                class="flex items-center justify-between w-full p-4 font-medium text-left text-white border border-red-500 bg-gray-900 rounded-lg"
                                data-accordion-target="#accordion-vuelta-body" aria-expanded="false"
                                aria-controls="accordion-vuelta-body">
                                <span>Sentido Vuelta</span>
                                <svg data-accordion-icon class="w-4 h-4 rotate-0 shrink-0 transition-transform" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                        </h2>
                        <div id="accordion-vuelta-body" class="hidden" aria-labelledby="accordion-vuelta-heading">
                            <ol class="w-full flex flex-col gap-4 p-4 bg-gray-800 rounded-b-lg">
                                @foreach ($paradasVuelta as $parada)
                                    <li class="relative flex items-center gap-2">
                                        <div data-popover-target="popover-chico-vuelta-{{ $loop->index }}"
                                            class="z-10 flex items-center justify-center w-8 h-8 border border-red-500 rounded-full text-white font-bold cursor-pointer">
                                            {{ $loop->index + 1 }}
                                        </div>
                                        <p class="text-sm text-white">
                                            {{ $parada->nombre_parada }}
                                        </p>

                                        <!-- Popover para versión móvil -->
                                        <div data-popover id="popover-chico-vuelta-{{ $loop->index }}" role="tooltip"
                                            class="absolute z-10 invisible inline-block w-54 text-sm transition-opacity duration-300 border rounded-lg shadow-lg opacity-0 text-gray-400 border-gray-600 bg-gray-800">
                                            <div class="px-3 py-2">
                                                <img src="{{ asset('storage/' . $parada->imagen) }}"
                                                    alt="{{ $parada->nombre_parada }}"
                                                    class="w-full h-48 object-cover transform transition-transform duration-300 group-hover:scale-110">
                                            </div>
                                            <div data-popper-arrow></div>
                                        </div>
                                    </li>
                                @endforeach
                            </ol>
                        </div>
                    </div>

                </div>
            </section>



            <!-- Sección de Video  -->
            <section class="bg-gray-800 py-16">
                <div class="container mx-auto grid grid-cols-1 lg:grid-cols-1 gap-8 items-start">
                    @if (!empty($rutas->video_link))
                        <div id="fb-root"></div>
                        <script async defer crossorigin="anonymous"
                            src="https://connect.facebook.net/es_ES/sdk.js#xfbml=1&version=v19.0">
                            </script>

                        <div class="fb-video mx-auto" data-href="{{ $rutas->video_link }}" data-width="800"
                            data-show-text="false">
                        </div>
                    @endif
                </div>
            </section>




            <!-- Botón para Regresar -->
            <section class="py-6 text-center">
                <a href="/bus-municipal"
                    class="px-6 py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition">
                    Volver a la Página Principal
                </a>
            </section>
        </div>
    </section>

    </div>
    </section>

@endsection