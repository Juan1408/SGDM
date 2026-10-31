"use strict";

const grupos = document.querySelectorAll(".torneo-menu-grupo");

document.querySelectorAll(".torneo-menu-toggle").forEach((boton) => {
   boton.addEventListener("click", (evento) => {
      evento.stopPropagation();
      const grupoActual = boton.closest(".torneo-menu-grupo");

      grupos.forEach((grupo) => {
         if (grupo !== grupoActual) {
            grupo.classList.remove("open");
         }
      });

      grupoActual.classList.toggle("open");
   });
});

document.addEventListener("click", (evento) => {
   if (!evento.target.closest(".torneo-menu-grupo")) {
      grupos.forEach((grupo) => grupo.classList.remove("open"));
   }
});

document.querySelectorAll("[data-section]").forEach((boton) => {
   boton.addEventListener("click", () => {
      window.location.href = `${window.ADMINISTRAR_URL}&seccion=${boton.dataset.section}`;
   });
});