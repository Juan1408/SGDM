/* Riel lateral de escritorio (jugador y organizador):
   submenú plegable y que no pise el footer. */
document.addEventListener("DOMContentLoaded", () => {
   const riel = document.querySelector(".sidebar-jugador-flotante");
   if (!riel) return;

   riel.querySelectorAll(".sidebar-flotante-toggle").forEach((boton) => {
      boton.addEventListener("click", () => {
         const item = boton.closest(".sidebar-flotante-desplegable");
         const cerrado = item.classList.toggle("cerrado");
         boton.setAttribute("aria-expanded", String(!cerrado));
      });
   });

   const footer = document.querySelector("footer");
   const ajustar = () => {
      const tope = footer ? footer.getBoundingClientRect().top : window.innerHeight;
      riel.style.bottom = Math.max(0, window.innerHeight - tope) + "px";
   };
   window.addEventListener("scroll", ajustar, { passive: true });
   window.addEventListener("resize", ajustar);
   window.addEventListener("load", ajustar);
   ajustar();
});