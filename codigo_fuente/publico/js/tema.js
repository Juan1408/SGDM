/* Modo día / modo noche de ASCEND.
   Se carga en el <head> de cada página para aplicar el tema guardado
   antes de pintar (evita el destello oscuro al cambiar de página). */
(function () {
   if (window.__ascendTema) { return; }
   window.__ascendTema = true;

   var CLAVE = 'ascend-tema';
   var raiz = document.documentElement;

   function leer() {
      try { return localStorage.getItem(CLAVE); } catch (e) { return null; }
   }

   function guardar(valor) {
      try { localStorage.setItem(CLAVE, valor); } catch (e) { /* sin almacenamiento */ }
   }

   function aplicar(tema) {
      if (tema === 'light') {
         raiz.setAttribute('data-theme', 'light');
      } else {
         raiz.removeAttribute('data-theme');
      }
   }

   aplicar(leer());

   document.addEventListener('DOMContentLoaded', function () {
      if (document.querySelector('.boton-tema')) { return; }

      var boton = document.createElement('button');
      boton.type = 'button';
      boton.className = 'boton-tema';

      function pintar() {
         var dia = raiz.getAttribute('data-theme') === 'light';
         boton.textContent = dia ? '\u{1F319}' : '☀️';
         boton.setAttribute('aria-label', dia ? 'Cambiar a modo noche' : 'Cambiar a modo día');
         boton.title = dia ? 'Modo noche' : 'Modo día';
      }

      boton.addEventListener('click', function () {
         var nuevo = raiz.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
         aplicar(nuevo);
         guardar(nuevo);
         pintar();
      });

      pintar();
      document.body.appendChild(boton);
   });
})();