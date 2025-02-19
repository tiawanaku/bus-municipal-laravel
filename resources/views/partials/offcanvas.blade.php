<!-- drawer (OffCanvaRUTAS) -->
<div id="drawer-right-rutas"
    class="fixed top-0 right-0 z-50 h-screen p-4 overflow-y-auto transition-transform translate-x-full bg-gray-700 w-80 dark:bg-gray-800"
    tabindex="-1" aria-labelledby="drawer-right-label">
    <h5 id="drawer-right-label"
        class="inline-flex items-center mb-4 text-base font-semibold text-gray-500 dark:text-gray-400"><svg
            class="w-4 h-4 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
            viewBox="0 0 20 20">
            <path
                d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
        </svg>Seleccione Ruta</h5>
    <button type="button" data-drawer-hide="drawer-right-rutas" aria-controls="drawer-right-example"
        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 absolute top-2.5 end-2.5 inline-flex items-center justify-center dark:hover:bg-gray-600 dark:hover:text-white">
        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
        </svg>
        <span class="sr-only">Close menu</span>
    </button>
    <!-- Tabs Rutas -->
    <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
        <ul class="flex flex-wrap -mb-px text-lg font-medium text-center" id="default-tab"
            data-tabs-toggle="#default-tab-content" role="tablist">
            <li class="me-2" role="presentation">
                <button
                    class="inline-block p-4 border-b-2 rounded-t-lg border-transparent text-sky-500 hover:bg-blue-900 hover:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition duration-200"
                    id="profile-tab" data-tabs-target="#profile" type="button" role="tab" aria-controls="profile"
                    aria-selected="true">
                    Ruta Norte
                </button>
            </li>
            <li class="me-2" role="presentation">
                <button
                    class="inline-block p-4 border-b-2 rounded-t-lg border-transparent text-red-500 hover:bg-red-800 hover:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition duration-200"
                    id="dashboard-tab" data-tabs-target="#dashboard" type="button" role="tab" aria-controls="dashboard"
                    aria-selected="false">
                    Ruta Sur
                </button>
            </li>
        </ul>
    </div>


    <!-- RADIO SENTIDO -->

    <ul class="grid w-full gap-6 md:grid-cols-2">
        <li>
            <input type="radio" id="ida" name="ruta" value="ida" class="hidden peer" required />
            <label for="ida"
                class="inline-flex items-center justify-between w-full p-5 text-gray-500  border border-gray-600 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-blue-500  peer-checked:text-white peer-checked:border-blue-600 hover:text-gray-200 hover:bg-gray-600 dark:text-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700">
                <div class="block">
                    <div class="w-full text-lg font-semibold">Ida</div>
                    <div class="flex justify-center mt-0">
                        <svg xmlns="http://www.w3.org/2000/svg" height="30px" viewBox="0 -960 960 960" width="40px"
                            fill="#FFFFFF">
                            <path d="M647-440H160v-80h487L423-744l57-56 320 320-320 320-57-56 224-224Z" />
                        </svg>
                    </div>
                </div>

            </label>
        </li>
        <li>
            <input type="radio" id="vuelta" name="ruta" value="vuelta" class="hidden peer">
            <label for="vuelta"
                class="inline-flex items-center justify-between w-full p-5 text-gray-500  border border-gray-600 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-blue-500  peer-checked:text-white  peer-checked:border-blue-600 hover:text-gray-200 hover:bg-gray-600 dark:text-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700">
                <div class="block">
                    <div class="w-full text-lg font-semibold">Vuelta</div>
                    <div class="flex justify-center mt-0">
                        <svg xmlns="http://www.w3.org/2000/svg" height="30px" viewBox="0 -960 960 960" width="40px"
                            fill="#FFFFFF">
                            <path d="m287-446.67 240 240L480-160 160-480l320-320 47 46.67-240 240h513v66.66H287Z" />
                        </svg>
                    </div>
                </div>

            </label>
        </li>
    </ul>

    <div id="default-tab-content">

        <div class="hidden p-4 rounded-lg bg-gray-700 dark:bg-gray-800" id="profile" role="tabpanel"
            aria-labelledby="profile-tab">


            <!-- Grid Paradas Norte -->
            <div id="ruta-norte" class="grid grid-cols-1 gap-2">
            <div>
                    <img class="h-auto max-w-full rounded-lg parada-image" src="/img/paradasNorte/paradaCEIBO.png" alt="" data-id="1">
                </div>
            <div>
                    <img class="h-auto max-w-full rounded-lg parada-image" src="/img/paradasNorte/paradaEXTRANSITO.png" alt="" data-id="2">
                </div>
                <div>
                    <img class="h-auto max-w-full rounded-lg parada-image" src="/img/paradasNorte/paradaCRUZPAPAL.png" alt="" data-id="3">
                </div>
                <div>
                    <img class="h-auto max-w-full rounded-lg parada-image" src="/img/paradasNorte/paradaLAPAZ.png" alt="" data-id="5">
                </div>
                <div>
                    <img class="h-auto max-w-full rounded-lg parada-image" src="/img/paradasNorte/paradaUPEA.png" alt="" data-id="7">
                </div>
                <div>
                    <img class="h-auto max-w-full rounded-lg parada-image" src="/img/paradasNorte/paradaRIOSECO.png" alt="" data-id="9">
                </div>
                <div>
                    <img class="h-auto max-w-full rounded-lg parada-image" src="/img/paradasNorte/paradaEXPARADA8.png" alt="" data-id="11">
                </div>
                <div>
                    <img class="h-auto max-w-full rounded-lg parada-image" src="/img/paradasNorte/paradaCRUCELAGUNAS.png" alt="" data-id="13">
                </div>
                <div>
                    <img class="h-auto max-w-full rounded-lg parada-image" src="/img/paradasNorte/paradaBELLAVISTA.png" alt="" data-id="15">
                </div>
                <div>
                    <img class="h-auto max-w-full rounded-lg parada-image" src="/img/paradasNorte/paradaSANROQUE.png" alt="" data-id="17">
                </div>
                <div>
                    <img class="h-auto max-w-full rounded-lg parada-image" src="/img/paradasNorte/paradaPLAYAVERDE.png" alt="" data-id="19">
                </div>
            </div>

        </div>


        <div class="hidden p-4 rounded-lg bg-gray-700 dark:bg-gray-800" id="dashboard" role="tabpanel"
            aria-labelledby="dashboard-tab">



            <!-- Grid Paradas Sur -->
            <div id="ruta-sur" class="grid grid-cols-1 gap-2">

                <div>
                    <img class="h-auto max-w-full rounded-lg parada-image" src="/img/paradasSur/paradaCALLE3.png" alt=""
                        data-id="21">
                </div>
                <div>
                    <img class="h-auto max-w-full rounded-lg parada-image" src="/img/paradasSur/paradaCALLE7.png" alt=""
                        data-id="22">
                </div>
                <div>
                    <img class="h-auto max-w-full rounded-lg parada-image" src="/img/paradasSur/paradaCRUCEVIACHA.png"
                        alt="" data-id="24">
                </div>
                <div>
                    <img class="h-auto max-w-full rounded-lg parada-image"
                        src="/img/paradasSur/paradaTELEFERICOMORADO.png" alt="" data-id="26">
                </div>
                <div>
                    <img class="h-auto max-w-full rounded-lg parada-image" src="/img/paradasSur/paradaPUENTEBOLIVIA.png"
                        alt="" data-id="28">
                </div>
                <div>
                    <img class="h-auto max-w-full rounded-lg parada-image" src="/img/paradasSur/paradaMOLINOANDINO.png"
                        alt="" data-id="30">
                </div>
                <div>
                    <img class="h-auto max-w-full rounded-lg parada-image" src="/img/paradasSur/paradaRIELESSENKATA.png"
                        alt="" data-id="32">
                </div>
                <div>
                    <img class="h-auto max-w-full rounded-lg parada-image" src="/img/paradasSur/paradaCRUCESENKATA.png"
                        alt="" data-id="34">
                </div>
                <div>
                    <img class="h-auto max-w-full rounded-lg parada-image" src="/img/paradasSur/paradaPUENTEVELA.png"
                        alt="" data-id="36">
                </div>
                <div>
                    <img class="h-auto max-w-full rounded-lg parada-image" src="/img/paradasSur/paradaCRUCEVENTILLA.png"
                        alt="" data-id="38">
                </div>
                <div>
                    <img class="h-auto max-w-full rounded-lg parada-image" src="/img/paradasSur/paradaCRUCELAYURI.png"
                        alt="" data-id="40">
                </div>
                <div>
                    <img class="h-auto max-w-full rounded-lg parada-image" src="/img/paradasSur/paradaSAMO.png"
                        alt="" data-id="40">
                </div>
            </div>
        </div>

    </div>
