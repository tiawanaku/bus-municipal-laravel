<div class="flex flex-col h-full">
    <div class="bg-cover bg-center h-24">
        <form class="max-w-md ml-0 mt-4">
            <label for="search"
                class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
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
                    class="absolute z-50 bg-white border border-gray-200 rounded-lg shadow-md max-h-52 overflow-y-auto w-full md:w-1/2 p-2 hidden transition-all ease-in-out duration-200 transform scale-95 hover:scale-100"></div>

            </div>
        </form>
    </div>

    <section class="flex flex-row flex-1">
        <div class="flex-grow mr-4 z-0">
            <div id="mapa_bus" class="h-5/6 sm:h-[30vh] md:h-96 w-11/12 sm:w-full md:h-96  w-full relative"></div>
        </div>

        <!-- Script para Leaflet -->
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script>
            var locations = @json($locations);
        </script>




