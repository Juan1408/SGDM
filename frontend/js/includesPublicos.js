"use strict";

/*
=====================================================
   CARGA DE COMPONENTES PÚBLICOS
=====================================================
*/

document.addEventListener("DOMContentLoaded", () => {
   cargarNavbarPublico();
   cargarFooterPublico();
});


/*
=====================================================
   NAVBAR
=====================================================
*/

async function cargarNavbarPublico() {
   const contenedor = document.getElementById(
      "navbar-publico-placeholder"
   );

   if (!contenedor) {
      return;
   }

   try {
      const respuesta = await fetch(
         "../../componentes/navbar-publico.html"
      );

      if (!respuesta.ok) {
         throw new Error(
            `Error HTTP ${respuesta.status}`
         );
      }

      contenedor.innerHTML =
         await respuesta.text();

      /*
         El navbar ya está en el DOM.
         Ahora navbar.js puede conectar sus eventos.
      */
      document.dispatchEvent(
         new Event("navbarPublicoListo")
      );

   } catch (error) {
      console.error(
         "Error cargando el navbar público:",
         error
      );
   }
}


/*
=====================================================
   FOOTER
=====================================================
*/

async function cargarFooterPublico() {
   const contenedor = document.getElementById(
      "footer-publico-placeholder"
   );

   if (!contenedor) {
      return;
   }

   try {
      const respuesta = await fetch(
         "../../componentes/footer.html"
      );

      if (!respuesta.ok) {
         throw new Error(
            `Error HTTP ${respuesta.status}`
         );
      }

      contenedor.innerHTML =
         await respuesta.text();

   } catch (error) {
      console.error(
         "Error cargando el footer público:",
         error
      );
   }
}