</div>



<!-- Modal Información -->
<div id="modal-informacion" tabindex="-1" aria-hidden="true"
    class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-2xl max-h-full">
        <!-- Modal content -->
        <div class="relative bg-gray-800 rounded-lg shadow dark:bg-gray-700">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-xl font-semibold text-gray-100 dark:text-white">
                    Bus Municipal Guía para el Usuario
                </h3>
                <button type="button"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    data-modal-hide="modal-informacion">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <div class="p-4 md:p-5 space-y-4">

                <div id="accordion-color" data-accordion="collapse"
                    data-active-classes="bg-blue-100 dark:bg-gray-800 text-blue-600 dark:text-white">
                    <h2 id="accordion-color-heading-1">
                        <button type="button"
                            class="flex items-center justify-between w-full p-5 font-medium rtl:text-right text-gray-500 border border-b-0 border-gray-200 rounded-t-xl focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-800 dark:border-gray-700 dark:text-gray-400 hover:bg-blue-100 dark:hover:bg-gray-800 gap-3"
                            data-accordion-target="#accordion-color-body-1" aria-expanded="true"
                            aria-controls="accordion-color-body-1">
                            <span>Pasaje Regular y Pasaje Preferencial</span>
                            <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M9 5 5 1 1 5" />
                            </svg>
                        </button>
                    </h2>
                    <div id="accordion-color-body-1" class="hidden" aria-labelledby="accordion-color-heading-1">
                        <div class="p-5 border border-b-0 border-gray-200 dark:border-gray-700 dark:bg-gray-900">
                           <img src="img/informacion/pasajesBus.png" alt="">
                        </div>
                    </div>
                    <h2 id="accordion-color-heading-2">
                        <button type="button"
                            class="flex items-center justify-between w-full p-5 font-medium rtl:text-right text-gray-500 border border-b-0 border-gray-200 focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-800 dark:border-gray-700 dark:text-gray-400 hover:bg-blue-100 dark:hover:bg-gray-800 gap-3"
                            data-accordion-target="#accordion-color-body-2" aria-expanded="false"
                            aria-controls="accordion-color-body-2">
                            <span>Reglas para usar el bus</span>
                            <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M9 5 5 1 1 5" />
                            </svg>
                        </button>
                    </h2>
                    <div id="accordion-color-body-2" class="hidden" aria-labelledby="accordion-color-heading-2">
                        <div class="p-5 border border-b-0 border-gray-200 dark:border-gray-700">
                            <img src="img/informacion/reglasBus.png" alt="">
                        </div>
                    </div>
                    <h2 id="accordion-color-heading-3">
                        <button type="button"
                            class="flex items-center justify-between w-full p-5 font-medium rtl:text-right text-gray-500 border border-gray-200 focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-800 dark:border-gray-700 dark:text-gray-400 hover:bg-blue-100 dark:hover:bg-gray-800 gap-3"
                            data-accordion-target="#accordion-color-body-3" aria-expanded="false"
                            aria-controls="accordion-color-body-3">
                            <span>Nuestras Restricciones</span>
                            <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M9 5 5 1 1 5" />
                            </svg>
                        </button>
                    </h2>
                    <div id="accordion-color-body-3" class="hidden" aria-labelledby="accordion-color-heading-3">
                        <div class="p-5 border border-t-0 border-gray-200 dark:border-gray-700">
                        <img src="img/informacion/restriccionesBus.png" alt="">

                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<!-- Modal para pantallas pequeñas -->

