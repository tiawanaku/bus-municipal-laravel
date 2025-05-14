<nav class="bg-gray-900 text-gray-300 fixed top-0 left-0 w-full z-50 shadow-md">
    <div class="container mx-auto flex flex-wrap items-center justify-between px-6 py-3">
        <a href="/bus-municipal" class="flex items-center shrink-0">
            <img src="img/logo.png" alt="Bus Municipal Logo" class="h-10 w-auto mr-2">
        </a>


        <!-- Botón que solo aparece en pantallas pequeñas (menores a 768px) -->
        <div class="md:hidden px-4 mt-2">
            <a href="/admin">
                <button type="button"
                    class="w-full text-blue-500 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-base py-2 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800">
                    Iniciar Sesión
                </button>
            </a>
        </div>

        <!-- Botón Drop Down en pantallas pequeñas -->
        <div data-dial-init class="fixed right-6 bottom-16 group block md:hidden">
            <div id="speed-dial-menu-dropdown"
                class="flex flex-col justify-end hidden py-1 mb-4 space-y-2 bg-gray-900 border border-gray-100 rounded-lg shadow-xs dark:border-gray-600 dark:bg-gray-700">
                <ul class="text-sm text-blue-500 dark:text-gray-300">
                    <li>
                        <a href="/"
                            class="flex items-center px-5 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 hover:text-gray-900 dark:hover:text-white">
                            <div class="w-3 h-3 rounded-full bg-red-600"></div>
                            <span class="text-sm font-medium">En Vivo</span>
                        </a>
                    </li>
                    <li>
                        <a href="/bus-municipal#about"
                            class="flex items-center px-5 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 hover:text-gray-900 dark:hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" class="w-3.5 h-3.5 me-2">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 21l-1-1C5 15 3 12 3 8a5 5 0 0 1 5-5c1.5 0 3 1 4 2 1-1 2.5-2 4-2a5 5 0 0 1 5 5c0 4-2 7-8 12l-1 1z" />
                            </svg>

                            <span class="text-sm font-medium">Sobre Nosotros</span>
                        </a>
                    </li>
                    <li>
                        <a href="/bus-municipal#rutas"
                            class="flex items-center px-5 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 hover:text-gray-900 dark:hover:text-white">

                            <svg class="w-3.5 h-3.5 me-2" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 2a1 1 0 011 1v11.382l3.707-3.708a1 1 0 111.414 1.414l-5 5a1 1 0 01-1.414 0l-5-5a1 1 0 111.414-1.414L9 14.382V3a1 1 0 011-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="text-sm font-medium">Rutas</span>
                        </a>
                    </li>
                    <li>
                        <a href="/bus-municipal#pasajes"
                            class="flex items-center px-5 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 hover:text-gray-900 dark:hover:text-white">
                            <i class="fa fa-money-bill-alt text-green-500"></i>
                            <span class="text-sm font-medium">Pasajes y Horarios</span>
                        </a>
                    </li>
                </ul>
            </div>
            <button type="button" data-dial-toggle="speed-dial-menu-dropdown" aria-controls="speed-dial-menu-dropdown"
                aria-expanded="false"
                class="flex items-center justify-center ml-auto text-white bg-blue-700 rounded-full w-14 h-14 hover:bg-blue-800 dark:bg-blue-600 dark:hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 focus:outline-none dark:focus:ring-blue-800 ">
                <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                    viewBox="0 0 16 3">
                    <path
                        d="M2 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Zm6.041 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM14 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Z" />
                </svg>
                <span class="sr-only">Open actions menu</span>
            </button>
        </div>






        <div class="hidden w-full md:block md:w-auto" id="navbar-default">
            <ul class="flex flex-col md:flex-row md:space-x-8 md:mt-0">
                <li><a href="/"
                        class="nav-link block py-2 px-3 rounded hover:bg-gray-800 hover:text-blue-500 md:hover:bg-transparent md:hover:text-sky-400">En
                        Vivo</a></li>
                <li><a href="/bus-municipal#about"
                        class="nav-link block py-2 px-3 rounded hover:bg-gray-800 hover:text-blue-500 md:hover:bg-transparent md:hover:text-sky-400">Sobre
                        Nosotros</a></li>
                <li>
                    <button id="dropdownNavbarLink" data-dropdown-toggle="dropdownNavbar"
                        class="nav-link flex items-center justify-between w-full py-2 px-3 rounded hover:bg-gray-800 hover:text-sky-500 md:hover:bg-transparent md:hover:text-sky-400">
                        Rutas
                        <svg class="w-4 h-4 ml-1" aria-hidden="true" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                    <div id="dropdownNavbar"
                        class="z-50 hidden font-normal bg-gray-900 divide-y divide-gray-100 rounded-lg shadow w-44">
                        <ul class="py-2 text-sm text-gray-200" aria-labelledby="dropdownNavbarLink">
                            <li><a href="{{ route('ruta-norte') }}" class="block px-4 py-2 hover:bg-gray-700">Ruta
                                    Norte</a></li>
                            <li><a href="{{ route('ruta-sur') }}" class="block px-4 py-2 hover:bg-gray-700">Ruta Sur</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li><a href="/bus-municipal#pasajes"
                        class="nav-link block py-2 px-3 rounded hover:bg-gray-800 hover:text-blue-500 md:hover:bg-transparent md:hover:text-sky-400">Pasajes
                        y Horarios</a></li>
            </ul>

        </div>
    </div>
</nav>