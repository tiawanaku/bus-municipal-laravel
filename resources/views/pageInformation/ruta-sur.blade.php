@extends('layouts.app')
@section('content')

    <section>
        <div class="bg-gray-900 text-white">
            <!-- Sección Principal -->
            <section
                class="relative min-h-screen flex items-center justify-center px-6 lg:px-16 bg-cover bg-center bg-no-repeat"
                style="background-image: url('/img/paradasRutaSur/calle3.jpg');">


                <div class="absolute inset-0 bg-black bg-opacity-60"></div>

                <!-- Contenido con filtro aplicado -->
                <div class="relative z-10 text-center text-white max-w-3xl">
                    <h1 class="text-5xl font-bold mb-6 text-indigo-400 text-shadow-lg/30">RUTA SUR</h1>
                    <p class="text-gray-200 leading-relaxed text-lg">
                        La <span class="font-bold text-white">Ruta Sur</span> conecta las principales zonas del lado Sur de
                        la
                        Ciudad de El Alto con el eje central la Ceja.
                        Este servicio está diseñado para garantizar un transporte eficiente y cómodo para todos los
                        usuarios.
                    </p>
                </div>
            </section>




            <!-- Sección de Video y Paradas -->
            <section class="bg-gray-800 py-16">
                <div class="container mx-auto grid grid-cols-1 lg:grid-cols-1 gap-8 items-start">
                    <!-- Video -->
                    <div style="display: flex; justify-content: center;">
                        <iframe
                            src="https://www.facebook.com/plugins/video.php?height=450&href=https%3A%2F%2Fwww.facebook.com%2FElAltoAlcaldia%2Fvideos%2F691230882925633%2F&show_text=false&width=800&t=0"
                            width="800" height="450" style="border:none; overflow:hidden;" scrolling="no" frameborder="0"
                            allowfullscreen="true"
                            allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share">
                        </iframe>
                    </div>






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



@endsection