<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- TAILWIND CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <!-- FLOWBITE CSS -->
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.css" rel="stylesheet" />

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>

     <!-- GoogleFonts -->
     <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
     <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Quicksand:wght@300..700&display=swap" rel="stylesheet">


    <link rel="stylesheet" href="css/style.css" />

    <title>BusMunicipal</title>
</head>

<body class="bg-gradient-to-r from-blue-900 to-black-800 min-h-screen flex flex-col ">

    <!-- HEADER -->
    @include('partials.header') 
    <!-- FIN HEADER -->

<!-- MENU -->
@include('partials.menu') 
 <!-- FIN MENU -->


    <!-- CONTENIDO -->
    @include('partials.index') 
    <!-- fin Contenido -->

    <!-- FOOTER -->
    @include('partials.footer') 
    <!-- FIN FOOTER -->


    @include('partials.offcanvas')

    <!-- JavaScript -->
    <script src="{{ asset('js/scripts.js') }}"></script>
    <!-- Flowbite  -->
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.js"></script>
</body>

</html>