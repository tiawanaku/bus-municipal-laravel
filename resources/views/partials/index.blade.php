
@extends('layouts.app')
@section('content')
<section>
<div class="flex flex-col ">
    <div class="bg-cover bg-center  flex items-center justify-between px-4 py-4">
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
                <input type="search" id="search" autocomplete="off"
                    class="block w-full md:w-1/4 lg:w-16 p-4 ps-10 text-sm text-gray-900 border border-gray-300 rounded-full bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Buscar Paradas" required />
                <div id="suggestions"
                    class="absolute z-50 bg-white border border-gray-200 rounded-lg shadow-md max-h-52 overflow-y-auto w-full md:w-1/2 p-2 hidden transition-all ease-in-out duration-200 transform scale-95 hover:scale-100">
                </div>
            </div>
        </form>
        <a href="/admin">
            <button type="button"
                class="hidden  md:block text-blue-500 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-xl px-5 py-2.5 ml-4 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800">
                Iniciar Sesión
            </button>
        </a>



    </div>


    <section class="mt-2 flex justify-center">
        <!-- Mapa -->
        <div class="w-full md:max-w-full lg:w-full px-6 mx-auto ">
            <div class="w-full h-96 rounded-lg border-y-4 shadow-lg">
                <div id="mapa_bus" class="w-full h-full rounded-lg bg-gray-300 z-0"></div>
            </div>
        </div>
    </section>


    <div>

    </div>

</div>



<!-- Tabs de Estado del Servicio -->
<div class="w-full max-w-6xl justify-center mx-auto pt-6 py-4">
    <div class="flex border-b border-gray-300 space-x-4 pb-4 ">
        <button
            class=" tab-btn px-4 py-2 text-white bg-gradient-to-r from-red-800 to-red-900 shadow-md rounded-full hover:bg-gray-700"
            data-tab="bus">Estado del
            servicio
            Bus</button>
        <button class="tab-btn px-4 py-2 text-white bg-gray-900 rounded-full shadow-md hover:bg-gray-700"
            data-tab="rutaNorte">Ruta
            Norte</button>
        <button class="tab-btn px-4 py-2 text-white bg-gray-900 rounded-full shadow-md hover:bg-gray-700"
            data-tab="rutaSur">Ruta
            Sur</button>
    </div>
    <div>
        <!-- Contenido de cada tab -->
        <div class="tab-content bg-gray-900 p-6 rounded-xl shadow-lg" id="bus">
    <h2 class="text-xl font-bold text-white">Estado del servicio Bus</h2>
    <p class="text-gray-200">Información sobre el estado de los buses...</p>
    <div class="w-full mt-3">
        <div class="space-y-4"> <!-- Espacio entre los items de los avisos -->

            <!-- Lista de avisos con cards -->
            @foreach ($avisos as $aviso)
                <div class="bg-gray-800 p-4 rounded-lg shadow-md">
                    <h5 class="font-semibold text-lg text-sky-500">Noticia: {{ $aviso->noticia }}</h5>
                    <p class="font-sans tracking-tight text-white"><span class="font-semibold text-white">Duración:</span> {{ $aviso->inicio_periodo }} ~ {{ $aviso->fin_periodo }}</p>
                    <p class="font-sans tracking-tight text-white"><span class="font-semibold text-white">Razón:</span> {{ $aviso->razon }}</p>
                    <p class="font-sans tracking-tight text-white"><span class="font-semibold text-white">Paradas afectadas:</span> {{ $aviso->paradas_afectadas }}</p>
                    <p class="font-sans tracking-tight text-white"><span class="font-semibold text-white">Detalle:</span> {{ $aviso->detalle }}</p>
                    <small class="text-gray-100">Actualizado hace {{ $aviso->created_at->diffForHumans() }}</small>
                </div>
            @endforeach

        </div>
    </div>