<!-- Main modal Avisos -->
<div id="modal-avisos" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <!-- Modal content -->
        <div class="relative bg-gray-700 rounded-lg shadow dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-2xl font-bold text-white mb-4">
                        Avisos
                    </h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm h-8 w-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="modal-avisos">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 md:p-5">
                <div id="modal-content" class="overflow-y-auto max-h-60vh">
                @foreach($avisos as $aviso)
                            <div class="card border-2 border-red-800 rounded-lg shadow-md bg-gray-700 mb-4">
                                <div class="p-4">
                                    <h5 class="font-semibold text-lg text-gray-200">{{ $aviso->title }}</h5>
                                    <p class="font-sans tracking-tight text-gray-200">{{ $aviso->description }}</p>
                                    <small class="text-gray-400">{{ $aviso->updated_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @endforeach
                  </div>
                  
                </div>
            </div>
    </div>
</div> 






<script>
    
   

    // Función para generar las cards
 /*    function generarCardsAvisos(contenedor) {
        contenedor.innerHTML = ''; 

        avisos.forEach(aviso => {
            const card = document.createElement('div');
            card.className = 'card border-2 border-red-800 rounded-lg shadow-md bg-gray-700 mb-4';
            card.innerHTML = `
                <img src="${aviso.imgSrc}" class="rounded-t-lg" alt="...">
                <div class="p-4">
                    <h5 class="font-semibold text-lg text-gray-200">Noticia: ${aviso.noticia}</h5>
                    <p class="font-sans tracking-tight text-gray-200">Duración: ${aviso.inicio_periodo} ~ ${aviso.fin_periodo}</p>
                    <p class="font-sans tracking-tight text-gray-200">Razón: ${aviso.razon}</p>
                    <p class="font-sans tracking-tight text-gray-200">Paradas afectadas: ${aviso.paradas_afectadas}</p>
                    <p class="font-sans tracking-tight text-gray-200">Detalle: ${aviso.detalle}</p>
                    <small class="text-gray-400">Actualizado hace ${aviso.created_at_humano}</small>
                </div>
            `;
            contenedor.appendChild(card);
        });
    } */

    // Insertar cards en el modal y en el contenedor de pantallas grandes
   /*  window.addEventListener('DOMContentLoaded', function () {
        const modalContent = document.getElementById('modal-content');
        const cardAvisosContent = document.getElementById('card-avisos');

        generarCardsAvisos(modalContent); 
        generarCardsAvisos(cardAvisosContent); 
    });

   
   
 */
    
    
</script>