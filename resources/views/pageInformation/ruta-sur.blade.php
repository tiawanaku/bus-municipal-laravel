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
                    <h1 class="text-5xl font-bold mb-6 text-shadow-lg/30" style="color: {{ $rutas->color ?? '#ffff' }}">
                        {{ strtoupper($rutas->nombre ?? 'Ruta no encontrada') }}
                    </h1>
                    <p class="text-gray-200 leading-relaxed text-lg">
                        {!! nl2br(e($rutas->descripcion ?? 'Sin descripción disponible')) !!}
                    </p>
                </div>
            </section>

            <!-- Paradas de la ruta -->
            <section class="bg-gray-800 py-16">


                <div class="max-w-screen-xl mx-auto overflow-x-auto px-4">
                    <div class="flex gap-4 overflow-x-auto snap-x snap-mandatory scroll-smooth">
                        @foreach ($paradas as $parada)
                            <div class="w-60 flex-shrink-0 snap-start rounded-lg overflow-hidden shadow group relative parada">
                                <img src="{{ asset('storage/' . $parada->imagen) }}" alt="{{ $parada->nombre_parada }}"
                                    class="w-full h-40 object-cover transform transition-transform duration-300 group-hover:scale-105">

                                <!-- Texto que aparece en hover -->
                                <div
                                    class="info-overlay absolute inset-0 bg-black bg-opacity-50 flex flex-col items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 text-center px-2">

                                    <p class="text-white text-lg font-semibold">{{ $parada->nombre_parada }}</p>

                                    <p class="text-white text-lg font-semibold">{{ $parada->descripcion }}</p>
                                </div>
                            </div>
                        @endforeach
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