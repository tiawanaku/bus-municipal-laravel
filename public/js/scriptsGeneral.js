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

/* Animación para desplazar li de izquierda a derecha */
document.addEventListener("DOMContentLoaded", function () {
    function animarLista(olSelector, delayStep = 300) {
        const elementos = document.querySelectorAll(olSelector + " li");
        elementos.forEach((el, index) => {
            el.style.opacity = "0";
            el.style.transform = "translateX(-100px)";
            el.style.transition = "opacity 0.6s ease-out, transform 0.6s ease-out";

            setTimeout(() => {
                el.style.opacity = "1";
                el.style.transform = "translateX(0)";
            }, index * delayStep);
        });
    }

    // Ejecutar ambas animaciones al mismo tiempo
    animarLista("#lista-ida");
    animarLista("#lista-vuelta");
});

/* Efecto de Escritura  */
document.addEventListener("DOMContentLoaded", function () {
    const elements = document.querySelectorAll('.typewrite');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !entry.target.classList.contains('animated')) {
                const el = entry.target;
                const text = el.getAttribute('data-text');
                let index = 0;
                const speed = 100;

                function typeChar() {
                    if (index < text.length) {
                        el.textContent += text.charAt(index);
                        index++;
                        setTimeout(typeChar, speed);
                    }
                }

                el.classList.add('animated');
                typeChar();
            }
        });
    }, { threshold: 0.8 });

    elements.forEach(el => observer.observe(el));
});
    