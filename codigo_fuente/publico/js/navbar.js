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


//La campana muestra las notificaciones reales de la base, renderizadas por
//vistas/componentes/panel-notificaciones.php. Aqui solo se abre y cierra el panel.
const campanaToggle = document.getElementById("campana-toggle");
const panelNotificaciones = document.getElementById("panel-notificaciones");

if (campanaToggle && panelNotificaciones) {
   campanaToggle.addEventListener("click", (evento) => {
      evento.stopPropagation();
      const abierto = panelNotificaciones.classList.toggle("open");
      campanaToggle.setAttribute("aria-expanded", String(abierto));
   });
}

const miPerfilToggle = document.getElementById("mi-perfil-toggle");
const dropdownMiPerfil = document.getElementById("dropdown-mi-perfil");

if (miPerfilToggle && dropdownMiPerfil) {
   miPerfilToggle.addEventListener("click", (evento) => {
      evento.stopPropagation();
      const abierto = dropdownMiPerfil.classList.toggle("open");
      miPerfilToggle.setAttribute("aria-expanded", String(abierto));
   });
}

document.addEventListener("click", () => {
   if (panelNotificaciones) panelNotificaciones.classList.remove("open");
   if (dropdownMiPerfil) dropdownMiPerfil.classList.remove("open");
});


   }
);