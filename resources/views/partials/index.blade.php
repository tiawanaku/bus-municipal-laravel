@extends('layouts.app')
@section('content')
    <section>
        <div class="flex flex-col ">
            <div class="bg-cover bg-center  flex items-center justify-between px-4 py-4">
                <form class="max-w-md ml-0 mt-4">
                    <label for="search"
                        class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Buscar</label>
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
                        class="hidden md:block text-blue-500 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-xl px-5 py-2.5 ml-4 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800">
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
                <!-- Avisos -->
                <div class="tab-content  bg-gray-900 p-8 rounded-2xl shadow-lg" id="bus">
                    @php
                        $tipos = [
                            'Cambio de Ruta' => 'Cambio de Ruta',
                            'Bloqueo de Vías' => 'Bloqueo de Vías',
                            'Nueva Ruta' => 'Nueva Ruta',
                            'Suspención del servicio' => 'Suspención del servicio',
                            'Otro' => 'Otro'
                        ];
                        // Conteo por tipo
                        $conteos = [];
                        foreach ($tipos as $key => $tipo) {
                            $conteos[$key] = $avisos->where('noticia', $key)->count();
                        }
                    @endphp

                    <ul class="flex flex-wrap text-sm font-medium text-center text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700 mb-4"
                        id="subTabs" role="tablist">
                        @foreach($tipos as $key => $tipo)
                            <li class="me-2">
                                <button id="tab-{{ Str::slug($key) }}" data-tabs-target="#contenido-{{ Str::slug($key) }}"
                                    class="inline-flex items-center p-2 rounded-t-lg hover:text-blue-600 dark:hover:text-blue-400 relative"
                                    type="button" role="tab">
                                    {{ $tipo }}
                                    @if($conteos[$key] > 0)
                                        <span
                                            class="ml-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                                            {{ $conteos[$key] }}
                                        </span>
                                    @endif
                                </button>
                            </li>
                        @endforeach
                    </ul>

                    <div id="subTabContent">
                        @foreach($tipos as $key => $tipo)
                            <div id="contenido-{{ Str::slug($key) }}" role="tabpanel"
                                class="{{ $loop->first ? '' : 'hidden' }}">
                                @php
                                    $avisosTipo = $avisos->where('noticia', $key);
                                @endphp

                                @forelse ($avisosTipo as $aviso)
                                    <div
                                        class="p-4 mb-4 bg-gray-900 rounded shadow-lg shadow-blue-500/50 flex items-center space-x-4 border-l-2 border-blue-500">
                                        <!-- GIF específico para cada tipo de noticia -->
                                        @if ($aviso->noticia == 'Cambio de Ruta')
                                            <img src="{{ asset('img/icons/cambio.png') }}" alt="Cambio de Ruta"
                                                class="w-12 h-12 object-contain">
                                        @elseif ($aviso->noticia == 'Bloqueo de Vías')
                                            <img src="{{ asset('img/icons/bloqueos.png') }}" alt="Bloqueo de Vías"
                                                class="w-12 h-12 object-contain">
                                        @elseif ($aviso->noticia == 'Nueva Ruta')
                                            <img src="{{ asset('img/icons/nuevo.png') }}" alt="Nueva Ruta"
                                                class="w-12 h-12 object-contain">
                                        @elseif ($aviso->noticia == 'Suspención del servicio')
                                            <img src="{{ asset('img/icons/suspencion.png') }}" alt="Suspención del servicio"
                                                class="w-12 h-12 object-contain">
                                        @elseif ($aviso->noticia == 'Otro')
                                            <img src="{{ asset('img/icons/otros.png') }}" alt="Otro" class="w-12 h-12 object-contain">
                                        @endif

                                        <div>
                                            {{-- Mostrar título solo si NO es del tipo "Otro" --}}
                                            @if ($aviso->noticia !== 'Otro')
                                                <h5 class="font-bold text-lg text-white">{{ $aviso->noticia }}</h5>
                                            @endif
                                            <div class="text-sm text-gray-200"> {!! $aviso->detalle !!}</div>

                                            <p class="text-xs text-gray-300 mt-2 text-right">Desde:
                                                {{ \Carbon\Carbon::parse($aviso->inicio_periodo)->format('d/m/Y H:i') }}
                                            </p>
                                            <p class="text-xs text-gray-300 mt-2 text-right">Hasta:
                                                {{ \Carbon\Carbon::parse($aviso->fin_periodo)->format('d/m/Y H:i') }}
                                            </p>

                                        </div>

                                    </div>
                                @empty
                                    <p class="text-gray-500">No hay avisos de tipo "{{ $tipo }}".</p>
                                @endforelse
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- RUTA NORTE -->
                <div class="tab-content hidden bg-gray-900 p-6 rounded-2xl shadow-xl" id="rutaNorte">
                    <h2 class="text-2xl font-bold text-blue-500 mb-4">🚌 Próximos buses en RUTA NORTE</h2>
                    <p class="text-gray-400 mb-6 text-sm">Consulta en tiempo real la llegada de buses por parada.</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Sentido Ida -->
                        <div>
                            <div class="flex items-center gap-3 mb-3 border-b border-blue-700 pb-1">
                                <img src="{{ asset('img/icons/Ida.png') }}" alt="Nueva Ruta"
                                    class="w-12 h-12 object-contain">
                                <h3 class="text-sm text-white font-semibold">Ida (Playa Verde ➔ Playa Ida)</h3>
                            </div>
                            <ul class="space-y-4">
                               @foreach (
                                $locations->filter(function($parada) {
                                    return stripos($parada->ruta->nombre ?? '', 'norte') !== false 
                                        && $parada->sentido === 'Ida';
                                })->sortBy('orden') as $parada)
                                <li class="flex items-center gap-3 bg-gray-800 border-b-2 border-blue-500 rounded-lg p-3 shadow-lg shadow-blue-500/50"
                                    data-parada="{{ $parada->nombre_parada }}" data-sentido="Ida">
                                    <div class="flex items-center justify-center w-8 h-8 bg-gray-700 text-white rounded-full">
                                        {{ $parada->orden }}
                                    </div>
                                    <div class="flex justify-between items-center w-full">
                                        <a href="javascript:void(0)" onclick="centrarEnParada({{ $parada->id_paradas }})"
                                            class="font-medium underline text-white font-medium hover:text-blue-400 transition">
                                            {{ $parada->nombre_parada }}
                                        </a>
                                        <span class="text-blue-400 text-sm">Llega en: <span class="tiempo-llegada">-- min</span></span>
                                    </div>
                                </li>
                            @endforeach
                            </ul>
                        </div>
                        <!-- Sentido Vuelta -->
                        <div>
                            <div class="flex items-center gap-3 mb-3 border-b border-blue-700 pb-1">
                                <img src="{{ asset('img/icons/vuelta.png') }}" alt="Nueva Ruta"
                                    class="w-12 h-12 object-contain">
                                <h3 class="text-sm text-white font-semibold">Vuelta (CEJA ➔ Playa Verde)</h3>
                            </div>

                            <ul class="space-y-4">
                                @foreach (
                                $locations->filter(function($parada) {
                                    return stripos($parada->ruta->nombre ?? '', 'norte') !== false 
                                        && $parada->sentido === 'Vuelta';
                                })->sortBy('orden') as $parada
                            )
                                <li class="flex items-center gap-3 bg-gray-800 border-b-2 border-blue-500 rounded-lg p-3 shadow-lg shadow-blue-500/50"
                                    data-parada="{{ $parada->nombre_parada }}" data-sentido="Vuelta">
                                    <div class="flex items-center justify-center w-8 h-8 bg-gray-700 text-white rounded-full">
                                        {{ $parada->orden }}
                                    </div>
                                    <div class="flex justify-between items-center w-full">
                                        <a href="javascript:void(0)" onclick="centrarEnParada({{ $parada->id_paradas }})"
                                            class="font-medium underline text-white font-medium hover:text-blue-400 transition">
                                            {{ $parada->nombre_parada }}
                                        </a>
                                        <span class="text-blue-400 text-sm">Llega en: <span class="tiempo-llegada">-- min</span></span>
                                    </div>
                                </li>
                            @endforeach
                            </ul>
                        </div>

                       
                    </div>
                </div>




                <!-- RUTA SUR -->
                <div class="tab-content hidden bg-gray-900 p-8 rounded-2xl shadow-lg" id="rutaSur">
                    <h2 class="text-2xl font-bold text-indigo-400 mb-4">Próximos buses en RUTA SUR</h2>
                    <p class="text-gray-400 mb-8">Información de llegada de próximos buses a las paradas...</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Sentido Ida -->
                        <div>
                            <div class="flex items-center gap-3 mb-3 border-b border-blue-700 pb-1">
                                <img src="{{ asset('img/icons/Ida.png') }}" alt="Nueva Ruta"
                                    class="w-12 h-12 object-contain">
                                <h3 class="text-sm text-white font-semibold">Ida (SAMO ➔ CEJA)</h3>
                            </div>
                            <ul class="space-y-4">
                               @foreach (
                                $locations->filter(function($parada) {
                                    return stripos($parada->ruta->nombre ?? '', 'sur') !== false 
                                        && $parada->sentido === 'Ida';
                                })->sortBy('orden') as $parada
                            )
                                <li class="flex items-center gap-3 bg-gray-800 border-b-2 border-violet-500 rounded-lg p-3 shadow-lg shadow-indigo-500/50"
                                    data-parada="{{ $parada->nombre_parada }}" data-sentido="Ida">
                                    <div class="flex items-center justify-center w-8 h-8 bg-gray-700 text-white rounded-full">
                                        {{ $parada->orden }}
                                    </div>
                                    <div class="flex justify-between items-center w-full">
                                        <a href="javascript:void(0)" onclick="centrarEnParada({{ $parada->id_paradas }})"
                                            class="font-medium underline text-white font-medium hover:text-blue-400 transition">
                                            {{ $parada->nombre_parada }}
                                        </a>
                                        <span class="text-blue-400 text-sm">Llega en: <span class="tiempo-llegada">-- min</span></span>
                                    </div>
                                </li>
                            @endforeach
                            </ul>
                        </div>
                        <!-- Sentido Vuelta -->
                        <div>
                            <div class="flex items-center gap-3 mb-3 border-b border-blue-700 pb-1">
                                <img src="{{ asset('img/icons/vuelta.png') }}" alt="Nueva Ruta"
                                    class="w-12 h-12 object-contain">
                                <h3 class="text-sm text-white font-semibold">Vuelta (CEJA ➔ SAMO)</h3>
                            </div>

                            <ul class="space-y-4">
                                @foreach (
                                $locations->filter(function($parada) {
                                    return stripos($parada->ruta->nombre ?? '', 'sur') !== false 
                                        && $parada->sentido === 'Vuelta';
                                })->sortBy('orden') as $parada
                            )
                                <li class="flex items-center gap-3 bg-gray-800 border-b-2 border-indigo-500 rounded-lg p-3 shadow-lg shadow-indigo-500/50"
                                    data-parada="{{ $parada->nombre_parada }}" data-sentido="Vuelta">
                                    <div class="flex items-center justify-center w-8 h-8 bg-gray-700 text-white rounded-full">
                                        {{ $parada->orden }}
                                    </div>
                                    <div class="flex justify-between items-center w-full">
                                        <a href="javascript:void(0)" onclick="centrarEnParada({{ $parada->id_paradas }})"
                                            class="font-medium underline text-white font-medium hover:text-blue-400 transition">
                                            {{ $parada->nombre_parada }}
                                        </a>
                                        <span class="text-blue-400 text-sm">Llega en: <span class="tiempo-llegada">-- min</span></span>
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


            window.rutasData = @json($rutas);
            window.locationsData = @json($locations);

        </script>
    </section>
@endsection
@section('scripts')
    <!-- Scripts específicos para esta vista -->
    <script src="{{ asset('js/scripts.js') }}"></script>
@endsection