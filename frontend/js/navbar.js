"use strict";

document.addEventListener(
   "navbarPublicoListo",
   () => {
      const menuToggle =
         document.getElementById("menu-toggle");

      const navLinks =
         document.getElementById("nav-links");

      const desplegable =
         document.querySelector(".desplegable");

      const desplegableBtn =
         document.getElementById(
            "torneo-desplegable"
         );

      /*
      ================================================
         MENÚ HAMBURGUESA
      ================================================
      */

      if (menuToggle && navLinks) {
         menuToggle.addEventListener(
            "click",
            () => {
               const abierto =
                  navLinks.classList.toggle(
                     "active"
                  );

               menuToggle.classList.toggle(
                  "active",
                  abierto
               );

               menuToggle.setAttribute(
                  "aria-expanded",
                  String(abierto)
               );
            }
         );
      }

      /*
      ================================================
         DESPLEGABLE DE TORNEOS
      ================================================
      */

      if (desplegable && desplegableBtn) {
         desplegableBtn.addEventListener(
            "click",
            (evento) => {
               evento.stopPropagation();

               const abierto =
                  desplegable.classList.toggle(
                     "open"
                  );

               desplegableBtn.setAttribute(
                  "aria-expanded",
                  String(abierto)
               );
            }
         );

         document.addEventListener(
            "click",
            (evento) => {
               if (
                  !desplegable.contains(
                     evento.target
                  )
               ) {
                  desplegable.classList.remove(
                     "open"
                  );

                  desplegableBtn.setAttribute(
                     "aria-expanded",
                     "false"
                  );
               }
            }
         );
      }
   }
);