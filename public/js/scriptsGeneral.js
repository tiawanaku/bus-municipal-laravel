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