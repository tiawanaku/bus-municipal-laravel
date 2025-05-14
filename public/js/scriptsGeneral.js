/* Menú Dropdown */
document.addEventListener("DOMContentLoaded", function () {
    const toggleButton = document.querySelector("[data-dial-toggle]");
    const menu = document.getElementById("speed-dial-menu-dropdown");

    // Alternar visibilidad del menú
    toggleButton.addEventListener("click", function (event) {
      event.stopPropagation(); 
      menu.classList.toggle("hidden");
    });

    // Cerrar el menú al hacer clic fuera
    document.addEventListener("click", function (event) {
      if (!menu.contains(event.target) && !toggleButton.contains(event.target)) {
        menu.classList.add("hidden");
      }
    });

    // Cerrar el menú al hacer clic en alguna opción
    menu.querySelectorAll("a").forEach(link => {
      link.addEventListener("click", () => {
        menu.classList.add("hidden");
      });
    });
  });

  /* Para las cards de las paradas en cada página de la Ruta */
  
  document.addEventListener('DOMContentLoaded', function () {
    const cards = document.querySelectorAll('.parada');

    cards.forEach(card => {
        const textOverlay = card.querySelector('.info-overlay');

        // Detectar si es dispositivo táctil o no
        const isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;

        if (isTouchDevice) {
            // Modo móvil: mostrar texto al hacer clic
            card.addEventListener('click', (e) => {
                e.stopPropagation();
                textOverlay.classList.toggle('opacity-100');
            });

            // Ocultar texto al hacer clic fuera de la tarjeta
            document.addEventListener('click', (e) => {
                if (!card.contains(e.target)) {
                    textOverlay.classList.remove('opacity-100');
                }
            });
        } else {
            // Modo escritorio: mostrar texto al pasar el ratón
            card.addEventListener('mouseenter', () => {
                textOverlay.classList.add('opacity-100');
            });
            card.addEventListener('mouseleave', () => {
                textOverlay.classList.remove('opacity-100');
            });
        }
    });
});


/* Efectos para los links del Navbar */
 document.addEventListener('DOMContentLoaded', function () {
        const navLinks = document.querySelectorAll('.nav-link');

        // Recupera el enlace activo almacenado y aplícalo
        const activeLink = localStorage.getItem('activeNavLink');
        if (activeLink) {
            const currentActive = document.querySelector(`.nav-link[href="${activeLink}"]`);
            if (currentActive) {
                currentActive.classList.add('bg-blue-500', 'text-white');
            }
        }

        navLinks.forEach(link => {
            link.addEventListener('click', function () {
                // Eliminar la clase activa de todos los enlaces
                navLinks.forEach(link => link.classList.remove('bg-blue-500', 'text-white'));

                // Añadir la clase activa al enlace clicado
                this.classList.add('bg-blue-500', 'text-white');

                localStorage.setItem('activeNavLink', this.getAttribute('href'));
            });
        });
    });


    /* Función para el Iframe y manejar video de youtube o faecbook */
    