/* Función para el carrusel de Galeria */
// Array para imagenes en carpeta Galeria
document.addEventListener('DOMContentLoaded', function() {
    const images = [
      "/img/Galeria/img_1.JPG",
      "/img/Galeria/img_2.JPG",
      "/img/Galeria/img_3.JPG",
      "/img/Galeria/img_4.JPG",
      "/img/Galeria/img_5.JPG",
      "/img/Galeria/img_6.JPG",
      "/img/Galeria/img_7.JPG",
      "/img/Galeria/img_8.JPG",
      "/img/Galeria/img_9.JPG",
      "/img/Galeria/img_10.JPG",
      "/img/Galeria/img_11.JPG",
    ];
  
    let currentIndex = 0;
    const gallery = document.getElementById('gallery');
    

if (gallery) {
    
    images.forEach((src, index) => {
        const img = document.createElement('img');
        img.src = src;
        img.alt = `Imagen ${index + 1}`;
        img.className = 'cursor-pointer rounded shadow';
        img.onclick = () => openCarousel(index);
        gallery.appendChild(img);
      });
}
  
  
    function openCarousel(index) {
      currentIndex = index;
      document.getElementById('carouselImage').src = images[currentIndex];
      document.getElementById('carouselModal').classList.remove('hidden');
    }
  
    function closeCarousel() {
      document.getElementById('carouselModal').classList.add('hidden');
    }
  
    function nextImage() {
      currentIndex = (currentIndex + 1) % images.length;
      document.getElementById('carouselImage').src = images[currentIndex];
    }
  
    function prevImage() {
      currentIndex = (currentIndex - 1 + images.length) % images.length;
      document.getElementById('carouselImage').src = images[currentIndex];
    }
  
    function handleBackdropClick(event) {
      if (event.target.id === 'carouselModal') {
        closeCarousel();
      }
    }
  
    
    window.openCarousel = openCarousel;
    window.closeCarousel = closeCarousel;
    window.nextImage = nextImage;
    window.prevImage = prevImage;
    window.handleBackdropClick = handleBackdropClick;
  });
  
/* fin */