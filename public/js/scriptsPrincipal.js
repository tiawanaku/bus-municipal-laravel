/* Función para el carrusel de Galeria */
let currentIndex = 0;

// Abre el modal y muestra la imagen seleccionada
function openModal(index) {
  currentIndex = index;
  const image = images[currentIndex];
  document.getElementById('modal').classList.remove('hidden');
  document.getElementById('modal-image').src = `/storage/${image.imagen}`;
  document.getElementById('modal-description').innerText = image.descripcion;
}

// Cierra el modal
function closeModal(event) {
  if (event) event.stopPropagation();  // Evita que se cierre si el clic es dentro del modal
  document.getElementById('modal').classList.add('hidden');
}

// Cambia la imagen (anterior o siguiente)
function changeImage(direction) {
  currentIndex = (currentIndex + direction + images.length) % images.length;
  const image = images[currentIndex];
  document.getElementById('modal-image').src = `/storage/${image.imagen}`;
  document.getElementById('modal-description').innerText = image.descripcion;
}
/* fin */