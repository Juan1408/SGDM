"use strict";

/*
================================================
   PESTAÑAS DEL PERFIL DE EQUIPO
   Miembros / Torneos / Ranking
   Los tres paneles ya vienen renderizados desde
   el servidor; acá solo se alterna su visibilidad.
================================================
*/

document.addEventListener(
   "DOMContentLoaded",
   () => {
      const botones = Array.from(
         document.querySelectorAll(
            ".equipo-tab[data-tab]"
         )
      );

      const paneles = Array.from(
         document.querySelectorAll(
            ".equipo-pagina [data-panel]"
         )
      );

      if (!botones.length || !paneles.length) {
         return;
      }

      const panelesValidos = paneles.map(
         (panel) => panel.dataset.panel
      );

      const activar = (nombre, actualizarHash) => {
         const destino = panelesValidos.includes(nombre)
            ? nombre
            : "miembros";

         botones.forEach((boton) => {
            const activo = boton.dataset.tab === destino;

            boton.classList.toggle("active", activo);
            boton.setAttribute(
               "aria-selected",
               activo ? "true" : "false"
            );
         });

         paneles.forEach((panel) => {
            panel.hidden = panel.dataset.panel !== destino;
         });

         if (actualizarHash) {
            const url = new URL(window.location.href);

            if (destino === "miembros") {
               url.hash = "";
            } else {
               url.hash = destino;
            }

            window.history.replaceState(null, "", url);
         }
      };

      botones.forEach((boton) => {
         boton.addEventListener("click", () => {
            activar(boton.dataset.tab, true);

            const panel = document.getElementById(
               boton.getAttribute("aria-controls")
            );

            if (panel) {
               panel.scrollIntoView({
                  behavior: "smooth",
                  block: "start"
               });
            }
         });
      });

      const hash = window.location.hash.replace("#", "");

      activar(
         hash === "torneos" || hash === "ranking"
            ? hash
            : "miembros",
         false
      );
   }
);
