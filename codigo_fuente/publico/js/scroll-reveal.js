// =========================================================
// SCROLL REVEAL — anima los elementos con clase "reveal"
// para que aparezcan con fade-in al entrar en pantalla
// =========================================================
document.addEventListener('DOMContentLoaded', function () {
   const elementos = document.querySelectorAll('.reveal');

   if (!elementos.length) return;

   const observer = new IntersectionObserver(function (entradas) {
      entradas.forEach(function (entrada) {
         if (entrada.isIntersecting) {
            entrada.target.classList.add('visible');
            observer.unobserve(entrada.target);
         }
      });
   }, {
      threshold: 0.15
   });

   elementos.forEach(function (el) {
      observer.observe(el);
   });
});