</div>

        <div class="tab-content hidden bg-gray-900 p-8 rounded-2xl shadow-lg" id="rutaNorte">
            <h2 class="text-2xl font-bold text-blue-500 mb-4">Próximos buses en RUTA NORTE</h2>
            <p class="text-gray-400 mb-8">Información de llegada de próximos buses a las paradas...</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

                {{-- Columna Sentido Ida --}}
                <div>
                    <h3 class="text-gray-300 font-semibold text-lg mb-6">Sentido Ida (Playa Verde ➔ CEJA)</h3>
                    <ul class="flex flex-col border-l-4 border-blue-500 pl-6 space-y-6">
                        @foreach ($locations->where('id_ruta', 1)->where('sentido', 'Ida')->sortBy('orden') as $parada)
                        <li class="relative group" data-parada="{{ $parada->nombre_parada }}" data-sentido="Ida">
                                <div
                                    class="absolute -left-5 top-2 w-4 h-4 bg-blue-500 rounded-full border-2 border-white group-hover:bg-indigo-300 transition">
                                </div>
                                <div
                                    class="flex justify-between items-center text-white text-sm group-hover:text-indigo-200 transition">
                                    <a href="javascript:void(0)"
                                        onclick=" centrarEnParada({{ $parada->id_paradas }})"
                                        class="font-medium underline hover:text-indigo-400 transition">
                                        {{ $parada->nombre_parada }}
                                    </a>
                                    <span class="tiempo-llegada text-green-400 font-semibold">-- min</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Columna Sentido Vuelta --}}
                <div>
                    <h3 class="text-gray-300 font-semibold text-lg mb-6">Sentido Vuelta (CEJA ➔ Playa Verde)</h3>
                    <ul class="flex flex-col border-l-4 border-blue-500 pl-6 space-y-6">
                        @foreach ($locations->where('id_ruta', 1)->where('sentido', 'Vuelta')->sortBy('orden') as $parada)
                            <li class="relative group" data-parada="{{ $parada->nombre_parada }}" data-sentido="Vuelta">
                                <div
                                    class="absolute -left-5 top-2 w-4 h-4 bg-blue-500 rounded-full border-2 border-white group-hover:bg-indigo-300 transition">
                                </div>
                                <div
                                    class="flex justify-between items-center text-white text-sm group-hover:text-indigo-200 transition">
                                    <a href="javascript:void(0)"
                                        onclick=" centrarEnParada({{ $parada->id_paradas }})"
                                        class="font-medium underline hover:text-indigo-400 transition">
                                        {{ $parada->nombre_parada }}
                                    </a>
                                    <span class="tiempo-llegada text-green-400 font-semibold">-- min</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

            </div>
        </div>
        <div class="tab-content hidden bg-gray-900 p-8 rounded-2xl shadow-lg" id="rutaSur">
            <h2 class="text-2xl font-bold text-indigo-400 mb-4">Próximos buses en RUTA SUR</h2>
            <p class="text-gray-400 mb-8">Información de llegada de próximos buses a las paradas...</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

                {{-- Columna Sentido Ida --}}
                <div>
                    <h3 class="text-gray-300 font-semibold text-lg mb-6">Sentido Ida (SAMO ➔ CEJA)</h3>
                    <ul class="flex flex-col border-l-4 border-indigo-500 pl-6 space-y-6">
                        @foreach ($locations->where('id_ruta', 2)->where('sentido', 'Ida')->sortBy('orden') as $parada)
                        <li class="relative group" data-parada="{{ $parada->nombre_parada }}" data-sentido="Ida">
                                <div
                                    class="absolute -left-5 top-2 w-4 h-4 bg-indigo-500 rounded-full border-2 border-white group-hover:bg-indigo-300 transition">
                                </div>
                                <div
                                    class="flex justify-between items-center text-white text-sm group-hover:text-indigo-200 transition">
                                    <a href="javascript:void(0)"
                                        onclick=" centrarEnParada({{ $parada->id_paradas }})"
                                        class="font-medium underline hover:text-indigo-400 transition">
                                        {{ $parada->nombre_parada }}
                                    </a>
                                    <span class="tiempo-llegada text-green-400 font-semibold">-- min</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Columna Sentido Vuelta --}}
                <div>
                    <h3 class="text-gray-300 font-semibold text-lg mb-6">Sentido Vuelta (CEJA ➔ SAMO)</h3>
                    <ul class="flex flex-col border-l-4 border-indigo-500 pl-6 space-y-6">
                        @foreach ($locations->where('id_ruta', 2)->where('sentido', 'Vuelta')->sortBy('orden') as $parada)
                        <li class="relative group" data-parada="{{ $parada->nombre_parada }}" data-sentido="Vuelta">
                                <div
                                    class="absolute -left-5 top-2 w-4 h-4 bg-indigo-500 rounded-full border-2 border-white group-hover:bg-indigo-300 transition">
                                </div>
                                <div
                                    class="flex justify-between items-center text-white text-sm group-hover:text-indigo-200 transition">
                                    <a href="javascript:void(0)"
                                        onclick=" centrarEnParada({{ $parada->id_paradas }})"
                                        class="font-medium underline hover:text-indigo-400 transition">
                                        {{ $parada->nombre_parada }}
                                    </a>
                                    <span class="tiempo-llegada text-green-400 font-semibold">-- min</span>
                                </div>
                            </li>
                        @endforeach
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
    
    const rutas = @json($rutas);
    

</script>
</section>
@endsection
@section('scripts')
    <!-- Scripts específicos para esta vista -->
    <script src="{{ asset('js/scripts.js') }}"></script>
@endsection